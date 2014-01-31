<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 02/10/13
 * Time: 16:41
 */

namespace Qubes\Defero\Cli\Campaign;

use Cubex\Cli\CliCommand;
use Cubex\Log\Log;
use Cubex\Mapper\Database\RecordCollection;
use Psr\Log\LogLevel;
use Qubes\Defero\Applications\Defero\Defero;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class QueueCampaigns extends CliCommand
{
  protected $_echoLevel = LogLevel::INFO;

  public function execute()
  {
    while(true)
    {
      $startedAt = time();
      $startedAt -= $startedAt % 60;
      $collection = new RecordCollection(new Campaign());
      foreach($collection as $campaign)
      {
        /** @var Campaign $campaign */
        if(!$campaign->active)
        {
          continue;
        }

        if(!$campaign->isDue())
        {
          continue;
        }
        try
        {
          Defero::pushCampaign($campaign->id(), $startedAt);
        }
        catch(\Exception $e)
        {
          Log::error('Campaign ' . $campaign->id() . ': ' . $e->getMessage());
        }
      }
      sleep(30);
    }
  }
}
