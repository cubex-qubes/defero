<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

use Cubex\Foundation\Config\IConfigurable;

interface IMessageProcessor extends IConfigurable
{
  public function __construct(IProcessMessage $message);
}
