<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Wizard;

interface IWizardStepIterator extends \Iterator
{
  /**
   * @param IWizardStep $step
   *
   * @return void
   */
  public function addStep(IWizardStep $step);

  /**
   * Moves the pointer forward, checks that the key exists and returns the step.
   * If the key does not exist we return null.
   *
   * @return IWizardStep|null
   */
  public function getNextStep();

  /**
   * @return IWizardStep|null
   */
  public function getCurrentStep();
}
