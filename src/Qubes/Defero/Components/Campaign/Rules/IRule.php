<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Rules;

use Qubes\Defero\Transport\IMessageProcessor;

interface IRule extends IMessageProcessor
{
  /**
   * @return bool can proceed past this rule
   */
  public function canProcess();
}
