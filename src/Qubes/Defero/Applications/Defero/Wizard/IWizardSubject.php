<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Wizard;

interface IWizardSubject extends \SplSubject
{
  /**
   * @param \SplObserver $observer
   *
   * @return \SplObserver|null
   */
  public function getNextStep(\SplObserver $observer);
}
