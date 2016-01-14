<?php
namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Cassandra\CassandraMapper;

class SentEmailLog extends CassandraMapper
{
  protected $_cassandraConnection = 'cassmain';

  /**
   * @var MailerLog
   */
  private static $_sentEmailLog;

  /**
   * @param bool $plural
   *
   * @return string
   */
  public function getTableName($plural = true)
  {
    return "SentEmail_Log";
  }

  /**
   * Insert a new log entry.
   *
   * @param $userId
   * @param $campaignId
   *
   * @throws \Cubex\Cassandra\CassandraException
   */
  public static function addLogEntry($userId, $campaignId)
  {
    if(self::$_sentEmailLog === null)
    {
      self::$_sentEmailLog = new static();
    }

    self::$_sentEmailLog->getCf()->insert(
      $userId.'-'.$campaignId, [date('Y-m-d') => 1]
    );
  }
}
