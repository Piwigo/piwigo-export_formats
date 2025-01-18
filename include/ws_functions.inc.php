<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

/**
 * `Auto Formats` : add new pwg method
 */
function af_add_methods($arr)
{
  $service = &$arr[0];

  $service->addMethod(
    'autoformats.getExport',
    'af_get_export',
    array(
      'image_id' => array('type' => WS_TYPE_ID),
      'export_id' => array('type' => WS_TYPE_ID),
      'settings' => array(
        'flags' => WS_PARAM_FORCE_ARRAY, WS_PARAM_OPTIONAL,
        'info' => 'Settings : width, height, x, y',
      ),
    ),
    '',
    null,
    array(
      'hidden' => false,
      'post_only' => true,
      'admin_only' => false,
    )
  );

  $service->addMethod(
    'autoformats.getAdminExportBtn',
    'af_get_export_btn',
    null,
    '',
    null,
    array(
      'hidden' => false,
      'post_only' => false,
      'admin_only' => true,
    )
  );

  $service->addMethod(
    'autoformats.createExport',
    'af_create_export',
    array(
      'name' => array(
        'info' => 'The name of the button'
      ),
      'ext' => array(
        'flag' => WS_TYPE_NOTNULL,
        'info' => 'Type included in $conf.picture_ext'
      ),
      'type' => array(
        'flag' => WS_TYPE_NOTNULL,
        'info' => 'Must be : "one", "both" or "custom"'
      ),
      // 'crop' => array(
      //   'type' => WS_TYPE_BOOL,
      //   'info' => 'Make it cropped',
      // ),
      'width' => array(
        'flags' => WS_PARAM_OPTIONAL,
        'type' => WS_TYPE_INT|WS_TYPE_POSITIVE,
        'info' => 'Settings : width or empty',
      ),
      'height' => array(
        'flags' => WS_PARAM_OPTIONAL,
        'type' => WS_TYPE_INT|WS_TYPE_POSITIVE,
        'info' => 'Settings : height or empty',
      ),
    ),
    '',
    null,
    array(
      'hidden' => false,
      'post_only' => true,
      'admin_only' => true,
    )
  );

  $service->addMethod(
    'autoformats.editExport',
    'af_edit_export',
    array(
      'id' => array('type'=>WS_TYPE_ID),
      'name' => array(
        'info' => 'The name of the button'
      ),
      'ext' => array(
        'flag' => WS_TYPE_NOTNULL,
        'info' => 'Type included in $conf.picture_ext'
      ),
      'type' => array(
        'flag' => WS_TYPE_NOTNULL,
        'info' => 'Must be : "one", "both" or "custom"'
      ),
      // 'crop' => array(
      //   'type' => WS_TYPE_BOOL,
      //   'info' => 'Make it cropped',
      // ),
      'width' => array(
        'flags' => WS_PARAM_OPTIONAL,
        'type' => WS_TYPE_INT|WS_TYPE_POSITIVE,
        'info' => 'Settings : width or empty',
      ),
      'height' => array(
        'flags' => WS_PARAM_OPTIONAL,
        'type' => WS_TYPE_INT|WS_TYPE_POSITIVE,
        'info' => 'Settings : height or empty',
      ),
    ),
    '',
    null,
    array(
      'hidden' => false,
      'post_only' => true,
      'admin_only' => true,
    )
  );

  $service->addMethod(
    'autoformats.deleteExport',
    'af_delete_export',
    array(
      'id' => array('type'=>WS_TYPE_ID)
    ),
    '',
    null,
    array(
      'hidden' => false,
      'post_only' => true,
      'admin_only' => true,
    )
  );

  $service->addMethod(
    'autoformats.activateExport',
    'af_activate_export',
    array(
      'id' => array('type'=>WS_TYPE_ID),
      'activate' => array(
        'type' => WS_TYPE_BOOL,
        'info' => 'True for activate, false for deactivate'
      )
    ),
    '',
    null,
    array(
      'hidden' => false,
      'post_only' => true,
      'admin_only' => true,
    )
  );

  $service->addMethod(
    'autoformats.getConfig',
    'af_get_config',
    null,
    '',
    null,
    array(
      'hidden' => false,
      'post_only' => false,
      'admin_only' => true,
    )
  );

  $service->addMethod(
    'autoformats.editConfig',
    'af_edit_config',
    array(
      'permission' => array('info' => '"admin" or group_id'),
      'show_as' => array('info' => 'only : above, below, inside or link')
    ),
    '',
    null,
    array(
      'hidden' => false,
      'post_only' => true,
      'admin_only' => true,
    )
  );
}

/**
 * `Auto Formats` : Check if the user is authorized to export.
 * Compatible plugin :
 * - Custom Download Link
 */
