<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 03/10/13
 * Time: 13:57
 */

namespace Qubes\Defero\Cli\Campaign;

use Cubex\Cli\CliCommand;
use Cubex\Cli\Shell;
use Cubex\Facade\Queue;
use Cubex\Figlet\Figlet;
use Cubex\Log\Log;
use Cubex\Queue\StdQueue;
use Psr\Log\LogLevel;
use Qubes\Defero\Components\Campaign\Consumers\CampaignQueueConsumer;

class ProcessCampaignQueue extends CliCommand
{
  protected $_echoLevel = LogLevel::INFO;

  /**
   * Queue Provider Service to read messages from
   *
   * @valuerequired
   */
  public $queueService = 'campaignqueue';

  /**
   * Queue Name to pull messages from
   *
   * @valuerequired
   */
  public $queueName = 'defero_campaigns';

  /**
   * @return int
   */
  public function execute()
  {
    echo Shell::colourText(
      (new Figlet("speed"))->render("Defero"),
      Shell::COLOUR_FOREGROUND_GREEN
    );
    echo "\n";

    Log::debug("Setting Default Queue Provider to " . $this->queueService);
    Queue::setDefaultQueueProvider($this->queueService);

    Log::info("Starting to consume queue " . $this->queueName);
    Queue::consume(new StdQueue($this->queueName), new CampaignQueueConsumer());

    Log::info("Exiting Defero Processor");
  }
}
