<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Cubex\Mapper\Database\RecordMapper;
use Cubex\Routing\Templates\ResourceTemplate;
use Qubes\Defero\Applications\Defero\Forms\ContactForm;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Campaign\Mappers\CampaignContact;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class CampaignContactsController extends ContactsController
{
  private $_campaignId;

  protected function _configure()
  {
    parent::_configure();
    $this->_campaignId = $this->getInt("id");
  }

  public function renderEdit($id)
  {
    return Redirect::to(sprintf("/contacts/%d/edit", $id))->with(
      "msg",
      new TransportMessage(
        "alert",
        "Changing this contact will affect all campaigns it's associated with."
      )
    );
  }

  public function renderIndex()
  {
    return Redirect::to($this->_request->offsetPath(0, 2));
  }

  public function actionDestroy($id)
  {
    $contact  = new Contact($id);
    $campaign = new Campaign($this->_campaignId);

    $campaignContact = CampaignContact::collection()->loadOneWhere(
      [
        "campaign_id" => $this->_campaignId,
        "contact_id"  => $id,
      ]
    );

    if(!$campaignContact instanceof RecordMapper)
    {
      if($id == $campaign->contactId)
      {
        $message = "Can't delete the default contact. Please edit the " .
          "campaign instead.";
      }
      else
      {
        $message = "Couldn't find contact id {$id}.";
      }

      return Redirect::to(sprintf("/campaigns/%d", $this->_campaignId))
        ->with('msg', new TransportMessage('error', $message));
    }

    $campaignContact->delete();

    return Redirect::to(sprintf("/campaigns/%d", $this->_campaignId))
      ->with(
        'msg',
        new TransportMessage(
          'info',
          "Contact '{$contact->name}' deleted from '{$campaign->name}'."
        )
      );
  }

  protected function _buildContactForm($id = null)
  {
    $action = "/campaigns/{$this->_campaignId}";
    $action .= $id ? "/contacts/{$id}" : "/contacts";

    $contactForm = new ContactForm("contact", $action);
    $contactForm->addHiddenElement("campaign_id", $this->_campaignId);

    return $contactForm->bindMapper(new Contact($id));
  }

  public function getRoutes()
  {
    return ResourceTemplate::getRoutes();
  }
}
