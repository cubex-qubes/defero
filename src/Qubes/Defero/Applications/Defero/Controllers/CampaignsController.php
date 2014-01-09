<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Form\FormElement;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Routing\StdRoute;
use Cubex\Routing\Templates\ResourceTemplate;
use Cubex\Facade\Redirect;
use Cubex\View\RenderGroup;
use Qubes\Defero\Applications\Defero\Defero;
use Qubes\Defero\Applications\Defero\Forms\CampaignForm;
use Qubes\Defero\Applications\Defero\Forms\DeferoForm;
use Qubes\Defero\Applications\Defero\Helpers\RecordCollectionPagination;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignsView;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignFormView;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignsController extends BaseDeferoController
{
  private $_sendAtOptions = [
    ''          => '',
    '* * * * *' => 'Every Minute',
    '0 * * * *' => 'Every Hour',
  ];

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
    $form->addTextElement('userId');
    $form->get('UserId')->setRequired(true);
    $form->addSubmitElement();

    if($post = $this->request()->postVariables())
    {
      $form->hydrate($post);
      if($form->isValid() && $form->csrfCheck(true))
      {
        $msg = 'Test queued for user';
        $ds  = $campaign->getDataSource();
        $ds->process(
          $id,
          time(),
          $campaign->lastSent,
          $post['userId'],
          $post['userId']
        );

        return Redirect::to("/campaigns/{$id}")
        ->with("msg", new TransportMessage("info", $msg));
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
    return $this->_updateCampaign();
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
    $campaigns = (new RecordCollection(new Campaign()))
      ->setOrderBy("sortOrder");

    return new CampaignsView($campaigns);
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

  public function getRoutes()
  {
    $routes = ResourceTemplate::getRoutes();
    array_unshift($routes, new StdRoute('/:id/send', 'send'));
    array_unshift($routes, new StdRoute('/:id/test', 'test'));

    return $routes;
  }
}
