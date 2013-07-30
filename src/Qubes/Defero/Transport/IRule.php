<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

interface IRule extends IMessageProcessor
{
  /**
   * @return bool can proceed past this rule
   */
  public function canProcess();
}
