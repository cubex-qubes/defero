<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Filter;

use Qubes\Defero\Components\Campaign\Rules\StdRule;
use Qubes\Defero\Transport\IProcessMessage;

class AttributeFilter extends StdRule implements IFilterRule
{
  protected $_attribute;
  protected $_value;
  protected $_check;
  protected $_checkType;

  const MATCH_EQUAL    = 'eq';
  const MATCH_NOTEQUAL = 'neq';
  const MATCH_START    = 'starts';
  const MATCH_END      = 'ends';
  const MATCH_CONTAINS = 'contains';

  public function __construct(IProcessMessage $message)
  {
    parent::__construct($message);

    $this->_attribute = $this->_message->getConfigValue("attribute_name");
    if($this->_attribute === null)
    {
      throw new \Exception("Attribute name not configured");
    }

    $this->_check = $this->_message->getConfigValue("attribute_check");
    if($this->_check === null)
    {
      throw new \Exception("Attribute check not configured");
    }

    $this->_checkType = $this->_message->getConfigValue("attribute_check_type");
    if($this->_checkType === null)
    {
      throw new \Exception("Attribute check type not configured");
    }
  }

  public function isFilterValid()
  {
    $this->_value = $this->_message->getAttribute($this->_attribute);
    if($this->_value === null)
    {
      throw new \Exception("Attribute value is null");
    }

    switch($this->_checkType)
    {
      case self::MATCH_EQUAL:
        if($this->_value == $this->_check)
        {
          return true;
        }
        throw new \Exception("The values do not match");
      case self::MATCH_NOTEQUAL:
        if($this->_value != $this->_check)
        {
          return true;
        }
        throw new \Exception("The values match");
      case self::MATCH_START:
        if(starts_with($this->_value, $this->_check))
        {
          return true;
        }
        throw new \Exception("The value does not start with " . $this->_check);
      case self::MATCH_END:
        if(ends_with($this->_value, $this->_check))
        {
          return true;
        }
        throw new \Exception("The value does not end with " . $this->_check);
      case self::MATCH_CONTAINS:
        if(strstr($this->_value, $this->_check))
        {
          return true;
        }
        throw new \Exception("The value does not contain " . $this->_check);
      default:
        throw new \Exception("Invalid attribute check type chosen");
    }
  }
}
