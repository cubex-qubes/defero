<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Consumers;

use Cubex\Queue\IBatchQueueConsumer;
use Cubex\Queue\IQueue;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
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
      $cid      = $message->getInt('campaignId');
      $started  = $message->getInt('startedAt');
      $lastSent = $message->getInt('lastSent') ? : 0;
      $startId  = $message->getInt('startId') ? : null;
      $endId    = $message->getInt('endId') ? : 0;
      $campaign = new Campaign($cid);
      $campaign->getDataSource()->process(
        $cid, $started, $lastSent, $startId, $endId
      );

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
