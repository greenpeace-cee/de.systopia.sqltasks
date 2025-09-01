<?php

use Civi\Api4\EntityTag;
use Civi\Api4\Tag;

/**
 * Test SyncTag Action
 *
 * @group headless
 */
class CRM_Sqltasks_Action_SyncTagTest extends CRM_Sqltasks_Action_AbstractActionTest {

  public function testSyncTag() {
    $tagId = $this->callApiSuccess('Tag', 'create', [
      'name'     => 'test',
      'used_for' => 'Contacts',
    ])['id'];

    $config = [
      'version' => CRM_Sqltasks_Config_Format::CURRENT,
      'actions' => [
        [
          'type'    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled' => TRUE,
          'script'  => "DROP TABLE IF EXISTS tmp_test_action_synctag;
                        CREATE TABLE tmp_test_action_synctag AS " . self::TEST_CONTACT_SQL,
        ],
        [
          'type'          => 'CRM_Sqltasks_Action_SyncTag',
          'enabled'       => TRUE,
          'contact_table' => 'tmp_test_action_synctag',
          'tag_id'        => $tagId,
          'entity_table'  => 'civicrm_contact',
        ],
        [
          'type'    => 'CRM_Sqltasks_Action_PostSQL',
          'enabled' => TRUE,
          'script'  => 'DROP TABLE IF EXISTS tmp_test_action_synctag;',
        ],
      ]
    ];

    $this->createAndExecuteTask([ 'config' => $config ]);

    $this->assertLogContains("Action 'Synchronise Tag' executed in", 'Synchronize Tag action should have succeeded');

    $entityTagCount = $this->callApiSuccess('EntityTag', 'getcount', [
      'entity_table' => 'civicrm_contact',
      'entity_id'    => $this->contactId,
      'tag_id'       => $tagId,
    ]);

    $this->assertEquals(1, $entityTagCount, 'Contact should have been tagged');

    $totalEntityTagCount = $this->callApiSuccess('EntityTag', 'getcount', [
      'entity_table' => 'civicrm_contact',
      'tag_id'       => $tagId,
    ]);

    $this->assertEquals(1, $totalEntityTagCount, 'Should have tagged one contact');

    // Create another contact and assign it to the tag
    $secondContactId = $this->callApiSuccess('Contact', 'create', [
      'first_name'   => 'Jane',
      'last_name'    => 'Doe',
      'contact_type' => 'Individual',
      'email'        => 'jane.doe@example.com',
    ])['id'];

    EntityTag::create(FALSE)
      ->addValue('entity_table', 'civicrm_contact')
      ->addValue('entity_id', $secondContactId)
      ->addValue('tag_id', $tagId)
      ->execute();

    $entityTagCount = $this->callApiSuccess('EntityTag', 'getcount', [
      'entity_table' => 'civicrm_contact',
      'entity_id'    => $secondContactId,
      'tag_id'       => $tagId,
    ]);

    $this->assertEquals(1, $entityTagCount, 'Second contact should have been tagged');

    // Re-run the task and ensure the manually-added contact was removed
    $this->createAndExecuteTask([ 'config' => $config ]);
    $this->assertLogContains("Action 'Synchronise Tag' executed in", 'Synchronize Tag action should have succeeded');

    $entityTagCount = $this->callApiSuccess('EntityTag', 'getcount', [
      'entity_table' => 'civicrm_contact',
      'entity_id'    => $secondContactId,
      'tag_id'       => $tagId,
    ]);

    $this->assertEquals(0, $entityTagCount, 'Second contact should no longer be tagged');
  }

