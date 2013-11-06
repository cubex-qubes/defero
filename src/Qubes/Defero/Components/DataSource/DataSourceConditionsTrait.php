<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 21/10/13
 * Time: 10:59
 */

namespace Qubes\Defero\Components\DataSource;

use Cubex\Form\FormElement;

trait DataSourceConditionsTrait
{
  protected $_conditions = [];
  protected $_conditionValues = [];
  protected $_conditionValuesFixed = [];

  public function setConditionValues($conditions)
  {
    $this->_conditionValues = $conditions;
  }

  public function getConditions()
  {
    return $this->_conditions;
  }

  public function getCondition($condition)
  {
    return $this->_conditions[$condition];
  }

  public function addCondition(
    $fieldName, $name, $callback, $comparisons = null,
    $input = FormElement::TEXT, $values = null
  )
  {
    $this->_conditions[$fieldName] = new DataSourceCondition(
      $name, $callback, $comparisons, $input, $values);
  }

  public function getFixedConditions()
  {
    return $this->_conditionValuesFixed;
  }

  public function addFixedCondition($field, $compare, $value)
  {
    $condition                     = new \stdClass();
    $condition->field              = $field;
    $condition->compare            = $compare;
    $condition->value              = $value;
    $condition->disabled           = true;
    $this->_conditionValuesFixed[] = $condition;
  }

  public function getConditionValues()
  {
    return array_merge($this->getFixedConditions(), $this->_conditionValues);
  }
}
