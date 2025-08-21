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
class CRM_Sqltasks_Action_SyncTag extends CRM_Sqltasks_Action_ContactSet {

  /**
   * Get identifier string
   */
  public function getID() {
    return 'tag';
  }

  /**
   * Get a human readable name
   */
  public function getName() {
    return E::ts('Synchronise Tag');
  }

  /**
   * Get default template order
   *
   * @return int
   */
  public static function getDefaultOrder() {
    return 500;
  }

  /**
   * RUN this action
   */
  public function execute() {
    $use_api = $this->getConfigValue('use_api');
    if ($use_api) {
      $this->executeAPI();
    } else {
      $this->executeSQL();
    }
  }

  /**
   * Run the synchronisation purely by SQL
   */
  protected function executeSQL() {
    $contact_table = $this->getContactTable();
    $entity_table = $this->getEntityTable();
    $use_tags_from_table = (bool) $this->getConfigValue('use_tags_from_table');

    if ($this->_columnExists($contact_table, 'exclude')) {
      $exclude_clause = '(ct.exclude IS NULL OR ct.exclude != 1)';
      $this->log('Column "exclude" exists, might skip some rows');
    } else {
      $exclude_clause = '1';
    }

    if ($use_tags_from_table) {
      // Un-tag all entities that are not in the contact table
      CRM_Core_DAO::executeQuery("
        DELETE et
        FROM civicrm_entity_tag et
        JOIN civicrm_tag t
          ON t.id = et.tag_id
          AND t.name IN (SELECT DISTINCT tag_name FROM $contact_table)
        LEFT JOIN $contact_table ct
          ON ct.contact_id = et.entity_id
          AND ct.tag_name = t.name
        WHERE et.entity_table = '$entity_table'
          AND ct.contact_id IS NULL
          AND $exclude_clause
      ");

      // Tag all entities in the contact table that are not already tagged
      CRM_Core_DAO::executeQuery("
        INSERT INTO civicrm_entity_tag (entity_table, entity_id, tag_id)
        SELECT
           '$entity_table' AS entity_table,
           ct.contact_id   AS entity_id,
           t.id            AS tag_id
        FROM $contact_table ct
        JOIN civicrm_tag t
          ON t.name = ct.tag_name
        LEFT JOIN civicrm_entity_tag et
          ON et.entity_id = ct.contact_id
          AND et.entity_table = '$entity_table'
          AND et.tag_id = t.id
        WHERE
          et.entity_id IS NULL
          AND $exclude_clause
      ");
    } else {
      $tag_id = (int) $this->getConfigValue('tag_id');

      // Un-tag all entities that are not in the contact table
      CRM_Core_DAO::executeQuery("
        DELETE et
        FROM civicrm_entity_tag et
        LEFT JOIN $contact_table ct
          ON ct.contact_id = et.entity_id
        WHERE
          et.entity_table = '$entity_table'
          AND et.tag_id = $tag_id
          AND ct.contact_id IS NULL
          AND $exclude_clause
      ");

      // Tag all entities in the contact table that are not already tagged
      CRM_Core_DAO::executeQuery("
        INSERT INTO civicrm_entity_tag (entity_table, entity_id, tag_id)
        SELECT DISTINCT
           '$entity_table' AS entity_table,
           ct.contact_id   AS entity_id,
           $tag_id         AS tag_id
        FROM $contact_table ct
        LEFT JOIN civicrm_entity_tag et
          ON et.entity_id = ct.contact_id
          AND et.entity_table = '$entity_table'
          AND et.tag_id = $tag_id
        WHERE
          et.entity_id IS NULL
          AND $exclude_clause
      ");
    }
  }

  /**
   * Run the synchronisation via API
   */
  protected function executeAPI() {
    $contact_table = $this->getContactTable();
    $entity_table = $this->getEntityTable();
    $use_tags_from_table = (bool) $this->getConfigValue('use_tags_from_table');

    if ($this->_columnExists($contact_table, 'exclude')) {
      $exclude_clause = '(ct.exclude IS NULL OR ct.exclude != 1)';
      $this->log('Column "exclude" exists, might skip some rows');
    } else {
      $exclude_clause = '1';
    }

    if ($use_tags_from_table) {
      // Un-tag all entities that are not in the contact table
      $entities2untag = CRM_Core_DAO::executeQuery("
        SELECT
          et.id        AS id,
          et.entity_id AS entity_id
        FROM civicrm_entity_tag et
        JOIN civicrm_tag t
          ON t.id = et.tag_id
          AND t.name IN (SELECT DISTINCT tag_name FROM $contact_table)
        LEFT JOIN $contact_table ct
          ON ct.contact_id = et.entity_id
          AND ct.tag_name = t.name
        WHERE et.entity_table = '$entity_table'
          AND ct.contact_id IS NULL
          AND $exclude_clause
      ");

      while ($entities2untag->fetch()) {
        $result = civicrm_api3('EntityTag', 'delete', [
          'id'         => $entities2untag->id,
          'contact_id' => $entities2untag->entity_id,
        ]);
      }

      // Tag all entities in the contact table that are not already tagged
      $entities2tag = CRM_Core_DAO::executeQuery("
        SELECT
           ct.contact_id AS entity_id,
           t.id          AS tag_id
        FROM $contact_table ct
        JOIN civicrm_tag t
          ON t.name = ct.tag_name
        LEFT JOIN civicrm_entity_tag et
          ON et.entity_id = ct.contact_id
          AND et.entity_table = '$entity_table'
          AND et.tag_id = t.id
        WHERE
          et.entity_id IS NULL
          AND $exclude_clause
      ");

      while ($entities2tag->fetch()) {
        civicrm_api3('EntityTag', 'create', [
          'entity_id'    => $entities2tag->entity_id,
          'entity_table' => $entity_table,
          'tag_id'       => $entities2tag->tag_id,
        ]);
      }
    } else {
      $tag_id = (int) $this->getConfigValue('tag_id');

      // Un-tag all entities that are not in the contact table
      $entities2untag = CRM_Core_DAO::executeQuery("
        SELECT
          et.id        AS id,
          et.entity_id AS entity_id
        FROM civicrm_entity_tag et
        LEFT JOIN $contact_table ct
          ON ct.contact_id = et.entity_id
        WHERE
          et.entity_table = '$entity_table'
          AND et.tag_id = $tag_id
          AND ct.contact_id IS NULL
          AND $exclude_clause
      ");

      while ($entities2untag->fetch()) {
        civicrm_api3('EntityTag', 'delete', [
          'id'         => $entities2untag->id,
          'contact_id' => $entities2untag->entity_id,
        ]);
      }

      // Tag all entities in the contact table that are not already tagged
      $entities2tag = CRM_Core_DAO::executeQuery("
        SELECT DISTINCT ct.contact_id AS entity_id
        FROM $contact_table ct
        LEFT JOIN civicrm_entity_tag et
          ON et.entity_id = ct.contact_id
          AND et.entity_table = '$entity_table'
          AND et.tag_id = $tag_id
        WHERE
          et.entity_id IS NULL
          AND $exclude_clause
      ");

      while ($entities2tag->fetch()) {
        civicrm_api3('EntityTag', 'create', [
          'entity_id'    => $entities2tag->entity_id,
          'entity_table' => $entity_table,
          'tag_id'       => $tag_id,
        ]);
      }
    }
  }

  /**
   * get a list of eligible groups
   */
  protected function getEligibleTags() {
    $tag_list = array();
    $tag_query = civicrm_api3('Tag', 'get', array(
      'is_enabled'   => 1,
      'option.limit' => 0,
      'return'       => 'id,name'))['values'];
    foreach ($tag_query as $tag) {
      $tag_list[$tag['id']] = CRM_Utils_Array::value('name', $tag, 'Tag') . ' [' . $tag['id'] . ']';
    }
    return $tag_list;
  }

  /**
   * Get a list of eligible groups
   */
  public static function getEligibleEntities() {
    return array(
      'civicrm_contact'      => E::ts("Contacts"),
      'civicrm_activity'     => E::ts("Activities"),
      'civicrm_case'         => E::ts("Cases"),
      'civicrm_file'         => E::ts("Attachments"),
      'civicrm_membership'   => E::ts("Memberships"),
      'civicrm_contribution' => E::ts("Contributions"),
    );
  }

  /**
   * get the entity table to use for the tag
   *
   * defaults to 'civicrm_contact'
   */
  public function getEntityTable() {
    $table_name = $this->getConfigValue('entity_table');
    $table_name = trim($table_name);
    if (empty($table_name)) {
      return 'civicrm_contact';
    } else {
      return $table_name;
    }
  }
}
