<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Components\MessageProcessor\Enums;

use Cubex\Type\Enum;

/**
 * Class MessageProcessorType
 * @package Qubes\Defero\Components\MessageProcessor\Enums
 *
 * @method static RULE
 * @method static PROCESSOR
 */
class MessageProcessorType extends Enum
{
  const __default = self::RULE;

  const RULE      = "rule";
  const PROCESSOR = "processor";
}
