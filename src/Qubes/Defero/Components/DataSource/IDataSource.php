<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 18/09/13
 * Time: 14:59
 */

namespace Qubes\Defero\Components\DataSource;

interface IDataSource
{
  public function getName();

  public function process($campaign_id, $startTime, $lastSent);
}

/*
class CassandraDataSource implements IDataSource
{
  public $mapperClass;

  public function getConfigItems()
  {
    ["mapper_class"];
  }
}

class UserDataSource extends CassandraDataSource
{
  public $mapperClass = 'lkfrhwkfwh';
  public $name;
  public $email;
  public $free;
  public $active;
  public function getData(){}
}

class FreeUsersDataSource extends UserDataSource {
  public $free = 1;
}

class RecordMapperDataSource implements IDataSource
{
  public function getConfigItems()
  {
    ["mapper_class"];
  }
}

class MessyDataSource implements IDataSource
{
  public function getData($processor)
  {
    $a = [1,2,4,,7,9,5,3,];
    foreach($a as $b)
    {
      $processor->add($b);
    }
  }
}
*/
