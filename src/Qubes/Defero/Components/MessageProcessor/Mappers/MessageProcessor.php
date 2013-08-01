<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Components\MessageProcessor\Mappers;

use Cubex\Mapper\Database\RecordMapper;

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
}
