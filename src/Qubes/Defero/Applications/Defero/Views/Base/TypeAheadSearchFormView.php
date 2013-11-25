<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Base;

use Cubex\View\HtmlElement;
use Cubex\View\ViewModel;
use Qubes\Defero\Applications\Defero\Enums\TypeAheadEnum;

class TypeAheadSearchFormView extends ViewModel
{
  private $_typeAheadEnum;
  private $_placeholder;
  private $_navBarSearch = false;

  public function __construct(
    TypeAheadEnum $typeAheadEnum = null,
    $placeholder = "Search..."
  )
  {
    $this->_typeAheadEnum = $typeAheadEnum ? : TypeAheadEnum::ALL();
    $this->_placeholder   = $placeholder;
  }

  public function setNavBarSearch($navBarSearch = true)
  {
    $this->_navBarSearch = (bool)$navBarSearch;

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

    $searchFromClass = $this->_navBarSearch ? "navbar-search" : "form-search";

    $searchForm = (new HtmlElement(
      "form",
      [
      "class"  => $searchFromClass,
      "action" => "/search",
      "method" => "get",
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
      ["type" => "hidden", "name" => "type", "value" => $searchType,]
    );

    return $searchForm;
  }
}
