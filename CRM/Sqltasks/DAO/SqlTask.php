<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from de.systopia.sqltasks/xml/schema/CRM/Sqltasks/SqlTask.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:bb6d9e6138f23983a051f41dc39e1837)
 */
use CRM_Sqltasks_ExtensionUtil as E;

/**
 * Database access object for the SqlTask entity.
 */
class CRM_Sqltasks_DAO_SqlTask extends CRM_Core_DAO {
  const EXT = E::LONG_NAME;
  const TABLE_ADDED = '';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_sqltasks';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique SqlTask ID
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * Name of the task
   *
   * @var string
   *   (SQL type: varchar(255))
   *   Note that values will be retrieved from the database as a string.
   */
  public $name;

  /**
   * Description of the task
   *
   * @var string
   *   (SQL type: text)
   *   Note that values will be retrieved from the database as a string.
   */
  public $description;

  /**
   * Category of the task
   *
   * @var string
   *   (SQL type: varchar(64))
   *   Note that values will be retrieved from the database as a string.
   */
  public $category;

  /**
   * Regular execution frequency ("daily", "weekly", "monthly" etc.)
   *
   * @var string
   *   (SQL type: varchar(256))
   *   Note that values will be retrieved from the database as a string.
   */
  public $scheduled;

  /**
   * Is the task enabled?
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $enabled;

  /**
   * Defines execution order
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $weight;

  /**
   * Date/time of the last task execution
   *
   * @var string
   *   (SQL type: datetime)
   *   Note that values will be retrieved from the database as a string.
   */
  public $last_execution;

  /**
   * Start time of the current execution (if the task is running)
   *
   * @var string
   *   (SQL type: datetime)
   *   Note that values will be retrieved from the database as a string.
   */
  public $running_since;

  /**
   * Required permissions to run this task
   *
   * @var string
   *   (SQL type: varchar(256))
   *   Note that values will be retrieved from the database as a string.
   */
  public $run_permissions;

  /**
   * Does the task require input data?
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $input_required;

  /**
   * Input parameter specification (JSON)
   *
   * @var string
   *   (SQL type: text)
   *   Note that values will be retrieved from the database as a string.
   */
  public $input_spec;

  /**
   * Date/time the task was archived
   *
   * @var string
   *   (SQL type: datetime)
   *   Note that values will be retrieved from the database as a string.
   */
  public $archive_date;

  /**
   * Duration of the last execution in milliseconds
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $last_runtime;

  /**
   * Should this task be executed in parallel?
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $parallel_exec;

  /**
   * Main SQL script
   *
   * @var string
   *   (SQL type: text)
   *   Note that values will be retrieved from the database as a string.
   */
  public $main_sql;

  /**
   * Cleanup SQL script
   *
   * @var string
   *   (SQL type: text)
   *   Note that values will be retrieved from the database as a string.
   */
  public $post_sql;

  /**
   * Task configuration (JSON)
   *
   * @var string
   *   (SQL type: text)
   *   Note that values will be retrieved from the database as a string.
   */
  public $config;

