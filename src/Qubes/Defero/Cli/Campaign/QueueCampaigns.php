<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 02/10/13
 * Time: 16:41
 */

namespace Qubes\Defero\Cli\Campaign;

use Cubex\Cli\CliCommand;
use Cubex\Cli\PidFile;
use Cubex\Log\Log;
use Cubex\Mapper\Database\RecordCollection;
use Psr\Log\LogLevel;
use Qubes\Defero\Applications\Defero\Defero;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Campaign\Mappers\MailStatistic;
use TomK\CronParser\CronParser;

class QueueCampaigns extends CliCommand
{
  /**
   * @valuerequired
   */
  public $instanceName;
  protected $_echoLevel = LogLevel::INFO;
  protected $_defaultLogLevel = LogLevel::INFO;
  private $_pidFile;

  public function execute()
  {
    $this->_pidFile = new PidFile("", $this->instanceName);

    while(true)
    {
      $startedAt = time();
      $startedAt -= $startedAt % 60;
      $collection = new RecordCollection(new Campaign());
      if(!$collection->hasMappers())
      {
        Log::warning('No mappers found');
      }
      foreach($collection as $campaign)
      {
        /** @var Campaign $campaign */
        if($campaign->isDue($startedAt))
        {
          try
          {
            Defero::pushCampaign($campaign->id(), $startedAt);

            if(CronParser::isValid($campaign->sendAt))
            {
              // check average sends on scheduled
              $avgEndDate   = (new \DateTime())->setTimestamp($startedAt);
              $avgStartDate = CronParser::prevRun(
                $campaign->sendAt,
                $avgEndDate
              );
              $avgEndDate->sub($avgStartDate->diff($avgEndDate));
              $avgStartDate->setTime($avgStartDate->format('H') - 1, 0, 0);
              $latestStats = MailStatistic::getCampaignStats(
                $campaign->id(),
                $avgStartDate,
                $avgEndDate
              );
              $diff        = $avgStartDate->diff($avgEndDate);
              $diffLatest  = max(
                1,
                intval($diff->format('%i')) +
                intval($diff->format('%h') * 60) +
                intval($diff->format('%d') * 3600)
              );

              $latestHourly = ($latestStats->sent / $diffLatest) * 60;

              if(($campaign->warnMin && ($latestHourly < $campaign->warnMin))
                || ($campaign->warnMax && ($latestHourly > $campaign->warnMax))
              )
              {
                Log::warning(
                  'Sending outside threshold',
                  [
                    'campaign' => $campaign->id(),
                    'average'  => $latestHourly,
                    'warnMin'  => $campaign->warnMin,
                    'warnMax'  => $campaign->warnMax,
                  ]
                );
              }
            }
          }
          catch(\Exception $e)
          {
            Log::error(
              'Campaign ' . $campaign->id() . ': ' . $e->getMessage()
              . ' (Line: ' . $e->getLine() . ')'
            );
          }
        }
        else
        {
          Log::debug('Campaign ' . $campaign->id() . ' not due');
        }
      }
      $endTime = time();
      $endTime -= $endTime % 60;
      if($endTime == $startedAt)
      {
        sleep(30);
      }
    }
  }
}
