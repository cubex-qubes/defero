<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 25/09/13
 * Time: 10:54
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Foundation\Config\Config;
use Cubex\Foundation\Container;
use Cubex\Routing\StdRoute;
use Qubes\Defero\Applications\Defero\Forms\CampaignMessageForm;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignMessageView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignMessageController extends DeferoController
{
  protected $_message;

  public function renderIndex()
  {
    $config    = $this->config('i18n');
    $languages = $config->getArr('languages');
    return new CampaignMessageView($this->_getMessage(), $languages);
  }

  public function postIndex()
  {
    $form = new CampaignMessageForm('campaign_message');
    $form->bindMapper($this->_getMessage());
    $form->hydrate($this->request()->postVariables());
    if($form->isValid() && $form->csrfCheck(true))
    {
      $form->saveChanges();
    }
    return $this->renderIndex();
  }

  protected function _getMessage()
  {
    if(!$this->_message)
    {
      $campaign       = new Campaign($this->getInt('id'));
      $this->_message = $campaign->message();

      $currentLanguage = $this->getStr('hl');
      if($currentLanguage)
      {
        $this->_message->setLanguage($currentLanguage);
      }
      $this->_message->reload();
    }
    return $this->_message;
  }

  public function renderTranslate()
  {
    $campaign = new Campaign($this->getInt('id'));
    $message  = $campaign->message();
    $message->reload();

    $subject = $message->subject;
    while($this->translateBlock($subject))
    {
    }

    $plainText = $message->plainText;
    while($this->translateBlock($plainText))
    {
    }

    $htmlContent = $message->htmlContent;
    while($this->translateBlock($htmlContent))
    {
    }

    $message->setLanguage($this->getStr('hl'));
    $message->reload();
    $message->subject     = $subject;
    $message->plainText   = $plainText;
    $message->htmlContent = $htmlContent;
    $message->saveChanges();

    return Redirect::to(
      '/campaigns/' . $this->getStr(
        'id'
      ) . '/message/' . $this->getStr('hl')
    );
  }

  public function translateBlock(&$string)
  {
    $startString = $string;

    // start, end
    $start = strpos($string, '{{');
    if($start === false)
    {
      return false;
    }
    $end = strpos($string, '}}', $start) + 2;
    if($end === false)
    {
      return false;
    }
    $match = substr($string, $start, $end - $start);

    // translate
    $config         = Container::config()->get("i18n", new Config());
    $translateClass = $config->getStr("translator", null);
    if(!$translateClass)
    {
      throw new \Exception(
        'Missing \'translator\' in i18n section of the config'
      );
    }
    $translator = new $translateClass();
    $replace    = $translator->translate(
      substr($match, 2, -2),
      'en',
      $this->getStr('hl')
    );
    // replace
    $string = str_replace($match, $replace, $string);

    return $string !== $startString;
  }

  public function getRoutes()
  {
    return [
      new StdRoute('/:hl/translate', 'translate'),
      new StdRoute('/:hl', 'index'),
      new StdRoute('/', 'index')
    ];
  }
}
