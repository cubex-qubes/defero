<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

use Cubex\Data\Handler\HandlerTrait;

class ProcessMessage implements IProcessMessage
{
  use HandlerTrait

  protected $_currentStep = 0;
  protected $_processQueue = [];
  protected $_dataAttributes = [];

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
