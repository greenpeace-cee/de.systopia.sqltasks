<div id="bootstrap-theme">
  <div class="crm-form-block">
    <div class="st__flex st__gap-10 st__pb-20">
      <a class="btn btn-secondary" href="{crmURL p='civicrm/sqltasks/manage' q='reset=1'}" title="{ts}Go to the SQL Task Manager{/ts}">
        <i class="crm-i fa-list"></i>
        <span>{ts}Go to the SQL Task Manager{/ts}</span>
      </a>
    </div>

    <div class="st__pb-20">
      <div>
        {foreach from=$settingsNames item=elementName}
          <div class="crm-section">
            <div class="label">{$form.$elementName.label}{help id="$elementName"}</div>
            <div class="content">{$form.$elementName.html}</div>
            <div class="clear"></div>
          </div>
        {/foreach}
      </div>
    </div>

    <div class="st__flex st__gap-10">
      {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
  </div>
</div>
