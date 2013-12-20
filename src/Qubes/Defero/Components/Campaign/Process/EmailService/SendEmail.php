<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Components\Campaign\Process\EmailService;

use Cubex\Facade\Email;
use Cubex\Log\Log;
use Qubes\Defero\Components\Campaign\Enums\SendType;
use Qubes\Defero\Components\Campaign\Mappers\MailStatistic;
use Qubes\Defero\Transport\StdProcess;

class SendEmail extends StdProcess implements IEmailProcess
{
  public function process()
  {
    $userData = $this->_message->getArr('data');

    $name  = trim($userData['firstname'] . ' ' . $userData['lastname']);
    $email = $userData['email'];

    Log::info("Sending to $name <$email>");

    $mailer = Email::getAccessor('email');

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

    try
    {
      $result = $mailer->send();
    }
    catch(\Exception $e)
    {
      $result = false;
      Log::error($e->getMessage());
      Email::getServiceManager()->destroy('email');
    }

    $campaignId = $this->_message->getStr('campaignId');
    $hour = time();
    $hour -= $hour % 3600;
    $statsCf = MailStatistic::cf();

    if($result !== false)
    {
      $statsCf->increment($campaignId, $hour . '|sent');
    }
    else
    {
      $statsCf->increment($campaignId, $hour . '|failed');
    }
    return false;
  }
}
