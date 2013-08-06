<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Cli;

use Cubex\Cli\ArgumentException;
use Cubex\Cli\CliCommand;
use Cubex\Mapper\DataMapper;
use Cubex\Text\TextTable;

abstract class MapperCliCommand extends CliCommand
{
  /**
   * @valuerequired
   * @validator int
   */
  public $id;

  abstract public function add();
  abstract public function get();
  abstract public function remove();
  abstract public function edit();

  public function execute()
  {
    $this->_help();
  }

  /**
   * @param string      $arg
   * @param null|string $cliMethod
   *
   * @throws \Cubex\Cli\ArgumentException
   */
  protected function _throwIfNotSet($arg, $cliMethod = null)
  {
    if(!$this->argumentIsSet($arg))
    {
      if($cliMethod === null)
      {
        $cliMethod = debug_backtrace()[1]['function'];
      }

      throw new ArgumentException(
        "'{$arg}' must be set when calling '{$cliMethod}'"
      );
    }
  }

  /**
   * @param DataMapper $mapper
   * @param array      $updatables array of arguments that are allowed to be
   *                               updated in the mapper
   */
  protected function _edit(DataMapper $mapper, array $updatables)
  {
    $className = class_shortname($mapper);

    if($mapper->exists())
    {
      $updates = $this->_updateMapper($mapper, $updatables);

      if($updates)
      {
        $mapper->saveChanges();
        echo "{$className} {$mapper->id()} updated;\n\n";
        echo TextTable::fromArray($updates);
      }
      else
      {
        echo "No updates were made\n";
        echo "Updatable fields: " . implode(", ", $updatables);
      }
    }
    else
    {
      echo "{$className} {$this->id} doesn't exist, did you mean to call add?";
    }
  }

  /**
   * @param DataMapper $mapper
   * @param array      $updatables
   *
   * @return array[][
   *   'field' => 'afield',
   *   'old'   => 'old_value',
   *   'new'   => 'new_value',
   * ]
   */
  private function _updateMapper(DataMapper $mapper, array $updatables)
  {
    $updates = [];

    foreach($updatables as $updatable)
    {
      if($this->argumentIsSet($updatable))
      {
        $updates[] = [
          "field" => $updatable,
          "old"   => $mapper->{$updatable},
          "new"   => $this->{$updatable},
        ];
        $mapper->{$updatable} = $this->{$updatable};
      }
    }

    return $updates;
  }
}
