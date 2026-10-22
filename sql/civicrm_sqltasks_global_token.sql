CREATE TABLE IF NOT EXISTS `civicrm_sqltasks_global_token` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique SqlTasks GlobalToken ID',
  `token_name` varchar(255) NOT NULL COMMENT 'token name',
  `data_type` int unsigned NOT NULL DEFAULT 1 COMMENT 'Token data type',
  `token_value` longtext NULL COMMENT 'Token value',
  `description` text NULL COMMENT 'Token description',
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB;
