<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

use Cubex\Foundation\Config\IConfigurable;

interface IProcess extends IConfigurable
{
  public function __construct(IProcessMessage $message);

  /**
   * @return bool
   */
  public function process();
}
