<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 20/09/13
 * Time: 11:09
 */

namespace Qubes\Defero\Components\MessageProcessor;

use Qubes\Defero\Transport\IMessageProcessor;

final class MessageProcessorCollection
{
  private static $_processors = [];

  public static function setMessageProcessors(array $processors)
  {
    self::$_processors = $processors;
  }

  public static function addMessageProcessor(
    $ident, IMessageProcessor $processor
  )
  {
    self::$_processors[$ident] = $processor;
  }

  public static function getMessageProcessor($ident)
  {
    return self::$_processors[$ident];
  }

  public static function getMessageProcessorIdent($ident)
  {
    return self::$_processors[$ident];
  }

  public static function getMessageProcessors()
  {
    $processors = self::$_processors;
    foreach($processors as $k => $v)
    {
      $class = get_class($v);
      if(($pos = strrpos($class, '\\')) !== false)
      {
        $class = substr($class, $pos + 1);
      }
      $processors[$k] = $class;
    }
    return ['' => ''] + $processors;
  }
}
