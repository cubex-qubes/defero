<?php
namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Cassandra\CassandraMapper;

class MailerLog extends CassandraMapper
{
  protected $_cassandraConnection = 'cassmain';

  /**
   * @var MailerLog
   */
  private static $_mailerLog;

  /**
   * @param bool $plural
   *
   * @return string
   */
  public function getTableName($plural = true)
  {
    return "Mailer_log";
  }

  /**
   * Insert a new log entry.
   *
   * @param $userId
   * @param $mailerId
   *
   * @throws \Cubex\Cassandra\CassandraException
   */
  public static function addLogEntry($userId, $mailerId)
  {
    if(self::$_mailerLog !== null)
    {
      self::$_mailerLog = new static();
    }

    self::$_mailerLog->getCf()->insert(
      $userId, [$mailerId => time()]
    );
  }

} 
