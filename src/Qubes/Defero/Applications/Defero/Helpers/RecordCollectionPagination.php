<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Helpers;

use Cubex\Mapper\Database\RecordCollection;

class RecordCollectionPagination extends Pagination
{
  protected $_recordCollection;

  public function __construct(RecordCollection $recordCollection, $page = 1)
  {
    $this->_recordCollection = $recordCollection;

    $this->setPage($page);
    $this->setNumResults($this->_recordCollection->count());
  }

  public function getPaginatedResults()
  {
    $this->paginate();

    return $this->_recordCollection->limit(
      ($this->_page * $this->_numResultsPerPage) - $this->_numResultsPerPage,
      $this->_numResultsPerPage
    );
  }
}
