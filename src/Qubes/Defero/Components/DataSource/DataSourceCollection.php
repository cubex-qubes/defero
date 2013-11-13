<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 18/09/13
 * Time: 14:33
 */

namespace Qubes\Defero\Components\DataSource;

final class DataSourceCollection
{
  /**
   * @var IDataSource[]
   */
  private static $_dataSources = [];

  public static function setDataSources(array $sources)
  {
    self::$_dataSources = $sources;
  }

  public static function addDataSource($ident, IDataSource $source)
  {
    self::$_dataSources[$ident] = $source;
  }

  public static function getDataSource($ident)
  {
    return self::$_dataSources[$ident];
  }

  public static function getDataSources()
  {
    $sources = self::$_dataSources;
    foreach($sources as $k => $v)
    {
      $sources[$k] = $v->getName();
    }
    return ['' => ''] + $sources;
  }
}
