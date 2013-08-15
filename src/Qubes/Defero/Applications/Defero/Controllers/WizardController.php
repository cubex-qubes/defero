<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Routing\StdRoute;
use Qubes\Defero\Applications\Defero\Wizard\IWizardObserver;
use Qubes\Defero\Applications\Defero\Wizard\WizardSubject;

class WizardController extends BaseDeferoController
{
  private $_wizardSubject;
  private $_routes;

  public function preProcess()
  {
    $this->_wizardSubject = new WizardSubject();

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
      if($stepObject instanceof IWizardObserver)
      {
        $this->_wizardSubject->attach($stepObject);
        $this->_routes[] = (new StdRoute(
          $stepObject->getRoute(), "run"
        ))->addRouteData("step", $stepObject);
      }
    }
  }

  public function actionRun(IWizardObserver $step)
  {
    return $step->process(
      $this->_request,
      $this->_response,
      $this->_wizardSubject,
      $this
    );
  }

  public function renderIndex()
  {
    echo "index";
  }

  public function defaultAction()
  {
    return "index";
  }

  public function getRoutes()
  {
    return $this->_routes;
  }
}
