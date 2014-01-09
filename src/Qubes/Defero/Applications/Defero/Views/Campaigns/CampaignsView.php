<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Campaigns;

use Cubex\Mapper\Database\RecordCollection;
use Qubes\Defero\Applications\Defero\Enums\TypeAheadEnum;
use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;
use Qubes\Defero\Applications\Defero\Views\Base\TypeAheadSearchFormView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignsView extends DeferoView
{
  /**
   * @var Campaign[]
   */
  public $campaigns;

  public $campaignsSearch;

  public function __construct(RecordCollection $campaigns)
  {

    $this->campaigns = $campaigns;

    $this->requireJsPackage("typeahead");
    $this->campaignsSearch = new TypeAheadSearchFormView(
      TypeAheadEnum::CAMPAIGNS(), "Search Campaigns..."
    );
  }
}
