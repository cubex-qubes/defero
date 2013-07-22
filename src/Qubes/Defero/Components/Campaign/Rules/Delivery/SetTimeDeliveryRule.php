<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Components\Campaign\Rules\StdRule;

class SetTimeDeliveryRule extends StdRule implements IDeliveryRule
{
  protected $_sendTime;

  public function setSendTime($timestamp)
  {
    $this->_sendTime = (int)$timestamp;
    return $this;
  }

  public function getSendDelay()
  {
    if((int)$this->_sendTime < time())
    {
      return 0;
    }
    else
    {
      return (int)$this->_sendTime - time();
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
