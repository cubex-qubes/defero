<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Process\DataCollection;

use Qubes\Defero\Transport\IProcess;

interface IDataCollectionProcess extends IProcess
{
  /**
   * @return DataCollectionAttribute[]
   */
  public function getAttributes();
}
