<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Form;

use Cubex\Form\FormRender;

class DeferoFormRender extends FormRender
{
  public function render()
  {
    $out = $this->_renderOpening();
    foreach($this->_form->elements() as $element)
    {
      $out .= $element->getRenderer()->render();
    }
    $out .= $this->_renderClosing();

    return $out;
  }
}
