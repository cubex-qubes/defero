<?php
/**
 * @author brooke.bryan
 * @author gareth.evans
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Data\Attribute\Attribute;
use Cubex\Data\Validator\Validator;
use Cubex\Foundation\Config\ConfigTrait;
use Cubex\Helpers\DateTimeHelper;
use Cubex\Helpers\Strings;
use Cubex\Mapper\Database\RecordMapper;
use Qubes\Defero\Applications\Defero\Forms\CampaignForm;
use Qubes\Defero\Components\Campaign\Enums\SendType;
use Qubes\Defero\Components\Campaign\Enums\TrackingType;
use Qubes\Defero\Components\Contact\Mappers\Contact;
use Qubes\Defero\Components\Cron\CronParser;
use Qubes\Defero\Components\DataSource\DataSourceConditionsTrait;
use Qubes\Defero\Components\DataSource\DataSource;
use Qubes\Defero\Components\Messages\Mappers\Message;

class Campaign extends RecordMapper
{
  use ConfigTrait;

  /**
   * Internal reference for triggers e.g user-joined
   *
   * @unique
   */
  public $reference;
  public $name;
  public $description;

  /**
   * @datatype varchar(50)
   */
  public $label;

  /**
   * @enum
   */
  public $dataSource;

  /**
   * @datatype text
   */
  public $dataSourceOptions;

  /**
   * @enumclass \Qubes\Defero\Components\Campaign\Enums\SendType
   */
  public $sendType;
  public $contactId;

  /**
   * @enumclass \Qubes\Defero\Components\Campaign\Enums\TrackingType
   */
  public $trackingType;

  /**
   * @datatype varchar(50)
   */
  public $sendAt;

  /**
   * @datatype int
   */
  public $lastSent;

  /**
   * @datatype tinyint
   * @default  0
   */
  public $active;

  /**
   * @datatype text
   */
  public $processors;

  /**
   * @datatype text
   */
  public $availableLanguages;

  /**
   * @datatype int
   */
  public $sortOrder;

  protected static $_labels;

  public static function setLabels($labels)
  {
    self::$_labels = $labels;
  }

  public static function labels()
  {
    return self::$_labels;
  }

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
      ->setRequired(true);

    $this->_attribute('dataSourceOptions')
      ->setSerializer(Attribute::SERIALIZATION_JSON);

    $this->_attribute('sendType')
      ->addValidator(Validator::VALIDATE_ENUM, [new SendType()])
      ->setRequired(true);

    $this->_attribute('trackingType')
      ->addValidator(Validator::VALIDATE_ENUM, [new TrackingType()])
      ->setRequired(true);

    $this->_attribute('contactId')
      ->addValidator(Validator::VALIDATE_INT)
      ->setRequired(true);

    $this->_attribute('active')->addValidator(Validator::VALIDATE_BOOL);

    $this->_attribute('processors')
      ->setSerializer(Attribute::SERIALIZATION_JSON);

    $this->_attribute('availableLanguages')
      ->setSerializer(Attribute::SERIALIZATION_JSON);
  }

  /**
   * @return Message
   */
  public function message()
  {
    $this->newInstanceOnFailedRelation(true);
    return $this->hasOne(new Message())->reload();
  }

  /**
   * @return DataSource
   */
  public function getDataSource()
  {
    if(isset($this->dataSource))
    {
      $sourceClass = $this->dataSource;
      if(!class_exists($sourceClass))
      {
        $sourceClass = $this->config('datasources')->getStr($sourceClass);
      }
      if(class_exists($sourceClass))
      {
        /**
         * @var $dataSource DataSource
         */

        $dataSource = new $sourceClass();
        if($this->dataSourceOptions)
        {
          $dataSource->hydrate((array)$this->dataSourceOptions);
        }
        return $dataSource;
      }
    }
    return null;
  }

  public function dataSources()
  {
    return ['' => ''] + array_fuse(
      $this->config('datasources')->availableKeys()
    );
  }

  public function sendTypes()
  {
    return new SendType();
  }

  public function trackingTypes()
  {
    return new TrackingType();
  }

  public function contacts()
  {
    $contacts = $this->belongsTo(new Contact());
    return $contacts::collection()->getKeyPair('id', 'reference');
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
    if($this->active && ($check = $this->nextRun()))
    {
      $time = time();
      $time -= $time % 60;
      $check = $check->getTimestamp();
      $check -= $check % 60;
      return ($check === $time);
    }
    return false;
  }

  public function nextRun()
  {
    if(!$this->sendAt || !$this->active)
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

  public function getRequiredFields()
  {
    // find what data the email message requires
    $requiredFields = ['firstName', 'lastName', 'email'];

    $message = $this->message();
    preg_match_all('/{!([^}]+)}/', $message->plainText, $matches);
    preg_match_all('/{!([^}]+)}/', $message->htmlContent, $matches2);
    $matches = array_unique(
      array_merge($requiredFields, $matches[1], $matches2[1])
    );

    // read from all processes for 'requiredData'

    return $matches;
  }

  public function getStats(\DateTime $from, \DateTime $to)
  {
    return MailStatistic::getCampaignStats($this->id(), $from, $to);
  }
}
