<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Controllers\WebpageController;
use Qubes\Defero\Applications\Defero\Views\Base\HeaderView;
use Qubes\Defero\Applications\Defero\Views\Base\PageFlashView;

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
