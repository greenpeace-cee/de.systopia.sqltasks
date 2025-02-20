<div id="bootstrap-theme">
  <div class="crm-form-block stgt__page">
    <div>
      <a class="btn btn-primary" href="{crmURL p='civicrm/sqltasks/manage' q='reset=1'}" title="{ts}Go to the SQL Task Manager{/ts}">
        <i class="crm-i fa-list"></i>
        <span>{ts}Go to the SQL Task Manager{/ts}</span>
      </a>
    </div>

    <div>
      <div class="help">
        <p>{ts}You can manage global tokens below. Global tokens can be used to store values that are used in multiple SQL Tasks, allowing for the value to be changed everywhere by just editing the token. This can be useful for things like credentials.{/ts}</p>
        <p>{ts}To use a global token, use this syntax in any task field:{/ts}</p>
        <p><code>{ts}{literal}{config.name_of_token}{/literal}{/ts}</code></p>
      </div>

      <div class="stgt__table-wrap">
        <table class="stgt__table form-layout">
          <thead>
          <tr>
            <th class="stgt__large-column">
              {ts}Name{/ts}
            </th>
            <th class="stgt__large-column">
              {ts}Value{/ts}
            </th>
            <th>
              {ts}Actions{/ts}
            </th>
          </tr>
          </thead>
          <tr class="stgt__add-new-token-row">
            <td>
              <input class="crm-form-text stgt__create-token-name-input crm-form-text" type="text" maxlength="{$maxLengthOfTokenName}">
              <div class="stgt__create-token-error-message-wrap"></div>
            </td>
            <td>
              <input class="crm-form-text stgt__create-token-value-input crm-form-text" type="text">
            </td>
            <td>
              <div>
                <button class="btn btn-primary st__m-0" id="globalTokenCreateButton">
                  <i class="crm-i fa-plus-circle"></i>
                  <span>{ts}Create new global token{/ts}</span>
                </button>
              </div>
            </td>
          </tr>
        </table>
      </div>

    </div>

    {*this table is hidden and the row will be used like a template*}
    <table class="stgt__table-template">
      <tr class="stgt__row-template">
        <td>
          <div class="stgt__edit-mode">
            <input class="stgt__edit-token-name-input crm-form-text" maxlength="{$maxLengthOfTokenName}" type="text">
            <div class="stgt__edit-token-error-message-wrap"></div>
          </div>
          <div class="stgt__view-mode">
            <div class="stgt__token-name"></div>
          </div>
        </td>
        <td>
          <div class="stgt__edit-mode">
            <input class="stgt__edit-token-value-input crm-form-text" type="text">
          </div>
          <div class="stgt__view-mode">
            <div class="stgt__token-value">
            </div>
          </div>
        </td>
        <td>
          <div class="stgt__edit-mode">
            <button class="btn btn-primary stgt__update-token-data-button"">
              <i class="crm-i fa-check"></i>
              <span>{ts}Update{/ts}</span>
            </button>
            <button class="btn btn-secondary stgt__cancel-editing-token-data-button">
              <i class="crm-i fa-close"></i>
              <span>{ts}Cancel{/ts}</span>
            </button>
          </div>
          <div class="stgt__view-mode">
            <button class="btn btn-secondary stgt__edit-token-data-button">
              <i class="crm-i fa-pencil"></i>
              <span>{ts}Edit{/ts}</span>
            </button>
            <button class="btn btn-danger stgt__delete-token-button">
              <i class="crm-i fa-trash"></i>
              <span>{ts}Delete{/ts}</span>
            </button>
          </div>
        </td>
      </tr>
    </table>
  </div>
</div>

