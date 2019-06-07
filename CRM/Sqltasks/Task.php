<?php
/*-------------------------------------------------------+
| SYSTOPIA SQL TASKS EXTENSION                           |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

use CRM_Sqltasks_ExtensionUtil as E;

/**
 * This class represents a single task
 *
 * @todo turn this into an entity
 */
class CRM_Sqltasks_Task {

  protected static $main_attributes = array(
    'name'            => 'String',
    'description'     => 'String',
    'category'        => 'String',
    'scheduled'       => 'String',
    'enabled'         => 'Integer',
    'weight'          => 'Integer',
    'last_execution'  => 'Date',
    'last_runtime'    => 'Integer',
    'parallel_exec'   => 'Integer',
    'main_sql'        => 'String',
    'post_sql'        => 'String');

  protected $task_id;
  protected $attributes;
  protected $config;
  protected $status;
  protected $error_count;
  protected $log_messages;

  /**
   * Constructor
   */
  public function __construct($task_id, $data = array()) {
    $this->task_id      = $task_id;
    $this->attributes   = array();
    $this->config       = array();
    $this->log_messages = array();
    $this->status       = 'init';
    $this->error_count  = 0;

    // main attributes go into $this->attributes
    foreach (self::$main_attributes as $attribute_name => $attribute_type) {
      $this->attributes[$attribute_name] = CRM_Utils_Array::value($attribute_name, $data);
    }

    // everything else goes into $this->config
    foreach ($data as $attribute_name => $value) {
      if (!isset(self::$main_attributes[$attribute_name])) {
        $this->config[$attribute_name] = $value;
      }
    }
  }

  /**
   * get a single attribute from the task
   */
  public function getID() {
    return $this->task_id;
  }

  /**
   * get the current status of this task
   *
   * Should return one of:
   *  'init'    - task object has not yet been executed
   *  'running' - task object is curreently being executed
   *  'success' - task has been executed successfully
   *  'error'   - task has report one or more errors during execution
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * check if the task has encountered errors during execution
   */
  public function hasExecutionErrors() {
    return $this->error_count > 0 || $this->status == 'error';
  }

  /**
   * get configuration
   */
  public function getConfiguration() {
    return $this->config;
  }

  /**
   * set entire configuration
   */
  public function setConfiguration($config) {
    return $this->config = $config;
  }

  /**
   * append log messages
   */
  public function log($message) {
    $this->log_messages[] = $message;
  }

  /**
   * clear log
   */
  public function resetLog() {
    $this->log_messages = array();
  }

  /**
   * write current log into a temp file
   */
  public function writeLogfile() {
    $logfile = tempnam(sys_get_temp_dir(), 'sqltask-') . '.log';
    if ($logfile) {
      $handle = fopen($logfile, 'w');
      foreach ($this->log_messages as $message) {
        // fwrite($handle, mb_convert_encoding($message . "\n", 'utf8'));
        fwrite($handle, $message . "\r\n");
      }
      fclose($handle);
    }
    return $logfile;
  }

  /**
   * get a single attribute from the task
   */
  public function getAttribute($attribute_name) {
    return CRM_Utils_Array::value($attribute_name, $this->attributes);
  }

  /**
   * set a single attribute
   */
  public function setAttribute($attribute_name, $value, $writeTrough = FALSE) {
    if (isset(self::$main_attributes[$attribute_name])) {
      $this->attributes[$attribute_name] = $value;
      if ($writeTrough && $this->task_id) {
        CRM_Core_DAO::executeQuery("UPDATE `civicrm_sqltasks`
                                    SET `{$attribute_name}` = %1
                                    WHERE id = {$this->task_id}",
                                    array(1 => array($value, self::$main_attributes[$attribute_name])));
      }
    } else {
      throw new Exception("Attribute '{$attribute_name}' unknown", 1);
    }
  }

