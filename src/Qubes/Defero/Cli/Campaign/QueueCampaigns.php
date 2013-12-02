<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 02/10/13
 * Time: 16:41
 */

namespace Qubes\Defero\Cli\Campaign;

use Cubex\Cli\CliCommand;
use Cubex\Queue\StdQueue;
use Cubex\Mapper\Database\RecordCollection;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Transport\ProcessMessage;

class QueueCampaigns extends CliCommand
{
  public function execute()
  {
    $startedAt = new \DateTime();
    $ts        = $startedAt->getTimestamp();
    $ts -= $ts % 60;
    $collection = new RecordCollection(new Campaign());
    foreach($collection as $campaign)
    {
      /** @var Campaign $campaign */
      if(!$campaign->active)
      {
        continue;
      }

      $lastTime  = $campaign->lastSent;
      $checkTime = $campaign->sendAt;
      if(!$checkTime || $lastTime == $ts)
      {
        continue;
      }

      if(!$campaign->isDue())
      {
        continue;
      }

      echo 'Adding ' . $campaign->id();
      $campaign->lastSent = $ts;
      $campaign->saveChanges();

      $message = new ProcessMessage();
      $message->setData('campaign_id', $campaign->id());
      $message->setData('started_at', $ts);
      $message->setData('last_sent', $lastTime);

      \Queue::push(new StdQueue('defero_campaigns'), serialize($message));
    }
  }
}
