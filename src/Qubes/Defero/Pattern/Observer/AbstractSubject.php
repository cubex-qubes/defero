<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Pattern\Observer;

class AbstractSubject implements \SplSubject
{
  /**
   * @var \SplObserver[]
   */
  protected $_observers;

  public function __construct()
  {
    $this->_observers = [];
  }

  /**
   * @param \SplObserver $observer
   */
  public function attach(\SplObserver $observer)
  {
    if($this->_observerExists($observer) === false)
    {
      $this->_observers[] = $observer;
    }
  }

  /**
   * @param \SplObserver $observer
   */
  public function detach(\SplObserver $observer)
  {
    $observerIndex = $this->_getObserverIndex($observer);

    if($observerIndex !== false)
    {
      unset($this->_observers[$observerIndex]);
    }
  }

  public function notify()
  {
    foreach($this->_observers as $observer)
    {
      $observer->update($this);
    }
  }

  /**
   * @param \SplObserver $observer
   *
   * @return bool
   */
  private function _observerExists(\SplObserver $observer)
  {
    return (bool)$this->_getObserverIndex($observer);
  }

  /**
   * @param \SplObserver $observer
   *
   * @return int|bool
   */
  protected function _getObserverIndex(\SplObserver $observer)
  {
    return array_search($observer, $this->_observers, true);
  }
}
