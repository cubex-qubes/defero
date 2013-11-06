<?php
/**
 * @author gareth.evans
 */
namespace Qubes\Defero\Applications\Defero;

use Cubex\Core\Application\Application;
use Themed\Sidekick\SidekickTheme;

class Defero extends Application
{
  public function name()
  {
    return "Defero";
  }

  public function description()
  {
    return "Mailer setup and configuration";
  }

  public function getTheme()
  {
    return new SidekickTheme();
  }

  public function defaultController()
  {
    return new Controllers\DeferoController();
  }

  public function getRoutes()
  {
    return [
      "/campaigns/:id@num/message/(.*)"     => "CampaignMessage",
      "/campaigns/:id@num/source/(.*)"      => "CampaignSource",
      "/campaigns/:cid@num/processors/(.*)" => "CampaignProcessors",
      "/campaigns/:id@num/contacts/(.*)"    => "CampaignContacts",
      "/campaigns/(.*)"                     => "Campaigns",
      "/contacts/(.*)"                      => "Contacts",
      "/typeahead/(.*)"                     => "TypeAhead",
      "/search/(.*)"                        => "Search",
      "/wizard/(.*)"                        => "Wizard",
    ];
  }
}
