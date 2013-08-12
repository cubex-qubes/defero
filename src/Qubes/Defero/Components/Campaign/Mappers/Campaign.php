<?php
/**
 * @author brooke.bryan
 * @author gareth.evans
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Data\Validator\Validator;
use Cubex\Helpers\Strings;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Mapper\Database\RecordMapper;
use Qubes\Defero\Components\Campaign\Enums\CampaignType;
use Qubes\Defero\Components\Campaign\Enums\SendType;
use Qubes\Defero\Components\Contact\Mappers\Contact;
use Qubes\Defero\Components\Messages\Mappers\Message;

class Campaign extends RecordMapper
{
  /**
   * Internal reference for triggers e.g user-joined
   *
   * @unique
   */
  public $reference;
  public $name;
  public $description;
  /**
   * @enumclass \Qubes\Defero\Components\Campaign\Enums\CampaignType
   */
  public $type;
  /**
   * @enumclass \Qubes\Defero\Components\Campaign\Enums\SendType
   */
  public $sendType;
  public $contactId;
  /**
   * @datatype tinyint
   * @default  0
   */
  public $active = false;

  protected function _configure()
  {
    $this->_dbServiceName = "defero_db";

    $this->_attribute('reference')
      ->addValidator(Validator::VALIDATE_SCALAR)
      ->setRequired(true);

    $this->_attribute('name')
      ->addValidator(Validator::VALIDATE_SCALAR)
      ->setRequired(true);

    $this->_attribute('type')
      ->addValidator(Validator::VALIDATE_ENUM, [new CampaignType])
      ->setRequired(true);

    $this->_attribute('sendType')
      ->addValidator(Validator::VALIDATE_ENUM, [new SendType()])
      ->setRequired(true);

    $this->_attribute('contactId')
      ->addValidator(Validator::VALIDATE_INT)
      ->setRequired(true);

    $this->_attribute('active')->addValidator(Validator::VALIDATE_BOOL);
  }

  /**
   * @return Message
   */
  public function message()
  {
    $this->newInstanceOnFailedRelation(true);

    return $this->hasOne(new Message());
  }

  public function types()
  {
    return new CampaignType();
  }

  public function sendTypes()
  {
    return new SendType();
  }

  public function contacts()
  {
    return $this->belongsTo(new Contact());
  }

  /**
   * @param string $language
   *
   * @return Contact
   */
  public function getContact($language)
  {
    $contact = CampaignContact::collection()->loadOneWhere(
      [
        "language"    => $language,
        "campaign_id" => $this->id()
      ]
    );

    if(!$contact)
    {
      $contact = new Contact($this->contactId);
    }

    return $contact;
  }

  /**
   * @return RecordCollection
   */
  public function getContacts()
  {
    $contactIds = CampaignContact::collection(['campaign_id' => $this->id()])
      ->getKeyPair("contact_id", "contact_id");

    $contactIds[$this->contactId] = $this->contactId;

    $contacts = Contact::collection()->loadIds($contactIds);

    return $contacts;
  }

  public function getTitledType()
  {
    return Strings::titleize(
      $this->types()->constFromValue((string)$this->type)
    );
  }

  public function getTitledSendType()
  {
    return Strings::titleize(
      $this->sendTypes()->constFromValue((string)$this->sendType)
    );
  }
}
