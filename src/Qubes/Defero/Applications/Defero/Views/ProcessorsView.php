<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\HtmlElement;
use Qubes\Defero\Applications\Defero\Enums\TypeAheadEnum;
use Qubes\Defero\Applications\Defero\Helpers\RecordCollectionPagination;
use Qubes\Defero\Components\MessageProcessor\Mappers\MessageProcessor;

class ProcessorsView extends DeferoView
{
  public $pager;
  public $pagerInfo;
  /**
   * @var MessageProcessor[]
   */
  public $processors;

  public $processorsSearch;

  public function __construct(
    RecordCollection $processors,
    RecordCollectionPagination $pagination
  )
  {
    $pagination->setNumResultsPerPage($this->getResultsPerPage());

    $this->pager      = $pagination->getPager();
    $this->pagerInfo  = $pagination->getInfo();
    $this->processors = $pagination->getPaginatedResults();

    $this->processorsSearch = new TypeAheadSearchFormView(
      TypeAheadEnum::PROCESSORS(), "Search Processors..."
    );
  }
}
