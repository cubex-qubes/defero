<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Mapper\Database\RecordMapper;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Contact\Mappers\Contact;
use Qubes\Defero\Components\MessageProcessor\Mappers\MessageProcessor;

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

    echo "No tag, do some more searching";
  }

  /**
   * @param string $query
   */
  private function _tryTagMatchAndRedirect($query)
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
        case 'P':
          $ep  = "/processes/%d";
          $map = new MessageProcessor($id);
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

  private function _getQuery()
  {
    return urldecode($this->_request->getVariables("q"));
  }

  public function defaultAction()
  {
    return "search";
  }
}
