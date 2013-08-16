<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Campaigns;

use Qubes\Defero\Applications\Defero\Forms\CampaignForm;
use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;

class CampaignFormView extends DeferoView
{
  public $campaignForm;

  public function __construct(CampaignForm $campaignForm)
  {
    $this->campaignForm = $campaignForm;
  }
}
