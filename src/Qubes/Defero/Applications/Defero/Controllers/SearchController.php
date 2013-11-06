<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Mapper\Database\RecordMapper;
use Qubes\Defero\Applications\Defero\Views\Search\SearchView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Contact\Mappers\Contact;
use Qubes\Defero\Lib\SearchLibrary;

class SearchController extends BaseDeferoController
{
  public function canProcess()
  {
    if(!$this->_request->getVariables("q"))
    {
      return false;
    }

    return true;
  }

  public function renderSearch()
  {
    $query = $this->_getQuery();
    $this->_tryTagMatchAndRedirect($query);

    if($this->canProcess())
    {
      $data = $this->getData($query, $this->_getType());
      if($data === null)
      {
        return 'Type not recognised';
      }
      return new SearchView($this->_sortResults($query, $data));
    }
    else
    {
      echo "No tag, do some more searching";
    }
  }

  public function getData($query, $type)
  {
    $data = null;
    switch($type)
    {
      case 'all':
        $data = SearchLibrary::getAll($query);
        break;
      case 'contacts':
        $data = SearchLibrary::getContacts($query);
        break;
      case 'campaigns':
        $data = SearchLibrary::getCampaigns($query);
        break;
    }

    foreach($data as $k => $item)
    {
      $data[$k] = ['text' => $item, 'url' => $this->_tryGetMapperUrl($item)];
    }

    return $data;
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

  /**
   * @param string $query
   */
  private static function _tryTagMatchAndRedirect($query)
  {
    if(preg_match("/^(?:.* |)(?<tag>[CcP][0-9]+)(?:.* |)$/", $query, $matches))
    {
      $tag = $matches['tag'];
      $id  = substr($tag, 1);
      $map = null;
      $ep  = null;

      switch(substr($tag, 0, 1))
      {
        case 'C':
          $ep  = "/campaigns/%d";
          $map = new Campaign($id);
          break;
        case 'c':
          $ep  = "/contacts/%d";
          $map = new Contact($id);
          break;
      }

      if($map instanceof RecordMapper)
      {
        if($map->exists())
        {
          Redirect::to(sprintf($ep, $map->id()))->now();
        }
      }
    }
  }

  private static function _tryGetMapperUrl($query)
  {
    if(preg_match("/^(?:.* |)(?<tag>[CcP][0-9]+)(?:.* |)$/", $query, $matches))
    {
      $tag = $matches['tag'];
      $id  = substr($tag, 1);
      $map = null;
      $ep  = null;

      switch(substr($tag, 0, 1))
      {
        case 'C':
          $ep  = "/campaigns/%d";
          $map = new Campaign($id);
          break;
        case 'c':
          $ep  = "/contacts/%d";
          $map = new Contact($id);
          break;
      }

      if($map instanceof RecordMapper)
      {
        if($map->exists())
        {
          return sprintf($ep, $map->id());
        }
      }
    }
    return null;
  }

  public function defaultAction()
  {
    return "search";
  }
}
