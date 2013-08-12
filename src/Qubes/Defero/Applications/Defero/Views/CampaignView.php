<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\HtmlElement;
use Qubes\Defero\Applications\Defero\Helpers\RecordCollectionPagination;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class CampaignView extends DeferoView
{
  /**
   * @var Campaign
   */
  public $campaign;

  /**
   * @var RecordCollection|Contact[]
   */
  public $contacts;

  private $_contactsPagination;

  public function __construct(
    Campaign $campaign, RecordCollection $contacts, $page
  )
  {
    $this->campaign = $campaign;
    $this->contacts = $contacts;

    $pagination = new RecordCollectionPagination($contacts, $page);
    $pagination->setUri(sprintf("/campaigns/%d/page", $campaign->id()));
    $this->_contactsPagination = $pagination;
  }

  public function getContactsView()
  {
    return (new ContactsView($this->contacts, $this->_contactsPagination, true))
      ->setHostController($this->getHostController());
  }
}
