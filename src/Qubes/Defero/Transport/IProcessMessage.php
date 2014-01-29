<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

use Cubex\Data\Handler\IDataHandler;

/**
 * Message to be sent around the queues for processing
 *
 * @package Qubes\Defero\Transport
 */
interface IProcessMessage extends IDataHandler
{
  /**
   * @return IProcessDefinition[]
   */
  public function getProcessQueue();
}
