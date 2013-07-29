<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Mapper\Database\RecordMapper;
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

  /**
   * @return Message
   */
  public function message()
  {
    $this->newInstanceOnFailedRelation(true);

    return $this->hasOne(new Message());
  }
}
