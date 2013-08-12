<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Messages\Mappers;

use Cubex\Mapper\Database\I18n\I18nRecordMapper;
use Cubex\Mapper\Database\I18n\TextContainer;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Messages\Mappers\Translatable;

class Message extends I18nRecordMapper
{
  public $campaignId;
  public $subject;
  public $plainText;
  public $htmlContent;
  public $messageType;

  protected function _configure()
  {
    $this->_dbServiceName = "defero_db";

    $this->_addTranslationAttribute("subject");
    $this->_addTranslationAttribute("plainText");
    $this->_addTranslationAttribute("htmlContent");
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
