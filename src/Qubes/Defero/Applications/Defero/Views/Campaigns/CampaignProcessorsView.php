<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 23/09/13
 * Time: 10:26
 */

namespace Qubes\Defero\Applications\Defero\Views\Campaigns;

use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignProcessorsView extends DeferoView
{
  public $campaign;

  public function __construct(Campaign $campaign)
  {
    $this->campaign = $campaign;
    $this->requireJsPackage("processors");
    $this->requireJsPackage("processors");
    $this->requireCssPackage("processors");
  }
}
