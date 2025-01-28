{combine_script id='af_script_export' load='footer' path="{$AF_PATH}admin/js/export.js"}
{footer_script}
const str_delete_af_btn = "{'Are you sure you want to delete the "%s" button?'|translate|escape:javascript}";
const str_af_and = "{'AND'|translate|escape:javascript}";
const str_af_or = "{'OR'|translate|escape:javascript}";
const str_af_ratio = "{'AND'|translate|escape:javascript}";
const str_af_add = "{'Add an export format'|translate|escape:javascript}";
const str_af_edit = "{'Edit an export format'|translate|escape:javascript}";
{/footer_script}
<div class="af-container">
  <p class="head-button-2 icon-plus-circled af-add-format" id="af_btn_add_format">{'Add an export format'|translate}</p>
  <div>
    <div class="af-tab-header">
      <p class="af-name">{'Name'|translate}</p>
      <p class="af-type">{'Type'|translate}</p>
      <p class="af-resize">{'Resize'|translate}</p>
      <p class="af-crop">{'Crop'|translate}</p>
      <p class="af-action">{'Action'|translate}</p>
    </div>
    <div class="af-tab-body" id="af_tab_body">
      {* template *}
      <div class="af-tab-line" id="af_example_line">
        <p class="af-tab-name">LinkedIn Banner</p>
        <p class="af-tab-type">PNG</p>
        <p class="af-tab-resize">
          <span class="af-icon-px-x af-tab-dimensions af-line-w">1540</span>
          <span class="af-tab-resize-dimensions af-line-dimtype">AND</span>
          <span class="af-icon-px-y af-tab-dimensions af-line-h">450</span>
        </p>
        <p class="af-tab-crop">Yes</p>
        <div class="af-tab-action">
          <span class="icon-pencil af-tab-edit"></span>
          <span class="icon-eye af-tab-view"></span>
          <span class="icon-trash-1 af-tab-delete"></span>
        </div>
      </div>
      {* end template *}
    </div>
    <p class="af-loading af-icon-spin6 animate-spin" id="af_btn_loading"></p>
    <p class="af-hidden af-message" id="af_message"></p>
  </div>
</div>

<div class="bg-modal" id="af_export">
  <div class="af-export-content">
    <a class="close-modal icon-cancel" id="af_export_close"></a>

    <div class="af-icon-header">
      <span class="af-icon icon-blue icon-plus-circled" id="af_icon"></span>
    </div>
    <p class="af-export-title" id="af_export_title">{'Add an export format'|translate}</p>
    <div class="af-export-body">
      <div class="af-export-field">
        <label class="af-export-field-label" for="export_name">{'Button name'|translate}</label>
        <input class="af-export-input" name="export_name" id="export_name" />
      </div>
      <div class="af-export-field">
        <label class="af-export-field-label" for="export_type">{'Type'|translate}</label>
        <select class="af-export-input" name="export_type" id="export_type">
          <option value="">---</option>
          {foreach from=$TYPE_AVAILABLE item=type}
            <option value="{$type}">{$type}</option>
          {/foreach}
        </select>
      </div>
      <div class="af-export-field">
        <p class="af-export-field-label">{'Image dimensions'|translate} <span class="tiptip icon-help-circled" title="{'None of your choice will ever distort your photo.'|translate}"></span></p>
        <select class="af-export-input" name="export_dimensions" id="export_dimensions">
          <option value="one">{'Set only one dimension'|translate}</option>
          <option value="both">{'Set both maximum dimensions'|translate}</option>
          <option value="custom">{'Crop at custom dimensions'|translate}</option>
        </select>
        <div class="af-export-radio">
          <div class="af-export-radio-tab" id="af_export_radio_one">
            <p class="af-export-radio-infos">
              {'The export will be at the exact dimension, the other will be depending on the original ratio of the photo.'|translate}
            </p>
            <p class="af-export-radio-infos">
              {'You can only set on side.'|translate}
            </p>
            <div class="af-export-radio-one">
              <label class="font-checkbox selected" id="af_export_radio_one_default">
                <span class="icon-circle-empty"></span>
                <input type="radio" name="export_dimensions_one" value="one_w" />
                <span class="af-icon-px-x"></span>
                <div class="af-input">
                  <input id="af_input_one_w" type="number" name="one_w" min="0" />
                  <p>px</p>
                </div>
              </label>
              <label class="font-checkbox" id="af_export_radio_one_h">
                <span class="icon-dot-circled"></span>
                <input type="radio" name="export_dimensions_one" value="one_h" />
                <span class="af-icon-px-y"></span>
                <div class="af-input">
                  <input id="af_input_one_h" type="number" placeholder="auto" name="one_h" min="0" disabled />
                  <p>px</p>
                </div>
              </label>
            </div>
          </div>

          <div class="af-export-radio-tab" id="af_export_radio_both">
            <p class="af-export-radio-infos">
              {'If your photo is landscape, the max dimension will be the width. If its in portrait, the max dim will be the height. In any case you’ll keep the original ratio.'|translate}
            </p>
            <p class="af-export-radio-infos">
              {'Depending on the ratio of your photo, the download will take into account the defined width or height.'|translate}
            </p>
            <div class="af-export-radio-both">
              <div class="af-export-radio-w">
                <span class="af-icon-px-x"></span>
                <div class="af-input">
                  <input type="number" name="both_w" min="0" id="af_export_both_w" />
                  <p>px</p>
                </div>
              </div>
              <div class="af-export-radio-h">
                <span class="af-icon-px-y"></span>
                <div class="af-input">
                  <input type="number" name="both_h" min="0" id="af_export_both_h" />
                  <p>px</p>
                </div>
              </div>
            </div>
          </div>

          <div class="af-export-radio-tab" id="af_export_radio_custom">
            <p class="af-export-radio-infos">
              {'Crop will allow you to choose the zone you want.'|translate}
            </p>
            <p class="af-export-radio-infos">
              {'Set dimensions for the two side and it will allow you to crop and choose wich zone of your photo to download. It won\'t be distort.'|translate}
            </p>
            <div class="af-export-radio-custom">
              <div class="af-export-radio-w">
                <span class="af-icon-px-x"></span>
                <div class="af-input">
                  <input type="number" name="custom_x" min="0" id="af_export_custom_w" />
                  <p>px</p>
                </div>
              </div>
              <div class="af-export-radio-h">
                <span class="af-icon-px-y"></span>
                <div class="af-input">
                  <input type="number" name="custom_y" min="0" id="af_export_custom_h" />
                  <p>px</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="af-export-footer">
      <p class="af-export-close" id="af_export_close">{'Close'|translate}</p>
      <div class="af-export-footer-group">
        <p class="af-hidden af-export-error" id="af_export_error"></p>
        <p class="af-export-save" id="af_export_save"><span class="icon-floppy"></span>{'Save property'|translate}</p>
      </div>
    </div>
  </div>
</div>

<div class="bg-modal" id="af_delete">
  <div class="af-delete-content">
    <p class="af-delete-title" id="af_delete_name"></p>
    <div class="af-delete-footer">
      <p class="af-delete-close" id="af_delete_close">{'No, I have changed my mind'|translate}</p>
      <p class="af-delete-btn" id="af_delete_btn">{'Yes, I am sure'|translate}</p>
    </div>
  </div>
</div>