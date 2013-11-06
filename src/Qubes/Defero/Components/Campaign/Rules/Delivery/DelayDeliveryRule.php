<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Transport\StdRule;

class DelayDeliveryRule extends StdRule implements IDeliveryRule
{
  /**
   * delay in seconds
   */
  public $delay = 0;

  public function getSendDelay()
  {
    return $this->config("process")->getInt("delay", $this->delay);
  }

  /**
   * @return bool if the message can be delivered
   */
  public function canProcess()
  {
    return true;
  }
}
