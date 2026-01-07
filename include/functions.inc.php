<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

/**
 * `Auto Formats` : auto_formats init
 */
function af_init()
{
  global $conf, $page;

  $conf['auto_formats_extended_list'] = conf_get_param('auto_formats_extended_list', array(
    'heic',
    'psd',
    'tif',
    'tiff',
    'eps',
    'ai'
  ));

  load_language('plugin.lang', AF_PATH);
}

/**
 * `Auto Formats` : is triggered at the beginning of the picture page
 */
function af_loc_begin_picture()
{
  global $user;

  // Get user enabled_high config before a
  // potentiel change by others plugins (e.g Custom Download Link)
  $user['af_enabled_high'] = $user['enabled_high'];
}

/**
 * `Auto Formats` : is triggered at the end of the picture page
 */
function af_loc_end_picture()
{
  global $template, $conf, $picture, $user;

  $current_picture = $picture['current'];
  $af_config = safe_unserialize($conf['af_config']);
  $all_export = af_get_all_export_btn(true);

  if (!$user['af_enabled_high']) return;
  if (!af_check_ext($current_picture)) return;
  if (!$all_export) return;
  if ('admin' === $af_config['permission'] and !is_admin()) return;
  $group_by_user = af_get_group_by_user();
  if ('admin' !== $af_config['permission'] and !in_array($af_config['permission'], $group_by_user)) return;

  $compatibily = in_array('custom_download_link', af_get_plugins_list()) && 'link' === $af_config['show_as'];
  
  $template->set_filename('auto_formats_picture', AF_REALPATH . '/template/picture.tpl');
  $template->assign(array(
    'AF_PATH' => AF_PATH,
    //'AF_BUTTONS' => $conf['auto_formats'],
    'AF_BUTTONS' => $all_export,
    'AF_SHOW_AS' => $compatibily ? 'inside' : $af_config['show_as']
  ));
  $template->parse('auto_formats_picture');
}

/**
 * `Auto Formats` : check picture extensions
 */
function af_check_ext($picture)
{
  global $conf;

  $ext = strtolower($picture['file_ext']);

  if (in_array($ext, af_get_entry_ext()))
  {
    return true;
  }

  if (in_array($ext, $conf['auto_formats_extended_list']) and $picture['representative_ext'])
  {
    return true;
  }

  return false;
}

/**
 * `Auto Formats` : get available entry extensions
 */
function af_get_entry_ext()
{
  return array(
    'png',
    'jpg',
    'jpeg',
    'webp'
  );
}

/**
 * `Auto Formats` : get available output extensions
 */
function af_get_output_ext()
{
  return array(
    'png',
    'jpg',
    'webp'
  );
}

/**
 * `Auto Formats` : get export button by id
 */
function af_get_export_by_id($id)
{
  if (!preg_match(PATTERN_ID, $id)) return false;
  $query = '
SELECT * FROM '.AF_TABLE.'
  WHERE id = '.$id.'
  ;';

  $result = pwg_db_fetch_assoc(pwg_query($query));
  if ($result) return $result;
  return false;
}

/**
 * `Auto Formats` : get all export button
 */
function af_get_all_export_btn($available = false)
{
  $query = '
SELECT *
  FROM '.AF_TABLE.'
';

  if ($available)
  {
    $query .= 'WHERE activated = \'true\'';
  }

  $query .= ';';

  $result = query2array($query);
  if (!$result)
  {
    return false;
  } else {
    return $result;
  }
}

/**
 * `Auto Formats` : get group by user_id
 */
function af_get_group_by_user()
{
  global $user;

  $query = '
SELECT group_id
  FROM '.USER_GROUP_TABLE.'
  WHERE user_id = '.$user['id'].'
;';

  $result = query2array($query, null, 'group_id');
  if ($result) return $result;
  return false;
}

/**
 * `Auto Formats` : get plugins list
 * 
 * copy from core of Piwigo
 */
function af_get_plugins_list()
{
  include_once(PHPWG_ROOT_PATH.'admin/include/plugins.class.php');

  $plugins = new plugins();
  $plugins->sort_fs_plugins('name');
  $plugin_list = array();

  foreach ($plugins->fs_plugins as $plugin_id => $fs_plugin)
  {
    if (isset($plugins->db_plugins_by_id[$plugin_id]))
    {
      $state = $plugins->db_plugins_by_id[$plugin_id]['state'];
      if ('active' === $state) {
        $plugin_list[] = $plugin_id;
      };
    }
  }

  return $plugin_list;
}