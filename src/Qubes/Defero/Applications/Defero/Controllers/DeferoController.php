<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Facade\Queue;
use Cubex\Queue\Provider\Database\DatabaseQueue;
use Qubes\Defero\Applications\Defero\Views\Index;

class DeferoController extends BaseDeferoController
{
  public function renderIndex()
  {
    $index = new Index();
    Queue::setDefaultQueueProvider('campaignqueue');

    $queue = Queue::getAccessor();
    if($queue instanceof DatabaseQueue)
    {
      $index->setQueueSize($queue->queueSize());
      $index->setActiveQueueSize($queue->queueSize(1));
    }
    return $index;
  }

  public function getRoutes()
  {
    return ["(.*)" => "index",];
  }
}
