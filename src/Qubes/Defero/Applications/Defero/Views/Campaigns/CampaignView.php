<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Campaigns;

use Cubex\Mapper\Database\RecordCollection;
use Qubes\Defero\Applications\Defero\Helpers\RecordCollectionPagination;
use Qubes\Defero\Applications\Defero\Views\Contacts\ContactsView;
use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class CampaignView extends DeferoView
{
  /**
   * @var Campaign
   */
  public $campaign;

  public function __construct(Campaign $campaign, $page)
  {
    $this->campaign = $campaign;
  }
}
