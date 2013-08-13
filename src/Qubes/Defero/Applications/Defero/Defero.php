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
      "/campaigns/:id@num/contacts/(.*)" => "CampaignContacts",
      "/campaigns/(.*)"                  => "Campaigns",
      "/contacts/(.*)"                   => "Contacts",
      "/processors/(.*)"                 => "Processors",
      "/processors/rules/(.*)"           => "Rules",
      "/processors/processes/(.*)"       => "Processes",
      "/typeahead/(.*)"                  => "TypeAhead",
      "/search/(.*)"                     => "Search",
    ];
  }
}
