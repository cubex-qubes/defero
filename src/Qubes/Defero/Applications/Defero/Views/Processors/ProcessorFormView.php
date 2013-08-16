<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views\Processors;

use Qubes\Defero\Applications\Defero\Forms\ProcessorForm;
use Qubes\Defero\Applications\Defero\Views\Base\DeferoView;

class ProcessorFormView extends DeferoView
{
  public $processorForm;

  public function __construct(ProcessorForm $processorForm)
  {
    $this->processorForm = $processorForm;
  }
}
