<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Campaigns;

use Cubex\Form\Form;
use Cubex\Foundation\Container;
use Cubex\Mapper\Database\RecordCollection;
use Qubes\Defero\Applications\Defero\Enums\TypeAheadEnum;
use Qubes\Defero\Applications\Defero\Helpers\LanguageHelper;
use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;
use Qubes\Defero\Applications\Defero\Views\Base\TypeAheadSearchFormView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class CampaignsView extends DeferoView
{
  /**
   * @var \stdClass[]
   */
  public $campaigns;

  public $sendTypeOptions;
  public $activeOptions;
  public $labelOptions;

  public $campaignsSearch;
  protected $_filterForm;

  public function __construct(array $campaigns, $options, $postData)
  {
    $this->requireJsPackage("campaigns");
    $this->requireJs("jquery-sortable-min");
    $this->requireCss("jquery-sortable-min");

    $this->campaigns       = $campaigns;
    $this->sendTypeOptions = $options['sendTypeOptions'];
    $this->activeOptions   = $options['activeOptions'];
    $this->labelOptions    = $options['labelOptions'];
    $this->postData        = $postData;

    $this->requireJsPackage("typeahead");
    $this->campaignsSearch = new TypeAheadSearchFormView(
      TypeAheadEnum::CAMPAIGNS(), "Search Campaigns..."
    );
  }

  public function filterForm()
  {
    if($this->_filterForm == null)
    {
      $labelOptions      = ['' => 'LABEL'] + $this->labelOptions;
      $sendTypeOptions   = ['' => 'SEND TYPE'] + $this->sendTypeOptions;
      $activeOptions     = ['' => 'ACTIVE'] + $this->activeOptions;
      $this->_filterForm = new Form('filterForm', '/campaigns/filter');
      $this->_filterForm->setDefaultElementTemplate('{{input}}');
      $this->_filterForm->addAttribute('class', 'form-search pull-right');
      $this->_filterForm->addSelectElement(
        'label',
        $labelOptions,
        idx($this->postData, 'label')
      );
      $this->_filterForm->addSelectElement(
        'sendType',
        $sendTypeOptions,
        idx($this->postData, 'sendType')
      );
      $this->_filterForm->addSelectElement(
        'active',
        $activeOptions,
        idx($this->postData, 'active')
      );
      $this->_filterForm->addSubmitElement('Filter');

      $this->_filterForm->getElement('submit')->addAttribute('class', 'btn');
      $this->_filterForm->getElement('sendType')->addAttribute(
        'class',
        'input-medium'
      );
      $this->_filterForm->getElement('active')->addAttribute(
        'class',
        'input-small'
      );
    }
    return $this->_filterForm;
  }

  public function inactiveLanguages($activeLanguages)
  {
    $allLanguages = LanguageHelper::getAvailableLanguages();
    return array_diff($allLanguages, $activeLanguages);
  }
}
