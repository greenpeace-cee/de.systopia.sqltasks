<?php

/**
 * Test RunSQL Action
 *
 * @group headless
 */
class CRM_Sqltasks_Action_RunSQLTest extends CRM_Sqltasks_Action_AbstractActionTest {
  const TMP_TABLE_PREFIX = 'tmp_sqltasks_test_run_sql_';

  private $tmp_table;

  public function setUp(): void {
    parent::setUp();

    $this->tmp_table = self::TMP_TABLE_PREFIX . '_' . bin2hex(random_bytes(4));

    CRM_Core_DAO::executeQuery("DROP TABLE IF EXISTS {$this->tmp_table}");

    CRM_Core_DAO::executeQuery("CREATE TABLE {$this->tmp_table} (
      a INT NOT NULL,
      b VARCHAR(64),
      c BOOL
    )");
  }

  public function tearDown(): void {
    CRM_Core_DAO::executeQuery("DROP TABLE IF EXISTS {$this->tmp_table}");

    parent::tearDown();
  }

  public function testExecuteSQL() {
    $config = [
      'version' => CRM_Sqltasks_Config_Format::CURRENT,
      'actions' => [
        [
          'type'    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled' => TRUE,
          'script'  => "
            INSERT INTO {$this->tmp_table} (a, b, c) VALUES (1, 'aaa', 1);
            INSERT INTO {$this->tmp_table} (a, b, c) VALUES (2, 'bbb', 0);
            INSERT INTO {$this->tmp_table} (a, b, c) VALUES (3, 'ccc', 1);
          ",
        ],
      ],
    ];

    $this->createAndExecuteTask([ 'config' => $config ]);

    $query = CRM_Core_DAO::executeQuery("SELECT COUNT(*) AS result FROM {$this->tmp_table} WHERE c");
    $query->fetch();

    $this->assertEquals(2, $query->result);
  }

  public function testStoredProcedures() {
    $config = [
      'version' => CRM_Sqltasks_Config_Format::CURRENT,
      'actions' => [
        [
          'type'                    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled'                 => TRUE,
          'run_as_stored_procedure' => TRUE,
          'script'  => "
            SET @i = 0;
            REPEAT
              INSERT INTO {$this->tmp_table} (a) VALUES (@i);
              SET @i = @i + 1;
            UNTIL @i >= @input END REPEAT;
          ",
        ],
      ],
    ];

    $this->createAndExecuteTask([
      'input_required' => TRUE,
      'config' => $config,
    ], [ 'input_val' => 5 ]);

    $query = CRM_Core_DAO::executeQuery("SELECT * FROM {$this->tmp_table}");
    $results = [];

    while ($query->fetch()) {
      $results[] = [
        'a' => $query->a,
        'b' => $query->b,
        'c' => $query->c,
      ];
    }

    $this->assertEquals([
      [ 'a' =>  0, 'b' => NULL, 'c' => NULL ],
      [ 'a' =>  1, 'b' => NULL, 'c' => NULL ],
      [ 'a' =>  2, 'b' => NULL, 'c' => NULL ],
      [ 'a' =>  3, 'b' => NULL, 'c' => NULL ],
      [ 'a' =>  4, 'b' => NULL, 'c' => NULL ],
    ], $results);
  }

}
