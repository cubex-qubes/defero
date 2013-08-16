<?php
/**
 * @author gareth.evans
 */

namespace Qubes\Defero\Applications\Defero\Controllers;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Routing\Templates\ResourceTemplate;
use Qubes\Defero\Applications\Defero\Forms\ProcessorForm;
use Qubes\Defero\Applications\Defero\Helpers\RecordCollectionPagination;
use Qubes\Defero\Applications\Defero\Views\Processors\ProcessorFormView;
use Qubes\Defero\Applications\Defero\Views\Processors\ProcessorsView;
use Qubes\Defero\Applications\Defero\Views\Processors\ProcessorView;
use Qubes\Defero\Components\MessageProcessor\Mappers\MessageProcessor;

class ProcessorsController extends BaseDeferoController
{
  /**
   * Show a blank processor form
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Processors\ProcessorFormView
   */
  public function renderNew()
  {
    return new ProcessorFormView($this->_buildProcessorForm());
  }

  /**
   * Show a pre-populated processor form
   *
   * @param int           $id
   * @param ProcessorForm $processorForm
   *
   * @return ProcessorFormView
   */
  public function renderEdit($id, ProcessorForm $processorForm = null)
  {
    return new ProcessorFormView(
      $processorForm ? : $this->_buildProcessorForm($id)
    );
  }

  /**
   * Update an existing processor
   *
   * @param int $id
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Processors\ProcessorFormView
   */
  public function actionUpdate($id)
  {
    return $this->_updateProcessor($id);
  }

  /**
   * Delete a processor
   *
   * @param int $id
   *
   * @return \Cubex\Core\Http\Redirect
   */
  public function actionDestroy($id)
  {
    $processor = new MessageProcessor($id);
    $processor->forceLoad();
    $processor->delete();

    return Redirect::to('/processor')
      ->with(
        'msg',
        new TransportMessage('info', "Processor '{$processor->name}' deleted.")
      );
  }

  /**
   * Output a single processor
   *
   * @param int $id
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Processors\ProcessorView
   */
  public function renderShow($id)
  {
    return new ProcessorView(new MessageProcessor($id));
  }

  /**
   * Create a new processor
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Processors\ProcessorFormView
   */
  public function postCreate()
  {
    return $this->_updateProcessor();
  }

  /**
   * Show a paginated list of processors
   *
   * @param int $page
   *
   * @return \Qubes\Defero\Applications\Defero\Views\Processors\ProcessorsView
   */
  public function renderIndex($page = 1)
  {
    $processors = (new RecordCollection(new MessageProcessor()))
      ->setOrderBy("id");

    $pagination = new RecordCollectionPagination(
      $processors, $page
    );
    $pagination->setUri("/processors/page");

    return new ProcessorsView($processors, $pagination);
  }

  /**
   * Helper method to handle create and update of processors. Will redirect to
   * the specific processors on success with a message. If there are any
   * validation or CSRF errors we render the form again with information.
   *
   * @param null|int $id
   *
   * @return ProcessorFormView
   */
  private function _updateProcessor($id = null)
  {
    $form = $this->_buildProcessorForm($id);
    $form->hydrate($this->request()->postVariables());

    if($form->isValid() && $form->csrfCheck(true))
    {
      $form->saveChanges();

      $msg = "Processor '{$form->name}'";
      $msg .= $id ? " Updated" : " Created";

      return Redirect::to("/processors/{$form->getMapper()->id()}")
        ->with("msg", new TransportMessage("info", $msg));
    }

    return $this->renderEdit($id, $form);
  }

  /**
   * Instantiates the form and binds the mapper. Also sets up the action based
   * on an id existing or not.
   *
   * @param null|int $id
   *
   * @return ProcessorForm
   */
  private function _buildProcessorForm($id = null)
  {
    $action = $id ? "/processors/{$id}" : "/processors";

    return (new ProcessorForm("processor", $action))
      ->bindMapper(new MessageProcessor($id));
  }

  public function getRoutes()
  {
    return ResourceTemplate::getRoutes();
  }
}
