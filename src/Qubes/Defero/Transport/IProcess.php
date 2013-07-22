<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

interface IProcess extends IMessageProcessor
{
  /**
   * @return bool
   */
  public function process();
}
