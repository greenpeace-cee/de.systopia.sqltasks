<div id="bootstrap-theme">
  <div class="crm-form-block" id="sqltasks-templates">
    <div class="st__pb-10">
      <a class="btn btn-secondary"
         href="{crmURL p='civicrm/sqltasks/manage' q='reset=1'}" title="{ts}Go to the SQL Task Manager{/ts}">
        <i class="crm-i fa-list"></i>
        <span>{ts}Go to the SQL Task Manager{/ts}</span>
      </a>
      <div class="st__flex st__gap-10">
        <button class="btn btn-primary" id="open-form">
          <i class="crm-i fa-plus"></i>
          <span>{ts}New Template{/ts}</span>
        </button>
      </div>
    </div>

    <div>
      <form id="new-template" class="hidden">
        <div class="panel panel-info">
          <div class="panel-heading">{ts}New Template{/ts}</div>
          <div class="panel-body">
            <div class="form-field">
              <label for="name">{ts}Name{/ts}</label>
              <input id="name" type="text" class="crm-form-text"/>
            </div>

            <div class="form-field">
              <label for="description">{ts}Description{/ts}</label>
              <input id="description" type="text" class="crm-form-text"/>
            </div>

            <div class="form-field">
              <label for="config">{ts}Configuration{/ts}</label>
              <input id="config" type="file" class="crm-form-file" />
            </div>

            <div class="st__flex st__gap-10 st__pt-10">
              <button id="submit-new-template" class="crm-button" type="submit">
                {ts}Submit{/ts}
              </button>

              <button id="clear-form" class="crm-button" type="button">
                {ts}Clear{/ts}
              </button>

              <button id="cancel" class="crm-button" type="button">
                <span>{ts}Cancel{/ts}</span>
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="st__pb-20">
      <div class="help">
        <p>{ts}Here you can manage configuration templates for SQL Tasks.{/ts}</p>
        <p>{ts}The selected default template will be used whenever a user wants to create a new task.{/ts}</p>
      </div>
    </div>

    <table class="dataTable">
      <thead>
      <tr class="columnheader">
        <th></th>
        <th>{ts}ID{/ts}</th>
        <th>{ts}Name{/ts}</th>
        <th>{ts}Description{/ts}</th>
        <th>{ts}Last modified{/ts}</th>
        <th>{ts}Configuration{/ts}</th>
        <th></th>
      </tr>
      </thead>

      <tbody>
      {foreach from=$templates item=template}
        <tr class="template-row crm-entity"
          data-action="create"
          data-entity="SqltaskTemplate"
          data-id="{$template.id}"
        >
          <td>
            {if $defaultTemplateId == $template.id}
              <i class="crm-i fa-check-circle"></i>
            {/if}
          </td>

          <td>{$template.id}</td>
          <td class="crm-editable" data-field="name">{$template.name}</td>
          <td class="crm-editable" data-field="description">{$template.description}</td>
          <td>{$template.last_modified}</td>
          <td class="crm-editable" data-field="config">{$template.config}</td>

          <td>
            <div class="st__flex st__gap-10">
              <button class="btn btn-secondary download" data-template-id="{$template.id}">
                <i class="crm-i fa-download"></i>
                <span>{ts}Download{/ts}</span>
              </button>

              <button class="btn btn-secondary set-default" data-template-id="{$template.id}"
                      {if $defaultTemplateId == $template.id}disabled{/if}>
                <i class="crm-i fa-check-circle"></i>
                <span>{ts}Set as default{/ts}</span>
              </button>

              <button class="btn btn-secondary delete" data-template-id="{$template.id}">
                <i class="crm-i fa-trash"></i>
                <span>{ts}Delete{/ts}</span>
              </button>
            </div>
          </td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  </div>
</div>

