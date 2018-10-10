{*-------------------------------------------------------+
| SYSTOPIA SQL TASKS EXTENSION                           |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*}

<style type="text/css">
{literal}
.crm-sqlactivity-action-icons a, .crm-sqlactivity-action-icons a:link, .crm-sqlactivity-action-icons a:visited {
  padding-right: 5px;
  color: inherit;
}
.crm-sqlactivity-action-warning {
  color: #ffba3b !important;
}
.crm-sqlactivity-action-warning-text {
  display: none;
  font-weight: normal;
  padding-right: 5px;
  color: #F5F6F1;
  font-size: 85%;
}
{/literal}
</style>

{$form.task_id.html}{$form.enabled.html}{$form.weight.html}

<div class="sql-tasks">

  <h3>{ts}Basic Information{/ts}</h3>

  <div class="spacer"></div>

  <div class="crm-section form-item">
    <div class="label">{$form.name.label}</div>
    <div class="content">{$form.name.html}</div>
    <div class="clear"></div>
  </div>

  <div class="crm-section form-item">
    <div class="label">{$form.description.label}&nbsp;<a onclick='CRM.help("{ts domain="de.systopia.sqltasks"}Description{/ts}", {literal}{"id":"id-configure-description","file":"CRM\/Sqltasks\/Form\/Configure"}{/literal}); return false;' href="#" title="{ts domain="de.systopia.sqltasks"}Help{/ts}" class="helpicon">&nbsp;</a></div>
    <div class="content">{$form.description.html}</div>
    <div class="clear"></div>
  </div>

  <div class="crm-section form-item">
    <div class="label">{$form.category.label}&nbsp;<a onclick='CRM.help("{ts domain="de.systopia.sqltasks"}Category{/ts}", {literal}{"id":"id-configure-category","file":"CRM\/Sqltasks\/Form\/Configure"}{/literal}); return false;' href="#" title="{ts domain="de.systopia.sqltasks"}Help{/ts}" class="helpicon">&nbsp;</a></div>
    <div class="content">{$form.category.html}</div>
    <div class="clear"></div>
  </div>

  <h3>{ts}Queries{/ts}</h3>

  <div class="spacer"></div>

  <div class="crm-section form-item">
    <div class="label">{$form.main_sql.label}&nbsp;<a onclick='CRM.help("{ts domain="de.systopia.sqltasks"}Main Script{/ts}", {literal}{"id":"id-configure-main","file":"CRM\/Sqltasks\/Form\/Configure"}{/literal}); return false;' href="#" title="{ts domain="de.systopia.sqltasks"}Help{/ts}" class="helpicon">&nbsp;</a></div>
    <div class="content">{$form.main_sql.html}</div>
    <div class="clear"></div>
  </div>

  <div class="crm-section form-item">
    <div class="label">{$form.post_sql.label}&nbsp;<a onclick='CRM.help("{ts domain="de.systopia.sqltasks"}Clean Up{/ts}", {literal}{"id":"id-configure-post","file":"CRM\/Sqltasks\/Form\/Configure"}{/literal}); return false;' href="#" title="{ts domain="de.systopia.sqltasks"}Help{/ts}" class="helpicon">&nbsp;</a></div>
    <div class="content">{$form.post_sql.html}</div>
    <div class="clear"></div>
  </div>

  <h3>{ts}Execution{/ts}</h3>

  <div class="spacer"></div>

  <div class="crm-section">
    <div class="label">{$form.scheduled.label}</div>
    <div class="content">{$form.scheduled.html}</div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.parallel_exec.label}&nbsp;<a onclick='CRM.help("{ts domain="de.systopia.sqltasks"}Parallel Execution{/ts}", {literal}{"id":"id-configure-parallel","file":"CRM\/Sqltasks\/Form\/Configure"}{/literal}); return false;' href="#" title="{ts domain="de.systopia.sqltasks"}Help{/ts}" class="helpicon">&nbsp;</a></div>
    <div class="content">{$form.parallel_exec.html}</div>
    <div class="clear"></div>
  </div>

  <h3>{ts}Actions{/ts}</h3>
  {foreach from=$action_list item=action key=action_id}
  <div class="crm-accordion-wrapper crm-sqltask-{$action_id} collapsed">
    {capture assign=enabledfield}{$action_id}_enabled{/capture}
    <div class="crm-accordion-header active">
      <div style="float: left">
        {$form.$enabledfield.html}&nbsp;{$form.$enabledfield.label}
      </div>
      <div style="text-align: right;" class="crm-sqlactivity-action-icons">
        &nbsp;
        {if !$action.isResultHandler}
        {if $action.name eq "Create Activity"}
        <a title="{ts domain="de.systopia.sqltasks"}Warning{/ts}" href="#" class="crm-sqlactivity-action-warning">
          <span class="crm-sqlactivity-action-warning-text">
            {ts domain="de.systopia.sqltasks"}This action might depend on "Assign to Campaign", consider moving it.{/ts}
          </span>
          <span>
            <i class="crm-i fa-exclamation-triangle"></i>
          </span>
        </a>
        {/if}
        <a title="{ts domain="de.systopia.sqltasks"}Delete Action{/ts}" href="#">
          <span>
            <i class="crm-i fa-trash"></i>
          </span>
        </a>
        <a title="{ts domain="de.systopia.sqltasks"}Add Action{/ts}" href="#">
          <span>
            <i class="crm-i fa-plus-circle"></i>
          </span>
        </a>
        <a title="{ts domain="de.systopia.sqltasks"}Move Action{/ts}" href="#">
          <span>
            <i class="crm-i fa-arrows"></i>
          </span>
        </a>
        {/if}
      </div>
    </div>
    <div class="crm-accordion-body">{include file=$action.tpl}</div>
  </div>
  {/foreach}
</div>

<br/>
<div id="help">
  {ts domain="de.systopia.sqltasks"}<strong>Caution!</strong> Be aware that these tasks can execute arbitrary SQL statements, which <i>can potentially destroy your database</i>. Only use this if you really know what you're doing, and always keep a backup of your database before experimenting.{/ts}
</div>


{* FOOTER *}
<br/>
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>


<!-- move to the right spot -->
{literal}
<script type="text/javascript">

// enable/disable actions
cj("input.crm-sqltask-action-enable").click(function(event) {
  // open accordeon
  var action = cj(this);
  if (action.prop('checked')) {
    action.closest("div.crm-accordion-wrapper").removeClass("collapsed");
  } else {
    action.closest("div.crm-accordion-wrapper").addClass("collapsed");
  }

  // stop further processing for this event
  event.stopPropagation();
});

// open all active task wrappers
cj("input.crm-sqltask-action-enable").each(function() {
  var action = cj(this);
  if (action.prop('checked')) {
    action.closest("div.crm-accordion-wrapper").removeClass("collapsed");
  }
});

function decodeHTML(selector) {
  var raw = cj(selector).val();
  var decoded = cj('<div/>').html(raw).text();
  cj(selector).val(decoded);
}

// decode HTML entities
decodeHTML("#main_sql");
decodeHTML("#post_sql");

CRM.$('.crm-sqlactivity-action-warning').hover(function() {
  CRM.$('.crm-sqlactivity-action-warning-text', this).fadeIn();
}, function() {
  CRM.$('.crm-sqlactivity-action-warning-text', this).fadeOut();
});

</script>
{/literal}