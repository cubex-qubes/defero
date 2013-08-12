<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Components\MessageProcessor\Mappers;

use Cubex\Data\Validator\Validator;
use Cubex\Mapper\Database\RecordMapper;
use Qubes\Defero\Components\MessageProcessor\Enums\MessageProcessorType;

class MessageProcessor extends RecordMapper
{
  public $name;
  /**
   * @datatype TEXT
   */
  public $description;
  /**
   * @datatype TEXT
   */
  public $config;
  /**
   * @enumclass \Qubes\Defero\Components\MessageProcessor\Enums\MessageProcessorType
   */
  public $type;

  protected function _configure()
  {
    $this->_dbServiceName = "defero_db";

    $this->_attribute('type')
      ->addValidator(Validator::VALIDATE_ENUM, [new MessageProcessorType()])
      ->setRequired(true);
  }

  public function types()
  {
    return new MessageProcessorType();
  }
}
