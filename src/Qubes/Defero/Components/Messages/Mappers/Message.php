<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Messages\Mappers;

use Cubex\Data\Validator\Validator;
use Cubex\Helpers\Inflection;
use Cubex\Mapper\Database\I18n\I18nRecordMapper;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class Message extends I18nRecordMapper
{
  public $campaignId;

  /**
   * @datatype tinyint
   * @default  1
   */
  public $active;

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
    $this->_attribute('active')->addValidator(Validator::VALIDATE_BOOL);
  }

  /**
   * @return Campaign
   */
  public function campaign()
  {
    return $this->belongsTo(new Campaign());
  }

  public function contacts()
  {
    return [0 => '- Campaign Default -'] + Contact::collection()->getKeyPair(
      'id', 'reference'
    );
  }

  public function getTableName($plural = true)
  {
    $tbl = 'defero_message';
    if($plural)
    {
      return Inflection::pluralise($tbl);
    }
    return $tbl;
  }

  public function findVariables()
  {
    $variables = [];
    foreach([$this->subject, $this->plainText, $this->htmlContent] as $text)
    {
      if(preg_match_all('/{[\!\?]([^{}|]*)/', $text, $matches))
      {
        $variables = array_merge($variables, $matches[1]);
      }
    }
    foreach($variables as $k => $var)
    {
      if(strncasecmp($var, 'link=', 5) === 0)
      {
        unset($variables[$k]);
      }
    }
    return $variables;
  }
}
