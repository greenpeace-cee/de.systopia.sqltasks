<?php

namespace Civi\Sqltasks\Actions;

use \Civi\ActionProvider\Action\AbstractAction;
use \Civi\ActionProvider\Exception\ExecutionException;
use \Civi\ActionProvider\Parameter\ParameterBagInterface;
use \Civi\ActionProvider\Parameter\SpecificationBag;
use \Civi\ActionProvider\Parameter\Specification;
use \Civi\Api4;

use CRM_Sqltasks_ExtensionUtil as E;

class RunSQLTask extends AbstractAction {

  /**
   * Run the action
   *
   * @param ParameterInterface $parameters
   * @param ParameterBagInterface $output
   * @return void
   */
  protected function doAction(ParameterBagInterface $parameters, ParameterBagInterface $output) {
    $task_id = $this->getTaskId() ?? $this->configuration->getParameter('task_id');

    try {
      civicrm_api3('Sqltask', 'get', [ 'id' => $task_id ]);
    } catch (\Exception $ex) {
      throw new ExecutionException(E::ts("Task with ID '$task_id' not found"));
    }

    $exec_params = [
      'id'          => $task_id,
      'input_val'   => $this->getInputValues($parameters),
      'log_to_file' => $this->configuration->getParameter('log_to_file'),
    ];

    try {
      $exec_result = civicrm_api3('Sqltask', 'execute', $exec_params);

      $output->setParameter('error_count', $exec_result['values']['error_count']);
      $output->setParameter('logs', $exec_result['values']['logs']);
      $output->setParameter('runtime', $exec_result['values']['runtime']);

      $return_parameter = $this->configuration->getParameter('return_parameter');

      if (isset($return_parameter) && isset($exec_result['values'][$return_parameter])) {
        $output->setParameter('return_value', $exec_result['values'][$return_parameter]);
      }
    } catch (\Exception $ex) {
      throw new ExecutionException(E::ts('Task execution failed'));
    }
  }

  /**
   * @return SpecificationBag
   */
  public function getConfigurationSpecification() {
    $specs = [];

    if (empty($this->getTaskId())) {
      $specs[] = new Specification(
        'task_id',        // string $name
        'Integer',        // string $dataType
        E::ts('Task ID'), // string $title
        TRUE,             // bool $required
        NULL,             // mixed $defaultValue
        NULL,             // string|null $fkEntity
        NULL,             // array $options
        FALSE             // bool $multiple
      );
    }

    $specs[] = new Specification(
      'log_to_file',
      'Boolean',
      E::ts('Log to file?'),
      FALSE,
      FALSE,
      NULL,
      NULL,
      FALSE
    );

    $specs[] = new Specification(
      'return_parameter',
      'String',
      E::ts('Return Parameter'),
      FALSE,
      NULL,
      NULL,
      NULL,
      FALSE
    );

    return new SpecificationBag($specs);
  }

  /**
   * @return SpecificationBag
   */
  public function getParameterSpecification() {
    $input_spec = $this->getInputSpec();

    if (empty($input_spec)) {
      return new SpecificationBag([
        new Specification(
          'input_values',
          'String',
          E::ts("Input values"),
          FALSE,
          NULL,
          NULL,
          NULL,
          TRUE
        ),
      ]);
    }

    if (is_array($input_spec)) {
      $specs = [];

      foreach ($input_spec as $input_param) {
        $name = $input_param['name'];
        $type = $input_param['type'];
        $default  = $input_param['default'] ?? NULL;

        $specs[] = new Specification(
          $name,
          $type,
          E::ts("Parameter $name"),
          is_null($default),
          $default,
          NULL,
          NULL,
          FALSE
        );
      }

      return new SpecificationBag($specs);
    }

    return new SpecificationBag([]);
  }

  /**
   * @return SpecificationBag
   */
  public function getOutputSpecification() {
    return new SpecificationBag([
      new Specification(
        'error_count',
        'Integer',
        E::ts('Error count'),
        FALSE,
        NULL,
        NULL,
        NULL,
        FALSE
      ),
      new Specification(
        'logs',
        'String',
        E::ts('Execution logs'),
        FALSE,
        NULL,
        NULL,
        NULL,
        FALSE
      ),
      new Specification(
        'runtime',
        'String',
        E::ts('Runtime'),
        FALSE,
        NULL,
        NULL,
        NULL,
        FALSE
      ),
      new Specification(
        'return_value',
        'String',
        E::ts('Return value'),
        FALSE,
        NULL,
        NULL,
        NULL,
        FALSE
      ),
    ]);
  }

  public function getTaskId() {
    return NULL;
  }

  public function getInputSpec() {
    return NULL;
  }

  /**
   * @param ParameterBagInterface $parameters
   * @return string|null
   */
  private function getInputValues($parameters) {
    $input_spec = $this->getInputSpec();

    if (empty($input_spec)) {
      $input_values = $parameters->getParameter('input_values');

      if (empty($input_values)) return NULL;

      return count($input_values) > 1 ? json_encode($input_values) : $input_values[0];
    } else {
      $exec_params = [];

      foreach ($input_spec as $input_param) {
        $name = $input_param['name'];
        $type = $input_param['type'] ?? 'String';
        $value = $parameters->getParameter($name);

        if (is_null($value) || $value === '') continue;

        switch ($type) {
          case 'String':
            $exec_params[$name] = (string) $value;
            break;

          case 'Float':
            $exec_params[$name] = (float) $value;
            break;

          case 'Boolean':
            $exec_params[$name] = (bool) $value;
            break;

          default:
            $exec_params[$name] = $value;
        }
      }

      return json_encode($exec_params);
    }

    return NULL;
  }

}
