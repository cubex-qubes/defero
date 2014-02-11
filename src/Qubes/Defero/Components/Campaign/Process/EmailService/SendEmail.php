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
    $userData       = $this->_message->getArr('data');
    $campaignActive = $this->_message->getInt('campaignActive');
    $accessor       = $campaignActive ? 'email' : 'send_database';

    $name = null;
    if(isset($userData['firstname']))
    {
      $name = $userData['firstname'];
      if(isset($userData['lastname']))
      {
        $name .= ' ' . $userData['lastname'];
      }
    }
    $email = $userData['email'];

    Log::info("Sending to $name <$email>");

    $mailer = Email::getAccessor($accessor);

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

    $returnPath = $this->_message->getStr('returnPath');
    if($returnPath)
    {
      $mailer->setSender($returnPath);
    }

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
    $hour       = time();
    $hour -= $hour % 3600;
    $statsCf = MailStatistic::cf();

    $column = $hour . '|failed-' . $userData['language'];
    if($result !== false)
    {
      $column = $hour . '|' . ($campaignActive ? 'sent' : 'test');
      $column .= '-' . $userData['language'];
      $statsCf->increment(
        $campaignId,
        $column
      );
    }
    else
    {
      $statsCf->increment($campaignId, $column);
    }
    return false;
  }
}
