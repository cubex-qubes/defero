<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Controllers\BaseController;
use Qubes\Defero\Lib\SearchLibrary;

class TypeAheadController extends BaseController
{
  public function canProcess()
  {
    if(!$this->_request->getVariables("q"))
    {
      return false;
    }

    return true;
  }

  public function actionSearchAll()
  {
    $query = $this->_getQuery();
    $list  = SearchLibrary::getAll($query);
    return $this->_sortResults($query, $list);
  }

  public function actionSearchContacts()
  {
    $query = $this->_getQuery();
    $list  = SearchLibrary::getContacts($query);
    return $this->_sortResults($query, $list);
  }

  public function actionSearchCampaigns()
  {
    $query = $this->_getQuery();
    $list  = SearchLibrary::getCampaigns($query);
    return $this->_sortResults($query, $list);
  }

  /**
   * Sort method, in case we ever want to override mysql's default sorting.
   *
   * @param string $value
   * @param array  $list
   *
   * @return array
   */
  private function _sortResults($value, array $list)
  {
    return $list;
  }

  private function _getQuery()
  {
    return urldecode($this->getVariables("q"));
  }

  private function _getType()
  {
    return urldecode($this->getVariables("type"));
  }

  public function getRoutes()
  {
    return [
      "/all"       => "searchall",
      "/contacts"  => "searchcontacts",
      "/campaigns" => "searchcampaigns",
    ];
  }
}
