<div id="bootstrap-theme">
  <div class="st__mb-20">
    <div class="crm-form-block">
      <div class="st__mb-20 st__flex st__gap-10">
        <a class="btn btn-secondary"
           href="{crmURL p='civicrm/sqltasks/global-token/list' q='reset=1'}" title="{ts}Back to list{/ts}">
          <i class="crm-i fa-list"></i>
          <span>{ts}Back to list{/ts}</span>
        </a>
      </div>
      <div>
        <div class="st__pb-20">
          <div class="crm-section">
            <div class="label">
              {$form.token_name.label}
            </div>
            <div class="content">
              <div class="st__max-width-300">
                {$form.token_name.html}
              </div>
            </div>
            <div class="clear"></div>
          </div>

          <div class="crm-section">
            <div class="label">{$form.token_value.label}</div>
            <div class="content">
              <div class="st__max-width-300">
                {$form.token_value.html}
              </div>
            </div>
            <div class="clear"></div>
          </div>

          <div class="crm-section">
            <div class="label">{$form.data_type.label}</div>
            <div class="content">
              <div class="st__max-width-300">
                {$form.data_type.html}
              </div>
            </div>
            <div class="clear"></div>
          </div>

          <div class="crm-section">
            <div class="label">{$form.description.label}</div>
            <div class="content">
              <div class="st__max-width-300">
                {$form.description.html}
              </div>
            </div>
            <div class="clear"></div>
          </div>
        </div>

        <div class="st__flex st__gap-10">
          {include file="CRM/common/formButtons.tpl" location="bottom"}
        </div>
      </div>
    </div>
  </div>
</div>
