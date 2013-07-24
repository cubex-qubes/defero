<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Process\DataCollection;

use Cubex\Helpers\Strings;

class DataCollectionAttribute
{
  public $key;
  public $name;
  public $description;

  public function __construct($key, $name = null, $description = null)
  {
    $this->key = $key;

    if($name === null)
    {
      $name = Strings::humanize($key);
    }
    $this->name = $name;

    if($description === null)
    {
      $description = Strings::humanize($name);
    }
    $this->description = $description;
  }
}
