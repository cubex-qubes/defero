<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Forms;

use Cubex\Form\Form;
use Cubex\View\TemplatedViewModel;

/**
 * Class BaseFormView
 * @package Qubes\Defero\Applications\Defero\Views\Forms
 *
 * @method \Cubex\Form\FormElement getSubmit
 */
abstract class BaseFormView extends TemplatedViewModel
{
  private $_form;

  const METHOD_TYPE_GET      = "get";
  const METHOD_TYPE_ERROR    = "error";
  const METHOD_TYPE_GETERROR = "geterror";
  const METHOD_TYPE_GETLABEL = "getlabel";

  public function __construct(Form $form)
  {
    $this->_form = $form;
    $this->_form->addAttribute("class", "form-horizontal");
    $this->_form->setElementTemplate("{{input}}");
    $this->_form->setNoValidate();
  }

  /**
   * @return \Cubex\Form\Form
   */
  public function getForm()
  {
    return $this->_form;
  }

  /**
   * @param string $name
   * @param array  $arguments
   *
   * @return \Cubex\Form\FormElement
   * @throws \BadMethodCallException
   */
  public function __call($name, $arguments)
  {
    $parsedMethod = $this->_parseMethodName($name);

    if($parsedMethod !== null)
    {
      $element = $this->_getElement($parsedMethod["element"]);

      switch($parsedMethod["type"])
      {
        case static::METHOD_TYPE_GET:
          return $element;
        case static::METHOD_TYPE_ERROR:
          return (bool)$element->validationErrors();
        case static::METHOD_TYPE_GETERROR:
          return $element->validationErrors()[0];
        case static::METHOD_TYPE_GETLABEL:
          return $element->label();
      }
    }

    $method = __NAMESPACE__ . '::' . $name;
    $file   = __FILE__;
    $line   = __LINE__;

    throw new \BadMethodCallException(
      "Call to undefined method {$method}() in {$file} on line {$line}"
    );
  }

  /**
   * @param string $elementName
   *
   * @return \Cubex\Form\FormElement
   * @throws \BadMethodCallException
   */
  protected function _getElement($elementName)
  {
    $elementName[0] = strtolower($elementName[0]);

    $element = $this->getForm()->getElement($elementName);
    if($element === null)
    {
      throw new \BadMethodCallException(
        "The '{$elementName}' form element does not exist;"
      );
    }

    return $element;
  }

  /**
   * @param string $name
   *
   * @return array|null
   */
  protected function _parseMethodName($name)
  {
    if(substr($name, -5) === "Label" && substr($name, 0, 3) === "get")
    {
      return [
        "element" => substr($name, 3, -5),
        "type"    => static::METHOD_TYPE_GETLABEL
      ];
    }
    else if(substr($name, -5) === "Error" && substr($name, 0, 3) === "get")
    {
      return [
        "element" => substr($name, 3, -5),
        "type"    => static::METHOD_TYPE_GETERROR
      ];
    }
    else if(substr($name, -5) === "Error")
    {
      return [
        "element" => substr($name, 0, -5),
        "type"    => static::METHOD_TYPE_ERROR
      ];
    }
    else if(substr($name, 0, 3) === "get")
    {
      return [
        "element" => substr($name, 3),
        "type"    => static::METHOD_TYPE_GET
      ];
    }

    return null;
  }
}
