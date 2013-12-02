<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 25/09/13
 * Time: 11:15
 */

namespace Qubes\Defero\Applications\Defero\Views\Campaigns;

use Cubex\Foundation\Config\ConfigTrait;
use Cubex\View\HtmlElement;
use Qubes\Defero\Applications\Defero\Forms\CampaignMessageForm;
use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Messages\Mappers\Message;

class CampaignMessageView extends DeferoView
{
  public $message;
  public $languages;

  public function __construct(Message $message, $languages)
  {
    $this->requireJs(
      'http://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.0.1/ckeditor.js'
    );

    $this->message   = $message;
    $this->languages = $languages;
    $this->form      = (new CampaignMessageForm('campaign_message'))
      ->bindMapper($message);
  }

  public function getConfirmPopover($href)
  {
    $popover = (new HtmlElement(
      "div", ["class" => "text-center"], "Are you sure?<br />"
    ))->nestElement("a", ["href" => "{$href}"], "Yes")
      ->nestElement("span", [], " | ")
      ->nestElement(
        "a",
        ["href" => "#", "class" => "js-popover-hide"],
        "<strong>No</strong>"
      );

    return htmlspecialchars($popover);
  }
}
