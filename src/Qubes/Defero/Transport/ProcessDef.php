<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

use Cubex\Foundation\Config\ConfigGroup;
use Cubex\Foundation\Config\ConfigTrait;

class ProcessDef implements IProcessDef
{
  use ConfigTrait;

  protected $_class;
  protected $_queueName;
  protected $_queueService;

  public function __construct()
  {
    $this->_configuration = new ConfigGroup();
  }

  /**
   * Returns class responsible for processing
   *
   * @return string
   */
  public function getProcessClass()
  {
    return $this->_class;
  }

  /**
   * @param $class
   *
   * @return $this
   */
  public function setProcessClass($class)
  {
    $this->_class = $class;
    return $this;
  }

  public function setQueueName($name)
  {
    $this->_queueName = $name;
    return $this;
  }

  public function getQueueName()
  {
    return $this->_queueName;
  }

  public function setQueueService($name)
  {
    $this->_queueService = $name;
    return $this;
  }

  public function getQueueService()
  {
    return $this->_queueService;
  }
}
