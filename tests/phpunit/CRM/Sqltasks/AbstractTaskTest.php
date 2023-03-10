<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use PHPUnit\Framework\TestCase;

/**
 * Base class for task tests
 *
 * @group headless
 */
abstract class CRM_Sqltasks_AbstractTaskTest extends TestCase implements HeadlessInterface, HookInterface {

  /**
   * @var array
   */
  protected $log;

  public function setUpHeadless() {
    return \Civi\Test::headless()
      ->uninstallMe(__DIR__)
      ->installMe(__DIR__)
      ->apply(TRUE);
  }

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  protected function createAndExecuteTask(array $data, array $params = []) {
    $task = new CRM_Sqltasks_Task(NULL, $data);
    $task->store();
    $taskExecutionResult = $task->execute($params);
    $this->log = $taskExecutionResult['logs'];

    return $task;
  }

  protected function assertLogContains($expected, $message = '') {
    $this->assertContains($expected, implode("\n", $this->log), $message);
  }

}
