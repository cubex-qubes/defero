<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Wizard\Steps;

use Cubex\Core\Application\IController;
use Cubex\Core\Http\Request;
use Cubex\Core\Http\Response;
use Cubex\Foundation\IRenderable;
use Cubex\View\Impart;
use Qubes\Defero\Applications\Defero\Wizard\IWizardStep;
use Qubes\Defero\Applications\Defero\Wizard\IWizardStepIterator;

class CampaignTypeConfig implements IWizardStep
{
  /**
   * @return array
   */
  public function getRoutePatterns()
  {
    return ["/campaign-type-config/(.*)",];
  }

  /**
   * @return string
   */
  public function getBaseRoutePattern()
  {
    return "/campaign-type-config";
  }

  /**
   * @param Request             $request
   * @param Response            $response
   * @param IWizardStepIterator $steps
   * @param IController         $controller
   *
   * @return IRenderable
   */
  public function process(
    Request $request,
    Response $response,
    IWizardStepIterator $steps,
    IController $controller
  )
  {
    return new Impart($request->path());
  }
}
