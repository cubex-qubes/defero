<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 25/09/13
 * Time: 10:54
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Facade\Redirect;
use Cubex\I18n\Translator\Reversulator;
use Cubex\Routing\StdRoute;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignMessageView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignMessageController extends DeferoController
{
  public function renderIndex()
  {
    $this->requireJs('http://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.0.1/ckeditor.js');
    return new CampaignMessageView($this->getInt('id'), $this->getStr('hl'));
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
    $translator = new Reversulator();
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
