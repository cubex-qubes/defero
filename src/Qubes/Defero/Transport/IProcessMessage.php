<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

/**
 * Message to be sent around the queues for processing
 *
 * @package Qubes\Defero\Transport
 */
interface IProcessMessage
{
  /**
   * @return mixed
   */
  public function remainingProcesses();

  /**
   * @return IProcessDef|null
   */
  public function currentProcess();

  /**
   * @return IProcessDef[]
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
