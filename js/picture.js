var $3 = jQuery.noConflict(true);
const af_modal = $3('#af_modal');
const af_img = $3('#af_modal_img');
let af_cropper;
let af_clicked = false;
let af_curr_format = "";

$3(function () {
  af_display();
  $3('.af-btn-export').on('click', function () {
    const id = $3(this).data('af');
    const exportBtn = AF_BUTTONS.filter(btn => btn.id == id)[0];
    if (!exportBtn) return;
    console.log(exportBtn);

    // const do_crop = $3(this).data('af_crop');
    if ('true' == exportBtn.crop) {
      af_open_modal(exportBtn);
    } else {
      af_curr_format = exportBtn.id;
      $(this).find('.af-icon-dl').removeClass('af-hidden');
      af_get_export({
        width: exportBtn.width ?? 0,
        height: exportBtn.height ?? 0,
        x: 0,
        y: 0
      });
    }
  });
});

function af_display() {
  const theImage = $3('#theImage');
  const af_download = $3('#af-download-content');

  switch (AF_SHOW_AS) {
    case 'above':
      theImage.prepend(af_download);
      break;

    case 'below':
      if ($3('#customDownloadLink').length > 0) { // custom download link plugin
        $3('#customDownloadLink').after(af_download);
      } else {
        theImage.find('img').after(af_download);
      }
      break;

    case 'inside':
      if ($3('#card-informations').length > 0) { // bootstrap darkroom
        $3('#card-informations').prepend(af_download);
      } else { // modus /default
        $3('#standard').before(af_download);
      }
      break;

    case 'link':
      if ($3('#downloadSwitchBox').length > 0) { // modus / default
        $3('#downloadSwitchBox ul').prepend(af_download.find('li'));
      } else if ($3('.fa-download').length > 0) { // bootstrap darkroom
        af_download.find('li').addClass('dropdown-item');
        $3('.fa-download').parent().siblings().prepend(af_download.find('li'));
      }
      break;
  }

  af_modal.addClass(af_light_or_dark());
  if ('link' !== AF_SHOW_AS) af_download.removeClass('af-hidden');
  af_load_event();
}

function af_open_modal(btn) {
  const name = btn.name;
  const type = btn.ext;

  af_curr_format = btn.id;
  af_start_cropper(btn.width, btn.height);
  $3('#af_name').html(name);
  $3('#af_sizes').html(`${btn.width}x${btn.height}`);
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

  $3('#af_modal_reset').on('click', function () {
    if (af_cropper) af_cropper.reset();
  });
}

function af_start_cropper(width, height) {
  const img = document.getElementById('af_modal_img');
  const options = {
    viewMode: 1,
    dragMode: 'move',
    autoCropArea: 1,
    cropBoxResizable: true,
    cropBoxMovable: true,
    aspectRatio: width / height,
    responsive: true,
    background: false,
    zoomOnWheel: false
  };
  af_cropper = new Cropper(img, options);

  $3('#af_modal_download').off('click').on('click', function () {
    if (af_clicked) return;
    af_clicked = true;
    $3('#af_wait_dl').removeClass('af-hidden');

    af_get_export();
  });
}

function af_get_export(settings = null) {
  const { width, height, x, y } = settings ? settings : af_cropper.getData();

  $3.ajax({
    url: 'ws.php?format=json&method=autoformats.getExport',
    type: 'POST',
    data: {
      image_id: AF_PICTURE_ID,
      export_id: af_curr_format,
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
  }).done(function (res, status, xhr) {
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
