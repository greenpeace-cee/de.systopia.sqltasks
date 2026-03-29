<?php

use CRM_Sqltasks_ExtensionUtil as E;

class CRM_Sqltasks_Page_Analyze extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(E::ts("SQL Task Analyzer"));
    $analyzer = \Civi\Api4\SqlTask::analyze(FALSE)
      ->addWhere('archive_date', 'IS NULL');
    if (!empty(CRM_Utils_Request::retrieve('category', 'String'))) {
      $analyzer->addWhere('category', '=', CRM_Utils_Request::retrieve('category', 'String'));
    }
    if (!empty(CRM_Utils_Request::retrieve('enabled', 'Integer'))) {
      $analyzer->addWhere('enabled', '=', CRM_Utils_Request::retrieve('enabled', 'Integer'));
    }
    $result = $analyzer->execute();
    $taskTables = [];
    foreach ($result as $task) {
      foreach ($task['actions'] ?? [] as $action) {
        if ($action['type'] == 'CRM_Sqltasks_Action_RunSQL' || $action['type'] == 'CRM_Sqltasks_Action_PostSQL') {
          $taskTables[$task['id']] = array_unique(array_merge($taskTables[$task['id']] ?? [], $action['sql']['created'], $action['sql']['dropped']));
        }
      }
    }

    $reusedTables = $this->findCommonValues($taskTables);
    $this->assign('reusedTables', json_encode($reusedTables, JSON_PRETTY_PRINT));

    parent::run();
  }

  private function findCommonValues(array $input): array {
    $valueToKeys = [];

    // Map each value to the keys where it appears.
    foreach ($input as $key => $values) {
      foreach ($values as $value) {
        $valueToKeys[$value][] = $key;
      }
    }

    // Filter out values that appear in only one key.
    $result = [];
    foreach ($valueToKeys as $value => $keys) {
      if (count($keys) > 1) {
        $result[$value] = $keys;
      }
    }

    return $result;
  }

}
