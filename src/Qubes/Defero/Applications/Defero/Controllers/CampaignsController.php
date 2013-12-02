<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Foundation\Config\Config;
use Cubex\Foundation\Config\ConfigGroup;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Queue\StdQueue;
use Cubex\Routing\StdRoute;
use Cubex\Routing\Templates\ResourceTemplate;
use Cubex\Facade\Redirect;
use Qubes\Defero\Applications\Defero\Defero;
use Qubes\Defero\Applications\Defero\Forms\CampaignForm;
use Qubes\Defero\Applications\Defero\Helpers\RecordCollectionPagination;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignsView;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignFormView;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignsController extends BaseDeferoController
{
  /**
   * Show a blank campaign form
   *
   * @return CampaignFormView
   */
  public function renderNew()
  {
    return new CampaignFormView($this->_buildCampaignForm());
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
    return new CampaignFormView(
      $campaignForm ? : $this->_buildCampaignForm($id)
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

  public function renderSend($id)
  {
    $message = [
      'firstName' => 'tom',
      'lastName'  => 'kay',
      'name'      => 'tom kay',
      'email'     => 'tom.kay@justdevelop.it'
    ];

    Defero::pushMessage($id, $message);

    echo 'Test sent to tom.kay@justdevelop.it';
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
   * @param int $page
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignView
   */
  public function renderShow($id, $page = 1)
  {
    $campaign = new Campaign($id);

    return new CampaignView($campaign, $page);
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
    $campaigns = (new RecordCollection(new Campaign()))->setOrderBy("id");

    $pagination = new RecordCollectionPagination(
      $campaigns, $page
    );
    $pagination->setUri("/campaigns/page");

    return new CampaignsView($campaigns, $pagination);
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
    $routes[] = (new StdRoute('/:id/page/:pnumber', 'show', ['ANY']))
      ->excludeVerb('POST')
      ->excludeVerb('DELETE')
      ->excludeVerb('PUT');

    return $routes;
  }
}
