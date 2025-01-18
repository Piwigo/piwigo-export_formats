<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class auto_formats_maintain extends PluginMaintain
{
  private $table;

  // permission : 'admin' or groud_id
  // show_as : above, below, inside or link
  private $default_conf = array(
    'permission' => 'admin',
    'show_as' => 'above',
    );

  function __construct($plugin_id)
  {
    parent::__construct($plugin_id);

    global $prefixeTable;

    $this->table = $prefixeTable . 'auto_formats';
  }

  /**
   * Plugin install
   */
  function install($plugin_version, &$errors = array())
  {
    global $conf;
    if (empty($conf['af_config']))
    {
      conf_update_param('af_config', $this->default_conf, true);
    }

    pwg_query('
CREATE TABLE IF NOT EXISTS `'. $this->table .'` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `ext` char(4) NOT NULL,
  `type` enum(\'one\',\'both\',\'custom\') NOT NULL default \'one\',
  `width` int(5) unsigned NULL,
  `height` int(5) unsigned NULL,
  `crop` enum(\'true\',\'false\') NOT NULL default \'false\',
  `activated` enum(\'true\',\'false\') NOT NULL default \'true\',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;');
  }

  /**
   * Plugin activate
   */
  function activate($plugin_version, &$errors = array())
  {
  }

  /**
   * Plugin deactivate
   */
  function deactivate()
  {
  }

  /**
   * Plugin update
   */
  function update($old_version, $new_version, &$errors = array())
  {
    $this->install($new_version, $errors);
  }

  /**
   * Plugin uninstallation
   */
  function uninstall()
  {
    conf_delete_param('af_config');
    pwg_query('DROP TABLE `'. $this->table .'`;');
  }

}
