<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Process\DataCollection;

use Cubex\Mapper\DataMapper;
use Qubes\Defero\Transport\StdProcess;

class MapperDataCollection extends StdProcess implements DataCollectionProcess
{
  /**
   * @return bool
   */
  public function process()
  {
    $config = $this->config("process");

    $mapperClass = $config->getStr("mapper_class");
    $loadId      = $config->getRaw("mapper_id");
    $attrMap     = $config->getArr("attribute_map");

    //Load Mapper Class with ID
    $source = new $mapperClass($loadId);
    if($source instanceof DataMapper)
    {
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
}
