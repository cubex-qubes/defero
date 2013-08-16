<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Contacts;

use Qubes\Defero\Applications\Defero\Forms\ContactForm;
use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;

class ContactFormView extends DeferoView
{
  public $contactForm;

  public function __construct(ContactForm $contactForm)
  {
    $this->contactForm = $contactForm;
  }
}
