<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Forms;

use Cubex\Data\Validator\Validator;
use Cubex\Form\Form;

class CampaignForm extends Form
{
  protected function _configure()
  {
    $this->setNoValidate();

    return parent::_configure();
  }

  protected function _postBind()
  {
    $this->getElement("active")->setLabelPosition(Form::LABEL_NONE);

    if($this->_mapper->id())
    {
      $this->getElement("reference")->addAttribute("disabled", "disabled");
    }

    return parent::_postBind();
  }
}
