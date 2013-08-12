<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Controllers\WebpageController;
use Qubes\Defero\Applications\Defero\Views\Header;

abstract class BaseDeferoController extends WebpageController
{
  public function preRender()
  {
    $this->tryNest("header", new Header());
    $this->requireCss("defero");
    $this->requireJs("defero");
    $this->requireJsPackage("bootstrap");
  }
}
