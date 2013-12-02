<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 14/10/13
 * Time: 15:29
 */

namespace Qubes\Defero\Components\Campaign\Process\EmailService;

use Qubes\Defero\Transport\StdProcess;

class SimulatedSend extends StdProcess implements IEmailProcess
{
  public function process()
  {
    echo json_pretty($this->_message->getData());
    return true;
  }
}
