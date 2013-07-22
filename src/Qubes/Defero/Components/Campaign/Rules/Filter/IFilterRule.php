<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Filter;

use Qubes\Defero\Components\Campaign\Rules\IRule;

interface IFilterRule extends IRule
{
  public function isFilterValid();
}
