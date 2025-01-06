<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

/**
 * `Auto Formats` : aut_formats init
 */
function af_init()
{
  global $conf, $page;
  if (defined('IN_ADMIN'))
  {
    if (!isset($conf['auto_formats']))
    {
      $page['warnings'][] = l10n('Auto Formats : Missing $conf.auto_formats');
    }
  }
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
  if (!in_array($current_picture['file_ext'], $conf['picture_ext'])) return;
  if (!isset($conf['auto_formats'])) return;
  if (!$user['af_enabled_high']) return;
  $template->set_filename('auto_formats_picture', AF_REALPATH . '/template/picture.tpl');
  $template->assign(array(
    'AF_PATH' => AF_PATH,
    'AF_BUTTONS' => $conf['auto_formats']
  ));
  $template->parse('auto_formats_picture');
}

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
      'auto_format' => array(
        'flag' => WS_TYPE_NOTNULL,
        'info' => 'Format from your $conf.auto-formats'
      ),
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
  if (!isset($conf['auto_formats']) and !isset($conf['auto_formats'][ $params['auto_format'] ]))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, $params['auto_format'].' not found');
  }
  $format = $conf['auto_formats'][ $params['auto_format' ]];

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
  if (!in_array($picture_ext, $conf['picture_ext']))
  {
    return new PwgError(403, 'The file is not a picture');
  }

  $src_image = new SrcImage($result);
  $crop = false;
  $resize = $format['dimensions'];
  if (true === $format['crop'])
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
  $exec.= $format['crop'] ? ' -crop '.$crop.'!' : '';
  $exec.= ' -filter Lanczos';
  $exec.= ' -resize '.$resize;
  $exec.= ' -strip -quality 95 -interlace line -sampling-factor 4:2:2';
  $exec.= ' '.$format['type'].':-';

  //exec
  $output = @shell_exec($exec);
  if (empty($output))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'Error when converting image with ImageMagick');
  }

  // TODO : enhance history table
  pwg_log($params['image_id'], 'high');

  // TODO: add dimensions to files
  header('Content-Type: image/'.$format['type']);
  header('Content-Disposition: attachment; filename="'.get_filename_wo_extension($result['file']).'_'.$params['auto_format'].'_'.$format['dimensions'].'.'.$format['type'].'"');
  echo $output;
  exit;
}

/**
 * `Auto Formats` : Check if the user is authorized to export.
 * Compatible plugin :
 * - Custom Download Link
 */
function af_is_authorized($image_id)
{
  global $user;

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

  return true;
}