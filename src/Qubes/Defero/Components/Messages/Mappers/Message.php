<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Messages\Mappers;

use Cubex\Data\Validator\Validator;
use Cubex\Mapper\Database\I18n\I18nRecordMapper;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class Message extends I18nRecordMapper
{
  public $campaignId;

  public $subject;
  /**
   * @datatype TEXT
   */
  public $plainText;
  /**
   * @datatype TEXT
   */
  public $htmlContent;
  public $contactId;

  protected $_dbServiceName = "defero_db";

  protected function _configure()
  {
    $this->_addTranslationAttribute(
      ["subject", "plainText", "htmlContent", "contactId"]
    );
  }

  /**
   * @return Campaign
   */
  public function campaign()
  {
    return $this->belongsTo(new Campaign());
  }
}
