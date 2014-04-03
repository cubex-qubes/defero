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
use Qubes\Defero\Components\Cron\CronParser;

class QueueCampaigns extends CliCommand
{
  protected $_echoLevel = LogLevel::INFO;

  /**
   * @valuerequired
   */
  public $instanceName;

  private $_pidFile;

  public function execute()
  {
    $this->_pidFile = new PidFile("", $this->instanceName);

    while(true)
    {
      $startedAt = time();
      $startedAt -= $startedAt % 60;
      $collection = new RecordCollection(new Campaign());
      foreach($collection as $campaign)
      {
        /** @var Campaign $campaign */
        if($campaign->isDue())
        {
          try
          {
            Defero::pushCampaign($campaign->id(), $startedAt);

            if(CronParser::isValid($campaign->sendAt))
            {
              // check average sends on scheduled
              $avgEndDate   = (new \DateTime())->setTimestamp($startedAt);
              $avgStartDate = CronParser::prevRun(
                $campaign->sendAt, $avgEndDate->getTimestamp()
              );
              $avgEndDate->sub($avgStartDate->diff($avgEndDate));
              $avgStartDate->setTime($avgStartDate->format('H') - 1, 0, 0);
              $latestStats = MailStatistic::getCampaignStats(
                $campaign->id(), $avgStartDate, $avgEndDate
              );
              $diff        = $avgStartDate->diff($avgEndDate);
              $diffLatest  = max(
                1,
                intval($diff->format('%i')) +
                intval($diff->format('%h') * 60) +
                intval($diff->format('%d') * 3600)
              );

              $avgEndDate->sub(new \DateInterval('PT1H'));
              $avgStartDate->sub(new \DateInterval('PT2H'));
              $avgStats = MailStatistic::getCampaignStats(
                $campaign->id(), $avgStartDate, $avgEndDate
              );
              $diff     = $avgStartDate->diff($avgEndDate);
              $diffAvg  = max(
                1,
                intval($diff->format('%i')) +
                intval($diff->format('%h') * 60) +
                intval($diff->format('%d') * 3600)
              );

              $compareLatest = ($latestStats->sent / $diffLatest) * 60;
              $compareAvg    = ($avgStats->sent / $diffAvg) * 60;
              $threshold     = ($compareAvg * 0.4) + 10;

              if($compareLatest < $compareAvg - $threshold)
              {
                Log::warning(
                  $campaign->id() . ' sending below average: '
                  . $compareLatest . ' / ' . $compareAvg . ' ~ ' . $threshold
                );
              }
              else if($compareLatest > $compareAvg + $threshold)
              {
                Log::warning(
                  $campaign->id() . ' sending above average: '
                  . $compareLatest . ' / ' . $compareAvg . ' ~ ' . $threshold
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
      }
      sleep(30);
    }
  }
}
