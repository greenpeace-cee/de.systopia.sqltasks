<?php

use Civi\Api4\SqlTasksGlobalToken;
use CRM_Sqltasks_ExtensionUtil as E;

class CRM_Sqltasks_Form_GlobalToken extends CRM_Core_Form {

  protected ?int $tokenId = null;
  protected ?string $action = null;

  public function preProcess() {
    $this->action = CRM_Utils_Request::retrieve('action', 'String', $this);

    if (!in_array($this->action, [CRM_Core_Action::ADD, CRM_Core_Action::UPDATE, CRM_Core_Action::DELETE])) {
      throw new Exception('Unknown action: ' . $this->action);
    }

    if ($this->action == CRM_Core_Action::UPDATE || $this->action == CRM_Core_Action::DELETE) {
      $this->tokenId = CRM_Utils_Request::retrieve('id', 'Positive', $this);
      if (!CRM_Sqltasks_BAO_SqlTasksGlobalToken::isTokenExistById($this->tokenId)) {
        throw new Exception('Unknown token: ' . $this->tokenId);
      }
    }

    if ($this->action == CRM_Core_Action::ADD) {
      $this->setTitle(E::ts('Add Global Token'));
    } elseif ($this->action == CRM_Core_Action::UPDATE) {
      $this->setTitle(E::ts('Update Global Token'));
    } elseif ($this->action == CRM_Core_Action::DELETE) {
      $this->setTitle(E::ts('Delete Global Token'));
    }

    CRM_Core_Resources::singleton()->addStyleFile('de.systopia.sqltasks', 'css/sqlTaskGeneral.css');
    CRM_Core_Resources::singleton()->addScriptFile('de.systopia.sqltasks', 'js/AddBodyClass.js', 1000, 'html-header');
    CRM_Core_Resources::singleton()->addStyleFile('de.systopia.sqltasks', 'css/globalTokenManager.css');

    $this->controller->setDestination(CRM_Utils_System::url('civicrm/sqltasks/global-token', http_build_query([
      'action' => $this->action,
      'id' => $this->tokenId,
    ])));

    $this->addFormRule(['CRM_Sqltasks_Form_GlobalToken', 'validateTokenValue']);
  }

  public static function validateTokenValue($fields) {
    $errors = [];

    if (isset($fields['data_type'])) {
      $dataTypeName = CRM_Sqltasks_BAO_SqlTasksGlobalToken::getDataTypeName($fields['data_type']);
      $tokenValue = $fields['token_value'];

        switch ($dataTypeName) {
          case 'string':
            if (!is_string($tokenValue)) {
              $errors['token_value'] = 'Value must be a string';
            }
            break;

          case 'integer':
            if (!(filter_var($tokenValue, FILTER_VALIDATE_INT) !== false)) {
              $errors['token_value'] = 'Value must be a integer';
            }
            break;

          case 'decimal':
            if (!(filter_var($tokenValue, FILTER_VALIDATE_FLOAT) !== false)) {
              $errors['token_value'] = 'Value must be a decimal';
            }
            break;

          case 'datetime':
            if (is_string($tokenValue)) {
              $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $tokenValue);

              if (empty($dateTime)) {
                $errors['token_value'] = 'Value must be a datetime(Y-m-d H:i:s)';
              } elseif ($dateTime->format('Y-m-d H:i:s') !== $tokenValue) {
                $errors['token_value'] = 'Value must be a datetime(Y-m-d H:i:s)';
              }
            } else {
              $errors['token_value'] = 'Value must be a datetime(Y-m-d H:i:s)';
            }

            break;

          case 'boolean':
            $isBooleanValid = in_array(strtolower($tokenValue), ['1', '0', 'true', 'false'], true) || is_bool($tokenValue);
            if (!$isBooleanValid) {
              $errors['token_value'] = 'Value must be a boolean';
            }
            break;

          case 'json':
            if (is_string($tokenValue)) {
              json_decode($tokenValue);
              if (!(json_last_error() === JSON_ERROR_NONE)) {
                $errors['token_value'] = 'Value must be a json';
              }
            } else {
              $errors['token_value'] = 'Value must be a json';
            }
            break;

          default:
            $errors['data_type'] = 'Unknown data type';
      }
    }

