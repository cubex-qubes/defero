<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Contact\Mappers;

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
}
