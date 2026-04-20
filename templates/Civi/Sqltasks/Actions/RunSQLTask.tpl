{literal}
namespace Civi\Sqltasks\Actions;

class RunSQLTask_{/literal}{$id}{literal} extends RunSQLTask {

  const TASK_ID = {/literal}{$id}{literal};

  private $inputSpec;

  public function __construct() {
    $task = \Civi\Api4\SqlTask::get(FALSE)
    ->addSelect('input_spec')
    ->addWhere('id', '=', self::TASK_ID)
    ->execute()
    ->first();

    $this->inputSpec = json_decode($task['input_spec'], TRUE);
    parent::__construct();
  }

  public function getTaskId() {
    return self::TASK_ID;
  }

  public function getInputSpec() {
    return $this->inputSpec;
  }

}
{/literal}
