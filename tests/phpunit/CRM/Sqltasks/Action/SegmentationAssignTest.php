<?php

use Civi\Api4\SegmentationOrder;

/**
 * Test SegmentationAssign Action, requires de.systopia.segmentation
 *
 * @group headless
 */
class CRM_Sqltasks_Action_SegmentationAssignTest extends CRM_Sqltasks_Action_AbstractActionTest {

  private $segmentationPresent;

  public function setUpHeadless() {
    $test = \Civi\Test::headless()
      ->uninstallMe(__DIR__)
      ->installMe(__DIR__);
    $this->segmentationPresent = $this->callApiSuccess('Extension', 'getcount', [
      'full_name' => 'de.systopia.segmentation',
    ]);
    if ($this->segmentationPresent) {
      $test->uninstall('de.systopia.segmentation');
      $test->install('de.systopia.segmentation');
    }
    return $test->apply(TRUE);
  }

  public function testSegmentationAssign() {
    if (!$this->segmentationPresent) {
      $this->markTestSkipped(
        'The de.systopia.segmentation extension is not available.'
      );
    }
    $contacts = [];
    $rows = [];
    for ($i = 0; $i < 5; $i++) {
      $contactID = self::createRandomTestContact();
      // test segment names with <> to check for HTMLInputCoder issues
      $segment = $i % 2 ? 'Segment > 1' : 'Segment < 2';

      $contacts[] = [
        'contact_id' => $contactID,
        'segment'    => $segment,
      ];

      $rows[] = 'SELECT ' . $contactID . ' AS contact_id, \'' . $segment . '\' AS segment_name';
    }

    $campaignId = $this->callApiSuccess('Campaign', 'create', array(
      'sequential' => 1,
      'name'       => 'testCampaign',
      'title'      => 'testCampaign',
    ))['id'];

    $config = [
      'version' => CRM_Sqltasks_Config_Format::CURRENT,
      'actions' => [
        [
          'type'    => 'CRM_Sqltasks_Action_RunSQL',
          'enabled' => TRUE,
          'script'  => "DROP TABLE IF EXISTS tmp_test_action_segmentationassign;
                        CREATE TABLE tmp_test_action_segmentationassign AS " . implode(' UNION ', $rows),
        ],
        [
          'type'                => 'CRM_Sqltasks_Action_SegmentationAssign',
          'enabled'             => TRUE,
          'table'               => 'tmp_test_action_segmentationassign',
          'campaign_id'         => $campaignId,
          'segment_name'        => 'testSegmentationAssign',
          'start'               => 'restart',
          'segment_order'       => "Segment &gt; 1\nSegment &lt; 2",
          'segment_order_table' => '',
          'segment_from_table'  => 1
        ],
        [
          'type'    => 'CRM_Sqltasks_Action_PostSQL',
          'enabled' => TRUE,
          'script'  => 'DROP TABLE IF EXISTS tmp_test_action_segmentationassign;',
        ],
      ],
    ];

    $this->createAndExecuteTask([ 'config' => $config ]);

    $this->assertLogContains('Resolved 2 segment(s).', 'Should have resolved two segments');
    $this->assertLogContains("Assigned 2 new contacts to segment 'Segment > 1'.", 'Should have assigned 2 contact to segment "Segment > 1"');
    $this->assertLogContains("Assigned 3 new contacts to segment 'Segment < 2'.", 'Should have assigned 3 contact to segment "Segment < 2"');
    $this->assertLogContains("Campaign 1 has been consolidated and (re)started.", 'Should have restarted campaign');
    $this->assertLogContains("Action 'Assign to Campaign (Segmentation)' executed in", 'Assign to Campaign action should have succeeded');
    $segmentationOrders = SegmentationOrder::get(FALSE)
      ->addSelect('segment_id', 'order_number', 'segment_id.name')
      ->addWhere('campaign_id', '=', $campaignId)
      ->execute()
      ->indexBy('segment_id.name');
    $this->assertEquals(1, $segmentationOrders['Segment > 1']['order_number'], 'Segment > 1 should be order_number=1');
    $this->assertEquals(2, $segmentationOrders['Segment < 2']['order_number'], 'Segment < 2 should be order_number=2');
    $query = CRM_Core_DAO::executeQuery(
      "SELECT s.entity_id, si.name
        FROM civicrm_segmentation s
        JOIN civicrm_segmentation_index si ON si.id = s.segment_id
        WHERE campaign_id = %0
        ORDER BY entity_id",
      [
        [$campaignId, 'Integer'],
      ]
    );
    $resultContacts = [];
    while ($query->fetch()) {
      $resultContacts[] = [
        'contact_id' => $query->entity_id,
        'segment'    => $query->name,
      ];
    }
    $this->assertEquals($contacts, $resultContacts, 'Contacts should have been assigned to the correct segments');
  }

}
