<?php
/**
 * @author gareth.evans
 */
namespace Qubes\Defero;

class Project extends \Cubex\Core\Project\Project
{
  public function name()
  {
    return "Defero";
  }

  public function defaultApplication()
  {
    return new Applications\Defero\Defero();
  }
}
