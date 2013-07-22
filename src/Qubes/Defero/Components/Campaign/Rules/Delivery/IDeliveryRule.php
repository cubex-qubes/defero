<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Components\Campaign\Rules\IRule;

interface IDeliveryRule extends IRule
{
  public function getSendDelay();

  /**
   * @return bool if the message can be delivered
   */
  public function canDeliver();
}
