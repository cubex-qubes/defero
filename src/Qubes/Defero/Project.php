<?php
/**
 * @author gareth.evans
 */
namespace Qubes\Defero;

use Bundl\DebugToolbar\DebugToolbarBundl;

class Project extends \Cubex\Core\Project\Project
{
  public function name()
  {
    return "Defero";
  }

  public function getBundles()
  {
    if(CUBEX_ENV == 'development')
    {
      return [new DebugToolbarBundl()];
    }
  }

  public function defaultApplication()
  {
    return new Applications\Defero\Defero();
  }
}
