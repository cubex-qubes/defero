<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

class ContactsController extends BaseDeferoController
{
  public function renderIndex()
  {
    echo "Contacts";
  }

  public function getRoutes()
  {
    return ["(.*)" => "index",];
  }
}