  /**
   * Store this task (create or update)
   */
  public function store() {
    // sort out paramters
    $params = array();
    $fields = array();
    $index  = 1;
    foreach (self::$main_attributes as $attribute_name => $attribute_type) {
      if (  $attribute_name == 'last_execution'
         || $attribute_name == 'last_runtime') {
        // don't overwrite timestamp
        continue;
      }
      $value = $this->getAttribute($attribute_name);
      if ($value === NULL || $value === '') {
        $fields[$attribute_name] = "NULL";
      } else {
        $fields[$attribute_name] = "%{$index}";
        $params[$index] = array($value, $attribute_type);
        $index += 1;
      }
    }
    $fields['config'] = "%{$index}";
    $params[$index] = array(json_encode($this->config), 'String');

    // generate SQL
    if ($this->task_id) {
      $field_assignments = array();
      foreach ($fields as $key => $value) {
        $field_assignments[] = "`{$key}` = {$value}";
      }
      $field_assignment_sql = implode(', ', $field_assignments);
      $sql = "UPDATE `civicrm_sqltasks` SET {$field_assignment_sql} WHERE id = {$this->task_id}";
    } else {
      $columns = array();
      $values  = array();
      foreach ($fields as $key => $value) {
        $columns[] = $key;
        $values[]  = $value;
      }
      $columns_sql = implode(',', $columns);
      $values_sql  = implode(',', $values);
      $sql = "INSERT INTO `civicrm_sqltasks` ({$columns_sql}) VALUES ({$values_sql});";
    }
    // error_log("STORE QUERY: " . $sql);
    // error_log("STORE PARAM: " . json_encode($params));
    CRM_Core_DAO::executeQuery($sql, $params);
  }



  /**
   * Executes the given task
   */
  public function execute() {
    $this->status = 'running';
    $this->error_count = 0;
    $this->resetLog();
    $task_timestamp = microtime(TRUE) * 1000;

    // 0. mark task as started
    $is_still_running = CRM_Core_DAO::singleValueQuery("SELECT running_since FROM `civicrm_sqltasks` WHERE id = {$this->task_id};");
    if ($is_still_running) {
      $this->status = 'error';
      $this->log("Task is still running. Execution skipped.");
      return $this->log_messages;
    } else {
      // set last_execution and running_since
      CRM_Core_DAO::executeQuery("UPDATE `civicrm_sqltasks` SET last_execution = NOW(), running_since = NOW() WHERE id = {$this->task_id};");
    }

    // 1. run the main SQL
    $this->executeSQLScript($this->getAttribute('main_sql'), "Main SQL");

    // 2. run the actions
    $actions = CRM_Sqltasks_Action::getAllActiveActions($this);
    foreach ($actions as $action) {
      if ($action->isResultHandler()) {
        continue; // result handlers will only be executed at the end
      }

      $action_name = $action->getName();
      $timestamp = microtime(TRUE);

      // check action configuration
      try {
        $action->checkConfiguration();
      } catch (Exception $e) {
        $this->error_count += 1;
        $this->log("Configuration Error '{$action_name}': " . $e -> getMessage());
        continue;
      }

      // run action
      try {
        $action->execute();
        $runtime = sprintf("%.3f", (microtime(TRUE) - $timestamp));
        $this->log("Action '{$action_name}' executed in {$runtime}s.");
      } catch (Exception $e) {
        $this->error_count += 1;
        $this->log("Error in action '{$action_name}': " . $e -> getMessage());
      }
    }

    // 3. run the post SQL
    $this->executeSQLScript($this->getAttribute('post_sql'), "Post SQL");

    // 4. update/close the task
    $task_runtime = (int) (microtime(TRUE) * 1000) - $task_timestamp;
    CRM_Core_DAO::executeQuery("UPDATE `civicrm_sqltasks` SET running_since = NULL, last_runtime = {$task_runtime} WHERE id = {$this->task_id};");
    if ($this->error_count) {
      $this->status = 'error';
    } else {
      $this->status = 'success';
    }

    // 5. run result handlers
    foreach ($actions as $action) {
      if ($action->isResultHandler()) {
        $action->executeResultHandler($actions);
      }
    }

    return $this->log_messages;
  }


  /**
   * execute a single SQL script
   */
  protected function executeSQLScript($script, $script_name) {
    if (empty($script)) {
      $this->log("No '{$script_name}' given.");
      return;
    }

    $timestamp = microtime(TRUE);
    try {
      // prepare
      $config = CRM_Core_Config::singleton();
      $script = html_entity_decode($script);

      // run the whole script (see CRM-20428 and
      //   https://github.com/systopia/de.systopia.sqltasks/issues/2)
      if (version_compare(CRM_Utils_System::version(), '4.7.20', '<')) {
        CRM_Utils_File::sourceSQLFile($config->dsn, $script, NULL, TRUE);
      } else {
        CRM_Utils_File::runSqlQuery($config->dsn, $script);
      }

      $runtime = sprintf("%.3f", (microtime(TRUE) - $timestamp));
      $this->log("Script '{$script_name}' executed in {$runtime}s.");
    } catch (Exception $e) {
      $this->error_count += 1;
      $this->log("Script '{$script_name}' failed: " . $e -> getMessage());
    }
  }

