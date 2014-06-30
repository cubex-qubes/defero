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

  public static function getCampaignStats(
    $campaignId, \DateTime $from, \DateTime $to = null, $language = null
  )
  {
    return self::getMultiCampaignStats(
      [$campaignId],
      $from,
      $to,
      $language
    )[$campaignId];
  }

  public static function getMultiCampaignStats(
    array $campaigns, \DateTime $from, \DateTime $to = null, $language = null
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

    $campaignSlice = $cf->multiGetSliceChunked(
      $campaigns,
      $from,
      $to,
      false,
      null,
      10,
      500
    );
    foreach($campaignSlice as $campaignId => $slice)
    {
      $stats[$campaignId]             = new CampaignStats();
      $stats[$campaignId]->campaignId = $campaignId;
      $stats[$campaignId]->dateFrom   = $from;
      $stats[$campaignId]->dateTo     = $to;
      foreach($slice as $k => $count)
      {
        list(, $typeLanguage) = explode('|', $k);
        list($type, $lang) = array_pad(explode('-', $typeLanguage), 2, null);
        if($language == null || $language == $lang)
        {
          switch($type)
          {
            case 'queued':
              $stats[$campaignId]->queued += $count;
              break;
            case 'test':
              $stats[$campaignId]->test += $count;
              break;
            case 'sent':
              $stats[$campaignId]->sent += $count;
              break;
            case 'failed':
              $stats[$campaignId]->failed += $count;
              break;
          }
        }
      }
    }
    return $stats;
  }

  public function getTableName($plural = true)
  {
    return "MailerStatistics";
  }
}
