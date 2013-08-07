<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Controllers\WebpageController;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Qubes\Defero\Applications\Defero\Views\Index;

class DeferoController extends WebpageController
{
  public function renderIndex()
  {
    $this->tryNest(
      "header",
      (new HtmlElement("ul", ["class" => "nav"]))->nest(
        new RenderGroup(
          (new HtmlElement("li"))->nestElement(
            "a", ["href" => "/campaigns"], "Campaigns"
          ),
          (new HtmlElement("li"))->nestElement(
            "a", ["href" => "/contacts"], "Contacts"
          ),
          (new HtmlElement("li"))->nestElement(
            "a", ["href" => "/message-processors"], "Message Processors"
          )
        )
      )
    );
    return new Index();
  }

  public function getRoutes()
  {
    return ["*" => "index",];
  }
}
