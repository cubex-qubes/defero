<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Components\Campaign\Process\EmailService;

use Cubex\Facade\Email;
use Cubex\Foundation\Config\Config;
use Cubex\Foundation\Container;
use Cubex\Log\Debug;
use Cubex\Log\Log;
use Cubex\ServiceManager\ServiceConfig;
use Qubes\Defero\Components\Campaign\Enums\SendType;
use Qubes\Defero\Transport\StdProcess;

class SendEmail extends StdProcess implements IEmailProcess
{
  public function process()
  {
    Log::info("Sending message through configured SMTP relay");

    $userData = $this->_message->getArr('data');

    $name  = trim($userData['firstname'] . ' ' . $userData['lastname']);
    $email = $userData['email'];

    Log::debug("Sending to $name <$email>");

    $mailer = Email::getAccessor();

    $mailer->addRecipient($email, $name);
    $mailer->setSubject($this->_message->getStr('subject'));

    switch($this->_message->getStr('sendType'))
    {
      case (SendType::PLAIN_TEXT):
        $mailer->setTextBody($this->_message->getStr('plainText'));
        break;
      case (SendType::HTML_ONLY):
        $mailer->setHtmlBody($this->_message->getStr('htmlContent'));
        break;
      case (SendType::HTML_AND_PLAIN):
        $mailer->setTextBody($this->_message->getStr('plainText'));
        $mailer->setHtmlBody($this->_message->getStr('htmlContent'));
        break;
    }

    $mailer->setFrom(
      $this->_message->getStr('senderEmail'),
      $this->_message->getStr('senderName')
    );

    return $mailer->send();
  }
}
