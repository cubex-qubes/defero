<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Components\Campaign\Process\EmailService;

use Cubex\Facade\Email;
use Cubex\Foundation\Config\Config;
use Cubex\Foundation\Container;
use Cubex\Log\Log;
use Cubex\ServiceManager\ServiceConfig;
use Qubes\Defero\Transport\StdProcess;

class Smtp extends StdProcess implements IEmailService
{
  public function process()
  {
    Log::info("Sending message through configured SMTP relay");

    $name  = $this->_message->getStr("name");
    $email = $this->_message->getStr("email");

    Log::debug("Sending to $name <$email>");

    $smtpConfig = Container::config()->get("defero", new Config());

    $mailer = Email::getAccessor();

    $config = new Config(
      [
        "smtp.host"     => $smtpConfig->getStr("smtp.host"),
        "smtp.port"     => $smtpConfig->getInt("smtp.port"),
        "smtp.security" => $smtpConfig->getStr("smtp.security"),
        "smtp.username" => $smtpConfig->getStr("smtp.username"),
        "smtp.password" => $smtpConfig->getStr("smtp.password"),
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
