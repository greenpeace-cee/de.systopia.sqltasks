<?php

use CRM_Sqltasks_ExtensionUtil as E;

class CRM_Sqltasks_Page_GlobalTokenList extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(E::ts("SQL Task Global Token Manager"));

    $this->assign('globalTokens', CRM_Sqltasks_BAO_SqlTasksGlobalToken::getAllTokenFullData());

    CRM_Core_Resources::singleton()->addStyleFile('de.systopia.sqltasks', 'css/sqlTaskGeneral.css');
    CRM_Core_Resources::singleton()->addScriptFile('de.systopia.sqltasks', 'js/AddBodyClass.js', 1000, 'html-header');
    CRM_Core_Resources::singleton()->addStyleFile('de.systopia.sqltasks', 'css/globalTokenManager.css');

    parent::run();
  }

}
