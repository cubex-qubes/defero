<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Base;

use Cubex\View\HtmlElement;
use Cubex\View\ViewModel;
use Qubes\Bootstrap\Dropdown;
use Qubes\Bootstrap\Nav;
use Qubes\Bootstrap\NavItem;
use Qubes\Defero\Applications\Defero\Enums\TypeAheadEnum;
use Qubes\Defero\Applications\Defero\Views\Base\TypeAheadSearchFormView;

class HeaderView extends ViewModel
{
  public function __construct()
  {
    $this->requireJsPackage('typeahead');

    parent::__construct();
  }

  public function render()
  {
    // Main nav
    $nav = new Nav(Nav::NAV_DEFAULT);
    $nav->addItem(
      new NavItem(
        new HtmlElement(
          "a", ["href" => "/wizard"], "<strong>**Wizard**</strong>"
        ),
        $this->_getNavItemState("/wizard")
      )
    )->addItem(
      new NavItem(
        new HtmlElement("a", ["href" => "/campaigns"], "Campaigns"),
        $this->_getNavItemState("/campaigns")
      )
    )->addItem(
      new NavItem(
        new HtmlElement("a", ["href" => "/contacts"], "Contacts"),
        $this->_getNavItemState("/contacts")
      )
    )->addItem(
      new NavItem(
        new HtmlElement("a", ["href" => "/stats"], "Stats"),
        $this->_getNavItemState("/stats")
      )
    );

    // Global typeahead search
    $searchForm = (new TypeAheadSearchFormView(TypeAheadEnum::ALL()))
      ->setNavBarSearch();

    // Logo or brand
    $brand = new HtmlElement(
      "a", ["class" => "brand", "href" => "/"], "Defero"
    );

    return $brand . $nav . $searchForm;
  }

  protected function _getNavItemState($startsWith)
  {
    return starts_with($this->request()->path(), $startsWith) ?
      NavItem::STATE_ACTIVE : NavItem::STATE_NONE;
  }
}
