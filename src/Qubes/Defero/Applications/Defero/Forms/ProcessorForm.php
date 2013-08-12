<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Forms;

use Cubex\Data\Validator\Validator;
use Cubex\Form\Form;

class ProcessorForm extends DeferoForm
{
  protected function _postBind()
  {
    if($this->_mapper->id())
    {
      $this->getElement("name")->addAttribute("disabled", "disabled");
    }

    $return = parent::_postBind();

    $this->getElement("submit")
      ->addAttribute("data-loading-text", "Submitting Processor");

    return $return;
  }
}
