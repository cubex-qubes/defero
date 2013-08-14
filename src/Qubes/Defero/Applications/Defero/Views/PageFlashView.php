<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\Facade\Session;
use Cubex\View\HtmlElement;
use Cubex\View\ViewModel;

class PageFlashView extends ViewModel
{
  public function render()
  {
    if(Session::getFlash('msg'))
    {
      $flash = new HtmlElement(
        "div",
        ["class" => "alert alert-".Session::getFlash('msg')->type],
        Session::getFlash('msg')->message
      );
      $flash->nestElement(
        "button",
        ["type" => "button", "class" => "close", "data-dismiss" => "alert"],
        "&times;"
      );

      return $flash;
    }

    return "";
  }
}
