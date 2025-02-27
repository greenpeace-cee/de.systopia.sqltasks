<?php
namespace Civi\Api4;

use Civi\Api4\Action\SqlTask\Analyze;

/**
 * SqlTask entity.
 *
 * Provided by the SQL Tasks extension.
 *
 * @package Civi\Api4
 */
class SqlTask extends Generic\DAOEntity {

  /**
   * Analyze SQL Task
   *
   * @param bool $checkPermissions
   * @return \Civi\Api4\Action\SqlTask\Analyze
   */
  public static function analyze($checkPermissions = TRUE): Analyze {
    $action = new Analyze(__CLASS__, __FUNCTION__);
    return $action->setCheckPermissions($checkPermissions);
  }
}
