<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules;

use Qubes\Defero\Transport\IProcessMessage;

interface IRule
{
  public function __construct(IProcessMessage $message);
}
