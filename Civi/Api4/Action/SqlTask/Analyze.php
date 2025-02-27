<?php

namespace Civi\Api4\Action\SqlTask;

use Civi\Api4\Generic\BasicBatchAction;
use Civi\Sqltasks\Analyzer;

class Analyze extends BasicBatchAction {
  public function __construct($entityName, $actionName) {
    parent::__construct($entityName, $actionName);
  }

  protected function doTask($item) {
    $task = \CRM_Sqltasks_BAO_SqlTask::findById($item['id']);
    $analyzer = new Analyzer($task);
    return ['id' => $item['id']] + $analyzer->run();
  }

}
