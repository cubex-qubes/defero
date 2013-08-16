<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Routing\Templates\ResourceTemplate;
use Qubes\Defero\Applications\Defero\Forms\ContactForm;
use Qubes\Defero\Applications\Defero\Helpers\RecordCollectionPagination;
use Qubes\Defero\Applications\Defero\Views\Contacts\ContactFormView;
use Qubes\Defero\Applications\Defero\Views\Contacts\ContactsView;
use Qubes\Defero\Applications\Defero\Views\Contacts\ContactView;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class ContactsController extends BaseDeferoController
{
  /**
   * Show a blank contact form
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Contacts\ContactFormView
   */
  public function renderNew()
  {
    return new ContactFormView($this->_buildContactForm());
  }

  /**
   * Show a pre-populated contact form
   *
   * @param int          $id
   * @param ContactForm $contactForm
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Contacts\ContactFormView
   */
  public function renderEdit($id, ContactForm $contactForm = null)
  {
    return new ContactFormView(
      $contactForm ? : $this->_buildContactForm($id)
    );
  }

  /**
   * Update an existing contact
   *
   * @param int $id
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Contacts\ContactFormView
   */
  public function actionUpdate($id)
  {
    return $this->_updateContact($id);
  }

  /**
   * Delete a contact
   *
   * @param int $id
   *
   * @return \Cubex\Core\Http\Redirect
   */
  public function actionDestroy($id)
  {
    $contact = new Contact($id);
    $contact->forceLoad();
    $contact->delete();

    return Redirect::to('/contacts')
      ->with(
        'msg',
        new TransportMessage('info', "Contact '{$contact->name}' deleted.")
      );
  }

  /**
   * Output a single contact
   *
   * @param int $id
   *
   * @return ContactView
   */
  public function renderShow($id)
  {
    return new ContactView(new Contact($id));
  }

  /**
   * Create a new contact
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Contacts\ContactFormView
   */
  public function postCreate()
  {
    return $this->_updateContact();
  }

  /**
   * Show a paginated list of contacts
   *
   * @param int $page
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Contacts\ContactsView
   */
  public function renderIndex($page = 1)
  {
    $contacts = (new RecordCollection(new Contact()))->setOrderBy("id");

    $pagination = new RecordCollectionPagination($contacts, $page);
    $pagination->setUri("/contacts/page");

    return new ContactsView($contacts, $pagination);
  }

  /**
   * Helper method to handle create and update of contacts. Will redirect to
   * the specific contact on success with a message. If there are any
   * validation or CSRF errors we render the form again with information.
   *
   * @param null|int $id
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Contacts\ContactFormView
   */
  private function _updateContact($id = null)
  {
    $form = $this->_buildContactForm($id);
    $form->hydrate($this->request()->postVariables());

    if($form->isValid() && $form->csrfCheck(true))
    {
      $form->saveChanges();

      $msg = "Contact '{$form->name}'";
      $msg .= $id ? " Updated" : " Created";

      return Redirect::to("/contacts/{$form->getMapper()->id()}")
        ->with("msg", new TransportMessage("info", $msg));
    }

    return $this->renderEdit($id, $form);
  }

  /**
   * Instantiates the form and binds the mapper. Also sets up the action based
   * on an id existing or not.
   *
   * @param null|int $id
   *
   * @return ContactForm
   */
  protected function _buildContactForm($id = null)
  {
    $action = $id ? "/contacts/{$id}" : "/contacts";

    return (new ContactForm("contact", $action))
      ->bindMapper(new Contact($id));
  }

  public function getRoutes()
  {
    return ResourceTemplate::getRoutes();
  }
}
