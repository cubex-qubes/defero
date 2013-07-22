<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Cli\Campaign;

use Cubex\Cli\CliCommand;
use Cubex\Foundation\Config\Config;
use Cubex\Foundation\Config\ConfigGroup;
use Cubex\Queue\StdQueue;
use Qubes\Defero\Components\Campaign\Rules\Filter\AttributeFilter;
use Qubes\Defero\Transport\ProcessDef;
use Qubes\Defero\Transport\ProcessMessage;

class SampleMessage extends CliCommand
{
  /**
   * @return int
   */
  public function execute()
  {
    $message = new ProcessMessage();
    $message->setData("firstName", "Brookes");
    $message->setData("lastName", "Bryan");
    $message->setData("name", "Brooke Bryan");
    $message->setData("email", "brooke@bajb.net");

    $config = new Config();
    $config->setData("attribute_name", "firstName");
    $config->setData("attribute_check", "Brooke");
    $config->setData("attribute_check_type", AttributeFilter::MATCH_EQUAL);

    $configGroup = new ConfigGroup();
    $configGroup->addConfig("process", $config);
    $process = new ProcessDef();
    $process->setProcessClass(
      'Qubes\Defero\Components\Campaign\Rules\Filter\AttributeFilter'
    );
    $process->setQueueName("defero");
    $process->setQueueService("queue");
    $process->configure($configGroup);
    $message->addProcess($process);

    $config = new Config();
    $config->setData("attribute_name", "lastName");
    $config->setData("attribute_check", "Bryan");
    $config->setData("attribute_check_type", AttributeFilter::MATCH_EQUAL);

    $configGroup = new ConfigGroup();
    $configGroup->addConfig("process", $config);
    $process = new ProcessDef();
    $process->setProcessClass(
      'Qubes\Defero\Components\Campaign\Rules\Filter\AttributeFilter'
    );
    $process->setQueueName("defero");
    $process->setQueueService("queue");
    $process->configure($configGroup);
    $message->addProcess($process);

    $process = new ProcessDef();
    $process->setProcessClass(
      'Qubes\Defero\Components\Campaign\Rules\Delivery\FailDeliveryRule'
    );
    $process->setProcessClass(
      'Qubes\Defero\Components\Campaign\Rules\Delivery\ImmediateDeliveryRule'
    );
    $process->setQueueName("defero");
    $process->setQueueService("queue");
    $message->addProcess($process);

    $process = new ProcessDef();
    $process->setQueueName("defero");
    $process->setQueueService("queue");
    $process->setProcessClass(
      'Qubes\Defero\Components\Campaign\Process\EmailService\AmazonSes'
    );
    $message->addProcess($process);

    \Queue::push(new StdQueue("defero"), serialize($message));
  }
}
