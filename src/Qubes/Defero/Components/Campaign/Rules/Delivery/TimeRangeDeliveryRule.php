<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Transport\StdRule;

class TimeRangeDeliveryRule extends StdRule implements IDeliveryRule
{
  public $timeRangeMin = 0;
  public $timeRangeMax = 3600;

  public function getOffsetRange()
  {
    return [$this->config("process")->getInt('timeRangeMin', $this->timeRangeMin),
            $this->config("process")->getInt('timeRangeMax', $this->timeRangeMax)];
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
