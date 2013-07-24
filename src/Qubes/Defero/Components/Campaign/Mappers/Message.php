<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Mappers;

use Cubex\Mapper\Database\I18n\I18nRecordMapper;
use Cubex\Mapper\Database\I18n\TextContainer;

class Message extends I18nRecordMapper
{
  public $campaignId;
  public $subject;
  public $message;

  protected function _configure()
  {
    $this->_addTranslationAttribute("subject");
    $this->_addTranslationAttribute("message");
  }

  /**
   * @return TextContainer
   */
  public function getTextContainer()
  {
    return new Translatable();
  }

  /**
   * @return Campaign
   */
  public function campaign()
  {
    return $this->belongsTo(new Campaign());
  }
}
