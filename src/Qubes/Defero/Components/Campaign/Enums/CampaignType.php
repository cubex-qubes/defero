<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Enums;

use Cubex\Type\Enum;

class CampaignType extends Enum
{
  const __default = 'action';
  //Manual trigger e.g. Renewal
  const ACTION = 'action';
  //CF of Type | Date - Key | Microtime - CN, UID - Val
  const SOURCE_EVENT = 'source';
  //Query loader
  const QUERY = 'query';
  //Mass Group Email - One time campaign
  const MASS = 'massmail';
}
