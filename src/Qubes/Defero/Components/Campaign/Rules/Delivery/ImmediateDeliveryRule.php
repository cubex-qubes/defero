<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Transport\IProcessMessage;

class ImmediateDeliveryRule implements IDeliveryRule
{
  public function __construct(IProcessMessage $message)
  {
  }

  public function getSendDelay()
  {
    return 0;
  }

  /**
   * @return bool if the message can be delivered
   */
  public function canDeliver()
  {
    return true;
  }
}
