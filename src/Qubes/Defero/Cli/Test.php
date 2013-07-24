<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Cli;

use Cubex\Cli\CliCommand;
use Cubex\Foundation\Config\Config;
use Cubex\Foundation\Config\ConfigGroup;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use
Qubes\Defero\Components\Campaign\Process\DataCollection\MapperDataCollection;
use Qubes\Defero\Transport\ProcessMessage;

class Test extends CliCommand
{
  /**
   * @return int
   */
  public function execute()
  {
    $collection = new MapperDataCollection(new ProcessMessage());

    $config = new Config();
    $config->setData("mapper_class", get_class(new Campaign()));

    $configGroup = new ConfigGroup();
    $configGroup->addConfig("process", $config);

    $collection->configure($configGroup);

    echo json_pretty($collection->getAttributes());
  }
}
