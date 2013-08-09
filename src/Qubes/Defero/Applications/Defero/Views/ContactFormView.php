<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Qubes\Defero\Applications\Defero\Forms\ContactForm;

class ContactFormView extends DeferoView
{
  public $contactForm;

  public function __construct(ContactForm $contactForm)
  {
    $this->contactForm = $contactForm;
  }
}
