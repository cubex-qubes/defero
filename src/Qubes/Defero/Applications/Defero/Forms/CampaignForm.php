<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Forms;

use Cubex\Form\Form;
use Cubex\Form\FormElement;
use Qubes\Defero\Components\DataSource\DataSourceCollection;

class CampaignForm extends DeferoForm
{
  protected function _postBind()
  {
    $this->getElement("active")->setLabelPosition(Form::LABEL_NONE);

    $this->getElement('dataSource')->setType(FormElement::NONE);
    $this->getElement('processors')->setType(FormElement::NONE);
    $this->getElement('lastSent')->setType(FormElement::NONE);

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
