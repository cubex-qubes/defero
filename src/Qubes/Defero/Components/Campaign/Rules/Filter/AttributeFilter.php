<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Filter;

use Qubes\Defero\Transport\StdRule;

class AttributeFilter extends StdRule implements IFilterRule
{
  public $attributeName = 'Field Name';
  /**
   * @enumclass \Qubes\Defero\Components\Campaign\Rules\Filter\AttributeFilterCheckTypes
   */
  public $checkType;
  public $check = 'Value To Compare';
  protected $_value;

  protected function _configure()
  {
    $config = $this->config('process');

    $this->attributeName = $config->getStr("attributeName");
    if($this->attributeName === null)
    {
      throw new \Exception("Attribute name not configured");
    }

    $this->check = $config->getRaw("check");
    if($this->check === null)
    {
      throw new \Exception("Attribute check not configured");
    }

    $this->checkType = $config->getStr("checkType");
    if($this->checkType === null)
    {
      throw new \Exception("Attribute check type not configured");
    }
  }

  public function canProcess()
  {
    $this->_configure();

    $data         = $this->_message->getRaw('data');
    $this->_value = $data[$this->attributeName];

    if($this->_value === null)
    {
      throw new \Exception("Attribute value is null");
    }

    switch($this->checkType)
    {
      case AttributeFilterCheckTypes::MATCH_EQUAL:
        if($this->_value == $this->check)
        {
          return true;
        }
        throw new \Exception("The values do not match");
      case AttributeFilterCheckTypes::MATCH_NOTEQUAL:
        if($this->_value != $this->check)
        {
          return true;
        }
        throw new \Exception("The values match");
      case AttributeFilterCheckTypes::MATCH_START:
        if(starts_with($this->_value, $this->check))
        {
          return true;
        }
        throw new \Exception("The value does not start with " . $this->check);
      case AttributeFilterCheckTypes::MATCH_END:
        if(ends_with($this->_value, $this->check))
        {
          return true;
        }
        throw new \Exception("The value does not end with " . $this->check);
      case AttributeFilterCheckTypes::MATCH_CONTAINS:
        if(strstr($this->_value, $this->check))
        {
          return true;
        }
        throw new \Exception("The value does not contain " . $this->check);
      default:
        throw new \Exception("Invalid attribute check type chosen");
    }
  }
}
