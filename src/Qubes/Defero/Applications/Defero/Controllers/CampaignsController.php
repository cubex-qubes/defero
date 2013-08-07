<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Form\Form;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Routing\Templates\ResourceTemplate;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignsController extends BaseDeferoController
{
  public function renderNew()
  {
    return $this->_buildCampaignForm();
  }

  public function renderEdit($id)
  {
    return $this->_buildCampaignForm($id);
  }

  public function actionUpdate($id)
  {
    $campaign = $this->_updateCampaign($id);

    echo "Campaign {$campaign->id()} updated";
  }

  public function actionDestroy($id)
  {
    (new Campaign($id))->delete();

    echo "Campaign {$id} deleted";
  }

  public function renderShow($id)
  {
    echo nl2br(json_pretty(new Campaign($id)));
  }

  public function actionCreate()
  {
    $campaign = $this->_updateCampaign();

    echo "Campaign {$campaign->id()} created";
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
    $campaign = new Campaign($id);
    $campaign
      ->hydrate($this->request()->postVariables())
      ->saveChanges();

    return $campaign;
  }

  private function _buildCampaignForm($id = null)
  {
    $action = $id ? "/campaigns/{$id}" : "/campaigns";

    return (new Form("campaign", $action))->bindMapper(new Campaign($id));
  }

  public function getRoutes()
  {
    return ResourceTemplate::getRoutes();
  }
}
