<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

use Cubex\Data\Handler\HandlerTrait;

class ProcessMessage implements IProcessMessage
{
  use HandlerTrait;

  protected $_processQueue = [];

  public function setProcessQueue($processDefinitions = [])
  {
    foreach($processDefinitions as $processDefinition)
    {
      $this->addProcess($processDefinition);
    }
    return $this;
  }

  public function addProcess(IProcessDefinition $process)
  {
    $this->_processQueue[] = $process;
    return $this;
  }

  public function getProcessQueue()
  {
    return $this->_processQueue;
  }
}
