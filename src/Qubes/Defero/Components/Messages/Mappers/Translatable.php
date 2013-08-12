<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Messages\Mappers;

use Cubex\Mapper\Database\I18n\TextContainer;

class Translatable extends TextContainer
{
  protected function _configure()
  {
    $this->_dbServiceName = "defero_db";
  }
}
