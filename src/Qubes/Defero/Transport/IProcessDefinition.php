<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

use Cubex\Foundation\Config\IConfigurable;

interface IProcessDefinition extends IConfigurable
{
  /**
   * Returns class responsible for processing
   *
   * @return string
   */
  public function getProcessClass();

  public function getQueueService();

  public function getQueueName();
}
