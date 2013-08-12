<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Views;

use Qubes\Defero\Applications\Defero\Forms\ProcessorForm;

class ProcessorFormView extends DeferoView
{
  public $processorForm;

  public function __construct(ProcessorForm $processorForm)
  {
    $this->processorForm = $processorForm;
  }
}
