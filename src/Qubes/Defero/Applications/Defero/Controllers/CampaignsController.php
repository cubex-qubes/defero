<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

class CampaignsController extends BaseDeferoController
{
  public function renderIndex()
  {
    echo "Campaigns";
  }

  public function getRoutes()
  {
    return ["(.*)" => "index",];
  }
}
