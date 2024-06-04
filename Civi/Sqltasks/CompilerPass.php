<?php

namespace Civi\Sqltasks;

use Civi\Api4;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use CRM_Sqltasks_ExtensionUtil as E;

class CompilerPass implements CompilerPassInterface {

  public function process(ContainerBuilder $container) {
    if ($container->hasDefinition('action_provider')) {
      $action_provider_definition = $container->getDefinition('action_provider');

      $action_provider_definition->addMethodCall('addAction', [
        'SqltasksRunSQLTask',
        'Civi\Sqltasks\Actions\RunSQLTask',
        E::ts('Run SQL Task'),
      ], []);

      $query = \CRM_Core_DAO::executeQuery("
        SELECT id FROM civicrm_sqltasks WHERE input_spec IS NOT NULL ORDER BY id ASC;
      ");

      while ($query->fetch()) {
        $task_id = $query->id;
        $task_class_name = "\\Civi\\Sqltasks\\Actions\\RunSQLTask_$task_id";

        $action_provider_definition->addMethodCall('addAction', [
          "SqltasksRunSQLTask_$task_id",
          $task_class_name,
          E::ts("Run SQL Task #$task_id"),
        ], []);
      }
    }
  }

}
