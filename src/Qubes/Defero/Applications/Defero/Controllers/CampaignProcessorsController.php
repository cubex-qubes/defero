<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 25/09/13
 * Time: 14:30
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Http\Response;
use Cubex\Data\DocBlock\DocBlockHelper;
use Cubex\Facade\Redirect;
use Cubex\Form\OptionBuilder;
use Cubex\Routing\StdRoute;
use Cubex\Routing\Templates\ResourceTemplate;
use Cubex\View\Templates\Errors\Error404;
use Qubes\Defero\Applications\Defero\Forms\DeferoForm;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignProcessorsView;

class CampaignProcessorsController extends DeferoController
{
  protected $_campaign;

  protected function _configure()
  {
    parent::_configure();
    $this->_campaign = new Campaign($this->getInt('cid'));
  }

  public function renderIndex()
  {
    return new CampaignProcessorsView($this->_campaign);
  }

  public function renderNew()
  {
    $definedProcessors = $this->config('processors')->jsonSerialize();

    $form = new DeferoForm('edit_processor');
    $form->addSelectElement(
      'processorType',
      array_map('class_shortname', $definedProcessors)
    );
    $form->addSubmitElement();
    $form->get('processorType')->setRequired(true);

    $form->hydrate($this->request()->postVariables());
    if($form->isValid() && $form->csrfCheck())
    {
      $campaign             = $this->_campaign;
      $processors           = $campaign->processors;
      $processors[]         = $form->jsonSerialize();
      $campaign->processors = $processors;
      $campaign->saveChanges();
      end($processors);
      return Redirect::to(
        '/campaigns/' . $this->getInt('cid') . '/processors/' . key(
          $processors
        ) . '/edit'
      );
    }

    return $form;
  }

  public function renderCreate()
  {
    return $this->renderNew();
  }

  public function renderDestroy($pid)
  {
    $processors = $this->_campaign->processors;
    unset($processors[$pid]);
    $this->_campaign->processors = array_values($processors);
    $this->_campaign->saveChanges();
    return Redirect::to('/campaigns/' . $this->getInt('cid'));
  }

  public function renderEdit($pid)
  {
    $processors        = $this->_campaign->processors;
    $definedProcessors = $this->config('processors')->jsonSerialize();
    if(!isset($processors[$pid]))
    {
      return $this->renderNew();
    }
    $pData = $processors[$pid];

    $form = new DeferoForm('edit_processor');
    $form->addSelectElement(
      'processorType',
      array_map('class_shortname', $definedProcessors)
    );
    $form->getElement('processorType')->addAttribute('disabled');

    $processor = $this->config('processors')->getStr($pData->processorType);
    $processor = new $processor;

    // populate types
    $class = new \ReflectionClass($processor);
    foreach($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $p)
    {
      $default = $p->getValue($processor);

      // get property type (enum)
      // use reflection to check for enumclass
      if($t = DocBlockHelper::getBlock($processor, $p->getName(), 'enumclass'))
      {
        $form->addSelectElement(
          $p->getName(), (new OptionBuilder(new $t()))->getOptions(), $default
        );
      }
      else
      {
        $form->addTextElement($p->getName(), $default);
      }
    }
    $form->addSubmitElement();

    $form->hydrate((array)$processor);
    $form->hydrate((array)$pData);

    if($this->request()->postVariables())
    {
      $form->hydrate($this->request()->postVariables());
      if($form->isValid() && $form->csrfCheck())
      {
        $processors[$pid]            = $form->jsonSerialize();
        $this->_campaign->processors = $processors;
        $this->_campaign->saveChanges();
        return Redirect::to('/campaigns/' . $this->getInt('cid'));
      }
    }

    echo $form;
  }

  public function renderReorder()
  {
    return new Error404();
  }

  public function ajaxReorder()
  {
    if(!$this->request()->postVariables())
    {
      return;
    }
    $processors    = $this->_campaign->processors;
    $newProcessors = [];
    // TODO: find a nicer way to verify the keys of processors
    foreach($this->request()->postVariables('order') as $data)
    {
      $newProcessors[] = $processors[$data['pid']];
    }
    $this->_campaign->processors = $newProcessors;
    $response                    = [
      'result' => $this->_campaign->saveChanges()
    ];
    $response                    = new Response($response);
    $response->addHeader('Content-Type', 'application/json');
    return $response;
  }

  public function getRoutes()
  {
    $routes = ResourceTemplate::getRoutes();
    array_unshift($routes, new StdRoute('/reorder', 'reorder'));

    return $routes;
  }
}