  /**
   * delete a task with the given ID
   */
  public static function delete($tid) {
    $tid = (int) $tid;
    if (empty($tid)) return NULL;
    CRM_Core_DAO::executeQuery("DELETE FROM civicrm_sqltasks WHERE id = {$tid}");
  }

  /**
   * Get a list of all tasks
   */
  public static function getAllTasks() {
    return self::getTasks('SELECT * FROM civicrm_sqltasks ORDER BY weight ASC, id ASC');
  }

  /**
   * Get a list of tasks ready for execution
   */
  public static function getExecutionTaskList() {
    return self::getTasks('SELECT * FROM civicrm_sqltasks WHERE enabled=1 ORDER BY weight ASC, id ASC');
  }

  /**
   * Get a list of tasks ready for execution
   */
  public static function getParallelExecutionTaskList() {
    return self::getTasks('SELECT * FROM civicrm_sqltasks WHERE enabled=1 AND parallel_exec = 1 ORDER BY weight ASC, id ASC');
  }

  /**
   * Load a list of tasks based on the data yielded by the given SQL query
   */
  public static function getTasks($sql_query) {
    $tasks = array();
    $task_search = CRM_Core_DAO::executeQuery($sql_query);
    while ($task_search->fetch()) {
      $data = array();
      foreach (self::$main_attributes as $attribute_name => $attribute_type) {
        $data[$attribute_name] = $task_search->$attribute_name;
      }
      if (isset($task_search->config)) {
        $config = json_decode($task_search->config, TRUE);
        foreach ($config as $key => $value) {
          $data[$key] = $value;
        }
      }
      $tasks[] = new CRM_Sqltasks_Task($task_search->id, $data);
    }

    return $tasks;
  }

  /**
   * Load a list of tasks based on the data yielded by the given SQL query
   */
  public static function getTask($tid) {
    $tid = (int) $tid;
    if (empty($tid)) return NULL;
    $tasks = self::getTasks("SELECT * FROM `civicrm_sqltasks` WHERE id = {$tid}");
    return reset($tasks);
  }

  /**
   * Export task configuration
   */
  public function exportConfiguration() {
    // copy the attributes
    $config = $this->attributes;
    unset($config['name']);
    unset($config['enabled']);
    unset($config['weight']);
    unset($config['last_execution']);
    $config['config'] = $this->config;
    return json_encode($config, JSON_PRETTY_PRINT);
  }



  //  +---------------------------------+
  //  |       Scheduling Logic          |
  //  +---------------------------------+

