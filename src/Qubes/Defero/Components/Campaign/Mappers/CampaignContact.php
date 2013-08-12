<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Qubes\Defero\Components\Contact\Mappers\Contact;

/**
 * Class CampaignContact
 * @package Qubes\Defero\Components\Campaign\Mappers
 *
 * @unique campaign_id,language
 */
class CampaignContact extends RecordMapper
{
  public $campaignId;
  /**
   * @length 2
   */
  public $language;
  public $contactId;

  protected function _configure()
  {
    $this->_dbServiceName = "defero_db";
  }

  /**
   * @return Campaign
   */
  public function campaign()
  {
    return $this->belongsTo(new Campaign());
  }

  /**
   * @return Contact
   */
  public function contact()
  {
    return $this->belongsTo(new Contact());
  }
}
