<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\View\HtmlElement;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignView extends DeferoView
{
  /**
   * @var Campaign
   */
  public $campaign;

  public function __construct(Campaign $campaign)
  {
    $this->campaign = $campaign;
  }
}
