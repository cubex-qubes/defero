<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\View\HtmlElement;
use Cubex\View\ViewModel;
use Qubes\Bootstrap\Dropdown;
use Qubes\Bootstrap\Nav;
use Qubes\Bootstrap\NavItem;

class Header extends ViewModel
{
  public function render()
  {
    // Drop down links
    $rules     = new NavItem(
      new HtmlElement("a", ["href" => "/processors/rules"], "Rules"),
      $this->_getNavItemState("/processors/rules")
    );
    $processes = new NavItem(
      new HtmlElement("a", ["href" => "/processors/processes"], "Processes"),
      $this->_getNavItemState("/processors/processes")
    );

    // Drop down nav and nav item
    $dropDownNav = new Nav();
    $dropDownNav->addItem($rules)->addItem($processes);
    $dropDownNavItem = new NavItem();
    $dropDownNavItem->setDropdown(
      new Dropdown("Message Processors", $dropDownNav)
    );
    $dropDownNavItem->addAttributes(
      ["class" => $this->_getNavItemState("/processors")]
    );

    // Main nav
    $nav = new Nav(Nav::NAV_DEFAULT);
    $nav->addItem(
      new NavItem(
        new HtmlElement("a", ["href" => "/campaigns"], "Campaigns"),
        $this->_getNavItemState("/campaigns")
      )
    )->addItem(
      new NavItem(
        new HtmlElement("a", ["href" => "/contacts"], "Contacts"),
        $this->_getNavItemState("/contacts")
      )
    )->addItem($dropDownNavItem);

    return $nav;
  }

  protected function _getNavItemState($startsWith)
  {
    return starts_with($this->request()->path(), $startsWith) ?
      NavItem::STATE_ACTIVE : NavItem::STATE_NONE;
  }
}
