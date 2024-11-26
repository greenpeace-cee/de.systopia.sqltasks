-- +--------------------------------------------------------------------+
-- | Copyright CiviCRM LLC. All rights reserved.                        |
-- |                                                                    |
-- | This work is published under the GNU AGPLv3 license with some      |
-- | permitted exceptions and without any warranty. For full license    |
-- | and copyright information, see https://civicrm.org/licensing       |
-- +--------------------------------------------------------------------+
--
-- Generated from schema.tpl
-- DO NOT EDIT.  Generated by CRM_Core_CodeGen
--
-- /*******************************************************
-- *
-- * Clean up the existing tables - this section generated from drop.tpl
-- *
-- *******************************************************/

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `civicrm_sqltasks_template`;
DROP TABLE IF EXISTS `civicrm_sqltasks_execution`;
DROP TABLE IF EXISTS `civicrm_sqltasks_action_template`;
DROP TABLE IF EXISTS `civicrm_sqltasks`;

SET FOREIGN_KEY_CHECKS=1;
-- /*******************************************************
-- *
-- * Create new tables
-- *
-- *******************************************************/

-- /*******************************************************
-- *
-- * civicrm_sqltasks
-- *
-- *******************************************************/
CREATE TABLE `civicrm_sqltasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique SqlTask ID',
  `name` varchar(255) NULL COMMENT 'Name of the task',
  `description` text NULL COMMENT 'Description of the task',
  `category` varchar(64) NULL COMMENT 'Category of the task',
  `scheduled` varchar(256) NULL COMMENT 'Regular execution frequency (\"daily\", \"weekly\", \"monthly\" etc.)',
  `enabled` int unsigned NULL COMMENT 'Is the task enabled?',
  `weight` int unsigned NULL COMMENT 'Defines execution order',
  `last_execution` datetime NULL COMMENT 'Date/time of the last task execution',
  `running_since` datetime NULL COMMENT 'Start time of the current execution (if the task is running)',
  `run_permissions` varchar(256) NULL COMMENT 'Required permissions to run this task',
  `input_required` int unsigned NOT NULL DEFAULT 0 COMMENT 'Does the task require input data?',
  `input_spec` text NULL COMMENT 'Input parameter specification (JSON)',
  `archive_date` datetime NULL COMMENT 'Date/time the task was archived',
  `last_runtime` int unsigned NULL COMMENT 'Duration of the last execution in milliseconds',
  `parallel_exec` int unsigned NOT NULL DEFAULT 0 COMMENT 'Should this task be executed in parallel?',
  `config` text NULL COMMENT 'Task configuration (JSON)',
  `abort_on_error` int unsigned NOT NULL DEFAULT 0 COMMENT 'Should task execution abort in case of an error?',
  `last_modified` datetime NULL COMMENT 'Date/time of the latest change to the task configuration',
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB;

-- /*******************************************************
-- *
-- * civicrm_sqltasks_action_template
-- *
-- * SQL Task configuration action template
-- *
-- *******************************************************/
CREATE TABLE `civicrm_sqltasks_action_template` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique SqltasksActionTemplates ID',
  `name` varchar(255) NOT NULL COMMENT 'Action Template Name',
  `type` varchar(255) NOT NULL COMMENT 'Action Template Type',
  `config` text NOT NULL COMMENT 'Action Template Configuration',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `index_unique_name_type`(name, type)
)
ENGINE=InnoDB;

-- /*******************************************************
-- *
-- * civicrm_sqltasks_execution
-- *
-- * SQL Task execution history
-- *
-- *******************************************************/
CREATE TABLE `civicrm_sqltasks_execution` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique SqltasksExecution ID',
  `sqltask_id` int unsigned COMMENT 'FK to SQL Task',
  `start_date` datetime NULL COMMENT 'Start date of execution',
  `end_date` datetime NULL COMMENT 'End date of execution',
  `runtime` int unsigned NULL COMMENT 'Task runtime in milliseconds',
  `input` longtext NULL COMMENT 'Task input',
  `log` longtext NULL COMMENT 'Task result log',
  `files` longtext NULL COMMENT 'Task result files (JSON)',
  `error_count` int unsigned NULL COMMENT 'Task execution error count',
  `created_id` int unsigned NULL COMMENT 'Contact ID of task executor',
  PRIMARY KEY (`id`),
  CONSTRAINT FK_civicrm_sqltasks_execution_sqltask_id FOREIGN KEY (`sqltask_id`) REFERENCES `civicrm_sqltasks`(`id`) ON DELETE SET NULL,
  CONSTRAINT FK_civicrm_sqltasks_execution_created_id FOREIGN KEY (`created_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE SET NULL
)
ENGINE=InnoDB;

-- /*******************************************************
-- *
-- * civicrm_sqltasks_template
-- *
-- * SQL Task configuration template
-- *
-- *******************************************************/
CREATE TABLE `civicrm_sqltasks_template` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique SqltasksTemplate ID',
  `name` varchar(255) COMMENT 'name of the template',
  `description` text COMMENT 'template description',
  `config` text COMMENT 'configuration (JSON)',
  `last_modified` datetime COMMENT 'last time the template has been modified',
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB;
