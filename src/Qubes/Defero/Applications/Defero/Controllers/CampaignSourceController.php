<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 10/10/13
 * Time: 13:57
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Qubes\Defero\Components\DataSource;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignSourceView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignSourceController extends DeferoController
{
  public function renderIndex()
  {
    $campaign = new Campaign($this->getInt('id'));
    if($post = $this->request()->postVariables())
    {
      if(!$campaign->dataSource)
      {
        $campaign->dataSource = new \stdClass();
      }
      $campaign->dataSource->conditions = [];
      if(isset($post['compareField']))
      {
        foreach($post['compareField'] as $k => $f)
        {
          if($f)
          {
            $s          = new \stdClass();
            $s->field   = $f;
            $s->compare = $post['conditionCompare'][$k];
            $s->value   = $post['conditionValue'][$k];

            $campaign->dataSource->conditions[] = $s;
          }
        }
      }
    }
    $campaign->getAttribute('dataSource')->setModified();
    $campaign->saveChanges();
    return new CampaignSourceView($campaign);
  }
}
