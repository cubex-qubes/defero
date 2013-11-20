<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 10/10/13
 * Time: 14:04
 */

namespace Qubes\Defero\Applications\Defero\Views\Campaigns;

use Cubex\Data\Validator\Validator;
use Cubex\Form\FormElement;
use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\DataSource\DataSourceCondition;
use Qubes\Defero\Components\DataSource\IDataSource;

class CampaignSourceView extends DeferoView
{
  public $campaign;
  public $elements = [];
  public $sourceElement;
  public $newCondition;

  protected $_namespace = 'Qubes\\Defero\\Components\\DataSource';
  protected $_currentKey = 1;

  protected function _getNextKey()
  {
    return ($this->_currentKey++);
  }

  public function __construct(Campaign $campaign)
  {
    $this->campaign      = $campaign;
    $this->sourceElement = $this->getSourceElement();

    if($source = $this->campaign->getDataSource())
    {
      foreach($source->getConditionValues() as $c)
      {
        $this->addConditionElements($source, $c);
      }
      $this->newCondition =
        (new FormElement('compareField[' . $this->_getNextKey() . ']'))
          ->setType(FormElement::SELECT)->setLabel('New Condition')
          ->setOptions(['' => ''] + $source->getConditions());
    }
  }

  public function addConditionElements($source, $data)
  {
    /**
     * @var $o      DataSourceCondition
     * @var $source IDataSource
     */
    $key      = $this->_getNextKey();
    $fieldEle = (new FormElement('compareField[' . $key . ']'))
      ->setType(FormElement::SELECT)
      ->setData($data->field)
      ->setOptions(['' => ''] + $source->getConditions())
      ->setRenderTemplate('{{input}}');

    $o = $source->getCondition($data->field);
    if($o)
    {
      $compareEle = $o->getCompareElement($key, $data->compare)
        ->setRenderTemplate('{{input}}');
      $valueEle   = $o->getValueElement($key, $data->value)
        ->setRenderTemplate('{{input}}');

      $group = [$fieldEle, $compareEle, $valueEle];
      if(isset($data->disabled))
      {
        foreach($group as $ele)
        {
          /**
           * @var $ele FormElement
           */
          $ele->addAttribute('disabled');
        }
      }

      $this->elements[] = $group;
    }
  }

  public function getSourceElement()
  {
    $post = $this->request()->postVariables();

    $sc = null;
    if(isset($this->campaign->dataSource->sourceClass))
    {
      $sc = $this->campaign->dataSource->sourceClass;
    }

    $ele = (new FormElement('sourceClass'))
      ->setType(FormElement::TEXT)
      ->setData($sc)
      ->addValidator(Validator::VALIDATE_NOTEMPTY)
      ->addValidator([$this, 'classExists'], [$this->_namespace])
      ->addValidator(
        [$this, 'isSubclass'],
        [$this->_namespace . '\\IDataSource', $this->_namespace]
      );
    if($post && $ele->isValid($post['sourceClass']))
    {
      $ele->setData($post['sourceClass']);
      $this->campaign->dataSource->sourceClass = $post['sourceClass'];
      $this->campaign->getAttribute('dataSource')->setModified();
      $this->campaign->saveChanges();
    }

    return $ele;
  }

  public static function classExists($input, $namespaces = [])
  {
    if(class_exists('\\' . $input))
    {
      return true;
    }
    $namespaces = (array)$namespaces;
    $namespaces = array_filter(array_unique($namespaces));
    foreach($namespaces as $namespace)
    {
      if(class_exists($namespace . '\\' . $input))
      {
        return true;
      }
    }
    throw new \Exception('Invalid class ' . $input);
  }

  public static function isSubclass($input, $class, $namespace = '')
  {
    if(is_subclass_of($namespace . '\\' . $input, $class))
    {
      return true;
    }
    throw new \Exception($input . ' not an instance of ' . $class);
  }
}
