<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Controllers\BaseController;
use Cubex\Facade\DB;
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
    $campaignsSelect = $this->_getDisplayResultPattern("C", "name");

    return Campaign::collection()->whereLike("name", $query)
      ->setColumns([$campaignsSelect, "name"])
      ->orderByKeys(["key"])
      ->setOrderByQuery($this->_getOrderByPattern($query, "name"))
      ->getFieldValues('key');
  }

  private function _getContacts($query)
  {
    $contactSelect   = $this->_getDisplayResultPattern("c", "name");

    return Contact::collection()->whereLike("name", $query)
      ->setColumns([$contactSelect, "name"])
      ->setOrderByQuery($this->_getOrderByPattern($query, "name"))
      ->getFieldValues('key');
  }

  private function _getAll($query)
  {
    $contactSelect   = $this->_getDisplayResultPattern("c", "name");
    $campaignsSelect = $this->_getDisplayResultPattern("C", "name");

    $orderQueryData = $this->_getOrderByPattern($query, "name", true);
    $orderQuery     = array_shift($orderQueryData);

    $queryData = [
      "SELECT %C FROM (
        (SELECT {$contactSelect}, %C FROM %T WHERE %C LIKE %~)
        UNION ALL
        (SELECT {$campaignsSelect}, %C FROM %T WHERE %C LIKE %~)
        ) %T ORDER BY {$orderQuery}",
      "key",
      "name",
      Contact::tableName(),
      "name",
      $query,
      "name",
      Campaign::tableName(),
      "name",
      $query,
      "temp"
    ];

    $queryData = array_merge($queryData, $orderQueryData);

    $results = $this->_getDeferoDb()->getRows(
      ParseQuery::parse($this->_getDeferoDb(), $queryData)
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

  private function _getDisplayResultPattern(
    $prefix, $columnFrom, $columnTo = "key", $id = "id"
  )
  {
    return ParseQuery::parse(
      $this->_getDeferoDb(),
      "CONCAT('{$prefix}', %C, ' ', %C) as %C",
      $id,
      $columnFrom,
      $columnTo
    );
  }

  private function _getOrderByPattern($query, $compare, $unParsed = false)
  {
    $queryData = [
      "CASE
      WHEN %C LIKE %> THEN 1
      WHEN %C LIKE %~ THEN 2
      WHEN %C LIKE %> THEN 3
      WHEN %C LIKE %~ THEN 4
      ELSE 5 END,
      %C",
      $compare,
      "{$query} ",
      $compare,
      " {$query} ",
      $compare,
      $query,
      $compare,
      $query,
      $compare,
    ];

    if($unParsed)
    {
      return $queryData;
    }

    return ParseQuery::parse($this->_getDeferoDb(), $queryData);
  }

  private function _getDeferoDb()
  {
    return DB::getAccessor("defero_db");
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
