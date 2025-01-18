{combine_script id='af_script_config' load='footer' path="{$AF_PATH}admin/js/config.js"}
<div class="af-container af-config">
  <div class="af-config-container">
    <p class="af-icon-header">
      <span class="af-icon icon-th-1 icon-green"></span>
      <span class="af-icon-text">{'Permissions'|translate}</span>
    </p>

    <div class="af-config-permissions">
      <label class="font-checkbox selected">
        <span class="icon-circle-empty"></span>
        <input type="radio" name="config_perm" value="perm_admin" id="config_admin" />
        <div class="af-config-select">
          <p>{'Allow all admins to download'|translate}</p>
        </div>
      </label>
      <label class="font-checkbox none">
        <span class="icon-dot-circled"></span>
        <input type="radio" name="config_perm" value="perm_group" id="config_group" />
        <div class="af-config-select">
          <p>{'Allow a specific group'|translate}</p>
        </div>
      </label>
      <select class="af-config-group" name="config_group" id="config_group_select" disabled>
        <option value="">---</option>
      {foreach from=$groups item=group}
        <option value="{$group.id}">{$group.name}</option>
      {/foreach}
      </select>
    </div>
  </div>


  <div class="af-config-container">
    <p class="af-icon-header">
      <span class="af-icon icon-menu icon-blue""></span>
      <span class=" af-icon-text">{'Show as'|translate}</span>
    </p>

    <div class="af-config-show">
      <label class="font-checkbox selected">
        <span class="icon-circle-empty"></span>
        <input type="radio" name="config_show" id="show_above" value="above" />
        <div class="af-config-select">
          <p>{'Buttons above the photo'|translate}</p>
        </div>
      </label>
      <label class="font-checkbox">
        <span class="icon-dot-circled"></span>
        <input type="radio" name="config_show" id="show_below" value="below" />
        <div class="af-config-select">
          <p>{'Buttons below the photo'|translate}</p>
        </div>
      </label>
      <label class="font-checkbox">
        <span class="icon-dot-circled"></span>
        <input type="radio" name="config_show" id="show_inside" value="inside" />
        <div class="af-config-select">
          <p>{'Buttons inside the informations block'|translate}</p>
        </div>
      </label>
      <label class="font-checkbox">
        <span class="icon-dot-circled"></span>
        <input type="radio" name="config_show" id="show_link" value="link" />
        <div class="af-config-select">
          <p>{'Links in the download dropdown'|translate}</p>
        </div>
      </label>
    </div>
  </div>
</div>