    if (empty($errors)) {
      return true;
    } else {
      return $errors;
    }
  }

  public function buildQuickForm() {
    $dataTypeOptions = CRM_Sqltasks_BAO_SqlTasksGlobalToken::getTokenDataTypeOptions();

    $this->add('hidden', 'id', $this->tokenId);
    $this->add('text', 'token_name', E::ts('Token name'), ['size' => 255], TRUE);
    $this->add('text', 'token_value', E::ts('Token value'), ['size' => 255], TRUE);
    $this->add('select', 'data_type', ts('Data type'), $dataTypeOptions, true, ['class' => 'huge']);
    $this->add('textarea', 'description', E::ts('Description'), ['rows' => 4, 'cols' => 50]);

    $buttonName = '';
    if ($this->action == CRM_Core_Action::ADD) {
      $buttonName = E::ts('Add');
    } elseif ($this->action == CRM_Core_Action::UPDATE) {
      $buttonName = E::ts('Update');
    } elseif ($this->action == CRM_Core_Action::DELETE) {
      $buttonName = E::ts('Delete');
    }

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => $buttonName,
        'isDefault' => TRUE,
      ],
    ]);

    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    if (!empty($this->tokenId)) {
      $sqlTasksGlobalTokens = SqlTasksGlobalToken::get(false)
        ->addWhere('id', '=', $this->tokenId)
        ->setLimit(1)
        ->execute();
      foreach ($sqlTasksGlobalTokens as $sqlTasksGlobalToken) {
        if (is_array($sqlTasksGlobalToken['token_value']) && count($sqlTasksGlobalToken['token_value']) > 0) {
          $tokenValue = array_shift($sqlTasksGlobalToken['token_value']);
        } else {
          $tokenValue = $sqlTasksGlobalToken['token_value'];
        }

        return [
          'id' => $sqlTasksGlobalToken['id'],
          'token_name' => $sqlTasksGlobalToken['token_name'],
          'data_type' => $sqlTasksGlobalToken['data_type'],
          'token_value' => $tokenValue,
          'description' => $sqlTasksGlobalToken['description'],
        ];
      }
    }

    return [];
  }

  public function postProcess() {
    $values = $this->exportValues();

    if ($this->action == CRM_Core_Action::ADD) {
      SqlTasksGlobalToken::create(false)
        ->addValue('token_name', $values['token_name'])
        ->addValue('data_type', $values['data_type'])
        ->addValue('token_value', $values['token_value'])
        ->addValue('description', $values['description'])
        ->execute();

      CRM_Core_Session::setStatus(E::ts('Global Token is created'), E::ts('Global Token'), 'success');
    } elseif ($this->action == CRM_Core_Action::UPDATE) {
      SqlTasksGlobalToken::update(false)
        ->addWhere('id', '=', $this->tokenId)
        ->addValue('token_name', $values['token_name'])
        ->addValue('data_type', $values['data_type'])
        ->addValue('token_value', $values['token_value'])
        ->addValue('description', $values['description'])
        ->execute();

      CRM_Core_Session::setStatus(E::ts('Global Token is updated'), E::ts('Global Token'), 'success');
    } elseif ($this->action == CRM_Core_Action::DELETE) {
      SqlTasksGlobalToken::delete(TRUE)
        ->addWhere('id', '=', $this->tokenId)
        ->execute();

      CRM_Core_Session::setStatus(E::ts('Global Token is deleted'), E::ts('Global Token'), 'success');
    }

    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/sqltasks/global-token/list', http_build_query(['reset' => 1])));

    parent::postProcess();
  }

}
