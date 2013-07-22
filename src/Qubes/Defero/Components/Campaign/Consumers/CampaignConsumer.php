<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Consumers;

use Cubex\Foundation\Config\IConfigurable;
use Cubex\Log\Log;
use Cubex\Queue\IQueue;
use Cubex\Queue\IQueueConsumer;
use Cubex\Queue\StdQueue;
use Qubes\Defero\Components\Campaign\Rules\Delivery\IDeliveryRule;
use Qubes\Defero\Components\Campaign\Rules\Filter\IFilterRule;
use Qubes\Defero\Components\Campaign\Rules\IRule;
use Qubes\Defero\Transport\IProcess;
use Qubes\Defero\Transport\IProcessDef;
use Qubes\Defero\Transport\IProcessMessage;

class CampaignConsumer implements IQueueConsumer
{
  protected $_queueDelay;

  /**
   * @param $queue
   * @param $message
   *
   * @return bool
   */
  public function process(IQueue $queue, $message)
  {
    if(is_scalar($message))
    {
      $message = unserialize($message);
    }
    $this->_queueDelay = 0;
    if($message instanceof IProcessMessage)
    {
      try
      {
        $pass = $this->runProcess($message, $message->currentProcess());
      }
      catch(\Exception $e)
      {
        Log::error($e->getMessage());
        $pass = false;
      }

      //If the process fails, the message should be dropped
      if($pass)
      {
        //Increment step to change currentProcess return
        $message->setStep($message->getCurrentStep() + 1);
        if(!$message->isComplete())
        {
          Log::debug("ReQueueing Message");
          //TODO: Add Queue Delays with $this->_queueDelay;
          \Queue::getAccessor($message->currentProcess()->getQueueService())
          ->push(
            new StdQueue($message->currentProcess()->getQueueName()),
            serialize($message)
          );
        }
        else
        {
          Log::debug("Message Complete");
        }
      }
    }
    return true;
  }

  public function runProcess(IProcessMessage $message, IProcessDef $process)
  {
    $class = $process->getProcessClass();
    if(class_exists($class))
    {
      $ruleClass = 'Qubes\Defero\Transport\IMessageProcessor';
      if(in_array($ruleClass, class_implements($class)))
      {
        $proc = new $class($message);
      }
      else
      {
        $proc = new $class();
      }

      if($proc instanceof IConfigurable)
      {
        $proc->configure($process->getConfig());
      }

      if($proc instanceof IRule)
      {
        if($proc->canProcess())
        {
          if($proc instanceof IDeliveryRule)
          {
            $this->_queueDelay = (int)$proc->getSendDelay();
            Log::debug("Setting queue delay to " . $this->_queueDelay);
          }
          return true;
        }
        return false;
      }
      else if($proc instanceof IProcess)
      {
        Log::debug("Processing Process");
        return $proc->process();
      }
    }
    return false;
  }

  /**
   * Seconds to wait before re-attempting, false to exit
   *
   * @param int $waits amount of times script has waited
   *
   * @return mixed
   */
  public function waitTime($waits = 0)
  {
    return false;
  }

  public function shutdown()
  {
    return true;
  }
}
