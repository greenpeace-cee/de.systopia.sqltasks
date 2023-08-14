<?php

namespace Civi\Utils\Sqltasks\WarningMessages\Actions;

use CRM_Sqltasks_Task;
use CRM_Utils_Type;

class ShowWhereTaskIsUsed extends Base {

  protected $requiredActionDataFields = [
    'taskId' => CRM_Utils_Type::T_INT,
  ];

  public function handleWarningWindowData($data) {
    $taskIds = CRM_Sqltasks_Task::findTaskIdsWhichUsesTask($this->params['action_data']['taskId']);

    $data['warningWindow']['title'] = 'Where tasks is used:';
    $data['warningWindow']['isShowYesButton'] = false;
    $data['warningWindow']['noButtonText'] = 'Back';

    if (empty($taskIds)) {
      $data['warningWindow']['message'] = "<p style='width: 300px;'>This task doesn't use by another tasks.</p>";
      return $data;
    }

    $data['warningWindow']['message'] = '<p style="width: 300px;">This task is used by tasks:</p>';
    $data['warningWindow']['message'] .= $this->prepareTaskLinks($taskIds);

    return $data;
  }

}
