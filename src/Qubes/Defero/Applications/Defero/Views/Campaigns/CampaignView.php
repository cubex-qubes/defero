<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Campaigns;

use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Campaign\Mappers\MailStatistic;

class CampaignView extends DeferoView
{
  /**
   * @var Campaign
   */
  public $campaign;
  private $_stats;

  public function __construct(Campaign $campaign)
  {
    $this->requireJsLibrary('jquery');
    $this->requireJs(
      '//cdn.jsdelivr.net/jquery.sparkline/2.1.2/jquery.sparkline.min.js'
    );
    $this->requireJs('statsgraph');
    $this->campaign = $campaign;
    $this->_get24hrsStats();
    $this->_get30DaysStats();
  }

  private function _get24hrsStats()
  {
    $this->_stats['24h'] = new \stdClass();
    $this->_stats['24h']->totalQueued = 0;
    $this->_stats['24h']->totalTest = 0;
    $this->_stats['24h']->totalSent = 0;
    $this->_stats['24h']->totalFailed = 0;

    $time                = strtotime('-23 hours');
    $time -= $time % 3600; //round off to the hour

    for($i = 24; $i > 0; $i--)
    {
      $stats = $this->campaign->getStats(
        (new \DateTime())->setTimestamp($time),
        (new \DateTime())->setTimestamp($time + 3599)
      );

      $this->_stats['24h']->queued[] = $stats->queued;
      $this->_stats['24h']->test[]   = $stats->test;
      $this->_stats['24h']->sent[]   = $stats->sent;
      $this->_stats['24h']->failed[] = $stats->failed;
      $this->_stats['24h']->totalQueued += $stats->queued;
      $this->_stats['24h']->totalTest += $stats->test;
      $this->_stats['24h']->totalSent += $stats->sent;
      $this->_stats['24h']->totalFailed += $stats->failed;
      $time += 3600;
    }
  }

  private function _get30DaysStats()
  {
    $this->_stats['30d'] = new \stdClass();
    $this->_stats['30d']->totalQueued = 0;
    $this->_stats['30d']->totalTest = 0;
    $this->_stats['30d']->totalSent = 0;
    $this->_stats['30d']->totalFailed = 0;

    //so counting 30 days brings us to today
    $time = strtotime('-29 days 00:00:00');
    for($i = 30; $i > 0; $i--)
    {
      $stats = $this->campaign->getStats(
        (new \DateTime())->setTimestamp($time),
        (new \DateTime())->setTimestamp($time + 86399)
      );

      $this->_stats['30d']->queued[] = $stats->queued;
      $this->_stats['30d']->test[]   = $stats->test;
      $this->_stats['30d']->sent[]   = $stats->sent;
      $this->_stats['30d']->failed[] = $stats->failed;
      $this->_stats['30d']->totalQueued += $stats->queued;
      $this->_stats['30d']->totalTest += $stats->test;
      $this->_stats['30d']->totalSent += $stats->sent;
      $this->_stats['30d']->totalFailed += $stats->failed;
      $time += 86400;
    }
  }

  public function getQueued($key)
  {
    return $this->_stats[$key]->queued;
  }

  public function getTest($key)
  {
    return $this->_stats[$key]->test;
  }

  public function getSent($key)
  {
    return $this->_stats[$key]->sent;
  }

  public function getFailed($key)
  {
    return $this->_stats[$key]->failed;
  }

  public function getTotalQueued($key)
  {
    return $this->_stats[$key]->totalQueued;
  }

  public function getTotalTest($key)
  {
    return $this->_stats[$key]->totalTest;
  }

  public function getTotalSent($key)
  {
    return $this->_stats[$key]->totalSent;
  }

  public function getTotalFailed($key)
  {
    return $this->_stats[$key]->totalFailed;
  }
}
