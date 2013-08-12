<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Cubex\View\HtmlElement;
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
