<?php
/**
 * @author gareth.evans
 */
namespace Qubes\Defero\Applications\Defero;

use Cubex\Core\Application\Application;
use Cubex\Data\Ephemeral\EphemeralCache;
use Cubex\Foundation\Config\Config;
use Cubex\Foundation\Config\ConfigGroup;
use Cubex\Foundation\Container;
use Cubex\Queue\StdQueue;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Contact\Mappers\Contact;
use Qubes\Defero\Transport\ProcessDefinition;
use Qubes\Defero\Transport\ProcessMessage;
use Themed\Sidekick\SidekickTheme;

class Defero extends Application
{
  public function name()
  {
    return "Defero";
  }

  public function description()
  {
    return "Mailer setup and configuration";
  }

  public function getTheme()
  {
    return new SidekickTheme();
  }

  public function defaultController()
  {
    return new Controllers\DeferoController();
  }

  public function getRoutes()
  {
    $base = '\Qubes\Defero\Applications\Defero\Controllers\\';
    return [
      "/campaigns/"     => [
        ":id@num/message/(.*)"     => $base . 'CampaignMessageController',
        ":id@num/source/(.*)"      => $base . 'CampaignSourceController',
        ":cid@num/processors/(.*)" => $base . 'CampaignProcessorsController',
        ":id@num/contacts/(.*)"    => $base . 'CampaignContactsController',
        "(.*)"                     => $base . 'CampaignsController',
      ],
      "/contacts/(.*)"  => $base . 'ContactsController',
      "/typeahead/(.*)" => $base . 'TypeAheadController',
      "/search/(.*)"    => $base . 'SearchController',
      "/wizard/(.*)"    => $base . 'WizardController',
    ];
  }

  public static function pushCampaign($campaignId, $startTime = null)
  {
    if($startTime === null)
    {
      $startTime = time();
      $startTime -= $startTime % 60;
    }
    $campaign = new Campaign($campaignId);
    $lastTime = $campaign->lastSent;

    $campaign->lastSent = $startTime;
    $campaign->saveChanges();

    $message = new ProcessMessage();
    $message->setData('campaign_id', $campaignId);
    $message->setData('started_at', $startTime);
    $message->setData('last_sent', $lastTime);

    \Queue::setDefaultQueueProvider("campaignqueue");
    \Queue::push(new StdQueue('defero_campaigns'), serialize($message));
  }

  public static function pushMessage($campaignId, $data)
  {
    self::pushMessageBatch($campaignId, [$data]);
  }

  public static function pushMessageBatch($campaignId, $batch)
  {
    $cacheId = 'DeferoQueueCampaign' . $campaignId;
    /**
     * @var Campaign $campaign
     * @var Contact  $contact
     */
    $campaign = EphemeralCache::getCache($cacheId, __CLASS__);
    if($campaign === null)
    {
      $campaign = new Campaign($campaignId);
      EphemeralCache::storeCache($cacheId, $campaign, __CLASS__);
    }

    $processorsCacheId = $cacheId . ':processors';
    $processors        = EphemeralCache::getCache(
      $processorsCacheId,
      __CLASS__
    );
    if($processors === null)
    {
      $processors       = [];
      $processorsConfig = Container::get(Container::CONFIG)->get('processors');
      foreach($campaign->processors as $processorData)
      {
        $config = new Config();
        $config->hydrate($processorData);

        $configGroup = new ConfigGroup();
        $configGroup->addConfig("process", $config);
        $process = new ProcessDefinition();
        $process->setProcessClass(
          $processorsConfig->getStr($processorData->processorType)
        );
        $process->setQueueName("defero");
        $process->setQueueService("queue");
        $process->configure($configGroup);
        $processors[] = $process;
      }
      EphemeralCache::storeCache($processorsCacheId, $processors, __CLASS__);
    }

    $messages = [];
    foreach($batch as $data)
    {
      $data['campaign_id'] = $campaignId;
      $data['mailer_tracking'] = $campaign->trackingType;

      $message = new ProcessMessage();
      $message->setData('data', $data);

      // move language here.
      $userLanguage    = 'en';
      $languageCacheId = $cacheId . ':' . $userLanguage;
      $msg             = EphemeralCache::getCache($languageCacheId, __CLASS__);
      if($msg === null)
      {
        $msg = $campaign->message();
        $msg->setLanguage($userLanguage);
        $msg->reload();

        EphemeralCache::storeCache($languageCacheId, $msg, __CLASS__);
      }

      $contactId      = $msg->contactId ? : $campaign->contactId;
      $contactCacheId = $cacheId . ':' . $contactId;
      $contact        = EphemeralCache::getCache($contactCacheId, __CLASS__);
      if($contact === null)
      {
        $contact = new Contact($contactId);
        EphemeralCache::storeCache($contactCacheId, $contact, __CLASS__);
      }
      $data['signature'] = $contact->signature;

      $message->setData(
        'senderName', self::replaceData($contact->name, $data)
      );
      $message->setData(
        'senderEmail', self::replaceData($contact->email, $data)
      );
      $message->setData(
        'sendType', self::replaceData($campaign->sendType, $data)
      );
      $message->setData(
        'subject', self::replaceData($msg->subject, $data)
      );
      $message->setData(
        'plainText', self::replaceData($msg->plainText, $data)
      );
      $message->setData(
        'htmlContent', self::replaceData($msg->htmlContent, $data, true)
      );

      foreach($processors as $process)
      {
        $message->addProcess($process);
      }
      $messages[] = serialize($message);
    }
    \Queue::setDefaultQueueProvider("messagequeue");
    \Queue::pushBatch(new StdQueue("defero_messages"), $messages);
  }

  public static function replaceData($text, $data, $nl2br = false)
  {
    foreach($data as $k => $v)
    {
      if($nl2br)
      {
        $v = nl2br($v);
      }
      $text = str_ireplace('{!' . $k . '}', $v, $text);
    }
    return $text;
  }
}
