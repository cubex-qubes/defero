<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 02/10/13
 * Time: 16:41
 */

namespace Qubes\Defero\Cli\Campaign;

use Cubex\Cli\CliCommand;
use Cubex\Mapper\Database\RecordCollection;
use Psr\Log\LogLevel;
use Qubes\Defero\Applications\Defero\Defero;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class QueueCampaign extends CliCommand
{
  protected $_echoLevel = LogLevel::INFO;

  /**
   * @required
   * @valuerequired
   */
  public $campaign;

  /**
   * @valuerequired
   * @datatype int
   */
  public $startId;
  /**
   * @valuerequired
   * @datatype int
   */
  public $endId;

  public function execute()
  {
    $startedAt = time();
    $startedAt -= $startedAt % 60;
    $campaign = new Campaign($this->campaign);

    Defero::pushCampaign(
      $campaign->id(), $startedAt, $this->startId, $this->endId
    );
  }
}
