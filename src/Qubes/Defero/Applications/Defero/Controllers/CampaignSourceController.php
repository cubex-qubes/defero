<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 10/10/13
 * Time: 13:57
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Qubes\Defero\Applications\Defero\Forms\DeferoForm;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignSourceView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignSourceController extends DeferoController
{
  public function renderIndex()
  {
    $campaign   = new Campaign($this->getInt('id'));
    $dataSource = $campaign->getDataSource();
    $dataSource->setExists();
    $form = (new DeferoForm('source'))->bindMapper($dataSource);

    foreach($dataSource->getFixedProperties() as $name => $value)
    {
      $form->get($name)->addAttribute('disabled');
    }

    if(($post = $this->request()->postVariables()))
    {
      $form->hydrate($post);
      if($form->isValid() && $form->csrfCheck())
      {
        $dataSource->hydrate($form->jsonSerialize());
        $campaign->dataSourceOptions = $dataSource->jsonSerialize();

        $campaign->getAttribute('dataSourceOptions')->setModified();
        $campaign->saveChanges();
        $msg = 'Data Source Saved';
        return Redirect::to('/campaigns/' . $campaign->id())
          ->with("msg", new TransportMessage("info", $msg));
      }
    }
    return new CampaignSourceView($form);
  }
}
