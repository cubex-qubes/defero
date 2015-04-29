<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 18/09/13
 * Time: 14:59
 */

namespace Qubes\Defero\Components\DataSource;

use Cubex\Database\IDatabaseService;
use Cubex\Form\Form;
use Cubex\Foundation\Container;
use Cubex\Mapper\DataMapper;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

abstract class DataSource extends DataMapper
{
  protected $_connections;
  protected $_autoTimestamp = false;
  protected $_fixedProperties;

  abstract public function getName();

  abstract public function process(
    $taskId, $campaignId, $startTime, $lastSent, $startId = null, $endId = null,
    $additionalData = null
  );

  public function configureForm(Form $form, Campaign $campaign)
  {
  }

  public function hydrate(
    array $data, $setUnmodified = false, $createAttributes = false, $raw = true
  )
  {
    foreach($this->getFixedProperties() as $name => $value)
    {
      unset($data[$name]);
    }
    parent::hydrate($data, $setUnmodified, $createAttributes, $raw);
  }

  public function jsonSerialize()
  {
    $return = parent::jsonSerialize();
    foreach($this->getFixedProperties() as $name => $value)
    {
      unset($return[$name]);
    }
    return $return;
  }

  public function isFixedProperty($name)
  {
    $properties = $this->getFixedProperties();
    return isset($properties[$name]);
  }

  public function getFixedProperties()
  {
    if(!$this->_fixedProperties)
    {
      $this->_fixedProperties = [];

      $class            = new \ReflectionClass($this);
      $publicProperties = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
      $defaults         = $class->getDefaultProperties();
      foreach($publicProperties as $prop)
      {
        if($defaults[$prop->name] !== null)
        {
          $this->_fixedProperties[$prop->name] = $defaults[$prop->name];
        }
      }
    }
    return $this->_fixedProperties;
  }

  protected function _mergeAdditionalData(&$data, $additionalData = null)
  {
    if($additionalData)
    {
      foreach($data as $k => $thisData)
      {
        $data[$k] = array_merge($thisData, $additionalData);
      }
    }
  }

  protected function _updateLastId($taskId, $campaignId, $lastId)
  {
    $path = $this->_getLastIdPath($campaignId, $taskId);

    // Perform a safe atomic save
    $tempFile = $path . '.tmp';
    $fr   = fopen($tempFile, 'w');
    if($fr)
    {
      $success = true;
      if(fwrite($fr, $lastId) === false)
      {
        $success = false;
      }
      if(!fclose($fr))
      {
        $success = false;
      }
      if($success)
      {
        rename($tempFile, $path);
      }
    }
  }

  protected function _getLastIdPath($campaignId, $taskId)
  {
    $campaignDir = build_path(
      "/var/lib/defero",
      class_shortname($this),
      $campaignId
    );
    if(!file_exists($campaignDir))
    {
      mkdir($campaignDir, 0777, true);
    }
    $fileName = 'campaign-'
      . preg_replace('/[\\\\\/\:\*\?]/', '-', $taskId)
      . '.lastId';
    return build_path($campaignDir, $fileName);
  }

  public function getLastId($campaignId, $taskId)
  {
    $path = $this->_getLastIdPath($campaignId, $taskId);
    if(file_exists($path))
    {
      return file_get_contents($path);
    }
  }

  /**
   * @param string $serviceName
   *
   * @return IDatabaseService
   */
  public function getConnection($serviceName)
  {
    if(!isset($this->_connections[$serviceName]))
    {
      $this->_connections[$serviceName] = Container::servicemanager()
        ->getWithType(
          $serviceName,
          '\Cubex\Database\IDatabaseService'
        );
      $this->_connections[$serviceName]->query("SET NAMES 'utf8'");
    }
    return $this->_connections[$serviceName];
  }
}
