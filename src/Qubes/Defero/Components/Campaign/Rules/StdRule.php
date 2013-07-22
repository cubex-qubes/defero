<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules;

use Qubes\Defero\Transport\IProcessMessage;

class StdRule implements IRule
{
  protected $_message;

  public function __construct(IProcessMessage $message)
  {
    $this->_message = $message;
  }
}
