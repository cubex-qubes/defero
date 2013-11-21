<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 18/09/13
 * Time: 15:23
 */

namespace Qubes\Defero\Components\DataSource;

use Cubex\Mapper\Database\RecordCollection;
use Qubes\Defero\Applications\Defero\Defero;

abstract class RecordMapperDataSource implements IDataSource
{
  use DataSourceConditionsTrait;

  protected $_batchSize = 250;
  protected $_batchPointer = 0;

  protected $_mapper;
  protected $_collection;

  protected abstract function _getMapper();

  public function compareString($field, $compare, $value)
  {
    $this->getCollection()->loadWhereAppend(
      '%C LIKE ' . $compare, $field, $value
    );
  }

  public function compareEquals($field, $compare, $value)
  {
    $this->getCollection()->loadWhereAppend(
      '%C ' . $compare . ' %s', $field, $value
    );
  }

  public function compareIn($field, $compare, $value)
  {
    $this->getCollection()->loadWhereAppend(
      '%C ' . $compare . ' (%Ls)', $field, $value
    );
  }

  public function getMapper()
  {
    if(!$this->_mapper)
    {
      $this->_mapper = $this->_getMapper();
    }
    return $this->_mapper;
  }

  public function getCollection()
  {
    if(!$this->_collection)
    {
      $this->_collection = new RecordCollection($this->getMapper());
    }
    return $this->_collection;
  }

  public function setBatchSize($size)
  {
    $this->_batchSize = $size;
    return $this;
  }

  public function resetPointer()
  {
    $this->_batchPointer = 0;
  }

  public function getBatch()
  {
    $collection = $this->getCollection();
    $collection->setLimit($this->_batchPointer, $this->_batchSize);

    $this->_batchPointer += $this->_batchSize;
    return $collection->get()->jsonSerialize();
  }

  public function process($campaign_id, $startTime, $lastSent)
  {
    foreach($this->getConditionValues() as $condition)
    {
      if($c = $this->getCondition($condition->field))
      {
        $c->call($condition);
      }
    }

    $this->resetPointer();
    while(($data = $this->getBatch()))
    {
      Defero::pushBatch($campaign_id, $data);
      if(count($data) < $this->_batchSize)
      {
        // don't search for more if the last batch was not full
        break;
      }
    }
  }
}
