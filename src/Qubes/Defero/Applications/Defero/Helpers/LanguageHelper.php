<?php
/**
 * @author  oke.ugwu
 */

namespace Qubes\Defero\Applications\Defero\Helpers;

class LanguageHelper
{
  public static function getAvailableLanguages()
  {
    $availableLanguages = [];
    $config          = Container::config()->get("i18n");
    if($config != null)
    {
      $languages = $config->getArr('languages', []);
      foreach($languages as $lang)
      {
        $availableLanguages[$lang] = $lang;
      }
    }

    return $availableLanguages;
  }
}
