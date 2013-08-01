<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Cli;

use Cubex\Data\Validator\Validator;
use Cubex\Text\TextTable;
use Qubes\Defero\Components\Campaign\Enums\CampaignType;
use Qubes\Defero\Components\Campaign\Enums\SendType;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;

class Campaigns extends MapperCliCommand
{
  /**
   * @valuerequired
   */
  public $reference;
  /**
   * @valuerequired
   */
  public $name;
  /**
   * @valuerequired
   */
  public $description;
  /**
   * @valuerequired
   */
  public $type;
  /**
   * @valuerequired
   */
  public $sendType;
  /**
   * @valuerequired
   * @validator int
   */
  public $contactId;
  /**
   * @valuerequired
   * @validator bool
   */
  public $active;
  /**
   * @valuerequired
   */
  public $language;

  protected function _configure()
  {
    $this->_getArgObjByName("type")->addValidator(
      Validator::VALIDATE_ENUM,
      [new CampaignType()]
    );

    $this->_getArgObjByName("sendType")->addValidator(
      Validator::VALIDATE_ENUM,
      [new SendType()]
    );
  }

  public function get()
  {
    $this->_throwIfNotSet("id");

    $campaign = new Campaign($this->id);

    echo (new TextTable())->setColumnHeaders(
      [
        "Id",
        "Ref",
        "Name",
        "Type",
        "Send Type",
      ]
    )->appendRows(
        [
          [
            $campaign->id(),
            $campaign->reference,
            $campaign->name,
            $campaign->type,
            $campaign->sendType,
          ],
        ]
      );
  }

  public function add()
  {
    $this->_throwIfNotSet("reference");
    $this->_throwIfNotSet("description");
    $this->_throwIfNotSet("name");
    $this->_throwIfNotSet("type");
    $this->_throwIfNotSet("sendType");

    $type     = strtoupper($this->type);
    $sendType = strtoupper($this->sendType);

    $campaign              = new Campaign();
    $campaign->reference   = $this->reference;
    $campaign->description = $this->description;
    $campaign->name        = $this->name;
    $campaign->type        = CampaignType::$type();
    $campaign->sendType    = SendType::$sendType();
    $campaign->contactId   = $this->contactId ? : 0;
    $campaign->active      = $this->active ? : 1;
    $campaign->saveChanges();

    echo "Added campaign id {$campaign->id()} ({$campaign->name})";
  }

  public function edit()
  {
    $this->_throwIfNotSet("id");

    $campaign = new Campaign($this->id);

    $updatables = [
      "reference",
      "description",
      "name",
      "type",
      "sendType",
      "contactId",
      "active",
    ];

    if($this->type)
    {
      $type       = strtoupper($this->type);
      $this->type = CampaignType::$type();
    }

    if($this->sendType)
    {
      $sendType       = strtoupper($this->sendType);
      $this->sendType = SendType::$sendType();
    }

    $this->_edit($campaign, $updatables);
  }

  public function remove()
  {
    $this->_throwIfNotSet("id");

    (new Campaign($this->id))->delete();

    echo "Campaign {$this->id} removed";
  }
}
