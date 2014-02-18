<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 25/09/13
 * Time: 10:47
 */

namespace Qubes\Defero\Applications\Defero\Forms;

use Cubex\Form\FormElement;

class CampaignMessageForm extends DeferoForm
{
  protected function _postBind()
  {
    parent::_postBind();

    $this->getElement('plainText')
      ->setType(FormElement::TEXTAREA);

    $this->getElement('htmlContent')
      ->setType(FormElement::TEXTAREA);

    $this->getElement('campaignId')
      ->setType(FormElement::NONE);
  }
}
