<?php

namespace Civi\Sqltasks\Migration;

use Civi;
use Civi\Api4\SqlTasksGlobalToken;
use CRM_Sqltasks_BAO_SqlTasksGlobalToken;

/**
 * Migrate from ‘sqltasks_global_tokens’ setting to ‘SqlTasksGlobalToken’ entity.
 */
class SqlTasksGlobalTokenMigration implements Migration {

  public function up() {
    $globalTokens = Civi::settings()->get("sqltasks_global_tokens");
    if (!empty($globalTokens) && is_array($globalTokens)) {
      foreach ($globalTokens as $globalTokenName => $globalTokenValue) {
        if (CRM_Sqltasks_BAO_SqlTasksGlobalToken::isTokenExist($globalTokenName)) {
          continue;
        }

        SqlTasksGlobalToken::create(false)
          ->addValue('token_name', $globalTokenName)
          ->addValue('token_value', $globalTokenValue)
          ->execute();
      }
    }
  }

}
