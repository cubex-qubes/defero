<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 10/10/13
 * Time: 14:04
 */

namespace Qubes\Defero\Applications\Defero\Views\Campaigns;

use Cubex\Form\Form;
use Cubex\Foundation\Config\ConfigTrait;
use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;

class CampaignSourceView extends DeferoView
{
  public $form;

  public function __construct(Form $form)
  {
    $this->form = $form;
  }
}
