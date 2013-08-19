<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Core\Http\Redirect;
use Cubex\Routing\StdRoute;
use Cubex\View\RenderGroup;
use Qubes\Defero\Applications\Defero\Views\Wizard\StepsInfo;
use Qubes\Defero\Applications\Defero\Wizard\IWizardStep;
use Qubes\Defero\Applications\Defero\Wizard\WizardStepIterator;

class WizardController extends BaseDeferoController
{
  /**
   * @var WizardStepIterator $_wizardIterator
   */
  private $_wizardIterator;

  /**
   * @var IWizardStep[]
   */
  private $_routes = [];

  public function preProcess()
  {
    $this->_wizardIterator = new WizardStepIterator();

    $wizardConfig = $this->getConfig()->get("wizard");
    if($wizardConfig === null)
    {
      throw new \Exception(
        "The wizard config must be set before using the mailer campaign wizard."
      );
    }

    $steps = $wizardConfig->getArr("steps");
    if($steps === null)
    {
      throw new \Exception(
        "The wizard steps must be set before using the mailer campaign wizard."
      );
    }

    foreach($steps as $step)
    {
      $stepObject = new $step;
      if($stepObject instanceof IWizardStep)
      {
        $this->_wizardIterator->addStep($stepObject);

        foreach($stepObject->getRoutePatterns() as $routePattern)
        {
          $this->_routes[] = (new StdRoute($routePattern, "run"))
            ->addRouteData("step", $stepObject);
        }
      }
    }
  }

  public function actionRun(IWizardStep $step)
  {
    $canProcess = $step->canProcess(
      $this->_request->getVariables(),
      $this->_request->postVariables(),
      $this->_request->getData()
    );

    if(! $canProcess)
    {
      return $this->actionGoToFirstStep();
    }

    foreach($this->_wizardIterator as $wizardStep)
    {
      if($wizardStep === $step)
      {
        break;
      }
    }

    $return = $step->process(
      $this->_request,
      $this->_response,
      $this->_wizardIterator,
      $this
    );

    if(! $return instanceof Redirect)
    {
      $return = new RenderGroup(new StepsInfo($this->_wizardIterator), $return);
    }

    return $return;
  }

  /**
   * If we go directly to the wizard page we redirect to the first step.
   *
   * @return Redirect
   */
  public function actionGoToFirstStep()
  {
    $uri = sprintf(
      "%s%s",
      $this->baseUri(),
      $this->_wizardIterator->offsetGet(0)->getBaseUri()
    );

    return \Cubex\Facade\Redirect::to($uri);
  }

  public function defaultAction()
  {
    return "GoToFirstStep";
  }

  public function getRoutes()
  {
    return $this->_routes;
  }
}
