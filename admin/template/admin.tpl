{combine_css path="{$AF_PATH}/admin/css/admin.css" order=0}
{combine_css path="{$AF_PATH}vendor/fontello/css/fontello.css" order=-10}
{combine_css path="{$AF_PATH}vendor/fontello/css/animation.css" order=10}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{if $themeconf['colorscheme'] == 'dark'}
  {combine_css path="{$AF_PATH}admin/css/plugin_dark.css" order=3}
{/if}
<h2>Export Formats</h2>
{$AF_TAB_CONTENT}