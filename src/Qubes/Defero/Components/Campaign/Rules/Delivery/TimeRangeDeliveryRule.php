<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Transport\IProcessMessage;

class TimeRangeDeliveryRule implements IDeliveryRule
{
  public function __construct(IProcessMessage $message)
  {
  }

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
  public function canDeliver()
  {
    return true;
  }
}
