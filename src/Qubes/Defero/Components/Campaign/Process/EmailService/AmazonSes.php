<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Process\EmailService;

use Cubex\Log\Log;
use Qubes\Defero\Transport\StdProcess;

class AmazonSes extends StdProcess implements IEmailService
{
  public function process()
  {
    Log::info("Sending message through SES");
  }
}
