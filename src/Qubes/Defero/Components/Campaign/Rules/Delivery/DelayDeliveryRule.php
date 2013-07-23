<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Transport\StdRule;

class DelayDeliveryRule extends StdRule implements IDeliveryRule
{
  public function getSendDelay()
  {
    return $this->config("process")->getInt("delay", 0);
  }

  /**
   * @return bool if the message can be delivered
   */
  public function canProcess()
  {
    return true;
  }
}
