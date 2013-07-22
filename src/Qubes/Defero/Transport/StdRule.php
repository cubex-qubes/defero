<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

use Cubex\Foundation\Config\ConfigTrait;
use Qubes\Defero\Transport\IProcessMessage;
use Qubes\Defero\Transport\IRule;

abstract class StdRule implements IRule
{
  use ConfigTrait;

  protected $_message;

  public function __construct(IProcessMessage $message)
  {
    $this->_message = $message;
  }
}
