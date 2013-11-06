<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Forms;

use Cubex\Form\Form;
use Cubex\Form\FormElement;

class DeferoForm extends Form
{
  protected function _configure()
  {
    $this->addAttribute("class", "form-horizontal");
    $this->setNoValidate();
  }

  protected function _postBind()
  {
    $this->getElement("submit")
      ->setType(FormElement::BUTTON)
      ->addAttribute("class", "btn js-btn-loading")
      ->addAttribute("data-loading-text", "Submitting");

    return parent::_postBind();
  }
}
