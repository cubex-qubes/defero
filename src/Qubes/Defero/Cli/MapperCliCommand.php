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
   * @param null|string $type
   *
   * @throws \Cubex\Cli\ArgumentException
   */
  protected function _throwIfNotSet($arg, $type = null)
  {
    if($type === null)
    {
      $type = debug_backtrace()[1]['function'];
    }

    if(!$this->argumentIsSet($arg))
    {
      throw new ArgumentException(
        "'{$arg}' must be set when calling '{$type}'"
      );
    }
  }

  protected function _edit(DataMapper $mappper, array $updatables)
  {
    $className = class_shortname($mappper);

    if($mappper->exists())
    {
      $updates = [];

      foreach($updatables as $updatable)
      {
        if($this->argumentIsSet($updatable))
        {
          $updates[] = [
            "field" => $updatable,
            "old"   => $mappper->{$updatable},
            "new"   => $this->{$updatable},
          ];
          $mappper->{$updatable} = $this->{$updatable};
        }
      }

      if($updates)
      {
        $mappper->saveChanges();

        echo "{$className} {$mappper->id()} updated;\n\n";

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
}
