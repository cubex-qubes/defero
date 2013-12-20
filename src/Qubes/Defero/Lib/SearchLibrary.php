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

    $data = Campaign::collection()
      ->loadWhereAppend(
        "%C LIKE %~ OR %C LIKE %~", 'name', $query, 'label', $query
      )
      ->setColumns([$campaignsSelect, "name"])
      ->orderByKeys(["key"])
      ->setOrderByQuery("name")
      ->jsonSerialize();

    foreach($data as $k => $d)
    {
      $data[$k]['type'] = 'Campaign';
    }
    return $data;
  }

  public static function getContacts($query)
  {
    $contactSelect = self::_getDisplayResultPattern("c", "name");

    $data = Contact::collection()->whereLike("name", $query)
      ->setColumns([$contactSelect, "name"])
      ->setOrderByQuery("name")
      ->jsonSerialize();

    foreach($data as $k => $d)
    {
      $data[$k]['type'] = 'Contact';
    }

    return $data;
  }

  public static function getAll($query)
  {
    $contactSelect   = self::_getDisplayResultPattern("c", "name");
    $campaignsSelect = self::_getDisplayResultPattern("C", "name");

    $queryData = [
      "SELECT * FROM (
        (SELECT {$contactSelect}, %C, 'Contact' as `type` FROM %T WHERE %C LIKE %~)
        UNION ALL
        (SELECT {$campaignsSelect}, %C, 'Campaign' as `type` FROM %T WHERE %C LIKE %~ OR %C LIKE %~)
        ) %T ORDER BY %C",
      "name",
      Contact::tableName(),
      "name",
      $query,
      "name",
      Campaign::tableName(),
      "name",
      $query,
      "label",
      $query,
      "temp",
      "name"
    ];

    $results = DB::getAccessor("defero_db")->getRows(
      ParseQuery::parse(DB::getAccessor("defero_db"), $queryData)
    );

    return $results;
  }
}
