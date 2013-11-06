<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Form;

use Cubex\Form\FormElement;
use Cubex\Form\FormElementRender;
use Cubex\View\HtmlElement;

class DeferoFormElementRender extends FormElementRender
{
  public function render()
  {
    $search = [
      '{{labelcontent}}',
      '{{labelfor}}',
      '{{errorclass}}',
    ];
    $replace = [
      $this->_element->label(),
      $this->_element->id(),
      $this->_element->validationErrors() ? "error" : "",
    ];

    return str_replace($search, $replace, parent::render());
  }

  public function getTemplate()
  {
    if ($this->_template) return $this->_template;
    switch($this->_element->type())
    {
      case FormElement::SUBMIT:
      case FormElement::BUTTON:
        return $this->_getNoLabelTemplate();
      default:
        return $this->_getDefaultTemplate();
    }
  }

  private function _getNoLabelTemplate()
  {
    return (new HtmlElement(
      "div", ["class" => "control-group form-inline"]
    ))->nestElement(
      "div", ["class" => "controls clearfix"], "{{input}}"
    )->render();
  }

  private function _getDefaultTemplate()
  {
    $controlGroup = (new HtmlElement(
      "div", ["class" => "control-group {{errorclass}}"]
    ))->nestElement(
      "label",
      ["class" => "control-label", "for" => "{{labelfor}}"],
      "{{labelcontent}}"
    );

    $control = new HtmlElement("div", ["class" => "controls"], "{{input}}");

    if($this->_element->validationErrors())
    {
      $control->nestElement(
        "span",
        ["class" => "help-inline"],
        $this->_element->validationErrors()[0]
      );
    }

    return $controlGroup->nest($control)->render();
  }
}
