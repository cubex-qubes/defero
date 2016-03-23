<?php
namespace Qubes\Defero\Components\Campaign\Process\EmailService;

use Cubex\Email\Mailer;
use Cubex\Email\Service\DatabaseMailer;
use Cubex\Facade\Email;
use Cubex\Log\Log;
use Qubes\Defero\Components\Campaign\Enums\SendType;
use Qubes\Defero\Components\Campaign\Mappers\MailerLog;
use Qubes\Defero\Components\Campaign\Mappers\MailStatistic;
use Qubes\Defero\Components\Campaign\Mappers\MailStatisticsByBrand;
use Qubes\Defero\Components\Campaign\Mappers\SentEmailLog;
use Qubes\Defero\Transport\StdProcess;

class SendEmail extends StdProcess implements IEmailProcess
{
  public function process()
  {
    $userData       = $this->_message->getArr('data');
    $campaignActive = $this->_message->getInt('campaignActive');
    $serviceName    = $this->_message->getStr(
      'emailService',
      $campaignActive ? 'email' : 'email_test'
    );

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

    Log::info("Sending to $name <$email> using $serviceName");

    $mailer = Email::getAccessor($serviceName);
    $mailer->addRecipient($email, $name);
    $mailer->setSubject($this->_message->getStr('subject'));

    switch($this->_message->getStr('sendType'))
    {
      case (SendType::PLAIN_TEXT):
        $mailer->setTextBody($this->_message->getStr('plainText') ?: null);
        break;
      case (SendType::HTML_ONLY):
        $mailer->setHtmlBody($this->_message->getStr('htmlContent') ?: null);
        break;
      case (SendType::HTML_AND_PLAIN):
        $mailer->setTextBody($this->_message->getStr('plainText') ?: null);
        $mailer->setHtmlBody($this->_message->getStr('htmlContent') ?: null);
        break;
    }

    if($mailer instanceof \Cubex\Email\Service\Mail)
    {
      $mailer->addHeader(
        "X-Defero-MID",
        uniqid(class_shortname($mailer), true)
      );
      $mailer->addHeader(
        "X-Defero-PID",
        getmypid()
      );
    }

    $campaignId = $this->_message->getStr('campaignId');
    if($mailer instanceof DatabaseMailer)
    {
      $mailer->setCampaignId($campaignId);
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

    $replyTo = $this->_message->getStr('replyTo');
    if($replyTo)
    {
      $mailer->setSender($replyTo);
    }
    else
    {
      $mailer->setSender(
        $this->_message->getStr('senderEmail'),
        $this->_message->getStr('senderName')
      );
    }

    try
    {
      $result = $mailer->send();
    }
    catch(\Exception $e)
    {
      Log::debug($e->getMessage());
      $result = false;
    }

    $hour       = time();
    $hour -= $hour % 3600;

    if(isset($userData['statskey']))
    {
      $brandStatsCf = MailStatisticsByBrand::cf();
      $column       = $hour . '|failed|' . $userData['statskey'] .
        '|' . $userData['language'];
      if($result !== false)
      {
        $column = $hour . '|' . ($campaignActive ? 'sent' : 'test');
        $column .= '|' . $userData['statskey'] .
          '|' . $userData['language'];
        $brandStatsCf->increment($campaignId, $column);
      }
      else
      {
        $brandStatsCf->increment($campaignId, $column);
      }
    }

    $statsCf = MailStatistic::cf();

    $column = $hour . '|failed-' . $userData['language'];
    if($result !== false)
    {
      $column = $hour . '|' . ($campaignActive ? 'sent' : 'test');
      $column .= '-' . $userData['language'];
      $statsCf->increment($campaignId, $column);
    }
    else
    {
      $statsCf->increment($campaignId, $column);
    }

    if(!$userData['user_id'])
    {
      $userData['user_id'] = 0;
    }
    MailerLog::addLogEntry($userData['user_id'], $campaignId);

    if($result !== false)
    {
      if($campaignActive)
      {
        SentEmailLog::addLogEntry($userData['user_id'], $campaignId);
      }
    }

    return false;
  }
}
