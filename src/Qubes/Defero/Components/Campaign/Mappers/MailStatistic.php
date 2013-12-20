<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 22/11/13
 * Time: 17:11
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Cassandra\CassandraMapper;

class MailStatistic extends CassandraMapper
{
  protected $_cassandraConnection = 'cass_analytics';

  public function getTableName($plural = true)
  {
    return "MailerStatistics";
  }
}
