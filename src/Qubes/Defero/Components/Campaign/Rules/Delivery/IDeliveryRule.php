<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules\Delivery;

use Qubes\Defero\Transport\IProcessMessage;

interface IDeliveryRule
{
  public function __construct(IProcessMessage $message);

  public function getSendDelay();

  /**
   * @return bool if the message can be delivered
   */
  public function canDeliver();
}
