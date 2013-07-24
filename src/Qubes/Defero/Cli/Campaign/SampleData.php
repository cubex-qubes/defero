<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Defero\Cli\Campaign;

use Cubex\Cli\CliCommand;
use Qubes\Defero\Components\Campaign\Enums\CampaignType;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Contact\Mappers\Contact;

class SampleData extends CliCommand
{
  /**
   * @return int
   */
  public function execute()
  {
    $contact              = new Contact(1);
    $contact->name        = "John Smith";
    $contact->description = "John the test monkey";
    $contact->email       = "john@example.com";
    $contact->jobTitle    = "Test Monkey";
    $contact->reference   = "johnsmith";
    $contact->signature   = '
Kind Regards

John Smith
Test Monkey
Cubex Tester
0123-456-789';
    $contact->saveChanges();

    $campaign              = new Campaign(1);
    $campaign->reference   = "testcampaign";
    $campaign->name        = "Test Campaign";
    $campaign->description = "A test created with sample data";
    $campaign->active      = true;
    $campaign->contactId   = 1;
    $campaign->type        = CampaignType::ACTION;
    $campaign->saveChanges();

    $message = $campaign->message();

    $message->setLanguage('en');
    $message->subject = "This is my subject";
    $message->message = "Hello {{name}}. how are you today?";
    $message->saveChanges();

    $message->setLanguage('es');
    $message->subject = "Este es mi tema";
    $message->message = "Hola {{name}}. ¿Cómo estás hoy?";
    $message->saveChanges();

    $message->setLanguage('de');
    $message->subject = "Dies ist mein Thema";
    $message->message = "Hallo {{name}}. Wie geht es Ihnen heute?";
    $message->saveChanges();
  }
}
