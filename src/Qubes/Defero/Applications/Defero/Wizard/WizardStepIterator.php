<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Wizard;

class WizardStepIterator extends \ArrayIterator implements IWizardStepIterator
{
  /**
   * @param IWizardStep $step
   *
   * @return void
   */
  public function addStep(IWizardStep $step)
  {
    $this->append($step);
  }

  /**
   * Checks that the next key exists and returns the step. If the key does not
   * exist we return null.
   *
   * @return IWizardStep|null
   */
  public function getNextStep()
  {
    $nextKey = $this->key() + 1;

    if($this->offsetExists($nextKey))
    {
      return $this->offsetGet($nextKey);
    }

    return null;
  }

  /**
   * Moves the pointer forward, checks that the key exists and returns the step.
   * If the key does not exist we return null.
   *
   * @return IWizardStep|null
   */
  public function getNextStepAndMovePointer()
  {
    $this->next();

    return $this->getCurrentStep();
  }

  /**
   * @return null|IWizardStep
   */
  public function getCurrentStep()
  {

    if($this->valid())
    {
      return $this->current();
    }

    return null;
  }
}