function af_is_authorized($image_id)
{
  global $user, $conf;

  $af_config = safe_unserialize($conf['af_config']);

  // check if user can download
  if (!$user['enabled_high'])
  {
    return false;
  }

  // check if user have access
  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON category_id = id
  WHERE image_id = '.$image_id.'
'.get_sql_condition_FandF(
  array(
      'forbidden_categories' => 'category_id',
      'forbidden_images' => 'image_id',
    ),
  '    AND'
  ).'
  LIMIT 1
;';

  if (pwg_db_num_rows(pwg_query($query))<1)
  {
    return false;
  }

  if ('admin' === $af_config['permission'] and !is_admin())
  {
    return false;
  };

  if ('admin' !== $af_config['permission'])
  {
    $group_by_user = af_get_group_by_user();
    if (!in_array($af_config['permission'], $group_by_user))
    {
      return false;
    }
  }

  return true;
}

// function af_is_authorized($image_id)
// {

//   $af_config = safe_unserialize($conf['af_config']);
//   $all_export = af_get_all_export_btn();
//   if (!in_array($current_picture['file_ext'], af_get_available_ext())) return;
//   // if (!isset($conf['auto_formats'])) return;
//   // if (!$user['af_enabled_high']) return;
//   if (!$all_export) return;
//   if ('admin' === $af_config['permission'] and !is_admin()) return;
//   $group_by_user = af_get_group_by_user();
//   if ('admin' !== $af_config['permission'] and !in_array($af_config['permission'], $group_by_user)) return;
// }

/**
 * `Auto Formats` : check method params
 */
function af_check_params($params)
{
  if (!preg_match('/^[A-Za-z0-9\s\-_.]{1,100}$/', $params['name']))
  {
    return 'The field name is not valid.';
  }

  if (!in_array(strtolower($params['ext']), af_get_available_ext()))
  {
    return 'The extension is not: ' . implode(", ", af_get_available_ext());
  }

  if (!preg_match('/^(one|both|custom)$/', $params['type']))
  {
    return 'The type must be: one, both or custom.';
  }

  if ('one' === $params['type'])
  {
    if (isset($params['width']) and isset($params['height']))
    {
      return 'When the "one" type is used, one of the two dimensions width or height must be empty';
    }
  } else {
    if (!isset($params['width']) or !isset($params['height']))
    {
      return 'When "both" or "custome" types is used, width or height must be an number';
    }
  }

  if (isset($params['width']) and !preg_match('/^\d{1,5}$/', $params['width']))
  {
    return 'Width must be number.';
  }

  if (isset($params['height']) and !preg_match('/^\d{1,5}$/', $params['height']))
  {
    return 'Height must be number.';
  }

  return true;
}

/**
 * `Auto Formats` : method getExport
 */
function af_get_export($params)
{
  global $conf;

  $settings = $params['settings'];
  if (!af_is_authorized($params['image_id']))
  {
    return new PwgError(401, 'Acces denied');
  }

  foreach ($settings as $key => $option)
  {
    if (!preg_match('/^\d+(\.\d+)?$/', $option))
    {
      return new PwgError(WS_ERR_INVALID_PARAM, $key.' must be an number');
    }
  }
  // check format
  if (!preg_match(PATTERN_ID, $params['export_id']))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'export_id must be int');
  }
  //$format = $conf['auto_formats'][ $params['auto_format' ]];

  $export = af_get_export_by_id($params['export_id']);
  if (!$export or 'false' == $export['activated'])
  {
    return new PwgError(403, 'Export not found');
  }

  // cancel
  // return 'canceled';

  $query = '
SELECT *
  FROM '. IMAGES_TABLE .'
  WHERE id = '. $params['image_id'] .'
