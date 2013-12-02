<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 29/11/13
 * Time: 11:59
 */

namespace Qubes\Defero\Components\Campaign\Enums;

use Cubex\Type\Enum;

class TrackingType extends Enum
{
  const __default = self::UNKNOWN;
  const UNKNOWN   = 'unknown';
  const UPGRADE   = 'email-upgrade';
  const RENEWAL   = 'email-renewal';
  const OTHER     = 'email-other';
}
