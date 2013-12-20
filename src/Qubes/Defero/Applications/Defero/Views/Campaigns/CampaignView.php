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

  public function __construct(Campaign $campaign, $page)
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
    $time                = strtotime('-23 hours');
    $time -= $time % 3600; //round off to the hour
    $statsCf = MailStatistic::cf();
    $d       = $statsCf->getSlice($this->campaign->id(), '', $time, true, 50);
    for($i = 24; $i > 0; $i--)
    {
      $queued = isset($d[$time . '|queued']) ? $d[$time . '|queued'] : 0;
      $sent   = isset($d[$time . '|sent']) ? $d[$time . '|sent'] : 0;
      $failed = isset($d[$time . '|failed']) ? $d[$time . '|failed'] : 0;

      $this->_stats['24h']->queued[] = $queued;
      $this->_stats['24h']->sent[]   = $sent;
      $this->_stats['24h']->failed[] = $failed;
      $this->_stats['24h']->totalQueued += $queued;
      $this->_stats['24h']->totalSent += $sent;
      $this->_stats['24h']->totalFailed += $failed;
      $time += 3600;
    }
  }

  private function _get30DaysStats()
  {
    $this->_stats['30d'] = new \stdClass();
    $time                = strtotime(
      '-29 days'
    ); //so counting 30 days brings us to today
    $time -= $time % 86400; //round off to the hour
    $statsCf = MailStatistic::cf();
    $d       = $statsCf->getSlice($this->campaign->id(), '', $time, true);
    for($i = 30; $i > 0; $i--)
    {
      $hTime  = $time;
      $queued = 0;
      $sent   = 0;
      $failed = 0;
      for($x = 24; $x > 0; $x--)
      {
        $queued += isset($d[$hTime . '|queued']) ? $d[$hTime . '|queued'] : 0;
        $sent += isset($d[$hTime . '|sent']) ? $d[$hTime . '|sent'] : 0;
        $failed += isset($d[$hTime . '|failed']) ? $d[$hTime . '|failed'] : 0;
        $hTime += 3600;
      }

      $this->_stats['30d']->queued[] = $queued;
      $this->_stats['30d']->sent[]   = $sent;
      $this->_stats['30d']->failed[] = $failed;
      $this->_stats['30d']->totalQueued += $queued;
      $this->_stats['30d']->totalSent += $sent;
      $this->_stats['30d']->totalFailed += $failed;
      $time += 86400;
    }
  }

  public function getQueued($key)
  {
    return $this->_stats[$key]->queued;
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

  public function getTotalSent($key)
  {
    return $this->_stats[$key]->totalSent;
  }

  public function getTotalFailed($key)
  {
    return $this->_stats[$key]->totalFailed;
  }
}
