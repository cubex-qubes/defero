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
use Qubes\Defero\Applications\Defero\Wizard\IWizardObserver;
use Qubes\Defero\Applications\Defero\Wizard\IWizardSubject;
use SplSubject;

class CampaignTypeConfig implements IWizardObserver
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
    return "/campaign-type-config/(.*)";
  }

  /**
   * @return string
   */
  public function getBaseRoute()
  {
    return "/campaign-type-config";
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
    return new Impart($request->path());
  }
}
