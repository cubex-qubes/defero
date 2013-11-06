<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 23/09/13
 * Time: 13:20
 */

namespace Qubes\Defero\Components\Campaign\Rules\Filter;

use Cubex\Type\Enum;

class AttributeFilterCheckTypes extends Enum
{
  const __default = self::MATCH_EQUAL;

  const MATCH_EQUAL    = 'eq';
  const MATCH_NOTEQUAL = 'neq';
  const MATCH_START    = 'starts';
  const MATCH_END      = 'ends';
  const MATCH_CONTAINS = 'contains';
}
