<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Cli\Campaign;

use Cubex\Cli\CliCommand;
use Qubes\Defero\Applications\Defero\Defero;

class Test extends CliCommand
{
  /**
   * @required
   * @valuerequired
   * @datatype int
   */
  public $campaignId;

  public function execute()
  {
    $message = [
      'firstname' => 'tom',
      'lastname'  => 'kay',
      'name'      => 'tom kay',
      'email'     => 'tom.kay@justdevelop.it',
      'domain_id' => '3',
      'user_id'   => '22492349',
      'currency'  => '£',
    ];

    Defero::pushMessage($this->campaignId, $message);
  }
}
