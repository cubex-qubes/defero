<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 18/09/13
 * Time: 14:59
 */

namespace Qubes\Defero\Components\DataSource;

use Cubex\Mapper\DataMapper;

abstract class DataSource extends DataMapper
{
  protected $_autoTimestamp = false;
  protected $_fixedProperties;

  abstract public function getName();

  abstract public function process(
    $taskId, $campaignId, $startTime, $lastSent, $startId = null, $endId = null,
    $additionalData = null
  );

  public function hydrate(
    array $data, $setUnmodified = false, $createAttributes = false, $raw = true
  )
  {
    foreach($this->getFixedProperties() as $name => $value)
    {
      unset($data[$name]);
    }
    parent::hydrate($data, $setUnmodified, $createAttributes, $raw);
  }

  public function jsonSerialize()
  {
    $return = parent::jsonSerialize();
    foreach($this->getFixedProperties() as $name => $value)
    {
      unset($return[$name]);
    }
    return $return;
  }

  public function isFixedProperty($name)
  {
    $properties = $this->getFixedProperties();
    return isset($properties[$name]);
  }

  public function getFixedProperties()
  {
    if(!$this->_fixedProperties)
    {
      $this->_fixedProperties = [];

      $class            = new \ReflectionClass($this);
      $publicProperties = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
      $defaults         = $class->getDefaultProperties();
      foreach($publicProperties as $prop)
      {
        if($defaults[$prop->name] !== null)
        {
          $this->_fixedProperties[$prop->name] = $defaults[$prop->name];
        }
      }
    }
    return $this->_fixedProperties;
  }

  protected function _mergeAdditionalData(&$data, $additionalData = null)
  {
    if($additionalData)
    {
      foreach($data as $k => $thisData)
      {
        $data[$k] = array_merge($thisData, $additionalData);
      }
    }
  }
}
