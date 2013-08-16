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
   * @return string
   */
  public function getName();

  /**
   * @return string
   */
  public function getDescription();

  /**
   * These are all the routes that should be associated with this step. If there
   * are conflicts with other steps it's first come first server.
   *
   * @return array
   */
  public function getRoutePatterns();

  /**
   * The base uri is usually the simplest route defined in your
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
   * Then getBaseUri would return;
   *
   * ```
   * return "/campaign";
   * ```
   *
   * NOTE: the script redirects directly to this URI
   *
   * @return string
   */
  public function getBaseUri();

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
  );
}
