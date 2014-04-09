<?php
/**
 * @author  oke.ugwu
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Cassandra\CassandraMapper;
use Qubes\Defero\Components\Campaign\Stats\CampaignStats;

class MailStatisticsByBrand extends CassandraMapper
{
  protected $_cassandraConnection = 'cass_analytics';

  public function getTableName($plural = true)
  {
    return "MailerStatisticsByBrand";
  }

  public static function getCampaignStats(
    $campaignId, \DateTime $from, \DateTime $to = null, $keyIn = null,
    $language = null
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
      list(, $type, $key, $lang) = explode('|', $k);
      if(($language == null || $language == $lang)
        && ($keyIn == null || $keyIn == $key)
      )
      {
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
    }
    return $stats;
  }
}
