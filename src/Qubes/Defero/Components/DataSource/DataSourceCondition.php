<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 18/10/13
 * Time: 09:49
 */

namespace Qubes\Defero\Components\DataSource;

use Cubex\Form\FormElement;

class DataSourceCondition
{
  protected $_name;
  protected $_comparisons;
  protected $_values;
  protected $_input;
  protected $_callback;

  public function getComparison($key)
  {
    $keys = array_keys($this->_comparisons);
    return $keys[$key];
  }

  public function __construct(
    $name, $callback, $comparisons = null, $input = FormElement::TEXT,
    $values = null
  )
  {
    if($comparisons === null)
    {
      $comparisons = [
        '%~' => 'Contains',
        '%>' => 'Begins With',
        '%<' => 'Ends With'
      ];
    }

    $this->_name        = $name;
    $this->_callback    = $callback;
    $this->_comparisons = $comparisons;
    $this->_input       = $input;
    $this->_values      = $values;
  }

  public function getCompareElement($key, $comparison)
  {
    $ele = new FormElement('conditionCompare[' . $key . ']');
    $ele->setLabel($this->_name . ' Compare Type');
    $ele->setType(FormElement::SELECT);
    $opts = array_merge([''], array_values($this->_comparisons));
    $ele->setOptions($opts);
    $ele->setData($comparison);
    return $ele;
  }

  public function getValueElement($key, $value)
  {
    $ele = new FormElement('conditionValue[' . $key . ']');
    $ele->setLabel($this->_name . ' Value');
    if($this->_input == FormElement::NONE)
    {
      $this->_input = FormElement::HIDDEN;
    }
    else
    {
      $ele->setData($value);
    }
    $ele->setType($this->_input);
    $ele->setOptions($this->_values);
    return $ele;
  }

  public function __toString()
  {
    return $this->_name;
  }

  public function call($condition)
  {
    $field   = $condition->field;
    $compare = $this->getComparison($condition->compare);
    $value   = $condition->value;
    if(isset($this->_values) && is_array($this->_values))
    {
      if(isset($value) && is_array($value))
      {
        foreach($value as $k => $v)
        {
          $value[$k] = $this->_values[$v];
        }
      }
      else
      {
        $value = $this->_values[$value];
      }
    }
    call_user_func($this->_callback, $field, $compare, $value, $condition);
  }
}
