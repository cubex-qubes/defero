<?php
/**
 * @author  oke.ugwu
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Cassandra\CassandraMapper;

class MailStatisticsByBrand extends CassandraMapper
{
  protected $_cassandraConnection = 'cass_analytics';

  public function getTableName($plural = true)
  {
    return "MailerStatisticsByBrand";
  }

  public static function getCampaignStats(
    $campaignId, \DateTime $from, \DateTime $to = null,
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

    $stats = [];
    $slice = $cf->getSliceChunked($campaignId, $from, $to, false);
    foreach($slice as $k => $count)
    {
      list(, $type, $key, $lang) = explode('|', $k);
      if(($language == null || $language == $lang))
      {
        if(!isset($stats[$key]))
        {
          $stats[$key]         = new \stdClass();
          $stats[$key]->queued = 0;
          $stats[$key]->test   = 0;
          $stats[$key]->sent   = 0;
          $stats[$key]->failed = 0;
        }

        switch($type)
        {
          case 'queued':
            $stats[$key]->queued += $count;
            break;
          case 'test':
            $stats[$key]->test += $count;
            break;
          case 'sent':
            $stats[$key]->sent += $count;
            break;
          case 'failed':
            $stats[$key]->failed += $count;
            break;
        }
      }
    }
    return $stats;
  }
}
