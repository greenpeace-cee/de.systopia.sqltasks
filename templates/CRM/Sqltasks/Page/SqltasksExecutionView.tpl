<div id="bootstrap-theme">
  <div class="crm-form-block">
    <div class="st__flex st__gap-10 st__align-items-center st__pb-20">
      <a class="btn btn-secondary" href="{crmURL p='civicrm/sqltasks-execution/list' q='reset=1'}" title="{ts}Go to lis{/ts}">
        <i class="crm-i fa-list"></i>
        <span>{ts}Back to list{/ts}</span>
      </a>
      <a class="btn btn-secondary" href="{$manageSqlTaskUrl}">
        <i class="crm-i fa-pencil"></i>
        <span>{ts}Configure Task{/ts}</span>
      </a>
    </div>

    <div>
      <div>
        <div><b>Task: [{$task.id}] {$task.name}</b></div>
        <div class="st__font-style-italic st__max-width-700 st__pt-10">{$task.description}</div>
      </div>

      <div class="st__flex st__gap-10">
        <div class="st__width-250 st__ptb-10">
          <div class="st__font-weight-bold">Start date</div>
          <div>{$sqltasksExecution.start_date}</div>
        </div>

        <div class="st__width-250 st__ptb-10">
          <div class="st__font-weight-bold">End date</div>
          <div>{$sqltasksExecution.end_date}</div>
        </div>

        <div class="st__width-250 st__ptb-10">
          <div class="st__font-weight-bold">Task runtime</div>
          <div>{$sqltasksExecution.runtime/1000}s</div>
        </div>
      </div>

      <div class="st__flex st__gap-10">
        <div class="st__width-250 st__ptb-10">
          <div class="st__font-weight-bold">Error Count</div>
          <div class=" {if $sqltasksExecution.is_has_errors}crm-error{/if}">{$sqltasksExecution.error_count}</div>
        </div>

        <div class="st__width-250 st__ptb-10">
          <div class="st__font-weight-bold">Input</div>
          <div>{$sqltasksExecution.input}</div>
        </div>

        <div class="st__width-250 st__ptb-10">
          <div class="st__font-weight-bold">Executed by</div>
          <div>
            <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$sqltasksExecution.id`"}"  target="_blank">{$sqltasksExecution.created_contact_display_name}</a>
          </div>
        </div>

        <div class="st__width-250 st__ptb-10">
          <div class="st__font-weight-bold">Files:</div>
          <div>{$sqltasksExecution.files}</div>
        </div>
      </div>
    </div>

    <div class="st__font-weight-bold">Execution log:</div>

    <pre>
{foreach from=$logsTaskExecution item=logItem}{$logItem.date_time_obj->format("m-d-Y H:i:s.u")}: {$logItem.message}
{/foreach}</pre>
  </div>
</div>
