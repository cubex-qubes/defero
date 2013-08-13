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
use Qubes\Defero\Components\MessageProcessor\Mappers\MessageProcessor;

class TypeAhead extends BaseController
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

  public function actionProcessors()
  {
    $query = $this->_getQuery();

    return $this->_sortResults($query, $this->_getProcessors($query));
  }

  private function _getCampaigns($query)
  {
    $campaignsSelect = $this->_getDisplayResultPattern("C", "name");

    return Campaign::collection()->whereLike("name", $query)
      ->setColumns([$campaignsSelect, "name"])
      ->orderByKeys(["key"])
      ->setOrderByQuery("name")
      ->getFieldValues('key');
  }

  private function _getProcessors($query)
  {
    $processorsSelect = $this->_getDisplayResultPattern("P", "name");

    return MessageProcessor::collection()->whereLike("name", $query)
      ->setColumns([$processorsSelect, "name"])
      ->orderByKeys(["key"])
      ->setOrderByQuery("name")
      ->getFieldValues('key');
  }

  private function _getContacts($query)
  {
    $contactSelect   = $this->_getDisplayResultPattern("c", "name");

    return Contact::collection()->whereLike("name", $query)
      ->setColumns([$contactSelect, "name"])
      ->setOrderByQuery("name")
      ->getFieldValues('key');
  }

  private function _getAll($query)
  {
    $contactSelect    = $this->_getDisplayResultPattern("c", "name");
    $campaignsSelect  = $this->_getDisplayResultPattern("C", "name");
    $processorsSelect = $this->_getDisplayResultPattern("P", "name");

    $queryData = [
      "SELECT %C FROM (
        (SELECT {$contactSelect}, %C FROM %T WHERE %C LIKE %~)
        UNION ALL
        (SELECT {$campaignsSelect}, %C FROM %T WHERE %C LIKE %~)
        UNION ALL
        (SELECT {$processorsSelect}, %C FROM %T WHERE %C LIKE %~)
        ) %T ORDER BY %C",
      "key",
      "name",
      Contact::tableName(),
      "name",
      $query,
      "name",
      Campaign::tableName(),
      "name",
      $query,
      "name",
      MessageProcessor::tableName(),
      "name",
      $query,
      "temp",
      "name"
    ];

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
      "CONCAT(%C, ': ', '{$prefix}', %C) as %C",
      $columnFrom,
      $id,
      $columnTo
    );
  }

  private function _getDeferoDb()
  {
    return DB::getAccessor("defero_db");
  }

  public function getRoutes()
  {
    return [
      "/all"        => "all",
      "/contacts"   => "contacts",
      "/campaigns"  => "campaigns",
      "/processors" => "processors",
    ];
  }
}
