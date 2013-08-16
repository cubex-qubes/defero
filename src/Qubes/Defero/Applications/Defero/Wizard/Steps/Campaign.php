<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Wizard\Steps;

use Cubex\Core\Application\IController;
use Cubex\Core\Http\Request;
use Cubex\Core\Http\Response;
use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Cubex\Foundation\IRenderable;
use Qubes\Defero\Applications\Defero\Wizard\IWizardStep;
use Qubes\Defero\Applications\Defero\Wizard\IWizardStepIterator;

class Campaign implements IWizardStep
{
  /**
   * @return array
   */
  public function getRoutePatterns()
  {
    return ["/campaign/(.*)",];
  }

  /**
   * @return string
   */
  public function getBaseRoutePattern()
  {
    return "/campaign";
  }

  /**
   * @param Request        $request
   * @param Response       $response
   * @param IWizardStepIterator $subject
   * @param IController    $controller
   *
   * @return IRenderable
   */
  public function process(
    Request $request,
    Response $response,
    IWizardStepIterator $subject,
    IController $controller
  )
  {
    $nextStep  = $subject->getNextStep($this);
    $nextRoute = $controller->baseUri();

    if($nextStep instanceof IWizardStep)
    {
      $nextRoute .= $nextStep->getBaseRoutePattern();
    }

    return Redirect::to($nextRoute)->with(
      "msg", new TransportMessage("info", "redirect")
    );
  }
}
