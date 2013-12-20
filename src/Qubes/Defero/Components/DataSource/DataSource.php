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
  protected $_fixedAttributes = [];

  abstract public function getName();

  abstract public function process(
    $campaignId, $startTime, $lastSent, $startId = null, $endId = null
  );

  public function __construct($id = null)
  {
    parent::__construct($id);
    if($this->_attributes)
    {
      foreach($this->_attributes as $attr)
      {
        if($attr->data() !== null)
        {
          $this->_fixedAttributes[] = $attr->name();
          $attr->setSaveToDatabase(false);
        }
      }
    }
  }

  public function hydrate(
    array $data, $setUnmodified = false, $createAttributes = false, $raw = true
  )
  {
    if($this->_attributes)
    {
      foreach($this->_attributes as $attr)
      {
        if(array_search($attr->name(), $this->_fixedAttributes) !== false)
        {
          unset($data[$attr->name()]);
        }
      }
    }
    parent::hydrate($data, $setUnmodified, $createAttributes, $raw);
  }

  public function jsonSerialize()
  {
    $return = parent::jsonSerialize();
    foreach($this->_fixedAttributes as $name)
    {
      unset($return[$name]);
    }
    return $return;
  }
}
