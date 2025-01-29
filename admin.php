<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $page, $conf;

$page['tab'] = isset($_GET['tab']) ? $_GET['tab'] : $page['tab'] = 'export';

// Create tabsheet
include_once(PHPWG_ROOT_PATH . 'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('auto_formats');
$tabsheet->add('export', '<span class="icon-download"></span>'.l10n('Export formats'), AF_ADMIN . '-export');
$tabsheet->add('config', '<span class="icon-cog"></span>'.l10n('Configuration'), AF_ADMIN . '-config');
$tabsheet->select($page['tab']);
$tabsheet->assign();

// get group
$query = '
SELECT id, name
  FROM `'.GROUPS_TABLE.'`
  ORDER BY name ASC
;';
$group = pwg_query($query);
while ($row = pwg_db_fetch_assoc($group))
{
  $template->append(
    'groups',
    array(
      'name' => $row['name'],
      'id' => $row['id']
      )
    );
}

$template->assign(array(
  'AF_PATH'=> AF_PATH,
  'AF_REALPATH'=> AF_REALPATH,
  'AF_ADMIN' => AF_ADMIN,
  'TYPE_AVAILABLE' => af_get_output_ext()
  ));

$template->set_filename('af_admin_content', AF_REALPATH . '/admin/template/admin.tpl');
$template->set_filename('af_tab_content', AF_REALPATH . '/admin/template/'.$page['tab'].'.tpl' );
$template->assign_var_from_handle('AF_TAB_CONTENT', 'af_tab_content');
$template->assign_var_from_handle('ADMIN_CONTENT', 'af_admin_content');
