<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Controllers\BaseController;
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

    return $this->_sortResults(
      $query,
      array_merge(
        $this->_getContacts($query),
        $this->_getCampaigns($query)
      )
    );
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
      ->setColumns(["CONCAT(`name`, ' (O', `id`, ')') as `key`"])
      ->orderByKeys(["key"])
      ->getFieldValues('key');
  }

  private function _getContacts($query)
  {
    return Contact::collection()->whereLike("name", $query)
      ->setColumns(["CONCAT(`name`, ' (C', `id`, ')') as `key`"])
      ->orderByKeys(["key"])
      ->getFieldValues('key');
  }

  private function _getQuery()
  {
    return urldecode($this->_request->getVariables("q"));
  }

  private function _sortResults($value, $list)
  {
    return $list;

    /*$sortComparator = function($a, $b) use ($value)
    {
      $aLev = levenshtein($a, $value);
      $bLev = levenshtein($b, $value);

      return $bLev - $aLev;
    };

    usort($list, $sortComparator);

    return $list;*/
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
