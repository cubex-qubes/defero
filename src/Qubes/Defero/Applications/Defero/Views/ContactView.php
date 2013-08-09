<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\View\HtmlElement;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class ContactView extends DeferoView
{
  /**
   * @var Contact
   */
  public $contact;

  public function __construct(Contact $contact)
  {
    $this->contact = $contact;
  }
}
