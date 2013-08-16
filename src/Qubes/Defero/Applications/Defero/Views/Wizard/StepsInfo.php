<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Wizard;

use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;
use Qubes\Defero\Applications\Defero\Wizard\WizardStepIterator;
use Qubes\Defero\Applications\Defero\Wizard\IWizardStep;

class StepsInfo extends ViewModel
{
  /**
   * @var \Qubes\Defero\Applications\Defero\Wizard\WizardStepIterator
   */
  private $_steps;

  public function __construct(WizardStepIterator $steps)
  {
    $this->_steps = $steps;
  }

  public function render()
  {
    $breadcrumb = new HtmlElement("ul", ["class" => "breadcrumb"]);

    $breadcrumb->nestElement(
      "li", [], new HtmlElement("span", ["class" => "divider"], "/")
    );

    foreach($this->_steps->getArrayCopy() as $stepKey => $step)
    {
      $active = $stepKey === $this->_steps->key() ? "active" : "";
      
      /**
       * @var IWizardStep $step
       */
      $breadcrumb->nestElement(
        "li",
        ["class" => $active],
        $step->getName() . new HtmlElement("span", ["class" => "divider"], "/")
      );
    }

    return new RenderGroup(
      $breadcrumb,
      new HtmlElement("h3", [], $this->_steps->getCurrentStep()->getName()),
      new HtmlElement(
        "p", [], $this->_steps->getCurrentStep()->getDescription()
      )
    );
  }
}
