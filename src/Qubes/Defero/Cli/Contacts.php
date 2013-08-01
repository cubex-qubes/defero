<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Cli;

use Cubex\Cli\ArgumentException;
use Cubex\Cli\CliCommand;
use Cubex\Text\TextTable;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class Contacts extends CliCommand
{
  /**
   * @valuerequired
   * @validator INT
   */
  public $id;
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
   * @validator EMAIL
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

  public function execute()
  {
    $this->_help();
  }

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

    $updates = [];

    if($contact->exists())
    {
      $updatables = [
        "reference",
        "description",
        "name",
        "email",
        "jobTitle",
        "signature",
      ];

      foreach($updatables as $updatable)
      {
        if($this->argumentIsSet($updatable))
        {
          $updates[] = [
            "old" => $contact->{$updatable},
            "new" => $this->{$updatable},
          ];
          $contact->{$updatable} = $this->{$updatable};
        }
      }

      if($updates)
      {
        $contact->saveChanges();

        echo "Contact {$contact->id()} updated;\n\n";

        echo TextTable::fromArray($updates);
      }
      else
      {
        echo "No updates were made\n";
        echo "Updatable fields: " . implode(", ", $updatables);
      }
    }
    else
    {
      echo "Contact {$this->id} does not exist, did you mean to call 'add'?";
    }
  }

  public function remove()
  {
    $this->_throwIfNotSet("id");

    (new Contact($this->id))->delete();

    echo "Contact {$this->id} removed";
  }

  /**
   * @param string      $arg
   * @param null|string $type
   *
   * @throws \Cubex\Cli\ArgumentException
   */
  private function _throwIfNotSet($arg, $type = null)
  {
    if($type === null)
    {
      $type = debug_backtrace()[1]['function'];
    }

    if(!$this->argumentIsSet($arg))
    {
      throw new ArgumentException(
        "'{$arg}' must be set when calling '{$type}'"
      );
    }
  }
}
