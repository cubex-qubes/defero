<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 22/11/13
 * Time: 17:11
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class MassMailStatistic extends RecordMapper
{
  public $campaignId;
  public $startedAt;
  public $endedAt;
  public $queueCount;

  protected $_idType = self::ID_COMPOSITE;

  protected function _configure()
  {
    $this->_dbServiceName = "defero_db";
    $this->_addCompositeAttribute("id", ["campaign_id", "started_at"]);
  }
}
