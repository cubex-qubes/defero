<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules;

use Cubex\Foundation\Config\ConfigTrait;
use Qubes\Defero\Transport\IProcessMessage;

class StdRule implements IRule
{
  use ConfigTrait

  protected $_message;

  public function __construct(IProcessMessage $message)
  {
    $this->_message = $message;
  }
}