  /**
   * main dispatcher, triggered by a scheduled Job
   */
  public static function runDispatcher() {
    $results = array();

    // FIRST reset timed out tasks (after 23 hours)
    CRM_Core_DAO::executeQuery("
      UPDATE `civicrm_sqltasks`
         SET running_since = NULL
       WHERE running_since < (NOW() - INTERVAL 23 HOUR);");

    // THEN: find out if still running
    $still_running = CRM_Core_DAO::singleValueQuery("
      SELECT COUNT(*)
        FROM `civicrm_sqltasks`
       WHERE running_since IS NOT NULL;");

    if (!$still_running) {
      // NORMAL DISPATCH
      $tasks = CRM_Sqltasks_Task::getExecutionTaskList();
      foreach ($tasks as $task) {
        if ($task->shouldRun()) {
          $results[] = $task->execute();
        }
      }

    } else {
      // PARALLEL DISPATCH: only run tasks flagged as parallel
      $tasks = CRM_Sqltasks_Task::getParallelExecutionTaskList();
      foreach ($tasks as $task) {
        if ($task->shouldRun()) {
          $results[] = $task->execute();
        }
      }
    }

    return $results;
  }

  /**
   * Check if the task should run according to scheduling
   */
  public function shouldRun() {
    $last_execution = strtotime($this->getAttribute('last_execution'));
    // if never ran, we need any day to compare
    if (empty($last_execution)) {
      $last_execution = '1970-01-01 00:00:00';
    }
    $scheduled = $this->getAttribute('scheduled');

    // if it should always be executed
    //  => YES!
    if ($scheduled == 'always') {
      return TRUE;
    }

    if (!empty($this->config['scheduled_weekday'])) {
      $scheduled_weekday = $this->config['scheduled_weekday'];
    }
    else {
      $scheduled_weekday = '1';
    }
    if (!empty($this->config['scheduled_day'])) {
      $scheduled_day = str_pad($this->config['scheduled_day'], 2, '0', STR_PAD_LEFT);;
    }
    else {
      $scheduled_day = '01';
    }
    if (!empty($this->config['scheduled_hour'])) {
      $scheduled_hour = str_pad($this->config['scheduled_hour'], 2, '0', STR_PAD_LEFT);
    }
    else {
      $scheduled_hour = '00';
    }
    if (!empty($this->config['scheduled_minute'])) {
      $scheduled_minute = str_pad($this->config['scheduled_minute'], 2, '0', STR_PAD_LEFT);
    }
    else {
      $scheduled_minute = '00';
    }

    $now = CRM_Utils_Date::currentDBDate();
    switch ($scheduled) {
      case 'always':
        return TRUE;

      // not fully implemented for Yearly
      case 'Yearly':
        return TRUE;

      case 'hourly':
        $thisMin = date('YmdHi', strtotime($now));
        $thisHour = date('YmdH', strtotime($now));
        $lastHour = date('YmdH', strtotime($last_execution));

        return (($thisMin >= $thisHour . $scheduled_minute) && ($thisHour > $lastHour));

      case 'daily':
        $thisHour = date('YmdHi', strtotime($now));
        $thisDay = date('Ymd', strtotime($now));
        $lastDay = date('Ymd', strtotime($last_execution));

        return (($thisHour >= $thisDay . $scheduled_hour . $scheduled_minute) && ($thisDay > $lastDay));

      case 'weekly':
        $thisWeekday = date('oWNHi', strtotime($now));
        $thisWeek = date('oW', strtotime($now));
        $lastWeek = date('oW', strtotime($last_execution));

        return (($thisWeekday >= $thisWeek . $scheduled_weekday . $scheduled_hour . $scheduled_minute) && ($thisWeek > $lastWeek));

      case 'monthly':
        $thisDay = date('YmdHi', strtotime($now));
        $thisMonth = date('Ym', strtotime($now));
        $lastMonth = date('Ym', strtotime($last_execution));

        return (($thisDay >= $thisMonth . $scheduled_day . $scheduled_hour . $scheduled_minute) && ($thisMonth > $lastMonth));

    }
    return FALSE;
  }

  /**
   * get the option for scheduling (simple version)
   */
  public static function getSchedulingOptions() {
    $frequencies = array(
      'always'  => E::ts('always'),
      'hourly'  => E::ts('every hour'),
      'daily'   => E::ts('every day (after midnight)'),
      'weekly'  => E::ts('every week'),
      'monthly' => E::ts('every month'),
      'yearly'  => E::ts('annually'),
      );

    // get scheduler information
    $config = CRM_Sqltasks_Config::singleton();
    $dispatcher_frequency = $config->getCurrentDispatcherFrequency();
    switch ($dispatcher_frequency) {
      case 'Always':
        break;

      case 'Hourly':
        $frequencies['always'] = $frequencies['always'] . ' ' . E::ts("(currently triggered hourly)");
        break;

      case 'Daily':
        $frequencies['always'] = $frequencies['always'] . ' ' . E::ts("(currently triggered daily)");
        $frequencies['hourly'] = $frequencies['hourly'] . ' ' . E::ts("(currently triggered daily)");
        break;

      default:
        // add a warning to all entries
        foreach ($frequencies as $key => &$value) {
          $value = $value . ' ' . E::ts("(warning: dispatcher currently disabled)");
        }
        break;
    }

    return $frequencies;
  }

  /**
   * calculate the next execution date
   */
  public static function getNextExecutionTime() {
    // TODO:
    // 1) find out if cron-job is there/enabled
    // 2) find out how often it runs
    // 3) calculate next date based on last exec date

    return 'TODO';
  }
}
