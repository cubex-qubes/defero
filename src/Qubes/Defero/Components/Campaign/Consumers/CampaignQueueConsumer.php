<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Consumers;

use Cubex\Foundation\Config\IConfigurable;
use Cubex\Log\Log;
use Cubex\Queue\IBatchQueueConsumer;
use Cubex\Queue\IQueue;
use Cubex\Queue\StdQueue;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Campaign\Rules\Delivery\IDeliveryRule;
use Qubes\Defero\Transport\IRule;
use Qubes\Defero\Transport\IProcess;
use Qubes\Defero\Transport\IProcessDefinition;
use Qubes\Defero\Transport\IProcessMessage;

class CampaignQueueConsumer implements IBatchQueueConsumer
{
  /**
   * @var IProcessMessage
   */
  protected $_batch;
  protected $_queueDelays = [];

  public function process(IQueue $queue, $message, $taskID = null)
  {
    if(is_scalar($message))
    {
      $message = unserialize($message);
    }
    if($message instanceof IProcessMessage)
    {
      $this->_batch[$taskID] = $message;
    }
    return true;
  }

  public function runBatch()
  {
    $results = [];
    /**
     * @var $message IProcessMessage
     */
    foreach($this->_batch as $taskId => $message)
    {
      $cid      = $message->getInt('campaign_id');
      $started  = $message->getInt('started_at');
      $campaign = new Campaign($cid);
      $campaign->process($started);

      $results[$taskId] = true;
    }
    $this->_batch = [];
    return $results;
  }

  public function getBatchSize()
  {
    return 1;
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

  /**
   * Time in seconds to treat queue locks as stale, false to never unlock
   *
   * @return bool|int
   */
  public function lockReleaseTime()
  {
    return 3600;
  }

  public function shutdown()
  {
    return true;
  }
}
