<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Transport\StdRule;

class SetTimeDeliveryRule extends StdRule implements IDeliveryRule
{
  public $sendTime;

  public function setSendTime($timestamp)
  {
    $this->sendTime = (int)$timestamp;
    return $this;
  }

  public function getSendDelay()
  {
    if((int)$this->config("process")->getInt('sendTime') < time())
    {
      return 0;
    }
    else
    {
      return (int)$this->config("process")->getInt('sendTime') - time();
    }
  }

  /**
   * @return bool if the message can be delivered
   */
  public function canProcess()
  {
    return true;
  }
}
