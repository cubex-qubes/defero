<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Http\Response;
use Cubex\Data\Transportable\TransportMessage;
use Cubex\Form\FormElement;
use Cubex\Foundation\Container;
use Cubex\Routing\StdRoute;
use Cubex\Routing\Templates\ResourceTemplate;
use Cubex\Facade\Redirect;
use Cubex\View\RenderGroup;
use Cubex\View\Templates\Errors\Error404;
use Qubes\Defero\Applications\Defero\Defero;
use Qubes\Defero\Applications\Defero\Forms\CampaignForm;
use Qubes\Defero\Applications\Defero\Forms\DeferoForm;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignsView;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignFormView;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignView;
use Qubes\Defero\Components\Campaign\Enums\SendType;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignsController extends BaseDeferoController
{
  private $_sendAtOptions = [
    ''          => '',
    '* * * * *' => 'Every Minute',
    '0 * * * *' => 'Every Hour',
  ];

  public function renderClone($id)
  {
    $campaign = new Campaign($id);
    $form     = new DeferoForm('cloneForm');
    $form->addTextElement('currentReference', $campaign->reference);
    $form->addTextElement('newReference');
    $form->addSubmitElement('Clone');
    $form->getElement('currentReference')->addAttribute('disabled');
    $form->hydrate($this->request()->postVariables());
    if(!$form->getData('newReference'))
    {
      return '<h3>Clone ' . $campaign->name . '</h3>' . $form;
    }

    try
    {
      $campaign->reference = $form->getData('newReference');
      $campaign->saveAsNew();
      $newId = $campaign->id();

      $campaign    = new Campaign($newId);
      $oldCampaign = new Campaign($id);
      $languages   = $this->config('i18n')->getArr('languages');

      $msg    = $campaign->message();
      $oldMsg = $oldCampaign->message();
      foreach($languages as $lang)
      {
        $msg->setLanguage($lang)->reload();
        $oldMsg->setLanguage($lang)->reload();
        $oldData = $oldMsg->jsonSerialize();
        unset(
        $oldData['id'], $oldData['campaign_id'],
        $oldData['created_at'], $oldData['updated_at']
        );
        $msg->hydrate($oldData);
        foreach($msg->getRawAttributes() as $attr)
        {
          $attr->setModified();
        }
        $msg->saveChanges();
      }
      return Redirect::to("/campaigns/{$newId}")
        ->with("msg", new TransportMessage('info', 'Campaign Cloned'));
    }
    catch(\Exception $e)
    {
      return Redirect::to("/campaigns/{$id}")
        ->with('msg', new TransportMessage("error", $e->getMessage()));
    }
  }

  /**
   * Show a blank campaign form
   *
   * @return CampaignFormView
   */
  public function renderNew()
  {
    $campaignForm = $this->_buildCampaignForm();
    $sendAt       = $campaignForm->getElement('send_at');
    $sendAtValue  = $sendAt->rawData();
    if(!isset($this->_sendAtOptions[$sendAtValue]))
    {
      $this->_sendAtOptions[$sendAtValue] = $sendAtValue;
    }
    $this->_sendAtOptions['custom'] = 'Custom';
    $sendAt->setType(FormElement::SELECT);
    $sendAt->setOptions($this->_sendAtOptions);
    return new CampaignFormView($campaignForm);
  }

  /**
   * Show a pre-populated campaign form
   *
   * @param int          $id
   * @param CampaignForm $campaignForm
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignFormView
   */
  public function renderEdit($id, CampaignForm $campaignForm = null)
  {
    $campaignForm = $campaignForm ? : $this->_buildCampaignForm($id);
    $sendAt       = $campaignForm->getElement('send_at');
    $sendAtValue  = $sendAt->rawData();
    if(!isset($this->_sendAtOptions[$sendAtValue]))
    {
      $this->_sendAtOptions[$sendAtValue] = $sendAtValue;
    }
    $this->_sendAtOptions['custom'] = 'Custom';

    $sendAt->setType(FormElement::SELECT);
    $sendAt->setOptions($this->_sendAtOptions);
    return new CampaignFormView(
      $campaignForm
    );
  }

  /**
   * Update an existing campaign
   *
   * @param int $id
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignFormView
   */
  public function actionUpdate($id)
  {
    return $this->_updateCampaign($id);
  }

  public function renderTest($id)
  {
    $campaign = new Campaign($id);

    $form = new DeferoForm('send_test_email');
    $form->addTextElement('email');
    foreach($campaign->message()->findVariables() as $var)
    {
      $form->addTextElement($var);
    }
    $form->get('email')->setRequired(true);
    $form->addSubmitElement();

    if($post = $this->request()->postVariables())
    {
      $form->hydrate($post);
      if($form->isValid() && $form->csrfCheck(true))
      {
        try
        {
          $msg = new TransportMessage("info", 'Test queued for user');
          Defero::pushMessage($id, $form->jsonSerialize());
        }
        catch(\Exception $e)
        {
          $msg = new TransportMessage("error", $e->getMessage());
        }

        return Redirect::to("/campaigns/{$id}")
          ->with("msg", $msg);
      }
    }

    return new RenderGroup(
      '<h1>Send a Test Campaign</h1>',
      $form
    );
  }

  public function renderSend($id)
  {
    $failMsg = 'Could not queue Campaign';
    try
    {
      $pushed = Defero::pushCampaign($id);
    }
    catch(\Exception $e)
    {
      $pushed  = false;
      $failMsg = $e->getMessage();
    }
    if($pushed)
    {
      return Redirect::to("/campaigns/{$id}")
        ->with("msg", new TransportMessage("info", 'Campaign Queued'));
    }
    else
    {
      return Redirect::to("/campaigns/{$id}")
        ->with("msg", new TransportMessage("error", $failMsg));
    }
  }

  /**
   * Delete a campaign
   *
   * @param int $id
   *
   * @return \Cubex\Core\Http\Redirect
   */
  public function actionDestroy($id)
  {
    $campaign = new Campaign($id);
    $campaign->forceLoad();
    $campaign->delete();

    return Redirect::to('/campaigns')->with(
      'msg',
      new TransportMessage('info', "Campaign '{$campaign->name}' deleted.")
    );
  }

  /**
   * Output a single campaign
   *
   * @param int $id
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignView
   */
  public function renderShow($id)
  {
    $campaign = new Campaign($id);

    return new CampaignView($campaign);
  }

  /**
   * Create a new campaign
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignFormView
   */
  public function postCreate()
  {
    $campaign   = new Campaign();
    $config     = Container::config()->get("default_processors");
    $processors = [];
    if($config != null)
    {
      $processorKeys = $config->availableKeys();

      foreach($processorKeys as $key)
      {
        $processors[]['processorType'] = $key;
      }
    }

    $campaign->processors = $processors;
    $campaign->saveChanges();
    return $this->_updateCampaign($campaign->id());
  }

  /**
   * Show a paginated list of campaigns
   *
   * @param int $page
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignsView
   */
  public function renderIndex($page = 1)
  {
    $postData = null;
    if($postData = $this->request()->postVariables())
    {
      $where = [];
      if($postData['label'] != "")
      {
        $where['label'] = $postData['label'];
      }
      if($postData['active'] != "")
      {
        $where['active'] = $postData['active'];
      }
      if($postData['sendType'] != "")
      {
        $where['send_type'] = $postData['sendType'];
      }
      $campaigns = Campaign::collection($where)->setOrderBy("sortOrder");
    }
    else
    {
      $campaigns = Campaign::collection()->setOrderBy("sortOrder");
    }

    $options['sendTypeOptions'] = array_flip((new SendType())->getConstList());
    $options['activeOptions']   = [1 => 'Yes', 0 => 'No'];
    $options['labelOptions']    = Campaign::labels();

    return new CampaignsView($campaigns, $options, $postData);
  }

  /**
   * Helper method to handle create and update of campaigns. Will redirect to
   * the specific campaign on success with a message. If there are any
   * validation or CSRF errors we render the form again with information.
   *
   * @param null|int $id
   *
   * @return CampaignFormView
   */
  private function _updateCampaign($id = null)
  {
    $form = $this->_buildCampaignForm($id);
    $form->hydrate($this->request()->postVariables());

    if($form->isValid() && $form->csrfCheck(true))
    {
      $form->saveChanges();

      $msg = "Campaign '{$form->name}'";
      $msg .= $id ? " Updated" : " Created";

      return Redirect::to("/campaigns/{$form->getMapper()->id()}")
        ->with("msg", new TransportMessage("info", $msg));
    }

    return $this->renderEdit($id, $form);
  }

  /**
   * Instantiates the form and binds the mapper. Also sets up the action based
   * on an id existing or not.
   *
   * @param null|int $id
   *
   * @return CampaignForm
   */
  private function _buildCampaignForm($id = null)
  {
    $action = $id ? "/campaigns/{$id}" : "/campaigns";

    return Campaign::buildCampaignForm($action, $id);
  }

  public function renderReorder()
  {
    return new Error404();
  }

  public function ajaxReorder()
  {
    if(!$this->request()->postVariables())
    {
      $response = ['result' => false];
      $response = new Response($response);
      $response->addHeader('Content-Type', 'application/json');
      return $response;
    }

    foreach($this->request()->postVariables('order') as $order => $cid)
    {
      $campaign            = new Campaign($cid);
      $campaign->sortOrder = $order;
      $campaign->saveChanges();
    }
    $response = ['result' => true];
    $response = new Response($response);
    $response->addHeader('Content-Type', 'application/json');
    return $response;
  }

  public function getRoutes()
  {
    $routes = ResourceTemplate::getRoutes();
    array_unshift($routes, new StdRoute('/:id/send', 'send'));
    array_unshift($routes, new StdRoute('/:id/test', 'test'));
    array_unshift($routes, new StdRoute('/:id/clone', 'clone'));
    array_unshift($routes, new StdRoute('/reorder', 'reorder'));
    array_unshift($routes, new StdRoute('/filter', 'index'));

    return $routes;
  }
}
