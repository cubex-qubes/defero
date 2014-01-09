<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 09/01/14
 * Time: 11:03
 */

namespace Qubes\Defero\Components\Campaign\Stats;

class CampaignStats
{
  public $campaignId;
  public $dateFrom;
  public $dateTo;
  public $queued = 0;
  public $sent = 0;
  public $failed = 0;
}
