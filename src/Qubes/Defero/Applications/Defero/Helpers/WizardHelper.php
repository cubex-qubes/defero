<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Helpers;

use Cubex\Core\Http\Request;

class WizardHelper
{
  /**
   * @param Request $request
   * @param array   $discard An array of keys to discard from the current get
   *                         data.
   *
   * @return string
   */
  public static function getGetRequestString(
    Request $request, array $discard = []
  )
  {
    $discardKeyed    = array_fill_keys($discard, true);
    $getVars         = $request->getVariables();
    $getVarsFiltered = array_diff_key($getVars, $discardKeyed);

    return http_build_query($getVarsFiltered);
  }
}
