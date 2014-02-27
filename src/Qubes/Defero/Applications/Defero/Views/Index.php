<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;

class Index extends ViewModel
{
  protected $_queueSize = 0;
  protected $_queueActiveSize = 0;

  public function setQueueSize($size = 0)
  {
    $this->_queueSize = $size;
    return $this;
  }

  public function setActiveQueueSize($size = 0)
  {
    $this->_queueActiveSize = $size;
    return $this;
  }

  public function render()
  {
    return new RenderGroup(
      new HtmlElement('h1', [], 'Campaign Queue:'),
      new HtmlElement(
        'h2',
        [],
        number_format($this->_queueActiveSize, 0) .
        '/' .
        number_format($this->_queueSize, 0) .
        ' Active'
      )
    );
  }
}
