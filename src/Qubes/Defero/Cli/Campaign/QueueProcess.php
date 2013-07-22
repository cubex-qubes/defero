<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Cli\Campaign;

use Cubex\Cli\CliCommand;
use Cubex\Cli\Shell;
use Cubex\Facade\Queue;
use Cubex\Figlet\Figlet;
use Cubex\Log\Log;
use Cubex\Queue\StdQueue;
use Qubes\Defero\Components\Campaign\Consumers\CampaignConsumer;

class QueueProcess extends CliCommand
{
  /**
   * Queue Provider Service to read messages from
   *
   * @valuerequired
   */
  public $queueService = 'queue';

  /**
   * Queue Name to pull messages from
   *
   * @valuerequired
   */
  public $queueName = 'defero';

  /**
   * @return int
   */
  public function execute()
  {
    echo Shell::colourText(
      (new Figlet("speed"))->render("Defero Processor"),
      Shell::COLOUR_FOREGROUND_GREEN
    );
    echo "\n";

    Log::debug("Setting Default Queue Provider to " . $this->queueService);
    Queue::setDefaultQueueProvider($this->queueService);

    Log::info("Starting to consume queue " . $this->queueName);
    Queue::consume(new StdQueue($this->queueName), new CampaignConsumer());

    Log::info("Exiting Defero Processor");
  }
}
