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
use Qubes\Defero\Components\Campaign\Enums\SendType;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Campaign\Mappers\MailStatistic;
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

  public static function pushCampaign(
    $campaignId, $startTime = null, $startId = null, $endId = null
  )
  {
    if($startTime === null)
    {
      $startTime = time();
      $startTime -= $startTime % 60;
    }
    $campaign = new Campaign($campaignId);
    if(!$campaign->processors)
    {
      throw new \Exception('Cannot queue a Campaign with no Processors');
    }

    $lastTime = $campaign->lastSent;
    if($lastTime != $startTime)
    {
      $campaign->lastSent = $startTime;
      $campaign->saveChanges();

      $message = new ProcessMessage();
      $message->setData('campaignId', $campaignId);
      $message->setData('startedAt', $startTime);
      $message->setData('lastSent', $lastTime);
      $message->setData('startId', $startId);
      $message->setData('endId', $endId);

      \Queue::setDefaultQueueProvider("campaignqueue");
      \Queue::push(new StdQueue('defero_campaigns'), serialize($message));
      \Log::info('Queued Campaign ' . $campaignId);
      return true;
    }
    return false;
  }

  //TODO: Move statistics tracking here, from DataSource
  public static function pushMessage($campaignId, $data)
  {
    return self::pushMessageBatch($campaignId, [$data]);
  }

  public static function pushMessageBatch($campaignId, $batch)
  {
    if(!$batch)
    {
      return false;
    }

    $cacheId = 'DeferoQueueCampaign' . $campaignId;
    /**
     * @var Campaign $campaign
     * @var Contact  $contact
     */
    $campaign = EphemeralCache::getCache($cacheId, __CLASS__);
    if($campaign === null)
    {
      if(!is_numeric($campaignId))
      {
        $campaign = Campaign::collection()->loadOneWhere(
          '%C = %s', 'reference', $campaignId
        );
      }
      else
      {
        $campaign = new Campaign($campaignId);
      }
      EphemeralCache::storeCache($cacheId, $campaign, __CLASS__);
    }
    if(!$campaign || !$campaign->exists())
    {
      throw new \Exception('Campaign does not exist');
    }
    $campaignId = $campaign->id();

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

    $lastUserId = null;
    $messages   = [];
    foreach($batch as $data)
    {
      $message = new ProcessMessage();
      $message->setData('campaignId', $campaignId);
      $message->setData('mailerTracking', $campaign->trackingType);
      $message->setData('data', array_change_key_case($data));

      // move language here.
      $userLanguage    = !empty($data['language']) ? $data['language'] : 'en';
      $languageCacheId = $cacheId . ':language:' . $userLanguage;
      $msg             = EphemeralCache::getCache($languageCacheId, __CLASS__);
      if($msg === null)
      {
        $msg = $campaign->message();
        $msg->setLanguage($userLanguage);
        $msg->reload();

        if($userLanguage !== 'en'
          && (!$msg->subject
            || ($campaign->sendType != SendType::PLAIN_TEXT
              && !$msg->htmlContent)
            || ($campaign->sendType != SendType::HTML_ONLY
              && !$msg->plainText)
          )
        )
        {
          $msg->setLanguage('en');
          $msg->reload();
        }

        EphemeralCache::storeCache($languageCacheId, $msg, __CLASS__);
      }

      $contactId      = $msg->contactId ? : $campaign->contactId;
      $contactCacheId = $cacheId . ':contact:' . $contactId;
      $contact        = EphemeralCache::getCache($contactCacheId, __CLASS__);
      if($contact === null)
      {
        $contact = new Contact($contactId);
        EphemeralCache::storeCache($contactCacheId, $contact, __CLASS__);
      }
      $data['signature'] = $contact->signature;

      $message->setData(
        'senderName',
        self::replaceData($contact->name, $data)
      );
      $message->setData(
        'senderEmail',
        self::replaceData($contact->email, $data)
      );
      $message->setData(
        'sendType',
        self::replaceData($campaign->sendType, $data)
      );
      $message->setData(
        'subject',
        self::replaceData($msg->subject, $data)
      );
      $message->setData(
        'plainText',
        self::replaceData($msg->plainText, $data)
      );
      $message->setData(
        'htmlContent',
        self::replaceData($msg->htmlContent, $data, true)
      );

      foreach($processors as $process)
      {
        $message->addProcess($process);
      }
      $messages[] = serialize($message);
      if(isset($data['user_id']))
      {
        $lastUserId = $data['user_id'];
      }
    }

    // queue
    \Queue::setDefaultQueueProvider("messagequeue");
    \Queue::pushBatch(new StdQueue("defero_messages"), $messages);

    // stats
    $hour = time();
    $hour -= $hour % 3600;
    $statsCf = MailStatistic::cf();
    $statsCf->increment($campaignId, $hour . '|queued', count($messages));

    \Log::info(
      'Queued ' . count($messages) . ' messages for Campaign ' . $campaignId
      . ($lastUserId ? ' | Last User ID: ' . $lastUserId : '')
    );
    return true;
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
