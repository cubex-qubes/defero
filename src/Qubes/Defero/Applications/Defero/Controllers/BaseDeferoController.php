<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Controllers\WebpageController;
use Qubes\Defero\Applications\Defero\Views\HeaderView;
use Qubes\Defero\Applications\Defero\Views\PageFlashView;

abstract class BaseDeferoController extends WebpageController
{
  public function preRender()
  {
    $this->requireCss("defero");
    $this->requireJs("defero");
    $this->requireJsPackage("bootstrap");
    $this->tryNest("header", new HeaderView());
    $this->renderBefore("content", new PageFlashView());
  }
}
