<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Wizard;

use Cubex\Core\Application\IController;
use Cubex\Core\Http\Request;
use Cubex\Core\Http\Response;
use Cubex\Foundation\IRenderable;

interface IWizardStep
{
  /**
   * These are all the routes that should be associated with this step. If there
   * are conflicts with other steps it's first come first server.
   *
   * @return array
   */
  public function getRoutePatterns();

  /**
   * The base route is usually the simplest route defined in your
   * `getRoutePatterns()` method. This should have no matching parts, just a
   * URI, e.g;
   *
   * If getRoutePatterns returned;
   *
   * ```
   * return array(
   *   "/campaign",
   *   "/campaign/:campaignId",
   * );
   * ```
   *
   * Then getBaseRoutePattern would return;
   *
   * ```
   * return "/campaign";
   * ```
   *
   * @return string
   */
  public function getBaseRoutePattern();

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
  );
}
