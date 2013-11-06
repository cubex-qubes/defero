<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Contact\Mappers;

use Cubex\Data\Validator\Validator;
use Cubex\Mapper\Database\RecordMapper;

class Contact extends RecordMapper
{
  /**
   * internal system reference for this contact
   */
  public $reference;
  /**
   * internal system description for this contact
   */
  public $description;

  public $name;
  public $email;
  public $jobTitle;
  public $signature;
  /**
   * @length 2
   */
  public $language;

  protected function _configure()
  {
    $this->_dbServiceName = "defero_db";

    $this->_attribute('reference')
      ->addValidator(Validator::VALIDATE_SCALAR)
      ->setRequired(true);

    $this->_attribute('name')
      ->addValidator(Validator::VALIDATE_SCALAR)
      ->setRequired(true);

    $this->_attribute('jobTitle')
      ->addValidator(Validator::VALIDATE_SCALAR)
      ->setRequired(true);

    $this->_attribute('email')
      ->addValidator(Validator::VALIDATE_EMAIL)
      ->setRequired(true);

    $this->_attribute('language')
      ->addValidator(Validator::VALIDATE_LENGTH, [2, 2])
      ->setRequired(true);

    $this->_attribute('signature')->addFilter('nl2br');
  }
}
