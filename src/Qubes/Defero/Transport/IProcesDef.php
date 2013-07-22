<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

interface IProcessDef
{
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
   * Returns class responsible for processing
   *
   * @return string
   */
  public function getProcessClass();

  public function getQueueServiceName();
}
