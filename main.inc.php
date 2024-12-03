<?php
/*
Version: auto
Plugin Name: Auto Formats
Plugin URI: auto
Author: Piwigo team
Author URI: https://github.com/Piwigo
Description: Download export configurable formats.
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// check root directory
if (basename(dirname(__FILE__)) != 'auto_formats')
{
  add_event_handler('init', 'af_error');
  function af_error()
  {
    global $page;
    $page['errors'][] = 'Auto-formats folder name is incorrect, uninstall the plugin and rename it to "auto_formats"';
  }
  return;
}

// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+

define('AF_ID', basename(dirname(__FILE__)));
define('AF_PATH', PHPWG_PLUGINS_PATH . AF_ID . '/');
define('AF_REALPATH', realpath(AF_PATH));
define('AF_ADMIN', get_root_url() . 'admin.php?page=plugin-' . AF_ID);

// +-----------------------------------------------------------------------+
// | Init Auto Formats                                                     |
// +-----------------------------------------------------------------------+

include_once(AF_PATH . 'include/functions.inc.php');
add_event_handler('init', 'af_init');
add_event_handler('loc_end_picture', 'af_loc_end_picture');
add_event_handler('ws_add_methods', 'af_add_methods', EVENT_HANDLER_PRIORITY_NEUTRAL);