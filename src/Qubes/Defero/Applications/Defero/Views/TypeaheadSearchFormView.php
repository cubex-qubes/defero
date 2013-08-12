<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\View\HtmlElement;
use Cubex\View\ViewModel;
use Qubes\Defero\Applications\Defero\Enums\TypeaheadEnum;

class TypeaheadSearchFormView extends ViewModel
{
  private $_typeaheadEnum;

  public function __construct(TypeaheadEnum $typeaheadEnum = null)
  {
    $this->_typeaheadEnum = $typeaheadEnum ? : TypeaheadEnum::ALL();
  }

  public function render()
  {
    $jsTriggerClass = "js-defero-typeahead-";

    switch((string)$this->_typeaheadEnum)
    {
      case TypeaheadEnum::CONTACTS:
        $jsTriggerClass .= "contacts";
        break;
      case TypeaheadEnum::CAMPAIGNS:
        $jsTriggerClass .= "campaigns";
        break;
      case TypeaheadEnum::ALL:
      default:
        $jsTriggerClass .= "all";
        break;
    }

    $searchForm = (new HtmlElement(
      "form",
      [
        "class"  => "navbar-search pull-right",
        "action" => "/search",
        "method" => "post",
      ]
    ))->nestElement(
        "input",
        [
          "id"           => "nav-search",
          "type"         => "text",
          "name"         => "q",
          "class"        => "search-query {$jsTriggerClass}",
          "placeholder"  => "Search...",
          "autocomplete" => "off",
        ]
      );

    return $searchForm;
  }
}
