<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\View\HtmlElement;
use Cubex\View\ViewModel;
use Qubes\Defero\Applications\Defero\Enums\TypeAheadEnum;

class TypeAheadSearchFormView extends ViewModel
{
  private $_typeAheadEnum;
  private $_placeholder;
  private $_navbarSearch = false;

  public function __construct(
    TypeAheadEnum $typeAheadEnum = null,
    $placeholder = "Search..."
  )
  {
    $this->_typeAheadEnum = $typeAheadEnum ? : TypeAheadEnum::ALL();
    $this->_placeholder   = $placeholder;
  }

  public function setNavbarSearch($navbarSearch = true)
  {
    $this->_navbarSearch = (bool)$navbarSearch;

    return $this;
  }

  public function render()
  {
    $jsTriggerClass = "js-defero-typeahead-";

    switch((string)$this->_typeAheadEnum)
    {
      case TypeAheadEnum::CONTACTS:
        $jsTriggerClass .= "contacts";
        $searchType = "contacts";
        break;
      case TypeAheadEnum::CAMPAIGNS:
        $jsTriggerClass .= "campaigns";
        $searchType = "campaigns";
        break;
      case TypeAheadEnum::PROCESSORS:
        $jsTriggerClass .= "processors";
        $searchType = "processors";
        break;
      case TypeAheadEnum::ALL:
      default:
        $jsTriggerClass .= "all";
        $searchType = "all";
        break;
    }

    $searchFromClass = $this->_navbarSearch ? "navbar-search" : "form-search";

    $searchForm = (new HtmlElement(
      "form",
      [
        "class"  => $searchFromClass,
        "action" => "/search",
        "method" => "post",
      ]
    ))->nestElement(
        "input",
        [
          "type"         => "text",
          "name"         => "q",
          "class"        => "search-query {$jsTriggerClass}",
          "placeholder"  => $this->_placeholder,
          "autocomplete" => "off",
        ]
      )->nestElement(
        "input",
        ["type"  => "hidden", "name"  => "type", "value" => $searchType, ]
      );

    return $searchForm;
  }
}
