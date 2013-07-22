<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

class ProcessMessage implements IProcessMessage
{
  protected $_currentStep = 0;
  protected $_processQueue = [];
  protected $_configuration = [];

  public function setConfiguration($config)
  {
    $this->_configuration = $config;
    return $this;
  }

  public function addConfigItem($name, $value = null)
  {
    $this->_configuration[$name] = $value;
    return $this;
  }

  /**
   * Keyed array of configuration items
   *
   * @return array
   */
  public function getConfiguration()
  {
    return $this->_configuration;
  }

  /**
   * Retrieve a single configuration item
   *
   * @param string $key     configuration key for item
   * @param null   $default Default value if config item not available
   *
   * @return mixed config value or default
   */
  public function getConfigValue($key, $default = null)
  {
    if(isset($this->_configuration[$key]))
    {
      return $this->_configuration[$key];
    }
    else
    {
      return $default;
    }
  }

  public function setProcessQueue($processDefinitions = [])
  {
    foreach($processDefinitions as $processDefinition)
    {
      $this->addProcess($processDefinition);
    }
    return $this;
  }

  public function addProcess(IProcessDef $process)
  {
    $this->_processQueue[] = $process;
    return $this;
  }

  public function setStep($step = 0)
  {
    $this->_currentStep = $step;
    return $this;
  }

  public function incrementStep()
  {
    $this->_currentStep++;
    return $this;
  }

  public function remainingProcesses()
  {
    if(!$this->isComplete())
    {
      return array_slice($this->_processQueue, $this->_currentStep);
    }
    return null;
  }

  public function currentProcess()
  {
    if(isset($this->_processQueue[$this->_currentStep]))
    {
      return $this->_processQueue[$this->_currentStep];
    }
    return null;
  }

  public function getProcessQueue()
  {
    return $this->_processQueue;
  }

  public function getCurrentStep()
  {
    return $this->_currentStep;
  }

  public function isComplete()
  {
    return $this->_currentStep >= count($this->_processQueue);
  }
}
