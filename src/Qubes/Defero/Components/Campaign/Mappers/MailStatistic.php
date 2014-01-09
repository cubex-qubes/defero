<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 22/11/13
 * Time: 17:11
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Cassandra\CassandraMapper;
use Qubes\Defero\Components\Campaign\Stats\CampaignStats;

class MailStatistic extends CassandraMapper
{
  protected $_cassandraConnection = 'cass_analytics';

  public function getTableName($plural = true)
  {
    return "MailerStatistics";
  }

  public static function getCampaignStats(
    $campaignId, \DateTime $from, \DateTime $to = null
  )
  {
    $cf = self::cf();

    if(!$to)
    {
      $to = time();
    }
    else
    {
      $to = $to->getTimestamp();
    }
    $from = $from->getTimestamp();

    $stats             = new CampaignStats();
    $stats->campaignId = $campaignId;
    $stats->dateFrom   = $from;
    $stats->dateTo     = $to;

    $slice = $cf->getSliceChunked($campaignId, $from, $to, false);
    foreach($slice as $k => $count)
    {
      list($time, $type) = explode('|', $k);
      switch($type)
      {
        case 'queued':
          $stats->queued += $count;
          break;
        case 'sent':
          $stats->sent += $count;
          break;
        case 'failed':
          $stats->failed += $count;
          break;
      }
      /*
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
      */
    }
    return $stats;
  }
}
