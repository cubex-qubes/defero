<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Cli\Campaign;

use Cubex\Cli\CliArgument;
use Cubex\Cli\CliCommand;
use Cubex\Data\Validator\Validator;
use Cubex\Foundation\Config\Config;
use Cubex\Foundation\Config\ConfigGroup;
use Cubex\Queue\StdQueue;
use Qubes\Defero\Components\Campaign\Rules\Filter\AttributeFilter;
use Qubes\Defero\Transport\ProcessDefinition;
use Qubes\Defero\Transport\ProcessMessage;

class SampleMessage extends CliCommand
{
  public $firstName;
  public $lastName;
  public $email;

  protected function _configure()
  {
    $firstName              = $this->_getArgObjByName("firstName");
    $firstName->shortName   = "f";
    $firstName->description = "First Name";
    $firstName->valueOption = CliArgument::VALUE_REQUIRED;
    $firstName->required    = true;

    $lastName              = $this->_getArgObjByName("lastName");
    $lastName->shortName   = "l";
    $lastName->description = "Last Name";
    $lastName->valueOption = CliArgument::VALUE_REQUIRED;
    $lastName->required    = true;

    $email              = $this->_getArgObjByName("email");
    $email->shortName   = "e";
    $email->description = "Email Address";
    $email->valueOption = CliArgument::VALUE_REQUIRED;
    $email->required    = true;
    $email->addValidator(Validator::VALIDATE_EMAIL);
  }

  /**
   * @return int
   */
  public function execute()
  {
    // Instantiate and set data for the actual message
    $message = new ProcessMessage();
    $message->setData("firstName", $this->firstName);
    $message->setData("lastName", $this->lastName);
    $message->setData("name", "{$this->firstName} {$this->lastName}");
    $message->setData("email", $this->email);

    // Build a config object for use in a Rule. This needs to be wrapped in a
    // config group and added to a process definition as well as the full class
    // name for the rule and some default requirements.
    // Once fully setup we add the process definition to the message processor.
    $config = new Config();
    $config->setData("attribute_name", "firstName");
    $config->setData("attribute_check", $this->firstName);
    $config->setData("attribute_check_type", AttributeFilter::MATCH_EQUAL);

    $configGroup = new ConfigGroup();
    $configGroup->addConfig("process", $config);
    $process = new ProcessDefinition();
    $process->setProcessClass(
      'Qubes\Defero\Components\Campaign\Rules\Filter\AttributeFilter'
    );
    $process->setQueueName("defero");
    $process->setQueueService("queue");
    $process->configure($configGroup);
    $message->addProcess($process);

    // Same as above. Running the same check on a different variable.
    $config = new Config();
    $config->setData("attribute_name", "lastName");
    $config->setData("attribute_check", $this->lastName);
    $config->setData("attribute_check_type", AttributeFilter::MATCH_EQUAL);

    $configGroup = new ConfigGroup();
    $configGroup->addConfig("process", $config);
    $process = new ProcessDefinition();
    $process->setProcessClass(
      'Qubes\Defero\Components\Campaign\Rules\Filter\AttributeFilter'
    );
    $process->setQueueName("defero");
    $process->setQueueService("queue");
    $process->configure($configGroup);
    $message->addProcess($process);

    // Here we add a delivery rule,
    /*$process = new ProcessDefinition();
    $process->setProcessClass(
      'Qubes\Defero\Components\Campaign\Rules\Delivery\FailDeliveryRule'
    );*/
    $process->setProcessClass(
      'Qubes\Defero\Components\Campaign\Rules\Delivery\DelayDeliveryRule'
    );
    $config = new Config();
    $config->setData("delay", 60);
    $configGroup = new ConfigGroup();
    $configGroup->addConfig("process", $config);
    $process->configure($configGroup);

    $process->setQueueName("defero");
    $process->setQueueService("queue");
    $message->addProcess($process);

    // Final process is used to send the message
    $process = new ProcessDefinition();
    $process->setQueueName("defero");
    $process->setQueueService("queue");
    $process->setProcessClass(
      'Qubes\Defero\Components\Campaign\Process\EmailService\AmazonSes'
    );
    $message->addProcess($process);

    \Queue::push(new StdQueue("defero"), serialize($message));
  }
}
