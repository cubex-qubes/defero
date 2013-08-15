<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Wizard;

use Cubex\Core\Application\IController;
use Cubex\Core\Http\Request;
use Cubex\Core\Http\Response;
use Cubex\Foundation\IRenderable;

interface IWizardObserver extends \SplObserver
{
  /**
   * @return string
   */
  public function getRoute();

  /**
   * @return string
   */
  public function getBaseRoute();

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
  );
}
