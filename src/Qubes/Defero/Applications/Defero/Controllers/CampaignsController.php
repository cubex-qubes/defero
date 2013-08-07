<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Form\Form;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Routing\Templates\ResourceTemplate;
use Cubex\Facade\Redirect;
use Cubex\Facade\Session;
use Qubes\Defero\Applications\Defero\Forms\CampaignForm;
use Qubes\Defero\Applications\Defero\Views\CampaignView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignsController extends BaseDeferoController
{
  public function renderNew()
  {
    return new CampaignView($this->_buildCampaignForm());
  }

  public function renderEdit($id, CampaignForm $campaignForm = null)
  {
    return new CampaignView($campaignForm ? : $this->_buildCampaignForm($id));
  }

  public function actionUpdate($id)
  {
    return $this->_updateCampaign($id);
  }

  public function actionDestroy($id)
  {
    (new Campaign($id))->delete();

    echo "Campaign {$id} deleted";
  }

  public function renderShow($id)
  {
    if(Session::getFlash('msg'))
    {
      echo Session::getFlash('msg');
      echo nl2br(json_pretty(new Campaign($id)));
      die;
    }

    return nl2br(json_pretty(new Campaign($id)));
  }

  public function actionCreate()
  {
    return $this->_updateCampaign();
  }

  public function renderIndex($page = 1)
  {
    echo "Campaigns index, page {$page}";
    echo "<br /><br />";

    $campaigns = new RecordCollection(new Campaign());
    foreach($campaigns as $campaign)
    {
      echo nl2br(json_pretty($campaign));
      echo "<br /><br />";
    }
  }

  private function _updateCampaign($id = null)
  {
    $form = $this->_buildCampaignForm($id);
    $form->hydrate($this->request()->postVariables());

    if($form->isValid() && $form->csrfCheck(true))
    {
      $form->saveChanges();


      Redirect::to("/campaigns/{$form->getMapper()->id()}")
        ->with("msg", "boo ya")
        ->now();
    }

    return $this->renderEdit($id, $form);
  }

  /**
   * @param null|int $id
   *
   * @return CampaignForm
   */
  private function _buildCampaignForm($id = null)
  {
    $action = $id ? "/campaigns/{$id}" : "/campaigns";

    return (new CampaignForm("campaign", $action))
      ->bindMapper(new Campaign($id));
  }

  public function getRoutes()
  {
    return ResourceTemplate::getRoutes();
  }
}
