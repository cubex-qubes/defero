<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Controllers\WebpageController;
use Qubes\Defero\Applications\Defero\Views\Header;
use Qubes\Defero\Applications\Defero\Views\PageFlash;

abstract class BaseDeferoController extends WebpageController
{
  public function preRender()
  {
    $this->requireCss("defero");
    $this->requireJs("defero");
    $this->requireJsPackage("bootstrap");
    $this->tryNest("header", new Header());
    $this->renderBefore("content", new PageFlash());
  }
}
