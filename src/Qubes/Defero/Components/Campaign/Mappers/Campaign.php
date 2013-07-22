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
  public $subject;
  /**
   * @enumclass \Qubes\Defero\Components\Campaign\Enums\CampaignType
   */
  public $type;
  public $contactId;
}
