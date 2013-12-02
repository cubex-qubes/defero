<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 25/09/13
 * Time: 10:47
 */

namespace Qubes\Defero\Applications\Defero\Forms;

use Cubex\Form\FormElement;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class CampaignMessageForm extends DeferoForm
{
  protected function _postBind()
  {
    parent::_postBind();

    $this->getElement('plainText')
      ->setType(FormElement::TEXTAREA);

    $this->getElement('htmlContent')
      ->setType(FormElement::TEXTAREA)
      ->addAttribute('class', 'ckeditor');

    $this->getElement('campaignId')
      ->setType(FormElement::NONE);

    $contacts = ['' => ''] + Contact::collection()->getKeyPair('id', 'name');

    $this->getElement('contactId')
      ->setType(FormElement::SELECT)
      ->setOptions($contacts);
  }
}
