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

  /**
   * Keyed array of configuration items
   *
   * @return array
   */
  public function getConfiguration();

  /**
   * Retrieve a single configuration item
   *
   * @param string $key     configuration key for item
   * @param null   $default Default value if config item not available
   *
   * @return mixed config value or default
   */
  public function getConfigValue($key, $default = null);

  /**
   * Keyed array of attributes
   *
   * @return array
   */
  public function getAttributes();

  /**
   * Retrieve a single attribute
   *
   * @param string $name    attribute key
   * @param null   $default Default value if attribute is not available
   *
   * @return mixed attribute or default
   */
  public function getAttribute($name, $default = null);
}
