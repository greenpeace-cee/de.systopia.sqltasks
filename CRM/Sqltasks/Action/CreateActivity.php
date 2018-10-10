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
 * This actions allows you to synchronise
 *  a resulting contact set with a group
 *
 */
class CRM_Sqltasks_Action_CreateActivity extends CRM_Sqltasks_Action_ContactSet {

  /**
   * Get identifier string
   */
  public function getID() {
    return 'activity';
  }

  /**
   * Get a human readable name
   */
  public function getName() {
    return E::ts('Create Activity');
  }

  public function getDefaultOrder() {
    return 300;
  }

  /**
   * Build the configuration UI
   */
  public function buildForm(&$form) {
    parent::buildForm($form);

    $form->add(
      'checkbox',
      $this->getID() . '_use_api',
      E::ts('Use API (slow)')
    );

    $form->add(
      'checkbox',
      $this->getID() . '_individual',
      E::ts('Individual Activities')
    );

    $form->add(
      'select',
      $this->getID() . '_activity_type_id',
      E::ts('Activity Type'),
      $this->getOptions('activity_type')
    );

    $form->add(
      'select',
      $this->getID() . '_status_id',
      E::ts('Status'),
      $this->getOptions('activity_status')
    );

    $form->add(
      'text',
      $this->getID() . '_subject',
      E::ts('Subject'),
      array('class' => 'huge', 'style' => 'font-family: monospace, monospace !important')
    );

    $form->add(
      'textarea',
      $this->getID() . '_details',
      E::ts('Details'),
      array('rows' => 4, 'cols' => 60, 'style' => 'font-family: monospace, monospace !important')
    );

    $form->add(
      'text',
      $this->getID() . '_activity_date_time',
      E::ts('Timestamp'),
      ['style' => 'font-family: monospace, monospace !important']
    );

    $form->add(
      'select',
      $this->getID() . '_campaign_id',
      E::ts('Campaign'),
      $this->getEligibleCampaigns(TRUE),
      FALSE,
      array('class' => 'crm-select2 huge')
    );

    $form->addEntityRef(
      $this->getID() . '_source_contact_id',
      E::ts('Source Contact')
    );

    $form->addEntityRef(
      $this->getID() . '_assigned_to',
      E::ts('Assigned To'),
      [
        'multiple' => TRUE
      ]
    );

    $form->add(
      'select',
      $this->getID() . '_medium_id',
      E::ts('Activity Medium'),
      $this->getOptions('encounter_medium')
    );

    $form->add(
      'text',
      $this->getID() . '_source_record_id',
      E::ts('Source Record ID'),
      ['style' => 'font-family: monospace, monospace !important']
    );

    $form->add(
      'select',
      $this->getID() . '_priority_id',
      E::ts('Priority'),
      $this->getOptions('priority')
    );

    $form->add(
      'select',
      $this->getID() . '_engagement_level',
      E::ts('Engagement Index'),
      $this->getOptions('engagement_index')
    );

    $form->add(
      'text',
      $this->getID() . '_location',
      E::ts('Location'),
      ['style' => 'font-family: monospace, monospace !important']
    );

    $form->add(
      'text',
      $this->getID() . '_duration',
      E::ts('Duration'),
      ['style' => 'font-family: monospace, monospace !important']
    );
  }


  /**
   * RUN this action
   */
  public function execute() {
    $this->resetHasExecuted();
    $individual = $this->getConfigValue('individual');
    if ($individual) {
      $this->createIndividualActivities();
    } else {
      $this->createMassActivity();
    }
    $this->createExclusionActivity();
  }

