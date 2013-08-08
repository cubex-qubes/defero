<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Qubes\Defero\Applications\Defero\Forms\CampaignForm;

class CampaignFormView extends DeferoView
{
  public $campaignForm;

  public function __construct(CampaignForm $campaignForm)
  {
    $this->campaignForm = $campaignForm;
  }
}
