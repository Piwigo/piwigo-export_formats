{combine_css path="{$AF_PATH}css/picture.css" order=0}
{combine_css path="{$AF_PATH}vendor/cropper.min.css" order=10}
{combine_script id='af_crop' require='jquery' load='footer' path="{$AF_PATH}vendor/cropper.min.js"}
{combine_script id='af_jquery' load='footer' path="{$AF_PATH}vendor/jquery.min.js"}
{combine_script id='af_script' load='footer' path="{$AF_PATH}js/picture.js"}
{combine_css path="{$AF_PATH}vendor/fontello/css/fontello.css" order=-10}
{combine_css path="{$AF_PATH}vendor/fontello/css/animation.css" order=10}

{footer_script}
const AF_PICTURE_ID = {$current.id};
{/footer_script}
<div id="af-download-content" class="af-download af-hidden">
    <h4>Exporter</h4>
    <div class="af-buttons">
        {if isset($AF_BUTTONS)}
            {foreach from=$AF_BUTTONS item=$button}
                <p class="af-button" data-af="{$button@key}" data-af_size="{$button.dimensions}"
                    data-af_crop="{($button.crop) ? "true" : "false"}" data-af_name="{$button.name}"
                    data-af_type="{$button.type}" title="{$button.name} - {$button.dimensions} - {$button.type}"
                >
                {if !$button.crop}<span class="af-icon-dl af-hidden af-icon-spin6 animate-spin"></span>{/if} {$button.name}
                </p>
            {/foreach}
        {/if}
    </div>
</div>
<div id="af_modal" class="af-modal">
    <div class="af-modal-content">
        <div class="af-modal-infos">
            <h2 id="af_name"></h2>
            <div>
                <p>Dimensions : <span id="af_sizes"></span></p>
                <p>Type : <span id="af_type"></span></p>
            </div>
        </div>
        <div id="af_modal_container_img" style="position: relative;">
            <img id="af_modal_img" class="af-modal-img" src="{$current.unique_derivatives['medium']->get_url()|@escape:javascript}"/>
        </div>
        <div class="af-modal-footer">
            <a id="af_download" href="#" class="af-download-link"></a>
            <p class="af-left-btn">
                <span id="af_modal_close" class="af-modal-btn close">Annuler</span>
                <span id="af_modal_reset" class="af-modal-btn close">Réinitialiser</span>
            </p>
            <p id="af_modal_download" class="af-modal-btn download">Télécharger <span id="af_wait_dl" class="af-icon-dl af-hidden af-icon-spin6 animate-spin"></span></p>
        </div>
    </div>
</div>