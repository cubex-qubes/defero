<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Wizard;

use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;
use Qubes\Defero\Applications\Defero\Wizard\WizardStepIterator;

class StepsInfo extends ViewModel
{
  private $_steps;

  public function __construct(WizardStepIterator $steps)
  {
    $this->_steps = $steps;
  }

  public function render()
  {
    return new RenderGroup(
      new HtmlElement("h3", [], $this->_steps->getCurrentStep()->getName()),
      new HtmlElement(
        "p", [], $this->_steps->getCurrentStep()->getDescription()
      )
    );
  }
}
