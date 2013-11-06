<?php
/**
 * @author brooke.bryan
 * @author gareth.evans
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Data\Attribute\Attribute;
use Cubex\Data\Validator\Validator;
use Cubex\Helpers\DateTimeHelper;
use Cubex\Helpers\Strings;
use Cubex\Mapper\Database\RecordMapper;
use Qubes\Defero\Applications\Defero\Forms\CampaignForm;
use Qubes\Defero\Components\Campaign\Enums\CampaignType;
use Qubes\Defero\Components\Campaign\Enums\SendType;
use Qubes\Defero\Components\Contact\Mappers\Contact;
use Qubes\Defero\Components\Cron\CronParser;
use Qubes\Defero\Components\DataSource\IDataSource;
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
   * @type \Qubes\Defero\Components\DataSource\DataSourceCollection
   */
  public $dataSource;

  /**
   * @enumclass \Qubes\Defero\Components\Campaign\Enums\SendType
   */
  public $sendType;
  public $contactId;

  public $sendAt;
  public $lastSent;

  /**
   * @datatype tinyint
   * @default  0
   */
  public $active;

  /**
   * @datatype TEXT
   */
  public $processors;

  protected function _configure()
  {
    $this->_dbServiceName = "defero_db";

    $this->_attribute('reference')
      ->addValidator(Validator::VALIDATE_SCALAR)
      ->setRequired(true);

    $this->_attribute('name')
      ->addValidator(Validator::VALIDATE_SCALAR)
      ->setRequired(true);

    $this->_attribute('dataSource')
      ->setSerializer(Attribute::SERIALIZATION_JSON);

    $this->_attribute('sendType')
      ->addValidator(Validator::VALIDATE_ENUM, [new SendType()])
      ->setRequired(true);

    $this->_attribute('contactId')
      ->addValidator(Validator::VALIDATE_INT)
      ->setRequired(true);

    $this->_attribute('active')->addValidator(Validator::VALIDATE_BOOL);

    $this->_attribute('processors')
      ->setSerializer(Attribute::SERIALIZATION_JSON);
  }

  /**
   * @return Message
   */
  public function message()
  {
    $this->newInstanceOnFailedRelation(true);
    return $this->hasOne(new Message());
  }

  /**
   * @return bool|IDataSource
   */
  public function dataSource()
  {
    if(!$this->dataSource || !$this->dataSource->sourceClass)
    {
      return false;
    }
    $dataSource = $this->getData('dataSource');
    $ds         = 'Qubes\\Defero\\Components\\DataSource\\' . $dataSource->sourceClass;
    $ds         = new $ds();
    $ds->setConditionValues($dataSource->conditions);
    return $ds;
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
  public function getContact($language = null)
  {
    $contactId = $this->contactId;

    $msg = $this->message();
    $msg->setLanguage($language);
    $msg->reload();
    if($msg->contactId)
    {
      $contactId = $msg->contactId;
    }
    return new Contact($contactId);
  }

  public function getTitledSendType()
  {
    return Strings::titleize(
      $this->sendTypes()->constFromValue((string)$this->sendType)
    );
  }

  /**
   * Instantiates the form and binds the mapper. Also sets up the action based
   * on an id existing or not.
   *
   * @param string   $action
   * @param null|int $id
   *
   * @return CampaignForm
   */
  public static function buildCampaignForm($action, $id = null)
  {
    return (new CampaignForm("campaign", $action))
      ->bindMapper(new Campaign($id));
  }

  public function isDue()
  {
    $time = time();
    $time -= $time % 60;
    $check = $this->nextRun()->getTimestamp();
    $check -= $check % 60;
    return ($check === $time);
  }

  public function nextRun()
  {
    if(!$this->sendAt)
    {
      return null;
    }

    if(!CronParser::isValid($this->sendAt))
    {
      return DateTimeHelper::dateTimeFromAnything($this->sendAt);
    }

    $nr = CronParser::nextRun($this->sendAt, null, true);
    return $nr ? : null;
  }

  public function process($time)
  {
    $ds = $this->dataSource();
    $ds->process($this->id(), $time);
  }
}
