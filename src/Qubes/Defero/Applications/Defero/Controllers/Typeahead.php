<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Controllers\BaseController;
use Cubex\Sprintf\ParseQuery;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class Typeahead extends BaseController
{
  public function canProcess()
  {
    if(!$this->_request->getVariables("q"))
    {
      return false;
    }

    return true;
  }

  public function actionAll()
  {
    $query = $this->_getQuery();

    return $this->_sortResults($query, $this->_getAll($query));
  }

  public function actionContacts()
  {
    $query = $this->_getQuery();

    return $this->_sortResults($query, $this->_getContacts($query));
  }

  public function actionCampaigns()
  {
    $query = $this->_getQuery();

    return $this->_sortResults($query, $this->_getCampaigns($query));
  }

  private function _getCampaigns($query)
  {
    return Campaign::collection()->whereLike("name", $query)
      ->setColumns(["CONCAT(`name`, ' (C', `id`, ')') as `key`"])
      ->orderByKeys(["key"])
      ->getFieldValues('key');
  }

  private function _getContacts($query)
  {
    return Contact::collection()->whereLike("name", $query)
      ->setColumns(["CONCAT(`name`, ' (c', `id`, ')') as `key`"])
      ->orderByKeys(["key"])
      ->getFieldValues('key');
  }

  private function _getAll($query)
  {
    $results = Campaign::conn()->getRows(
      ParseQuery::parse(
        Campaign::conn(),
        "SELECT %C FROM (
        (SELECT CONCAT(%C, ' (c', %C, ')') as %C FROM %T WHERE %C LIKE %~)
        UNION ALL
        (SELECT CONCAT(%C, ' (C', %C, ')') as %C FROM %T WHERE %C LIKE %~)
        ) %T ORDER BY %C",
        "key",
        "name",
        "id",
        "key",
        Contact::tableName(),
        "name",
        $query,
        "name",
        "id",
        "key",
        Campaign::tableName(),
        "name",
        $query,
        "temp",
        "key"
      )
    );

    return ppull($results, "key");
  }

  private function _getQuery()
  {
    return urldecode($this->_request->getVariables("q"));
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

  public function getRoutes()
  {
    return [
      "/all"       => "all",
      "/contacts"  => "contacts",
      "/campaigns" => "campaigns",
    ];
  }
}