;';

  $result = pwg_db_fetch_assoc(pwg_query($query));
  if (empty($result))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'No images found');
  }

  $picture_ext = get_extension($result['file']);
  if (!in_array($picture_ext, af_get_available_ext()))
  {
    return new PwgError(403, 'The file is not a picture');
  }

  $src_image = new SrcImage($result);
  $crop = false;

  $export_width = $export['width'] ?? '';
  $export_height = $export['height'] ?? '';
  $resize = $export_width.'x'.$export_height;
  $resize_name = $resize;

  if ('true' == $export['crop'])
  {
    $cropped_image = DerivativeImage::get_one(IMG_MEDIUM, $result);
    $original_size = $src_image->get_size();
    $medium_size = $cropped_image->get_size();
    
    // calcul ratio
    $ratio = $original_size[0] / $medium_size[0];
    // calcul conversion
    $x = $settings['x'] * $ratio;
    $y = $settings['y'] * $ratio;
    $width = $settings['width'] * $ratio;
    $height = $settings['height'] * $ratio;

    $crop = $width.'x'.$height.'+'.$x.'+'.$y;
    $resize .= '!';
  }
  
  // Prepare exec
  $exec = $conf['ext_imagick_dir'].'convert ';
  $exec.= $src_image->get_path();
  $exec.= $crop ? ' -crop '.$crop.'!' : '';
  $exec.= ' -filter Lanczos';
  $exec.= ' -resize '.$resize;
  $exec.= ' -strip -quality 95 -interlace line -sampling-factor 4:2:2';
  $exec.= ' '.$export['ext'].':-';

  //exec
  $output = @shell_exec($exec);
  if (empty($output))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'Error when converting image with ImageMagick');
  }

  // TODO : enhance history table
  pwg_log($params['image_id'], 'high');

  $export_name = str_replace(' ', '_', strtolower($export['name']));
  header('Content-Type: image/'.$export['ext']);
  // header('Content-Disposition: attachment; filename="'.get_filename_wo_extension($result['file']).'_'.$params['auto_format'].'_'.$format['dimensions'].'.'.$format['type'].'"');
  header('Content-Disposition: attachment; filename="'.get_filename_wo_extension($result['file']).'_'.$export_name.'_'.$resize_name.'.'.$export['ext'].'"');
  echo $output;
  exit;
}

/**
 * `Auto Formats` : method getAdminExportBtn
 */
function af_get_export_btn()
{
  $query = '
SELECT *
  FROM '.AF_TABLE.'  
;';

  $result = query2array($query);
  if (!$result)
  {
    return 'No buttons yet.';
  } else {
    return $result;
  }
}

/**
 * `Auto Formats` : method getExport
 */
function af_create_export($params)
{
  $check = af_check_params($params);
  if (true !== $check) {
    return new PwgError(WS_ERR_INVALID_PARAM, $check);
  }

  $btn_name = $params['name'];
  $ext = strtolower($params['ext']);
  $type = $params['type'];
  $crop = 'custom' === $params['type'] ? 'true' : 'false';
  $width = $params['width'] ?? null;
  $height = $params['height'] ?? null;

  $insert = array(
    'name' => $btn_name,
    'ext' => $ext,
    'type' => $type,
    'width' => $width,
    'height' => $height,
    'crop' => $crop
  );

single_insert(
  AF_TABLE,
  $insert
);

  $insert['activated'] = 'true';
  return $insert;
}

/**
 * `Auto Formats` : method editExport
 */
function af_edit_export($params)
{
  $check = af_check_params($params);
  if (true !== $check) {
    return new PwgError(WS_ERR_INVALID_PARAM, $check);
  }

  $curr_export = af_get_export_by_id($params['id']);
  if (!$curr_export) {
    return new PwgError(404, 'Export not found');
  };
  
  $btn_name = $params['name'];
  $ext = strtolower($params['ext']);
  $type = $params['type'];
  $crop = 'custom' === $params['type'] ? 'true' : 'false';
  $width = $params['width'] ?? 'null';
  $height = $params['height'] ?? 'null';

  $update = array(
    'name' => $btn_name,
    'ext' => $ext,
    'type' => $type,
    'width' => $width,
    'height' => $height,
    'crop' => $crop
  );

single_update(
  AF_TABLE,
  $update,
  array(
    'id' => $curr_export['id']
  )
);

  $update['id'] = $curr_export['id'];
  return $update;
}

/**
 * `Auto Formats` : method deleteExport
 */
function af_delete_export($params)
{
  $export = af_get_export_by_id($params['id']);
  if (!$export) {
    return new PwgError(404, 'Export not found');
  };

  $query = '
DELETE FROM '.AF_TABLE.'
  WHERE id = '.$export['id'].'
;';

  $is_deleted = pwg_query($query);

  return $is_deleted;
}

/**
 * `Auto Formats` : method activateExport
 */
function af_activate_export($params)
{
  $export = af_get_export_by_id($params['id']);
  if (!$export) {
    return new PwgError(404, 'Export not found');
  };

  $activate = $params['activate'] ? 'true' : 'false';

  single_update(
    AF_TABLE,
    array('activated' => $activate),
    array('id' => $export['id'])
  );
  return $export['name'] . ' set to ' . $activate;
}

function af_get_config($params) 
{
  return safe_unserialize(conf_get_param('af_config'));
}

/**
 * `Auto Formats` : method editConfig
 */
function af_edit_config($params)
{
  if (!preg_match('/^admin|^\d+$/', $params['permission']))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'permission must be "admin" or group_id');
  }

  if (!preg_match('/^above|below|inside|link/', $params['show_as']))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'show_as must be above, below, inside or link');
  }

  $new_conf = array(
    'permission' => $params['permission'],
    'show_as' => $params['show_as'],
  );

  conf_update_param('af_config', $new_conf, true);

  return 'Auto_formats configuration updated !';
}