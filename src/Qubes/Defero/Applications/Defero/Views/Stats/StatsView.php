<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 09/01/14
 * Time: 11:01
 */

namespace Qubes\Defero\Applications\Defero\Views\Stats;

use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;

class StatsView extends DeferoView
{
  public $campaigns;

  public function __construct($campaigns)
  {
    $this->campaigns = $campaigns;
  }
}
