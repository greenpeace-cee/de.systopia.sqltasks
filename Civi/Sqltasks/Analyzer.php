<?php

namespace Civi\Sqltasks;

use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\CreateStatement;
use PhpMyAdmin\SqlParser\Statements\DropStatement;
use PhpMyAdmin\SqlParser\Tests\Parser\DropStatementTest;

class Analyzer {

  private \CRM_Sqltasks_BAO_SqlTask $task;
  public function __construct(\CRM_Sqltasks_BAO_SqlTask $task) {
    $this->task = $task;
  }

  public function run() {
    $results = [
      'actions' => [],
    ];
    foreach (\CRM_Sqltasks_Action::getTaskActions($this->task) as $index => $action) {
      $result = [
        'type' => get_class($action),
      ];
      if ($action instanceof \CRM_Sqltasks_Action_RunSQL) {
        $result['sql'] = $this->analyzeSqlAction($action);
      }
      $results['actions'][$index] = $result;
    }
    return $results;
  }

  private function analyzeSqlAction(\CRM_Sqltasks_Action_RunSQL $action) {
    $results = [
      'created' => [],
      'dropped' => [],
    ];
    $sql = $action->getConfigValue('script', FALSE);
    $parser = new Parser($sql);
    foreach ($parser->statements as $statement) {
      if ($statement instanceof CreateStatement) {
        $results['created'][] = $statement->name->table;
      }
      if ($statement instanceof DropStatement) {
        $results['dropped'][] = $statement->fields[0]->table;
      }
    }
    return $results;
  }
}