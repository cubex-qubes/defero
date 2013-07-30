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
   * @return mixed
   */
  public function remainingProcesses();

  /**
   * @return IProcessDefinition|null
   */
  public function currentProcess();

  /**
   * @return IProcessDefinition[]
   */
  public function getProcessQueue();

  /**
   * @param int $step step to jump to (Zero Based)
   *
   * @return mixed
   */
  public function setStep($step = 0);

  /**
   * @return self
   */
  public function incrementStep();

  /**
   * @return int
   */
  public function getCurrentStep();

  /**
   * @return bool
   */
  public function isComplete();
}
