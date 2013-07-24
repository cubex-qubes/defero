<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Process\DataCollection;

use Cubex\Helpers\Strings;
use Cubex\Mapper\DataMapper;
use Qubes\Defero\Transport\StdProcess;

class MapperDataCollection extends StdProcess implements IDataCollectionProcess
{
  /**
   * @return DataCollectionAttribute[]
   */
  public function getAttributes()
  {
    $attrs  = [];
    $source = $this->_getMapper();
    if($source !== null)
    {
      $attributes = $source->getRawAttributes();
      if($attributes)
      {
        foreach($attributes as $attribute)
        {
          $attrs[] = new DataCollectionAttribute(
            $attribute->sourceProperty(),
            Strings::humanize($attribute->name()),
            $attribute->getDescription()
          );
        }
      }
    }
    return $attrs;
  }

  /**
   * @return bool
   */
  public function process()
  {
    $config = $this->config("process");

    $loadId  = $config->getRaw("mapper_id");
    $attrMap = $config->getArr("attribute_map");

    //Load Mapper Class with ID
    $source = $this->_getMapper();
    if($source !== null)
    {
      $source->load($loadId);
      if(!$source->exists())
      {
        return false;
      }

      //Retrieve Attributes
      //$attrMap = ['source' => 'attrname', 'dest' => 'attrname'];
      foreach($attrMap as $map)
      {
        if(isset($source->$map['source']))
        {
          //Append onto Message
          $this->_message->setData($map['dest'], $source->$map['source']);
        }
      }
    }

    return true;
  }

  /**
   * @return DataMapper|null
   */
  protected function _getMapper()
  {
    $config      = $this->config("process");
    $mapperClass = $config->getStr("mapper_class");
    $source      = new $mapperClass();
    if($source instanceof DataMapper)
    {
      return $source;
    }
    return null;
  }
}
