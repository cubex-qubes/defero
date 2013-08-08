<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\HtmlElement;
use Cubex\View\TemplatedViewModel;
use Qubes\Defero\Applications\Defero\Helpers\RecordCollectionPagination;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignsView extends TemplatedViewModel
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
    $pagination->setNumResultsPerPage(5);

    $this->pager = $pagination->getPager();
    $this->pagerInfo = $pagination->getInfo();
    $this->campaigns = $pagination->getPaginatedResults();
  }

  public function getDeletePopover($id)
  {
    $popover = (new HtmlElement(
      "div", ["class" => "text-center"], "Are you sure?<br />"
    ))->nestElement("a", ["href" => "/campaigns/{$id}/delete"], "Yes")
      ->nestElement("span", [], " | ")
      ->nestElement(
        "a",
        ["href" => "#", "class" => "js-popover-hide"],
        "<strong>No</strong>");
    return htmlspecialchars($popover);
  }
}
