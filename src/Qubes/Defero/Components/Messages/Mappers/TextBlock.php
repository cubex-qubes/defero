<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\HtmlMessages\Mappers;

use Cubex\Mapper\Database\I18n\I18nRecordMapper;
use Cubex\Mapper\Database\I18n\TextContainer;
use Qubes\Defero\Components\Campaign\Mappers\Translatable;

class TextBlock extends I18nRecordMapper
{
  public $text;
  public $messageId;

  /**
   * @return TextContainer
   */
  public function getTextContainer()
  {
    return new Translatable();
  }
}
