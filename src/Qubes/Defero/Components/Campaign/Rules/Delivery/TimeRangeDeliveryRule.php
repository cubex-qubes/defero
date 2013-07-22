<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Components\Campaign\Rules\StdRule;

class TimeRangeDeliveryRule extends StdRule implements IDeliveryRule
{
  public function getOffsetRange()
  {
    return [0, 3600];
  }

  public function getSendDelay()
  {
    return call_user_func_array("rand", $this->getOffsetRange());
  }

  /**
   * @return bool if the message can be delivered
   */
  public function canProcess()
  {
    return true;
  }
}
