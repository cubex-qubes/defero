<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Pattern\Iterator;

abstract class AbstractIterator implements \Iterator
{
  protected $_position;
  protected $_array;

  public function __construct()
  {
    $this->_position = 0;
    $this->_array    = [];
  }

  /**
   * @return mixed Can return any type.
   */
  public function current()
  {
    return $this->_array[$this->_position];
  }

  /**
   * Move forward to next element
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   */
  public function next()
  {
    $this->_position++;
  }

  /**
   * Return the key of the current element
   * @link http://php.net/manual/en/iterator.key.php
   * @return mixed scalar on success, or null on failure.
   */
  public function key()
  {
    return $this->_position;
  }

  /**
   * Checks if current position is valid
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then
   *                 evaluated. Returns true on success or false on failure.
   */
  public function valid()
  {
    return array_key_exists($this->_position, $this->_array);
  }

  /**
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   */
  public function rewind()
  {
    $this->_position = 0;
  }
}
