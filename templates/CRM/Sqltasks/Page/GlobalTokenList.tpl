<div id="bootstrap-theme">
  <div class="crm-form-block stgt__page">
    <div class="st__flex st__gap-10 st__mb-10">
      <a class="btn btn-primary" href="{crmURL p='civicrm/sqltasks/manage' q='reset=1'}" title="{ts}Go to the SQL Task Manager{/ts}">
        <i class="crm-i fa-list"></i>
        <span>{ts}Go to the SQL Task Manager{/ts}</span>
      </a>
      <a href="{crmURL p='civicrm/sqltasks/global-token' q='reset=1&action=add'}" class="btn btn-primary st__m-0">
        <i class="crm-i fa-plus-circle"></i>
        <span>{ts}Create new global token{/ts}</span>
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
            <th>
              {ts}Name{/ts}
            </th>
            <th>
              {ts}Value{/ts}
            </th>
            <th>
              {ts}Data Type{/ts}
            </th>
            <th>
              {ts}description{/ts}
            </th>
            <th>
              {ts}Actions{/ts}
            </th>
          </tr>
          </thead>
          {foreach from=$globalTokens item=globalToken}
            <tr class="stgt__token-row" data-token-id="{$globalToken.id}">
              <td>
                {$globalToken.token_name}
              </td>
              <td>
                {$globalToken.token_value}
              </td>
              <td>
                <div>{$globalToken.data_type_label}<small> [{$globalToken.data_type}]</small></div>
              </td>
              <td>
                {$globalToken.description}
              </td>
              <td>
                <div class="st__flex st__gap-10 st__width-250">
                  <a href="{crmURL p='civicrm/sqltasks/global-token' q="reset=1&action=update&id={$globalToken.id}"}" class="btn btn-secondary">
                    <i class="crm-i fa-pencil"></i>
                    <span>{ts}Edit{/ts}</span>
                  </a>
                  <a class="btn btn-danger stgt__delete-token-button">
                    <i class="crm-i fa-trash"></i>
                    <span>{ts}Delete{/ts}</span>
                  </a>
                </div>
              </td>
            </tr>
          {/foreach}
        </table>
      </div>
    </div>
  </div>
</div>

{literal}
<script>
  CRM.$(function ($) {
    $('.stgt__delete-token-button').click(function () {
      var tokenRow = $(this).closest('.stgt__token-row');
      var tokenId = tokenRow.data('token-id');

      CRM.confirm({
        title: ts('Remove global token'),
        message: ts('Are you sure?'),
      }).on('crmConfirm:yes', function() {
        console.log('tokenId');
        console.log(tokenId);
        CRM.api4('SqlTasksGlobalToken', 'delete', {
          where: [["id", "=", tokenId]]
        }).then(function(results) {
          if (results.count > 0) {
            CRM.alert(ts('Global token successfully deleted!'), ts("Deleting global token"), "success");
            tokenRow.remove();
          } else {
            CRM.alert('Something went wrong', ts("Error deleting global token"), "error");
          }
        }, function(failure) {
          CRM.alert(failure, ts("Error deleting global token"), "error");
        });
      });
    });
  });
</script>
{/literal}
