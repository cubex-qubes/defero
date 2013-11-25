<?php
/**
 * Created by PhpStorm.
 * User: tom.kay
 * Date: 12/09/13
 * Time: 13:47
 */

namespace Qubes\Defero\Lib;

use Cubex\Facade\DB;
use Cubex\Sprintf\ParseQuery;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class SearchLibrary
{
  private static function _getDisplayResultPattern(
    $prefix, $columnFrom, $columnTo = "key", $id = "id"
  )
  {
    return ParseQuery::parse(
      DB::getAccessor("defero_db"),
      "CONCAT(%C, ': ', '{$prefix}', %C) as %C",
      $columnFrom,
      $id,
      $columnTo
    );
  }

  public static function getCampaigns($query)
  {
    $campaignsSelect = self::_getDisplayResultPattern("C", "name");

    return Campaign::collection()->whereLike("name", $query)
    ->setColumns([$campaignsSelect, "name"])
    ->orderByKeys(["key"])
    ->setOrderByQuery("name")
    ->getFieldValues('key');
  }

  public static function getContacts($query)
  {
    $contactSelect = self::_getDisplayResultPattern("c", "name");

    return Contact::collection()->whereLike("name", $query)
    ->setColumns([$contactSelect, "name"])
    ->setOrderByQuery("name")
    ->getFieldValues('key');
  }

  public static function getAll($query)
  {
    $contactSelect   = self::_getDisplayResultPattern("c", "name");
    $campaignsSelect = self::_getDisplayResultPattern("C", "name");

    $queryData = [
      "SELECT %C FROM (
        (SELECT {$contactSelect}, %C FROM %T WHERE %C LIKE %~)
        UNION ALL
        (SELECT {$campaignsSelect}, %C FROM %T WHERE %C LIKE %~)
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
      "temp",
      "name"
    ];

    $results = DB::getAccessor("defero_db")->getRows(
      ParseQuery::parse(DB::getAccessor("defero_db"), $queryData)
    );

    return ppull($results, "key");
  }
}
