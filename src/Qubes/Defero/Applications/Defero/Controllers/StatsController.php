<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 09/01/14
 * Time: 10:59
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Qubes\Defero\Applications\Defero\Views\Stats\StatsView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class StatsController extends DeferoController
{
  public function renderIndex()
  {
    $campaigns = Campaign::collection()->setOrderBy('sortOrder');
    return new StatsView($campaigns, $this->request()->postVariables('date'));
  }
}
