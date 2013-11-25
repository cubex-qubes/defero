<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 03/10/13
 * Time: 10:53
 */

namespace Qubes\Defero\Components\Cron;

use Cubex\Helpers\DateTimeHelper;

class CronParser
{
  const MINUTE  = 0;
  const HOUR    = 1;
  const DAY     = 2;
  const MONTH   = 3;
  const WEEKDAY = 4;
  const YEAR    = 5;
  protected static $_formats = ['i', 'H', 'd', 'm', 'w', 'Y'];
  protected static $_min = [0, 0, 1, 1, 0, 1970];
  protected static $_max = [59, 23, 31, 12, 6, 2099];
  protected static $_intervalIds = ['M', 'H', 'D', 'M', 'D', 'Y'];
  protected static $_templates = [
    '@yearly'  => '0 0 1 1 *',
    '@monthly' => '0 0 1 * *',
    '@weekly'  => '0 0 * * 0',
    '@daily'   => '0 0 * * *',
    '@hourly'  => '0 * * * *'
  ];
  protected static $_groupings = [
    self::YEAR   => [self::YEAR],
    self::MONTH  => [self::MONTH],
    self::DAY    => [self::DAY, self::WEEKDAY],
    self::HOUR   => [self::HOUR],
    self::MINUTE => [self::MINUTE]
  ];

  protected static function _resetTime(&$time)
  {
    if($time === null)
    {
      $time = time();
    }

    // trim time back to last round minute
    $time -= $time % 60;
  }

  protected static function _getInterval($type, $interval)
  {
    return new \DateInterval('P' . ($type <= 1 ? 'T' : '') . $interval . self::$_intervalIds[$type]);
  }

  protected static function _parse($pattern)
  {
    if(is_array($pattern))
    {
      return $pattern;
    }

    if(isset(self::$_templates[$pattern]))
    {
      $pattern = self::$_templates[$pattern];
    }
    $split = preg_split('/\s+/', $pattern, count(self::$_formats));
    if(count($split) < 5)
    {
      return false;
    }

    foreach($split as $k => $segment)
    {
      $split[$k] = explode(',', $segment);

      foreach($split[$k] as $kk => $part)
      {
        preg_match(
          '/(((?<min>[0-9]+)\-(?<max>[0-9]+))|(?<val>[*0-9]+))(\/(?<mod>[0-9]+))?/',
          $part,
          $matches
        );

        $matches['full'] = $matches[0];
        foreach($matches as $mk => $mv)
        {
          if(is_numeric($mk) || $mv === '')
          {
            unset($matches[$mk]);
          }
        }
        $split[$k][$kk] = $matches;
      }
    }

    return $split;
  }

  protected static function _getPartDiff($pattern, $type, $time = null)
  {
    self::_resetTime($time);
    $pattern = self::_parse($pattern);
    if(!isset($pattern[$type]))
    {
      return 0;
    }
    $fmt  = self::$_formats[$type];
    $ret  = DateTimeHelper::dateTimeFromAnything($time);
    $curr = intval(date($fmt, $ret->getTimestamp()));

    $minimum = null;
    foreach($pattern[$type] as $part)
    {
      if($part['full'] == '*')
      {
        return false;
      }

      // start at $curr, increase to max, then start back at min until this part matches the range or the val AND mod
      $checks = array_unique(
        array_merge(
          range($curr, self::$_max[$type]),
          range(
            self::$_min[$type],
            max($curr - 1, self::$_min[$type])
          )
        )
      );

      $cv = -1;
      foreach($checks as $check)
      {
        $cv++;
        if(isset($part['mod']) && ($check % $part['mod']))
        {
          continue;
        }
        if(
        (isset($part['val']) && ($part['val'] == $check || $part['val'] == '*')) ||
        (isset($part['min']) && $check >= $part['min'] && $check <= $part['max'])
        )
        {
          $offset = isset($part['min']) ? $part['min'] : 0;
          if($minimum === null || ($cv + $offset) < $minimum)
          {
            $minimum = $cv + $offset;
          }
          break;
        }
      }
    }

    return $minimum;
  }

  public static function isValid($pattern)
  {
    return self::_parse($pattern) !== false;
  }

  public static function isDue($pattern, $time = null)
  {
    self::_resetTime($time);

    $pattern = self::_parse($pattern);
    if(!$pattern)
    {
      return false;
    }

    foreach(self::$_formats as $pos => $fmt)
    {
      if($pos > 4 && !isset($pattern[$pos]))
      {
        continue;
      }

      $cmp = intval(date($fmt, $time));

      $posSuccess = false;
      foreach($pattern[$pos] as $part)
      {
        if($part['full'] == '*' || $part['full'] == $cmp || $posSuccess)
        {
          $posSuccess = true;
          break;
        }

        // process order: range, modulus

        // doesn't match val
        if(isset($part['val']) && $part['val'] != '*' && $cmp != $part['val'])
        {
          continue;
        }

        // out of range
        if(isset($part['min']) && isset($part['max']) && ($cmp < $part['min'] || $cmp > $part['max']))
        {
          continue;
        }

        $offset = isset($part['min']) ? $part['min'] : 0;
        if(isset($part['mod']) && (($cmp - $offset) % $part['mod']) != 0)
        {
          continue;
        }

        $posSuccess = true;
      }
      if(!$posSuccess)
      {
        return false;
      }
    }
    return true;
  }

  public static function nextRun($pattern, $time = null, $now = false)
  {
    self::_resetTime($time);
    if(!$now)
    {
      $time += 60;
    }
    $originalPattern = $pattern;
    $pattern         = self::_parse($pattern);

    $orig = DateTimeHelper::dateTimeFromAnything($time);
    $ret  = DateTimeHelper::dateTimeFromAnything($time);

    foreach(self::$_groupings as $pos => $grp)
    {
      $values = [];
      foreach($grp as $type)
      {
        $v = self::_getPartDiff($pattern, $type, $ret->getTimestamp());
        if($v !== false)
        {
          $values[] = $v;
        }
      }
      if(!$values)
      {
        continue;
      }
      $diff = min($values);

      if(!$diff)
      {
        continue;
      }

      $fmt = self::$_formats[$pos];
      $ret->add(self::_getInterval($pos, $diff));
      if($orig->format($fmt) != $ret->format($fmt))
      {
        for($i = $pos - 1; $i >= 0; $i--)
        {
          $ret->sub(
            self::_getInterval(
              $i,
            $ret->format(self::$_formats[$i]) - self::$_min[$i]
            )
          );
        }
      }
    }

    if(!self::isDue($pattern, $ret->getTimestamp()))
    {
      throw new \Exception("Cron Error: Calculated nextRun is not due. $originalPattern $time");
    }

    return $ret;
  }
}
