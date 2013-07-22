<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

use Qubes\Defero\Transport\IMessageProcessor;

interface IRule extends IMessageProcessor
{
  /**
   * @return bool can proceed past this rule
   */
  public function canProcess();
}
