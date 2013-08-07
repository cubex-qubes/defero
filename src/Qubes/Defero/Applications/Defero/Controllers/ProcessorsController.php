<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

class ProcessorsController extends BaseDeferoController
{
  public function renderIndex()
  {
    echo "Processors";
  }

  public function getRoutes()
  {
    return ["(.*)" => "index",];
  }
}
