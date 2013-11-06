<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 12/09/13
 * Time: 14:38
 */

namespace Qubes\Defero\Applications\Defero\Views\Search;

use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;

class SearchView extends DeferoView
{
  public $data;
  public function __construct($data) {
    $this->data = $data;
  }
}
