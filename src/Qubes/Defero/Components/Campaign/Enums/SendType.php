<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Enums;

use Cubex\Type\Enum;

class SendType extends Enum
{
  const __default      = 'plain';
  const PLAIN_TEXT     = 'plain';
  const HTML_ONLY      = 'html';
  const HTML_AND_PLAIN = 'both';
}
