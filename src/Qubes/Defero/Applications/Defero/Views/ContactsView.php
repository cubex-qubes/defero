<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\Mapper\Database\RecordCollection;
use Qubes\Defero\Applications\Defero\Enums\TypeaheadEnum;
use Qubes\Defero\Applications\Defero\Helpers\RecordCollectionPagination;
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

  public function __construct(
    RecordCollection $contacts,
    RecordCollectionPagination $pagination
  )
  {
    $pagination->setNumResultsPerPage($this->getResultsPerPage());

    $this->pager = $pagination->getPager();
    $this->pagerInfo = $pagination->getInfo();
    $this->contacts = $pagination->getPaginatedResults();

    $this->contactsSearch = new TypeaheadSearchFormView(
      TypeaheadEnum::CONTACTS(), "Search Contacts..."
    );
  }
}
