<?php
/*
Version: auto
Plugin Name: Export Formats
Plugin URI: auto
Author: Piwigo team
Author URI: https://github.com/Piwigo
Description: Download export configurable formats.
Has Settings: true
*/

// The plugin name has officially changed from ‘Auto Formats’ to ‘Export Formats’ 
// but in the code we have kept the prefixes: ‘auto_formats’, ‘af_*’.

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// check root directory
if (basename(dirname(__FILE__)) != 'export_formats')
{
  add_event_handler('init', 'af_error');
  function af_error()
  {
    global $page;
    $page['errors'][] = 'Export Formats folder name is incorrect, uninstall the plugin and rename it to "export_formats"';
  }
  return;
}

// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+
global $prefixeTable;

define('AF_ID', basename(dirname(__FILE__)));
define('AF_PATH', PHPWG_PLUGINS_PATH . AF_ID . '/');
define('AF_REALPATH', realpath(AF_PATH));
define('AF_ADMIN', get_root_url() . 'admin.php?page=plugin-' . AF_ID);
define('AF_TABLE',   $prefixeTable . 'auto_formats');

// +-----------------------------------------------------------------------+
// | Init Auto Formats                                                     |
// +-----------------------------------------------------------------------+

include_once(AF_PATH . 'include/functions.inc.php');

$af_ws_method = AF_PATH . 'include/ws_functions.inc.php';
add_event_handler('init', 'af_init');
add_event_handler('loc_begin_picture', 'af_loc_begin_picture', -10);
add_event_handler('loc_end_picture', 'af_loc_end_picture');
add_event_handler('ws_add_methods', 'af_add_methods', EVENT_HANDLER_PRIORITY_NEUTRAL, $af_ws_method);