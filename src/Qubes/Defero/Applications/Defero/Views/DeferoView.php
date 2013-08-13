<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\View\HtmlElement;
use Cubex\View\TemplatedViewModel;

class DeferoView extends TemplatedViewModel
{
  private $_resultsPerPage = 10;

  /**
   * @param int $perPage
   *
   * @return $this
   */
  public function setResultsPerPage($perPage)
  {
    $this->_resultsPerPage = (int)$perPage;

    return $this;
  }

  /**
   * @return int
   */
  public function getResultsPerPage()
  {
    return $this->_resultsPerPage;
  }

  public function getDeletePopover($id)
  {
    $popover = (new HtmlElement(
      "div", ["class" => "text-center"], "Are you sure?<br />"
    ))->nestElement("a", ["href" => "{$this->baseUri()}/{$id}/delete"], "Yes")
      ->nestElement("span", [], " | ")
      ->nestElement(
        "a",
        ["href" => "#", "class" => "js-popover-hide"],
        "<strong>No</strong>");

    return htmlspecialchars($popover);
  }
}
