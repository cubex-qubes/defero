<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Forms;

use Qubes\Defero\Applications\Defero\Forms\CampaignForm;
use Qubes\Defero\Applications\Defero\Views\Forms\BaseFormView;

/**
 * Class CampaignFormView
 * @package Qubes\Defero\Applications\Defero\Views\Forms
 *
 * @method \Cubex\Form\FormElement getReference
 * @method \Cubex\Form\FormElement getName
 * @method \Cubex\Form\FormElement getDescription
 * @method \Cubex\Form\FormElement getType
 * @method \Cubex\Form\FormElement getSendType
 * @method \Cubex\Form\FormElement getContactId
 * @method \Cubex\Form\FormElement getActive
 *
 * @method \Cubex\Form\FormElement referenceError
 * @method \Cubex\Form\FormElement nameError
 * @method \Cubex\Form\FormElement descriptionError
 * @method \Cubex\Form\FormElement typeError
 * @method \Cubex\Form\FormElement sendTypeError
 * @method \Cubex\Form\FormElement contactIdError
 * @method \Cubex\Form\FormElement activeError
 *
 * @method \Cubex\Form\FormElement getReferenceError
 * @method \Cubex\Form\FormElement getNameError
 * @method \Cubex\Form\FormElement getDescriptionError
 * @method \Cubex\Form\FormElement getTypeError
 * @method \Cubex\Form\FormElement getSendTypeError
 * @method \Cubex\Form\FormElement getContactIdError
 * @method \Cubex\Form\FormElement getActiveError
 *
 * @method \Cubex\Form\FormElement getReferenceLabel
 * @method \Cubex\Form\FormElement getNameLabel
 * @method \Cubex\Form\FormElement getDescriptionLabel
 * @method \Cubex\Form\FormElement getTypeLabel
 * @method \Cubex\Form\FormElement getSendTypeLabel
 * @method \Cubex\Form\FormElement getContactIdLabel
 * @method \Cubex\Form\FormElement getActiveLabel
 */
class CampaignFormView extends BaseFormView
{
  public function __construct(CampaignForm $form)
  {
    parent::__construct($form);
  }
}
