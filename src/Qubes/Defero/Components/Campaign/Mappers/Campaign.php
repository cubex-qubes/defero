<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Mapper\Database\RecordMapper;

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
