<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Components\Campaign\Rules\StdRule;

class ImmediateDeliveryRule extends StdRule implements IDeliveryRule
{
  public function getSendDelay()
  {
    return 0;
  }

  /**
   * @return bool if the message can be delivered
   */
  public function canProcess()
  {
    return true;
  }
}
