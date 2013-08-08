<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\View\TemplatedViewModel;
use Qubes\Defero\Applications\Defero\Forms\CampaignForm;

class CampaignView extends TemplatedViewModel
{
  public $campaignForm;

  public function __construct(CampaignForm $campaignForm)
  {
    $this->campaignForm = $campaignForm;
  }
}
