<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

use Cubex\Data\Handler\HandlerTrait;
use Cubex\Foundation\Config\ConfigTrait;

abstract class StdRule implements IRule
{
  use ConfigTrait, HandlerTrait;

  protected $_message;

  public function __construct(IProcessMessage $message = null)
  {
    $this->_message = $message;
  }
}
