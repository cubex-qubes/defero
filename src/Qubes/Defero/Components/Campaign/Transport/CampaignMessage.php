<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Components\Campaign\Transport;

use Qubes\Defero\Transport\ProcessMessage;

class CampaignMessage extends ProcessMessage
{
  protected $_campaignId;

  public function setCampaignId($campaignId)
  {
    $this->_campaignId = $campaignId;
    return $this;
  }

  public function getCampaignId()
  {
    return $this->_campaignId;
  }
}
