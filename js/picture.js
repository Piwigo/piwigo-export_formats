var $3 = jQuery.noConflict(true);
const af_modal = $3('#af_modal');
const af_img = $3('#af_modal_img');
let af_cropper;
let af_clicked = false;
let af_curr_format = "";

$3(function () {
  af_display();
  $3('.af-button').on('click', function () {
    const do_crop = $3(this).data('af_crop');
    if (do_crop) {
      af_open_modal(this);
    } else {
      const no_crop_size = parseInt($3(this).data('af_size'));
      af_curr_format = $3(this).data('af');
      $(this).find('.af-icon-dl').removeClass('af-hidden');
      af_get_export({
        width: no_crop_size,
        height: 0,
        x: 0,
        y: 0
      });
    }
  });
});

function af_display() {
  const af_download = $3('#af-download-content');
  if ($3('#customDownloadLink').length > 0) {
    $3('#customDownloadLink').after(af_download);
  } else if ($3('#card-informations').length > 0) {
    $3('#card-informations').prepend(af_download);
  } else if ($3('#standard').length > 0) {
    $3('#standard').before(af_download);
  }
  af_modal.addClass(af_light_or_dark());
  af_download.removeClass('af-hidden');
  af_load_event();
}

function af_open_modal(button) {
  const btn = $3(button);
  af_curr_format = btn.data('af');
  const size = btn.data('af_size');
  const name = btn.data('af_name');
  const type = btn.data('af_type');

  af_start_cropper(size);
  $3('#af_name').html(name);
  $3('#af_sizes').html(size);
  $3('#af_type').html(type);
  af_modal.fadeIn();
}

function af_close_modal() {
  af_modal.fadeOut();
  af_clicked = false;
  $('.af-icon-dl').addClass('af-hidden');
  if (af_cropper) af_cropper.destroy();
}

function af_load_event() {
  $3('#af_modal_close').on('click', function () {
    af_close_modal();
  });

  $3('#af_modal_reset').on('click', function() {
    if (af_cropper) af_cropper.reset();
  });
}

function af_start_cropper(size) {
  const ratio = size.split('x');
  const img = document.getElementById('af_modal_img');
  const options = {
    viewMode: 1,
    dragMode: 'move',
    autoCropArea: 1,
    cropBoxResizable: true,
    cropBoxMovable: true,
    aspectRatio: ratio[0] / ratio[1],
    responsive: true,
    background: false,
    zoomOnWheel: false
  };
  af_cropper = new Cropper(img, options);

  $3('#af_modal_download').on('click', function () {
    if (af_clicked) return;
    af_clicked = true;
    $3('#af_wait_dl').removeClass('af-hidden');

    af_get_export();
  });
}

function af_get_export(settings=null) {
  const { width, height, x, y } = settings ? settings : af_cropper.getData();

  $3.ajax({
    url: 'ws.php?format=json&method=autoformats.getExport',
    type: 'POST',
    data: {
      image_id: AF_PICTURE_ID,
      auto_format: af_curr_format,
      settings: {
        width,
        height,
        x,
        y
      }
    },
    xhrFields: {
      responseType: 'blob'
    }
  }).done(function(res, status, xhr) {
    let filename = af_curr_format + '_downloaded_piwigo_image.' + res.type.split('/')[1];
    const contentDisposition = xhr.getResponseHeader('Content-Disposition');
    if (contentDisposition) {
      const matches = contentDisposition.match(/filename="(.+)"/);
      if (matches && matches[1]) {
        filename = matches[1];
      }
    }
    const url = URL.createObjectURL(res);
    const downloadLink = document.getElementById('af_download');

    downloadLink.href = url;
    downloadLink.download = filename;
    downloadLink.click();
    URL.revokeObjectURL(url);
    af_close_modal();
  });
}

function af_light_or_dark() {
  let color = $3('body').css('background-color');
  let r, g, b, hsp;
  if (color.match(/^rgb/)) {
    color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);

    r = color[1];
    g = color[2];
    b = color[3];
  }
  else {
    color = +("0x" + color.slice(1).replace(
      color.length < 5 && /./g, '$&$&'));

    r = color >> 16;
    g = color >> 8 & 255;
    b = color & 255;
  }

  hsp = Math.sqrt(
    0.299 * (r * r) +
    0.587 * (g * g) +
    0.114 * (b * b)
  );

  if (hsp > 127.5) {
    return 'af-light';
  }
  else {
    return 'af-dark';
  }
}
