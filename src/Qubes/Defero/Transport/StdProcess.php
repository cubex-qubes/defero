<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

use Cubex\Foundation\Config\ConfigTrait;

abstract class StdProcess implements IProcess
{
  use ConfigTrait;

  protected $_message;

  public function __construct(IProcessMessage $message)
  {
    $this->_message = $message;
  }
}
