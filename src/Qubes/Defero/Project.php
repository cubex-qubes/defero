<?php
/**
 * @author gareth.evans
 */
namespace Qubes\Defero;

use Bundl\Debugger\DebuggerBundle;
use Qubes\Defero\Components\Campaign\Process\EmailService\SimulatedSend;
use Qubes\Defero\Components\Campaign\Process\EmailService\Smtp;
use Qubes\Defero\Components\DataSource\IDataSource;
use Qubes\Defero\Components\DataSource\RecordMapperDataSource;
use Qubes\Defero\Components\Campaign\Mappers\Campaign;
use Qubes\Defero\Components\Campaign\Rules\Delivery\DelayDeliveryRule;
use Qubes\Defero\Components\Campaign\Rules\Delivery\SetTimeDeliveryRule;
use Qubes\Defero\Components\Campaign\Rules\Delivery\TimeRangeDeliveryRule;
use Qubes\Defero\Components\Campaign\Rules\Filter\AttributeFilter;
use Qubes\Defero\Components\DataSource\DataSourceCollection;
use Qubes\Defero\Components\MessageProcessor\MessageProcessorCollection;

class Project extends \Cubex\Core\Project\Project
{
  public function name()
  {
    return "Defero";
  }

  protected function _configure()
  {
    MessageProcessorCollection::setMessageProcessors(
      [
      'attribute_filter' => new AttributeFilter(),
      'delay_send'       => new DelayDeliveryRule(),
      'time_send'        => new SetTimeDeliveryRule(),
      'range_send'       => new TimeRangeDeliveryRule(),
      'send_smtp'        => new Smtp(),
      'send_simulated'   => new SimulatedSend()
      ]
    );

    return parent::_configure();
  }

  public function getBundles()
  {
    //return [new DebuggerBundle()];
  }

  public function defaultApplication()
  {
    return new Applications\Defero\Defero();
  }
}
