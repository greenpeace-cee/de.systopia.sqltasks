<?php

use Civi\Utils\Settings;
use CRM_Sqltasks_ExtensionUtil as E;

class CRM_Sqltasks_Form_SqltaskSettings extends CRM_Core_Form {

  public function buildQuickForm() {
    $this->setTitle(E::ts('Sqltasks Settings'));
    $this->add('checkbox', Settings::SQLTASKS_IS_DISPATCHER_DISABLED, E::ts('IS sqltask dispatcher disabled?'));
    $this->add('number', Settings::SQLTASKS_MAX_FAILS_NUMBER, E::ts('Max fails number for sqltask executions'), null, TRUE);
    $this->addButtons([[
      'type' => 'submit',
      'name' => E::ts('Save'),
      'isDefault' => TRUE,
    ]]);

    $this->assign('settingsNames', [Settings::SQLTASKS_IS_DISPATCHER_DISABLED, Settings::SQLTASKS_MAX_FAILS_NUMBER]);
    $this->assign('SqltaskManagerLink', CRM_Utils_System::url('civicrm/a/', NULL, TRUE, "/sqltasks/manage"));
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    return [
        Settings::SQLTASKS_IS_DISPATCHER_DISABLED => Settings::isDispatcherDisabled(),
        Settings::SQLTASKS_MAX_FAILS_NUMBER => Settings::getMaxFailsNumber(),
    ];
  }

  public function postProcess() {
    $values = $this->exportValues();

    if (!empty($values[Settings::SQLTASKS_IS_DISPATCHER_DISABLED]) && $values[Settings::SQLTASKS_IS_DISPATCHER_DISABLED] == 1) {
      Settings::disabledDispatcher();
    } else {
      Settings::enabledDispatcher();
    }

    if (!empty($values[Settings::SQLTASKS_MAX_FAILS_NUMBER])) {
      Settings::setMaxFailsNumber($values[Settings::SQLTASKS_MAX_FAILS_NUMBER]);
    }

    CRM_Core_Session::setStatus(E::ts('Saved!'));
    parent::postProcess();
  }

}
