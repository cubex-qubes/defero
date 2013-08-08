<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Forms;

use Cubex\Data\Validator\Validator;
use Cubex\Form\Form;

class CampaignForm extends DeferoForm
{
  protected function _postBind()
  {
    $this->getElement("active")->setLabelPosition(Form::LABEL_NONE);

    if($this->_mapper->id())
    {
      $this->getElement("reference")->addAttribute("disabled", "disabled");
    }

    $return = parent::_postBind();

    $this->getElement("submit")
      ->addAttribute("data-loading-text", "Submitting Campaign");

    return $return;
  }
}
