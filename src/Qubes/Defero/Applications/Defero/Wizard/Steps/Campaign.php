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
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignFormView;
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
    if($request->is("POST"))
    {
      return $this->_handlePost($request, $response, $steps, $controller);
    }

    return $this->_handleGet($request, $response, $steps, $controller);
  }

  /**
   * @param Request             $request
   * @param Response            $response
   * @param IWizardStepIterator $steps
   * @param IController         $controller
   *
   * @return IRenderable
   */
  private function _handlePost(
    Request $request,
    Response $response,
    IWizardStepIterator $steps,
    IController $controller
  )
  {
    return Redirect::to($steps->getNextStep()->getBaseRoutePattern())->with(
      "msg",
      new TransportMessage("info", "You got redirect after sending some post!")
    );
  }

  /**
   * @param Request             $request
   * @param Response            $response
   * @param IWizardStepIterator $steps
   * @param IController         $controller
   *
   * @return IRenderable
   */
  private function _handleGet(
    Request $request,
    Response $response,
    IWizardStepIterator $steps,
    IController $controller
  )
  {
    return new CampaignFormView(
      \Qubes\Defero\Components\Campaign\Mappers\Campaign::buildCampaignForm(
        sprintf("%s%s", $controller->baseUri(), $this->getBaseRoutePattern())
      )
    );
  }
}
