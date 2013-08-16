<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Processors;

use Cubex\View\HtmlElement;
use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;
use Qubes\Defero\Components\MessageProcessor\Mappers\MessageProcessor;

class ProcessorView extends DeferoView
{
  /**
   * @var MessageProcessor
   */
  public $processor;

  public function __construct(MessageProcessor $processor)
  {
    $this->processor = $processor;
  }
}