  public function testSyncTagsFromTable_SQL() {
    // Create test contacts
    $contact_ids = [
      self::createRandomTestContact(),
      self::createRandomTestContact(),
      self::createRandomTestContact(),
    ];

    // Create tags
    $tag_names = [];

    for ($i = 1; $i < 4; $i++) {
      $tag_names[] = Tag::create(FALSE)
        ->addValue('name', "tag_$i")
        ->addValue('used_for', ['civicrm_contact'])
        ->execute()
        ->first()['name'];
    }

    // Assert the newly created contacts do not have any tags
    $tag_count = EntityTag::get(FALSE)
      ->addWhere('entity_table', '=', 'civicrm_contact')
      ->addWhere('entity_id', 'IN', $contact_ids)
      ->selectRowCount()
      ->execute()
      ->rowCount;

    $this->assertEquals(0, $tag_count, 'Contacts should not be tagged');

    // Create and execute a task with the SyncTag action
    $this->createAndExecuteTask([ 'config' => [
      'version' => CRM_Sqltasks_Config_Format::CURRENT,
      'actions' => [
        [
          'type'    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled' => TRUE,
          'script'  => "
            DROP TABLE IF EXISTS tmp_test_action_synctag;

            CREATE TABLE tmp_test_action_synctag (
              tag_name VARCHAR(64),
              contact_id INT
            );

            INSERT INTO tmp_test_action_synctag (tag_name, contact_id) VALUES
              ('tag_1', $contact_ids[0]),
              ('tag_1', $contact_ids[1]),
              ('tag_1', $contact_ids[2]),
              ('tag_2', $contact_ids[0]),
              ('tag_2', $contact_ids[1]),
              ('tag_3', $contact_ids[0]);
          ",
        ],
        [
          'type'                => 'CRM_Sqltasks_Action_SyncTag',
          'enabled'             => TRUE,
          'contact_table'       => 'tmp_test_action_synctag',
          'entity_table'        => 'civicrm_contact',
          'use_tags_from_table' => TRUE,
          'use_api'             => FALSE,
        ],
        [
          'type'    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled' => TRUE,
          'script'  => "DROP TABLE IF EXISTS tmp_test_action_synctag",
        ],
      ]
    ]]);

    // Assert the tags have been synced correctly
    $entity_tags = (array) civicrm_api4('EntityTag', 'get', [
      'checkPermissions' => FALSE,
      'select' => [ 'GROUP_CONCAT(tag_id:name) AS tags' ],
      'where' => [
        ['entity_table', '=', 'civicrm_contact'],
        ['entity_id', 'IN', $contact_ids],
      ],
      'groupBy' => [ 'entity_id' ],
    ], [ 'entity_id' => 'tags' ]);

    $this->assertEquals([
      $contact_ids[0] => ['tag_1','tag_2','tag_3'],
      $contact_ids[1] => ['tag_1','tag_2'],
      $contact_ids[2] => ['tag_1'],
    ], $entity_tags);

    // Create and execute a task with the SyncTag action using an exclude column
    $this->createAndExecuteTask([ 'config' => [
      'version' => CRM_Sqltasks_Config_Format::CURRENT,
      'actions' => [
        [
          'type'    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled' => TRUE,
          'script'  => "
            DROP TABLE IF EXISTS tmp_test_action_synctag;

            CREATE TABLE tmp_test_action_synctag (
              tag_name VARCHAR(64),
              contact_id INT,
              exclude BIT
            );

            INSERT INTO tmp_test_action_synctag (tag_name, contact_id, exclude) VALUES
              ('tag_1', $contact_ids[0], 0),
              ('tag_1', $contact_ids[2], 1),
              ('tag_2', $contact_ids[0], 0),
              ('tag_2', $contact_ids[2], 1),
              ('tag_3', $contact_ids[0], 0),
              ('tag_3', $contact_ids[2], 1);
          ",
        ],
        [
          'type'                => 'CRM_Sqltasks_Action_SyncTag',
          'enabled'             => TRUE,
          'contact_table'       => 'tmp_test_action_synctag',
          'entity_table'        => 'civicrm_contact',
          'use_tags_from_table' => TRUE,
          'use_api'             => FALSE,
        ],
        [
          'type'    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled' => TRUE,
          'script'  => "DROP TABLE IF EXISTS tmp_test_action_synctag",
        ],
      ],
    ]]);

    // Assert the tags have been synced correctly
    $entity_tags = (array) civicrm_api4('EntityTag', 'get', [
      'checkPermissions' => FALSE,
      'select' => [ 'GROUP_CONCAT(tag_id:name) AS tags' ],
      'where' => [
        ['entity_table', '=', 'civicrm_contact'],
        ['entity_id', 'IN', $contact_ids],
      ],
      'groupBy' => [ 'entity_id' ],
    ], [ 'entity_id' => 'tags' ]);

    $this->assertEquals([
      $contact_ids[0] => ['tag_1','tag_2','tag_3'],
    ], $entity_tags);

  }

