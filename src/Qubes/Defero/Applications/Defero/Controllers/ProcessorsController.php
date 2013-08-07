<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

class ProcessorsController extends BaseDeferoController
{
  public function renderIndex($type)
  {
    echo "Processors: {$type}";
  }

  public function getRoutes()
  {
    return ["/:type@alpha" => "index",];
  }
}
