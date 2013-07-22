<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Components\Campaign\Rules\IRule;

interface IDeliveryRule extends IRule
{
  public function getSendDelay();
}
