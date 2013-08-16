<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Wizard;

use Qubes\Defero\Pattern\Iterator\AbstractIterator;

class WizardStepIterator extends AbstractIterator implements IWizardStepIterator
{
  /**
   * @param IWizardStep $step
   *
   * @return void
   */
  public function addStep(IWizardStep $step)
  {
    $this->_array[] = $step;
  }

  /**
   * Moves the pointer forward, checks that the key exists and returns the step.
   * If the key does not exist we return null.
   *
   * @return IWizardStep|null
   */
  public function getNextStep()
  {
    $this->next();

    if($this->valid() && $this->current() instanceof IWizardStep)
    {
      return $this->current();
    }

    return null;
  }
}
