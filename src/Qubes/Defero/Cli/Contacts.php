<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Cli;

use Cubex\Text\TextTable;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class Contacts extends MapperCliCommand
{
  /**
   * @valuerequired
   */
  public $reference;
  /**
   * @valuerequired
   */
  public $description;
  /**
   * @valuerequired
   */
  public $name;
  /**
   * @valuerequired
   * @validator email
   */
  public $email;
  /**
   * @valuerequired
   */
  public $jobTitle;
  /**
   * @valuerequired
   */
  public $signature;

  public function get()
  {
    $this->_throwIfNotSet("id");

    $contact = new Contact($this->id);

    echo (new TextTable())->setColumnHeaders(
      [
        "Id",
        "Ref",
        "Desc",
        "Name",
        "Email",
        "Job Title",
        "Signature",
      ]
    )->appendRows(
      [
        [
          $contact->id(),
          $contact->reference,
          $contact->description,
          $contact->name,
          $contact->email,
          $contact->jobTitle,
          $contact->signature,
        ],
      ]
    );
  }

  public function add()
  {
    $this->_throwIfNotSet("reference");
    $this->_throwIfNotSet("description");
    $this->_throwIfNotSet("name");
    $this->_throwIfNotSet("email");

    $contact              = new Contact();
    $contact->reference   = $this->reference;
    $contact->description = $this->description;
    $contact->name        = $this->name;
    $contact->email       = $this->email;
    $contact->jobTitle    = $this->jobTitle;
    $contact->signature   = $this->signature;
    $contact->saveChanges();

    echo "Added contact id {$contact->id()} ({$contact->name})";
  }

  public function edit()
  {
    $this->_throwIfNotSet("id");

    $contact = new Contact($this->id);

    $updatables = [
      "reference",
      "description",
      "name",
      "email",
      "jobTitle",
      "signature",
    ];

    $this->_edit($contact, $updatables);
  }

  public function remove()
  {
    $this->_throwIfNotSet("id");

    (new Contact($this->id))->delete();

    echo "Contact {$this->id} removed";
  }
}