{literal}
<script>
    CRM.$(function ($) {
        var tokens = {/literal}{$tokens}{literal};
        var templateColumns = $('.stgt__table-template .stgt__row-template').html();
        var table =  $('.stgt__table');
        var createTokenErrorMessageElement =  $('.stgt__create-token-error-message-wrap');

        addTokensToTable();
        initCreatingNewToken();

        function addTokensToTable() {
            for (var i = 0; i < tokens.length; i++) {
                addTokenToTable(tokens[i]);
            }
        }

        function addTokenToTable(token) {
            table.find('.stgt__add-new-token-row').before('<tr class="stgt__row" data-token-name="' + token.name + '"></td>');
            var tokenRow = $("tr.stgt__row[data-token-name='" + token.name + "']");
            tokenRow.append(templateColumns);

            tokenRow.find('.stgt__token-name').text(token.name);
            tokenRow.find('.stgt__token-value').text(token.value);

            tokenRow.find('.stgt__edit-token-data-button').click(function () {
                tokenRow.addClass('edit-mode');
                tokenRow.find('.stgt__edit-token-name-input').val(tokenRow.find('.stgt__token-name').text());
                tokenRow.find('.stgt__edit-token-value-input').val(tokenRow.find('.stgt__token-value').text());
            });

            tokenRow.find('.stgt__delete-token-button').click(function () {
                showDeleteTokenWindow(tokenRow);
            });

            tokenRow.find('.stgt__update-token-data-button').click(function () {
                updateTokenData(tokenRow);
            });

            tokenRow.find('.stgt__cancel-editing-token-data-button').click(function () {
                tokenRow.removeClass('edit-mode');
            });

            return tokenRow;
        }

        function updateTokenData(tokenRow) {
            var newName = tokenRow.find('.stgt__edit-token-name-input').val();
            var value = tokenRow.find('.stgt__edit-token-value-input').val();
            var name = tokenRow.data('token-name');
            var errorMessageElement = tokenRow.find('.stgt__edit-token-error-message-wrap');
            errorMessageElement.empty();

            if (newName.length < 1) {
                errorMessageElement.append(getErrorMessageHtml(ts("Name cannot be empty.")));
                return;
            }

            CRM.api3('SqltaskGlobalToken', 'update_token', {
                "new_name": newName,
                "value": value,
                "name": name
            }).done(function(result) {
                if (result.is_error === 0) {
                    tokenRow.removeClass('edit-mode');
                    tokenRow.find('.stgt__token-name').text(newName);
                    tokenRow.find('.stgt__token-value').text(value);
                    tokenRow.data('token-name', newName);
                    tokenRow.effect('highlight', {}, 1500);
                    CRM.alert(ts('Global token successfully updated!'), ts("Updating global token"), "success");
                } else {
                    errorMessageElement.append(getErrorMessageHtml(result.error_message));
                }
            });
        }

        function showDeleteTokenWindow(tokenRow) {
            CRM.confirm({
                title: ts('Remove global token'),
                message: ts('Are you sure?'),
            }).on('crmConfirm:yes', function() {
                var tokenName = tokenRow.data('token-name');
                CRM.api3('SqltaskGlobalToken', 'delete_token', {"name": tokenName}).done(function(result) {
                    if (result.is_error === 0) {
                        CRM.alert(ts('Global token successfully deleted!'), ts("Deleting global token"), "success");
                        tokenRow.remove();
                    } else {
                        CRM.alert(result.error_message, ts("Error deleting global token"), "error");
                    }
                });
            });
        }

        function initCreatingNewToken() {
            $('#globalTokenCreateButton').click(function () {
                var row = $(this).closest('.stgt__add-new-token-row');
                var nameElement = row.find('.stgt__create-token-name-input');
                var valueElement = row.find('.stgt__create-token-value-input');
                var name = nameElement.val();
                var value = valueElement.val();
                createTokenErrorMessageElement.empty();

                if (name.length < 1) {
                    createTokenErrorMessageElement.append(getErrorMessageHtml(ts("Name cannot be empty.")));
                    return;
                }

                CRM.api3('SqltaskGlobalToken', 'create', {
                    "name": name,
                    "value": value
                }).done(function(result) {
                    if (result.is_error === 0) {
                        var tokenRow = addTokenToTable({'name' : name, 'value' : value});
                        tokenRow.effect('highlight', {}, 1500);
                        valueElement.val('');
                        nameElement.val('');
                        CRM.alert(ts('Global token successfully created!'), ts("Creating global token"), "success");
                    } else {
                        createTokenErrorMessageElement.append(getErrorMessageHtml(result.error_message));
                    }
                });
            });
        }

        function getErrorMessageHtml(message) {
            return '<span class="crm-error">' + message + '</span>';
        }

    });
</script>
{/literal}
