<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\HtmlElement;
use Qubes\Defero\Applications\Defero\Helpers\RecordCollectionPagination;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignsView extends DeferoView
{
  public $pager;
  public $pagerInfo;
  /**
   * @var Campaign[]
   */
  public $campaigns;

  public function __construct(
    RecordCollection $campaigns,
    RecordCollectionPagination $pagination
  )
  {
    $pagination->setNumResultsPerPage($this->getResultsPerPage());

    $this->pager     = $pagination->getPager();
    $this->pagerInfo = $pagination->getInfo();
    $this->campaigns = $pagination->getPaginatedResults();
  }
}