  public function testSyncTagsFromTable_API() {
    // Create test contacts
    $contact_ids = [
      self::createRandomTestContact(),
      self::createRandomTestContact(),
      self::createRandomTestContact(),
    ];

    // Create tags
    $tag_names = [];

    for ($i = 1; $i < 4; $i++) {
      $tag_names[] = Tag::create(FALSE)
        ->addValue('name', "tag_$i")
        ->addValue('used_for', ['civicrm_contact'])
        ->execute()
        ->first()['name'];
    }

    // Assert the newly created contacts do not have any tags
    $tag_count = EntityTag::get(FALSE)
      ->addWhere('entity_table', '=', 'civicrm_contact')
      ->addWhere('entity_id', 'IN', $contact_ids)
      ->selectRowCount()
      ->execute()
      ->rowCount;

    $this->assertEquals(0, $tag_count, 'Contacts should not be tagged');

    // Create and execute a task with the SyncTag action
    $this->createAndExecuteTask([ 'config' => [
      'version' => CRM_Sqltasks_Config_Format::CURRENT,
      'actions' => [
        [
          'type'    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled' => TRUE,
          'script'  => "
            DROP TABLE IF EXISTS tmp_test_action_synctag;

            CREATE TABLE tmp_test_action_synctag (
              tag_name VARCHAR(64),
              contact_id INT
            );

            INSERT INTO tmp_test_action_synctag (tag_name, contact_id) VALUES
              ('tag_1', $contact_ids[0]),
              ('tag_1', $contact_ids[1]),
              ('tag_1', $contact_ids[2]),
              ('tag_2', $contact_ids[0]),
              ('tag_2', $contact_ids[1]),
              ('tag_3', $contact_ids[0]);
          ",
        ],
        [
          'type'                => 'CRM_Sqltasks_Action_SyncTag',
          'enabled'             => TRUE,
          'contact_table'       => 'tmp_test_action_synctag',
          'entity_table'        => 'civicrm_contact',
          'use_tags_from_table' => TRUE,
          'use_api'             => TRUE,
        ],
        [
          'type'    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled' => TRUE,
          'script'  => "DROP TABLE IF EXISTS tmp_test_action_synctag",
        ],
      ]
    ]]);

    // Assert the tags have been synced correctly
    $entity_tags = (array) civicrm_api4('EntityTag', 'get', [
      'checkPermissions' => FALSE,
      'select' => [ 'GROUP_CONCAT(tag_id:name) AS tags' ],
      'where' => [
        ['entity_table', '=', 'civicrm_contact'],
        ['entity_id', 'IN', $contact_ids],
      ],
      'groupBy' => [ 'entity_id' ],
    ], [ 'entity_id' => 'tags' ]);

    $this->assertEquals([
      $contact_ids[0] => ['tag_1','tag_2','tag_3'],
      $contact_ids[1] => ['tag_1','tag_2'],
      $contact_ids[2] => ['tag_1'],
    ], $entity_tags);

    // Create and execute a task with the SyncTag action using an exclude column
    $this->createAndExecuteTask([ 'config' => [
      'version' => CRM_Sqltasks_Config_Format::CURRENT,
      'actions' => [
        [
          'type'    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled' => TRUE,
          'script'  => "
            DROP TABLE IF EXISTS tmp_test_action_synctag;

            CREATE TABLE tmp_test_action_synctag (
              tag_name VARCHAR(64),
              contact_id INT,
              exclude BIT
            );

            INSERT INTO tmp_test_action_synctag (tag_name, contact_id, exclude) VALUES
              ('tag_1', $contact_ids[0], 0),
              ('tag_1', $contact_ids[2], 1),
              ('tag_2', $contact_ids[0], 0),
              ('tag_2', $contact_ids[2], 1),
              ('tag_3', $contact_ids[0], 0),
              ('tag_3', $contact_ids[2], 1);
          ",
        ],
        [
          'type'                => 'CRM_Sqltasks_Action_SyncTag',
          'enabled'             => TRUE,
          'contact_table'       => 'tmp_test_action_synctag',
          'entity_table'        => 'civicrm_contact',
          'use_tags_from_table' => TRUE,
          'use_api'             => TRUE,
        ],
        [
          'type'    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled' => TRUE,
          'script'  => "DROP TABLE IF EXISTS tmp_test_action_synctag",
        ],
      ],
    ]]);

    // Assert the tags have been synced correctly
    $entity_tags = (array) civicrm_api4('EntityTag', 'get', [
      'checkPermissions' => FALSE,
      'select' => [ 'GROUP_CONCAT(tag_id:name) AS tags' ],
      'where' => [
        ['entity_table', '=', 'civicrm_contact'],
        ['entity_id', 'IN', $contact_ids],
      ],
      'groupBy' => [ 'entity_id' ],
    ], [ 'entity_id' => 'tags' ]);

    $this->assertEquals([
      $contact_ids[0] => ['tag_1','tag_2','tag_3'],
    ], $entity_tags);

  }

}
