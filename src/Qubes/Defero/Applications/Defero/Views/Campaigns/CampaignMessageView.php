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

class CampaignMessageView extends DeferoView
{
  use ConfigTrait;

  public $campaign;
  public $form;
  public $languages;
  public $defaultLanguage;
  public $currentLanguage;
  /**
   * @var \Qubes\Defero\Components\Messages\Mappers\Message
   */
  protected $_message;

  public function __construct($campaignId, $language = null)
  {
    $this->requireJs(
      'http://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.0.1/ckeditor.js'
    );

    $config          = $this->config('i18n');
    $this->languages = $config->getArr('languages');

    $this->campaign = new Campaign($campaignId);
    $this->_message = $this->campaign->message();

    $this->defaultLanguage = $this->_message->language();
    $this->currentLanguage = $language;

    if($language)
    {
      $this->_message->setLanguage($language);
    }
    $this->_message->reload();

    $this->form = (new CampaignMessageForm('campaign_message'))
      ->bindMapper($this->_message);

    $this->form->hydrate($this->request()->postVariables());
    if($this->form->isValid() && $this->form->csrfCheck(true))
    {
      $this->form->saveChanges();
    }
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
