<div id="bootstrap-theme">
  <div class="st__mb-20">
    <div class="crm-form-block">
      <div class="st__pb-20">
        <a class="btn btn-secondary"
           href="{crmURL p='civicrm/sqltasks/manage' q='reset=1'}" title="{ts}Go to the SQL Task Manager{/ts}">
          <i class="crm-i fa-list"></i>
          <span>{ts}Go to the SQL Task Manager{/ts}</span>
        </a>
      </div>

      <div class="crm-accordion-light {if $isCollapseFilter}collapsed{/if}">
        <div class="crm-accordion-header crm-master-accordion-header">
          {ts}Filter{/ts}
        </div>
        <div class="crm-accordion-body">
          {strip}
          <div class="st__pb-20">
            <div class="ste__search-item">
              <div>{$form.sqltask_id.label}</div>
              <div>{$form.sqltask_id.html}</div>
            </div>

            <div class="ste__search-item">
              <div>{$form.created_id.label}</div>
              <div>{$form.created_id.html}</div>
            </div>

            <div class="st__flex st__gap-10">
              <div class="ste__search-item">
                <div>{$form.from_start_date.label}</div>
                <div>{$form.from_start_date.html}</div>
              </div>
              <div class="ste__search-item">
                <div>{$form.to_start_date.label}</div>
                <div>{$form.to_start_date.html}</div>
              </div>
              <div class="ste__search-item">
                <div>{$form.from_end_date.label}</div>
                <div>{$form.from_end_date.html}</div>
              </div>
              <div class="ste__search-item">
                <div>{$form.to_end_date.label}</div>
                <div>{$form.to_end_date.html}</div>
              </div>
            </div>

            <div class="ste__search-item">
              <div>{$form.error_status.html}</div>
            </div>

            <div class="ste__search-item">
              <div>{$form.limit_per_page.label}</div>
              <div>{$form.limit_per_page.html}</div>
            </div>
          </div>

          <div class="st__flex st__gap-10">
            <button class="btn btn-primary" value="1" type="submit" name="_qf_SqltasksExecutionList_submit">
              <i class="crm-i fa-check"></i>
              <span>Search</span>
            </button>

            <a class="btn btn-secondary"
               href="{crmURL p='civicrm/sqltasks-execution/list' q='reset=1'}" title="{ts}Clear all search criteria{/ts}">
              <i class="crm-i fa-undo"></i>
              <span>{ts}Reset Form{/ts}</span>
            </a>
          </div>
        </div>
        {/strip}
      </div>
    </div>
  </div>

  {if $sqltasksExecutions}
  <div class="crm-form-block">
    <div class="st__pb-10 st__pt-10">
      <div>{$sqltasksExecutionsCount} Executions</div>
    </div>
  </div>

  <div class="crm-form-block st__p-0">
    {include file="CRM/Sqltasks/Chank/SqltasksExecutionListPagination.tpl"}
    <div>
      <table class="dataTable selector row-highlight st__m-0">
        <tr>
          <th>Task Id</th>
          <th>Error Count</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>
            Task Runtime
            <p class="ste__runtime-summary">Avg: {$summary.avg/1000|round:3}s | Min: {$summary.min/1000|round:3}s | Max: {$summary.max/1000|round:3}s</p>
          </th>
          <th>Executor</th>
          <th>Actions</th>
        </tr>
        {foreach from=$sqltasksExecutions item=sqltasksExecution}
          <tr class="{if $sqltasksExecution.is_has_errors}ste__error-execution{else}ste__success-execution{/if}">
            <td>{$sqltasksExecution.sqltask_id}</td>
            <td>{$sqltasksExecution.error_count}</td>
            <td>{$sqltasksExecution.start_date}</td>
            <td>{$sqltasksExecution.end_date}</td>
            <td>{$sqltasksExecution.runtime/1000}s</td>
            <td>
              <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$sqltasksExecution.created_id`"}"  target="_blank">{$sqltasksExecution.created_contact_display_name}</a>
            </td>
            <td>
              <a href="{crmURL p='civicrm/sqltasks-execution/view' q="reset=1&id=`$sqltasksExecution.id`"}" target="_blank">Detailed logs</a>
            </td>
          </tr>
        {/foreach}
      </table>
    </div>

    {include file="CRM/Sqltasks/Chank/SqltasksExecutionListPagination.tpl"}

    {else}
      <div>Empty result.</div>
    {/if}
  </div>
</div>
