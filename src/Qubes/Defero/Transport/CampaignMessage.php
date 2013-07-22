<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Transport;

class CampaignMessage extends ProcessMessage
{
  protected $_campaignId;
  protected $_dataAttributes;

  public function setCampaignId($campaignId)
  {
    $this->_campaignId = $campaignId;
    return $this;
  }

  public function getCampaignId()
  {
    return $this->_campaignId;
  }

  public function addAttribute($key, $value)
  {
    $this->_dataAttributes[$key] = $value;
    return $this;
  }

  public function addAttributes($attributes)
  {
    foreach($attributes as $k => $v)
    {
      $this->addAttribute($k, $v);
    }
    return $this;
  }

  public function getAttributes()
  {
    return $this->_dataAttributes;
  }

  public function getAttribute($name, $default = null)
  {
    if(isset($this->_dataAttributes[$name]))
    {
      return $this->_dataAttributes[$name];
    }
    return $default;
  }
}