  /**
   * Should task execution abort in case of an error?
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $abort_on_error;

  /**
   * Date/time of the latest change to the task configuration
   *
   * @var string
   *   (SQL type: datetime)
   *   Note that values will be retrieved from the database as a string.
   */
  public $last_modified;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_sqltasks';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? E::ts('Sql Tasks') : E::ts('Sql Task');
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('ID'),
          'description' => E::ts('Unique SqlTask ID'),
          'required' => TRUE,
          'where' => 'civicrm_sqltasks.id',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => NULL,
        ],
        'name' => [
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Name'),
          'description' => E::ts('Name of the task'),
          'required' => FALSE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_sqltasks.name',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
          'add' => NULL,
        ],
        'description' => [
          'name' => 'description',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => E::ts('Description'),
          'description' => E::ts('Description of the task'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.description',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'TextArea',
          ],
          'add' => NULL,
        ],
        'category' => [
          'name' => 'category',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Category'),
          'description' => E::ts('Category of the task'),
          'required' => FALSE,
          'maxlength' => 64,
          'size' => CRM_Utils_Type::BIG,
          'where' => 'civicrm_sqltasks.category',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
          'add' => NULL,
        ],
        'scheduled' => [
          'name' => 'scheduled',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Scheduled'),
          'description' => E::ts('Regular execution frequency ("daily", "weekly", "monthly" etc.)'),
          'required' => FALSE,
          'maxlength' => 256,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_sqltasks.scheduled',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
          'add' => NULL,
        ],
        'enabled' => [
          'name' => 'enabled',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Enabled'),
          'description' => E::ts('Is the task enabled?'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.enabled',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'CheckBox',
          ],
          'add' => NULL,
        ],
        'weight' => [
          'name' => 'weight',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Weight'),
          'description' => E::ts('Defines execution order'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.weight',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
          'add' => NULL,
        ],
        'last_execution' => [
          'name' => 'last_execution',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => E::ts('Last Execution'),
          'description' => E::ts('Date/time of the last task execution'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.last_execution',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'Select Date',
          ],
          'add' => NULL,
        ],
        'running_since' => [
          'name' => 'running_since',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => E::ts('Running Since'),
          'description' => E::ts('Start time of the current execution (if the task is running)'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.running_since',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'Select Date',
          ],
          'add' => NULL,
        ],
        'run_permissions' => [
          'name' => 'run_permissions',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Run Permissions'),
          'description' => E::ts('Required permissions to run this task'),
          'required' => FALSE,
          'maxlength' => 256,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_sqltasks.run_permissions',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
          'add' => NULL,
        ],
        'input_required' => [
          'name' => 'input_required',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Input Required'),
          'description' => E::ts('Does the task require input data?'),
          'required' => TRUE,
          'where' => 'civicrm_sqltasks.input_required',
          'default' => '0',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'CheckBox',
          ],
          'add' => NULL,
        ],
        'input_spec' => [
          'name' => 'input_spec',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => E::ts('Input Spec'),
          'description' => E::ts('Input parameter specification (JSON)'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.input_spec',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'TextArea',
          ],
          'add' => NULL,
        ],
        'archive_date' => [
          'name' => 'archive_date',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => E::ts('Archive Date'),
          'description' => E::ts('Date/time the task was archived'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.archive_date',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'Select Date',
          ],
          'add' => NULL,
        ],
        'last_runtime' => [
          'name' => 'last_runtime',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Last Runtime'),
          'description' => E::ts('Duration of the last execution in milliseconds'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.last_runtime',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
          'add' => NULL,
        ],
        'parallel_exec' => [
          'name' => 'parallel_exec',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Parallel Exec'),
          'description' => E::ts('Should this task be executed in parallel?'),
          'required' => TRUE,
          'where' => 'civicrm_sqltasks.parallel_exec',
          'default' => '0',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'CheckBox',
          ],
          'add' => NULL,
        ],
        'main_sql' => [
          'name' => 'main_sql',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => E::ts('Main Sql'),
          'description' => E::ts('Main SQL script'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.main_sql',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'TextArea',
          ],
          'add' => NULL,
        ],
        'post_sql' => [
          'name' => 'post_sql',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => E::ts('Post Sql'),
          'description' => E::ts('Cleanup SQL script'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.post_sql',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'TextArea',
          ],
          'add' => NULL,
        ],
        'config' => [
          'name' => 'config',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => E::ts('Config'),
          'description' => E::ts('Task configuration (JSON)'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.config',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'TextArea',
          ],
          'add' => NULL,
        ],
        'abort_on_error' => [
          'name' => 'abort_on_error',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Abort On Error'),
          'description' => E::ts('Should task execution abort in case of an error?'),
          'required' => TRUE,
          'where' => 'civicrm_sqltasks.abort_on_error',
          'default' => '0',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'CheckBox',
          ],
          'add' => NULL,
        ],
        'last_modified' => [
          'name' => 'last_modified',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => E::ts('Last Modified'),
          'description' => E::ts('Date/time of the latest change to the task configuration'),
          'required' => FALSE,
          'where' => 'civicrm_sqltasks.last_modified',
          'table_name' => 'civicrm_sqltasks',
          'entity' => 'SqlTask',
          'bao' => 'CRM_Sqltasks_DAO_SqlTask',
          'localizable' => 0,
          'html' => [
            'type' => 'Select Date',
          ],
          'add' => NULL,
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'sqltasks', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'sqltasks', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}
