<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Contacts;

use Cubex\Mapper\Database\RecordCollection;
use Qubes\Defero\Applications\Defero\Enums\TypeAheadEnum;
use Qubes\Defero\Applications\Defero\Helpers\RecordCollectionPagination;
use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;
use Qubes\Defero\Applications\Defero\Views\Base\TypeAheadSearchFormView;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class ContactsView extends DeferoView
{
  public $pager;
  public $pagerInfo;
  /**
   * @var Contact[]
   */
  public $contacts;

  public $contactsSearch;

  private $_tableOnly;

  public function __construct(
    RecordCollection $contacts,
    RecordCollectionPagination $pagination,
    $tableOnly = false
  )
  {
    $pagination->setNumResultsPerPage($this->getResultsPerPage());

    $this->pager     = $pagination->getPager();
    $this->pagerInfo = $pagination->getInfo();
    $this->contacts  = $pagination->getPaginatedResults();

    $this->requireJsPackage("typeahead");
    $this->contactsSearch = new TypeAheadSearchFormView(
      TypeAheadEnum::CONTACTS(), "Search Contacts..."
    );

    $this->_tableOnly = (bool)$tableOnly;
  }

  public function tableOnly()
  {
    return $this->_tableOnly;
  }
}
