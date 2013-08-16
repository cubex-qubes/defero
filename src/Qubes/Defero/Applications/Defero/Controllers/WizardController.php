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
  private $_wizardIterator;
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
