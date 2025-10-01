<?php
// phpcs:disable
use Civi\Api4\OptionValue;
use Civi\Api4\SqlTasksGlobalToken;
use CRM_Sqltasks_ExtensionUtil as E;
// phpcs:enable

class CRM_Sqltasks_BAO_SqlTasksGlobalToken extends CRM_Sqltasks_DAO_SqlTasksGlobalToken {

  public static function isTokenExist($tokenName): bool {
    $sqlTasksGlobalTokens = SqlTasksGlobalToken::get(false)
      ->addWhere('token_name', '=', $tokenName)
      ->setLimit(1)
      ->execute();

    foreach ($sqlTasksGlobalTokens as $sqlTasksGlobalToken) {
      return true;
    }

    return false;
  }

  public static function getTokenValue($tokenName): string {
    $sqlTasksGlobalTokens = SqlTasksGlobalToken::get(false)
      ->addWhere('token_name', '=', $tokenName)
      ->setLimit(1)
      ->execute();

    foreach ($sqlTasksGlobalTokens as $sqlTasksGlobalToken) {
      return $sqlTasksGlobalToken['token_value'];
    }

    return '';
  }

  public static function isTokenExistById($tokenId): bool {
    $sqlTasksGlobalTokens = SqlTasksGlobalToken::get(false)
      ->addWhere('id', '=', $tokenId)
      ->setLimit(1)
      ->execute();

    foreach ($sqlTasksGlobalTokens as $sqlTasksGlobalToken) {
      return true;
    }

    return false;
  }

  public static function getTokenDataTypeOptions(): array {
    $options = [];
    $optionValues = OptionValue::get(false)
      ->addWhere('option_group_id:name', '=', 'sqltasks_global_token_data_type')
      ->execute();

    foreach ($optionValues as $optionValue) {
      $options[$optionValue['value']] = $optionValue['label'];
    }

    return $options;
  }

  public static function getDataTypeName($dataTypeValue): string {
    $optionValues = OptionValue::get(false)
      ->addWhere('option_group_id:name', '=', 'sqltasks_global_token_data_type')
      ->addWhere('value', '=', $dataTypeValue)
      ->execute();

    foreach ($optionValues as $optionValue) {
      return $optionValue['name'];
    }

    return '';
  }

  public static function getAllTokenFullData(): array {
    $sqlTasksGlobalTokens = SqlTasksGlobalToken::get(false)->execute();
    $dataTypeOptions = CRM_Sqltasks_BAO_SqlTasksGlobalToken::getTokenDataTypeOptions();

    $tokensData = [];
    foreach ($sqlTasksGlobalTokens as $sqlTasksGlobalToken) {
      $dataTypeLabel = !empty($dataTypeOptions[$sqlTasksGlobalToken['data_type']]) ? $dataTypeOptions[$sqlTasksGlobalToken['data_type']] : 'Unknown';

      $tokensData[] = [
        'id' => $sqlTasksGlobalToken['id'],
        'token_name' => $sqlTasksGlobalToken['token_name'],
        'token_value' => $sqlTasksGlobalToken['token_value'],
        'data_type' => $sqlTasksGlobalToken['data_type'],
        'data_type_label' => $dataTypeLabel,
        'description' => $sqlTasksGlobalToken['description'],
      ];
    }

    return $tokensData;
  }

  /**
   * Create a new SqlTasksGlobalToken based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Sqltasks_DAO_SqlTasksGlobalToken|NULL
   */
  public static function create(array $params): ?CRM_Sqltasks_DAO_SqlTasksGlobalToken
  {
    $className = 'CRM_Sqltasks_DAO_SqlTasksGlobalToken';
    $entityName = 'SqlTasksGlobalToken';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

}
