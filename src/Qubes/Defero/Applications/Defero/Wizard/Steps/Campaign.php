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
use Qubes\Defero\Applications\Defero\Wizard\IWizardObserver;
use Qubes\Defero\Applications\Defero\Wizard\IWizardSubject;
use SplSubject;

class Campaign implements IWizardObserver
{
  /**
   * @param SplSubject $subject
   *
   * @return void
   */
  public function update(SplSubject $subject)
  {
    if($subject instanceof IWizardSubject)
    {
    }
  }

  /**
   * @return string
   */
  public function getRoute()
  {
    return "/campaign/(.*)";
  }

  /**
   * @return string
   */
  public function getBaseRoute()
  {
    return "/campaign";
  }

  /**
   * @param Request        $request
   * @param Response       $response
   * @param IWizardSubject $subject
   * @param IController    $controller
   *
   * @return IRenderable
   */
  public function process(
    Request $request,
    Response $response,
    IWizardSubject $subject,
    IController $controller
  )
  {
    $nextStep  = $subject->getNextStep($this);
    $nextRoute = $controller->baseUri();

    if($nextStep instanceof IWizardObserver)
    {
      $nextRoute .= $nextStep->getBaseRoute();
    }

    return Redirect::to($nextRoute)->with(
      "msg", new TransportMessage("info", "redirect")
    );
  }
}
