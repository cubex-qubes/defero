<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Wizard;

use Qubes\Defero\Pattern\Observer\AbstractSubject;

class WizardSubject extends AbstractSubject implements IWizardSubject
{
  /**
   * @param \SplObserver $observer
   *
   * @return \SplObserver|null
   */
  public function getNextStep(\SplObserver $observer)
  {
    $observerIndex = $this->_getObserverIndex($observer);
    $observerIndex++;

    if(!isset($this->_observers[$observerIndex]))
    {
      return null;
    }

    return $this->_observers[$observerIndex];
  }
}