  /**
   * Generate individual activities
   */
  public function createMassActivity() {
    $use_api       = $this->getConfigValue('use_api');
    $contact_table = $this->getContactTable();

    // load one line for the tokens
    $record = CRM_Core_DAO::executeQuery("SELECT * FROM {$contact_table} LIMIT 1;");
    $entries_exist = $record->fetch();
    if ($entries_exist) {
      $this->setHasExecuted();
    }
    else {
      return;
    }

    // create activity first
    $activity_data = array(
      'activity_date_time' => $this->getDateTime($this->getConfigValue('activity_date_time')),
      'activity_type_id'   => $this->getConfigValue('activity_type_id'),
      'campaign_id'        => $this->getConfigValue('campaign_id'),
      'status_id'          => $this->getConfigValue('status_id'),
      'source_contact_id'  => $this->getConfigValue('source_contact_id'),
      'subject'            => $this->resolveTokens($this->getConfigValue('subject'), $record),
      'details'            => $this->resolveTokens($this->getConfigValue('details'), $record),
      'assignee_id'        => $this->getIDList($this->getConfigValue('assigned_to')),
      'medium_id'          => $this->getConfigValue('medium_id'),
      'source_record_id'   => $this->resolveTokens($this->getConfigValue('source_record_id'), $record),
      'priority_id'        => $this->getConfigValue('priority_id'),
      'engagement_level'   => $this->getConfigValue('engagement_level'),
      'location'           => $this->resolveTokens($this->getConfigValue('location'), $record),
      'duration'           => $this->resolveTokens($this->getConfigValue('duration'), $record),
    );
    $unsetIfEmpty = [
      'source_contact_id', 'campaign_id', 'medium_id', 'source_record_id',
      'priority_id', 'engagement_level', 'location', 'duration'
    ];
    foreach ($unsetIfEmpty as $field) {
      if (empty($activity_data[$field])) {
        unset($activity_data[$field]);
      }
    }
    $activity = civicrm_api3('Activity', 'create', $activity_data);

    $excludeSql = '';
    if ($this->_columnExists($contact_table, 'exclude')) {
      $excludeSql = 'AND (exclude IS NULL OR exclude != 1)';
      $this->log('Column "exclude" exists, might skip some rows');
    }

    if ($use_api) {
      // add all targets separately
      $target_query = CRM_Core_DAO::executeQuery("SELECT contact_id FROM `{$contact_table}` WHERE contact_id IS NOT NULL {$excludeSql}");
      while ($target_query->fetch()) {
        civicrm_api3('ActivityContact', 'create', array(
          'activity_id'    => $activity['id'],
          'contact_id'     => (int) $target_query->contact_id,
          'record_type_id' => 3));
      }

    } else {
      // just add everyone in the group as a target
      CRM_Core_DAO::executeQuery("
        INSERT IGNORE INTO civicrm_activity_contact
         (SELECT
            NULL              AS id,
            {$activity['id']} AS activity_id,
            contact_id        AS contact_id,
            3                 AS record_type
          FROM `{$contact_table}`
          WHERE contact_id IS NOT NULL {$excludeSql});");

      if (class_exists('CRM_Segmentation_Logic')) {
        CRM_Segmentation_Logic::addSegmentForMassActivity($activity['id'], $this->getConfigValue('campaign_id'));
      }
    }
  }


  /**
   * Generate individual activities
   */
  public function createIndividualActivities() {
    $use_api       = $this->getConfigValue('use_api');
    $contact_table = $this->getContactTable();

    // static activity parameters
    $activity_template = array(
      'activity_date_time' => $this->getDateTime($this->getConfigValue('activity_date_time')),
      'activity_type_id'   => $this->getConfigValue('activity_type_id'),
      'campaign_id'        => $this->getConfigValue('campaign_id'),
      'status_id'          => $this->getConfigValue('status_id'),
      'source_contact_id'  => $this->getConfigValue('source_contact_id'),
      'assignee_id'        => $this->getIDList($this->getConfigValue('assigned_to')),
      'medium_id'          => $this->getConfigValue('medium_id'),
      'priority_id'        => $this->getConfigValue('priority_id'),
      'engagement_level'   => $this->getConfigValue('engagement_level'),
    );
    $unsetIfEmpty = ['source_contact_id', 'campaign_id', 'medium_id', 'priority_id', 'engagement_level'];
    foreach ($unsetIfEmpty as $field) {
      if (empty($activity_template[$field])) {
        unset($activity_template[$field]);
      }
    }
    if (!$use_api) {
      // add some defaults for SQL
      $activity_template['priority_id'] = 2;
      $activity_template['is_test'] = 0;
      $activity_template['is_auto'] = 0;
      $activity_template['is_current_revision'] = 1;
      $activity_template['is_deleted'] = 0;
    }

    $excludeSql = '';
    if ($this->_columnExists($contact_table, 'exclude')) {
      $excludeSql = 'WHERE (exclude IS NULL OR exclude != 1)';
      $this->log('Column "exclude" exists, might skip some rows');
    }

    // now iterate through all entries
    $record = CRM_Core_DAO::executeQuery("SELECT * FROM {$contact_table} {$excludeSql}");
    $unsetIfEmpty = ['source_record_id', 'location', 'duration'];
    while ($record->fetch()) {
      if (empty($record->contact_id)) continue;
      $this->setHasExecuted();

      // compile activity
      $activity = $activity_template;
      $activity['subject']          = $this->resolveTokens($this->getConfigValue('subject'), $record);
      $activity['details']          = $this->resolveTokens($this->getConfigValue('details'), $record);
      $activity['source_record_id'] = $this->resolveTokens($this->getConfigValue('source_record_id'), $record);
      $activity['location']         = $this->resolveTokens($this->getConfigValue('location'), $record);
      $activity['duration']         = $this->resolveTokens($this->getConfigValue('duration'), $record);
      $activity['target_id'] = (int) $record->contact_id;
      foreach ($unsetIfEmpty as $field) {
        if (empty($activity[$field])) {
          unset($activity[$field]);
        }
      }
      if ($use_api) {
        civicrm_api3('Activity', 'create', $activity);

      } else {
        $this->createActivitySQL($activity);
      }
    }
  }

  public function createExclusionActivity() {
    $contact_table = $this->getContactTable();
    if (!$this->_columnExists($contact_table, 'exclude')) {
      return;
    }
    $count = CRM_Core_DAO::singleValueQuery("
      SELECT
        COUNT(*) AS contact_count
      FROM {$contact_table}
      JOIN civicrm_segmentation_exclude ON {$contact_table}.contact_id = civicrm_segmentation_exclude.contact_id AND campaign_id = %0
      WHERE exclude = 1", [[$this->getConfigValue('campaign_id'), 'Integer']]);
    if ($count > 0) {
      $record = CRM_Core_DAO::executeQuery("SELECT * FROM {$contact_table} WHERE exclude = 1 LIMIT 1")->fetch();
      $activity_data = [
        'activity_date_time' => $this->getDateTime($this->getConfigValue('activity_date_time')),
        'activity_type_id' => CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'activity_type_id', 'Exclusion Record'),
        'campaign_id' => $this->getConfigValue('campaign_id'),
        'status_id' => $this->getConfigValue('status_id'),
        'source_contact_id' => $this->getConfigValue('source_contact_id'),
        'subject' => ts('Control Group - %1', [
          1 => $this->resolveTokens($this->getConfigValue('subject'), $record)
        ]),
        'medium_id' => $this->getConfigValue('medium_id'),
        'priority_id' => $this->getConfigValue('priority_id'),
        'engagement_level' => $this->getConfigValue('engagement_level'),
      ];
      $unsetIfEmpty = [
        'source_contact_id',
        'campaign_id',
        'medium_id',
        'priority_id',
        'engagement_level',
      ];
      foreach ($unsetIfEmpty as $field) {
        if (empty($activity_data[$field])) {
          unset($activity_data[$field]);
        }
      }
      $activity = civicrm_api3('Activity', 'create', $activity_data);
      $query = "INSERT IGNORE INTO civicrm_activity_contact
                   (SELECT
                      NULL               AS id,
                      %0                 AS activity_id,
                      civicrm_contact.id AS contact_id,
                      3                  AS record_type
                    FROM {$contact_table}
                    JOIN civicrm_segmentation_exclude ON {$contact_table}.contact_id = civicrm_segmentation_exclude.contact_id
                    LEFT JOIN civicrm_contact ON civicrm_contact.id = civicrm_segmentation_exclude.contact_id
                    LEFT JOIN civicrm_segmentation_order ON civicrm_segmentation_order.campaign_id = civicrm_segmentation_exclude.campaign_id AND civicrm_segmentation_order.segment_id = civicrm_segmentation_exclude.segment_id
                    WHERE civicrm_segmentation_exclude.campaign_id = %1
                      AND civicrm_contact.is_deleted = 0)";
      CRM_Core_DAO::executeQuery($query, [[$activity['id'], 'Integer'], [$this->getConfigValue('campaign_id'), 'Integer']]);
      CRM_Segmentation_Logic::addExclusionSegmentForMassActivity($activity['id'], $this->getConfigValue('campaign_id'));
      $this->log("Created exclusion record activities for {$count} contacts");
    }
  }

  /**
   * use SQL to create that activity
   */
  protected function createActivitySQL($data) {
    // use the BAO
    $activity = new CRM_Activity_BAO_Activity();
    foreach ($data as $key => $value) {
      $activity->$key = $value;
    }
    $activity = $activity->save();

    if (!empty($data['target_id'])) {
      $link = new CRM_Activity_BAO_ActivityContact();
      $link->contact_id     = (int) $data['target_id'];
      $link->activity_id    = (int) $activity->id;
      $link->record_type_id = 3;
      $link->save();
      if (class_exists('CRM_Segmentation_Logic')) {
        CRM_Segmentation_Logic::addSegmentForActivityContact(
          $link->activity_id, $link->contact_id
        );
      }
      $link->free();
    }

    if (!empty($data['source_contact_id'])) {
      $link = new CRM_Activity_BAO_ActivityContact();
      $link->contact_id     = (int) $data['source_contact_id'];
      $link->activity_id    = (int) $activity->id;
      $link->record_type_id = 2;
      $link->save();
      $link->free();
    }

    if (!empty($data['assignee_id']) && is_array($data['assignee_id'])) {
      foreach ($data['assignee_id'] as $contact_id) {
        $link = new CRM_Activity_BAO_ActivityContact();
        $link->contact_id     = (int) $contact_id;
        $link->activity_id    = (int) $activity->id;
        $link->record_type_id = 1;
        $link->save();
        $link->free();
      }
    }

    $activity->free();
  }

  /**
   * Extract and format the time
   */
  protected function getDateTime($string) {
    if (empty($string)) {
      $string = 'now';
    }

    return date('YmdHis', strtotime($string));
  }
}