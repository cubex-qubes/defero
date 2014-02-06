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
      $to = $to->setTimezone((new \DateTimeZone('UTC')))->getTimestamp();
    }
    $from = $from->setTimezone((new \DateTimeZone('UTC')))->getTimestamp();

    $stats             = new CampaignStats();
    $stats->campaignId = $campaignId;
    $stats->dateFrom   = $from;
    $stats->dateTo     = $to;

    $slice = $cf->getSliceChunked($campaignId, $from, $to, false);
    foreach($slice as $k => $count)
    {
      list(, $type) = explode('|', $k);
      switch($type)
      {
        case 'queued':
          $stats->queued += $count;
          break;
        case 'test':
          $stats->test += $count;
          break;
        case 'sent':
          $stats->sent += $count;
          break;
        case 'failed':
          $stats->failed += $count;
          break;
      }
    }
    return $stats;
  }
}
