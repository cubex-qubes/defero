<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Controllers\WebpageController;
use Qubes\Defero\Applications\Defero\Views\Header;
use Qubes\Defero\Applications\Defero\Views\Index;

class DeferoController extends WebpageController
{
  public function renderIndex()
  {
    $this->tryNest("header", new Header());
    return new Index();
  }

  public function getRoutes()
  {
    return ["(.*)" => "index",];
  }
}
