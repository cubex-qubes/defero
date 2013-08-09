<?php
/**
 * @author brooke.bryan
 * @author gareth.evans
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Data\Validator\Validator;
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

  public function contact()
  {
    return $this->belongsTo(new Contact());
  }
}
