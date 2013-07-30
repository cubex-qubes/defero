<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Process\EmailService;

use Cubex\Facade\Email;
use Cubex\Foundation\Config\Config;
use Cubex\Log\Log;
use Cubex\ServiceManager\ServiceConfig;
use Qubes\Defero\Transport\StdProcess;

class AmazonSes extends StdProcess implements IEmailService
{
  public function process()
  {
    Log::info("Sending message through SES");

    $name  = $this->_message->getStr("name");
    $email = $this->_message->getStr("email");

    Log::debug("Sending to $name <$email>");

    $mailer = Email::getAccessor();

    $config = new Config(
      [
        "smtp.host"     => "smtp.gmail.com",
        "smtp.port"     => "465",
        "smtp.security" => "ssl",
        "smtp.username" => "",
        "smtp.password" => "",
        "transport"     => "smtp",
      ]
    );

    $mailer->configure((new ServiceConfig())->fromConfig($config));

    $mailer->addRecipient($email, $name);
    $mailer->setSubject("Test");
    $mailer->setBody("Test");
    $mailer->setFrom($email, $name);
    $mailer->setSender($email, $name);

    return $mailer->send();
  }
}
