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
use Qubes\Defero\Applications\Defero\Forms\CampaignForm;
use Qubes\Defero\Applications\Defero\Helpers\WizardHelper;
use Qubes\Defero\Applications\Defero\Views\Campaigns\CampaignFormView;
use Qubes\Defero\Components\Campaign\Mappers\Campaign as CampaignMapper;
use Qubes\Defero\Applications\Defero\Wizard\IWizardStep;
use Qubes\Defero\Applications\Defero\Wizard\IWizardStepIterator;

class Campaign implements IWizardStep
{
  /**
   * If the process has request data dependencies use this method to ensure
   * they're available. If they're not, return false.
   *
   * @param array $get
   * @param array $post
   * @param array $routedData
   *
   * @return bool
   */
  public function canProcess(array $get, array $post, array $routedData)
  {
    return true;
  }

  public function getName()
  {
    return "Add Campaign";
  }

  public function getDescription()
  {
    return "Add a new or select an existsing campaign to configure.";
  }

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
  public function getBaseUri()
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
    $form = $this->_buildCampaignForm($controller->baseUri(), $request);
    $form->hydrate($request->postVariables());

    if($form->isValid() && $form->csrfCheck(true))
    {
      $form->saveChanges();

      $msg = sprintf("Campaign '%s' Created", $form->name);
      $id  = $form->getMapper()->id();

      $uri = sprintf(
        "%s%s?campaign_id=%d&%s",
        $controller->baseUri(),
        $steps->getNextStep()->getBaseUri(),
        $id,
        WizardHelper::getGetRequestString($request, ["campaign_id"])
      );

      return Redirect::to($uri)->with(
        "msg", new TransportMessage("info", $msg)
      );
    }

    return new CampaignFormView($form);
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
      $this->_buildCampaignForm($controller->baseUri(), $request)
    );
  }

  /**
   * @param string  $baseUri
   * @param Request $request
   *
   * @return CampaignForm
   */
  private function _buildCampaignForm($baseUri, Request $request)
  {
    return CampaignMapper::buildCampaignForm(
      sprintf(
        "%s%s?%s",
        $baseUri,
        $this->getBaseUri(),
        WizardHelper::getGetRequestString($request)
      )
    );
  }
}
