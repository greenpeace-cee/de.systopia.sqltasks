<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from de.systopia.sqltasks/xml/schema/CRM/Sqltasks/SqltasksExecution.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:fa4de32875d01a8b099e2d357fa8a670)
 */
use CRM_Sqltasks_ExtensionUtil as E;

/**
 * Database access object for the SqltasksExecution entity.
 */
class CRM_Sqltasks_DAO_SqltasksExecution extends CRM_Core_DAO {
  const EXT = E::LONG_NAME;
  const TABLE_ADDED = '';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_sqltasks_execution';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique SqltasksExecution ID
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * FK to SQL Task
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $sqltask_id;

  /**
   * Start date of execution
   *
   * @var string
   *   (SQL type: datetime)
   *   Note that values will be retrieved from the database as a string.
   */
  public $start_date;

  /**
   * End date of execution
   *
   * @var string
   *   (SQL type: datetime)
   *   Note that values will be retrieved from the database as a string.
   */
  public $end_date;

  /**
   * Task runtime in milliseconds
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $runtime;

  /**
   * Task input
   *
   * @var string
   *   (SQL type: longtext)
   *   Note that values will be retrieved from the database as a string.
   */
  public $input;

  /**
   * Task result log
   *
   * @var string
   *   (SQL type: longtext)
   *   Note that values will be retrieved from the database as a string.
   */
  public $log;

  /**
   * Task result files (JSON)
   *
   * @var string
   *   (SQL type: longtext)
   *   Note that values will be retrieved from the database as a string.
   */
  public $files;

  /**
   * Task execution error count
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $error_count;

  /**
   * Contact ID of task executor
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $created_id;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_sqltasks_execution';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? E::ts('Sqltasks Executions') : E::ts('Sqltasks Execution');
  }

  /**
   * Returns foreign keys and entity references.
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  public static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'sqltask_id', 'civicrm_sqltasks', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'created_id', 'civicrm_contact', 'id');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
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
          'description' => E::ts('Unique SqltasksExecution ID'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_sqltasks_execution.id',
          'table_name' => 'civicrm_sqltasks_execution',
          'entity' => 'SqltasksExecution',
          'bao' => 'CRM_Sqltasks_DAO_SqltasksExecution',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => NULL,
        ],
        'sqltask_id' => [
          'name' => 'sqltask_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Sqltask ID'),
          'description' => E::ts('FK to SQL Task'),
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_sqltasks_execution.sqltask_id',
          'table_name' => 'civicrm_sqltasks_execution',
          'entity' => 'SqltasksExecution',
          'bao' => 'CRM_Sqltasks_DAO_SqltasksExecution',
          'localizable' => 0,
          'FKClassName' => 'CRM_Sqltasks_DAO_SqlTask',
          'html' => [
            'type' => 'Number',
          ],
          'add' => NULL,
        ],
        'start_date' => [
          'name' => 'start_date',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => E::ts('Start Date'),
          'description' => E::ts('Start date of execution'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_sqltasks_execution.start_date',
          'table_name' => 'civicrm_sqltasks_execution',
          'entity' => 'SqltasksExecution',
          'bao' => 'CRM_Sqltasks_DAO_SqltasksExecution',
          'localizable' => 0,
          'html' => [
            'type' => 'Select Date',
          ],
          'add' => NULL,
        ],
        'end_date' => [
          'name' => 'end_date',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => E::ts('End Date'),
          'description' => E::ts('End date of execution'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_sqltasks_execution.end_date',
          'table_name' => 'civicrm_sqltasks_execution',
          'entity' => 'SqltasksExecution',
          'bao' => 'CRM_Sqltasks_DAO_SqltasksExecution',
          'localizable' => 0,
          'html' => [
            'type' => 'Select Date',
          ],
          'add' => NULL,
        ],
        'runtime' => [
          'name' => 'runtime',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Runtime (ms)'),
          'description' => E::ts('Task runtime in milliseconds'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_sqltasks_execution.runtime',
          'table_name' => 'civicrm_sqltasks_execution',
          'entity' => 'SqltasksExecution',
          'bao' => 'CRM_Sqltasks_DAO_SqltasksExecution',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'add' => NULL,
        ],
        'input' => [
          'name' => 'input',
          'type' => CRM_Utils_Type::T_LONGTEXT,
          'title' => E::ts('Input'),
          'description' => E::ts('Task input'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_sqltasks_execution.input',
          'table_name' => 'civicrm_sqltasks_execution',
          'entity' => 'SqltasksExecution',
          'bao' => 'CRM_Sqltasks_DAO_SqltasksExecution',
          'localizable' => 0,
          'html' => [
            'type' => 'TextArea',
          ],
          'add' => NULL,
        ],
        'log' => [
          'name' => 'log',
          'type' => CRM_Utils_Type::T_LONGTEXT,
          'title' => E::ts('Log'),
          'description' => E::ts('Task result log'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_sqltasks_execution.log',
          'table_name' => 'civicrm_sqltasks_execution',
          'entity' => 'SqltasksExecution',
          'bao' => 'CRM_Sqltasks_DAO_SqltasksExecution',
          'localizable' => 0,
          'html' => [
            'type' => 'TextArea',
          ],
          'add' => NULL,
        ],
        'files' => [
          'name' => 'files',
          'type' => CRM_Utils_Type::T_LONGTEXT,
          'title' => E::ts('Files'),
          'description' => E::ts('Task result files (JSON)'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_sqltasks_execution.files',
          'table_name' => 'civicrm_sqltasks_execution',
          'entity' => 'SqltasksExecution',
          'bao' => 'CRM_Sqltasks_DAO_SqltasksExecution',
          'localizable' => 0,
          'html' => [
            'type' => 'TextArea',
          ],
          'add' => NULL,
        ],
        'error_count' => [
          'name' => 'error_count',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Error Count'),
          'description' => E::ts('Task execution error count'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_sqltasks_execution.error_count',
          'table_name' => 'civicrm_sqltasks_execution',
          'entity' => 'SqltasksExecution',
          'bao' => 'CRM_Sqltasks_DAO_SqltasksExecution',
          'localizable' => 0,
          'add' => NULL,
        ],
        'created_id' => [
          'name' => 'created_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Created ID'),
          'description' => E::ts('Contact ID of task executor'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_sqltasks_execution.created_id',
          'table_name' => 'civicrm_sqltasks_execution',
          'entity' => 'SqltasksExecution',
          'bao' => 'CRM_Sqltasks_DAO_SqltasksExecution',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
          'html' => [
            'label' => E::ts("Created By"),
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'sqltasks_execution', $prefix, []);
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'sqltasks_execution', $prefix, []);
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
