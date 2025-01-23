// +-----------------------------------------------------------------------+
// | Definition of constants                                               |
// +-----------------------------------------------------------------------+
const afTemplateLine = $('#af_example_line');
const afMessage = $('#af_message');
const afLoading = $('#af_btn_loading');
const afExportError = $('#af_export_error');
let afAllBtn = [];

// +-----------------------------------------------------------------------+
// | On dom ready                                                          |
// +-----------------------------------------------------------------------+
$(function () {
  $('#af_btn_add_format').on('click', function () {
    afShowExportModal();
    
  });

  $('#af_export_close, #af_export_close').on('click', function () {
    afCloseExportModal();
  });

  $('#export_dimensions').on('change', function () {
    const selected = $(this).val();
    afToggleRadio(selected);
  });
  afToggleRadio('one');

  $('input[name="export_dimensions_one"]').on('change', function () {
    const selected = $(this).val();
    afToggleRadioOne(selected);
  });
  afToggleRadioOne('one_w');

  afDisplayBtn();

});

// +-----------------------------------------------------------------------+
// | Definition of functions                                               |
// +-----------------------------------------------------------------------+

function afBuildTemplate(btn, template) {
  template.addClass('af-line');

    template.find('.af-tab-name').html(btn.name);
    template.find('.af-tab-type').html(btn.ext);
    template.find('.af-tab-crop').html(btn.crop == "true" ? "Yes" : "No");
    if ('false' == btn.activated) {
      template.find('.af-tab-view').removeClass('icon-eye').addClass('icon-eye-off');
    }

    const operator = template.find('.af-tab-resize-dimensions');
    switch (btn.type) {
      case 'one':
        btn.width = btn.width ? btn.width : 'auto';
        btn.height = btn.height ? btn.height : 'auto';
        operator.html(str_af_and);
        break;
      
      case 'both':
        operator.html(str_af_or);
        break;
    
      case 'custom':
        operator.html(str_af_ratio);
        break;
    }

    template.find('.af-line-w').html(btn.width === 'auto' ? btn.width : `${btn.width}px`);
    template.find('.af-line-h').html(btn.height === 'auto' ? btn.height :`${btn.height}px`);

    template.attr('id', "af_line_" + btn.id);
    template.find('.af-tab-view').attr('data-line', btn.id);
    template.find('.af-tab-edit').attr('data-line', btn.id);
    template.find('.af-tab-delete').attr('data-line', btn.id);

    return template;
}

async function afDisplayBtn() {
  let displayBtn;
  const tmpAllBtn = await afGetExportBtn();
  if (!tmpAllBtn) {
    console.log('Failed to retrieve the export button');
    return;
  }

  if (typeof tmpAllBtn === 'string') {
    console.log(tmpAllBtn);
    afShowMessage("You don't have any exports yet.");
    afHideLoading();
    return;
  }

  if (!afAllBtn.length > 0) {
    displayBtn = tmpAllBtn;
    afClearLine();
    afHideMessage();
    afShowLoading();
  } else {
    const tmpId = tmpAllBtn.map(b => b.id);
    const oldId = afAllBtn.map(b => b.id);
    const diffId = tmpId.filter(id => !oldId.includes(id));
    const newBtn = tmpAllBtn.filter(btn => btn.id == diffId);
    displayBtn = newBtn;
  }

  afAllBtn = tmpAllBtn;
  displayBtn.forEach(btn => {
    const template = afBuildTemplate(btn, afTemplateLine.clone());
    $('#af_tab_body').append(template);
  });

  afHideLoading();
  afActionEvents();
}

function afDisplayEditBtn(btn) {
  const currDiv = $(`#af_line_${btn.id}`);
  let newBtn = afAllBtn.filter(b => b.id == btn.id);
  if (!newBtn.length) return;
  newBtn = newBtn[0];

  newBtn.name = btn.name;
  newBtn.ext = btn.ext;
  newBtn.type = btn.type;
  newBtn.crop = btn.crop;
  newBtn.width = btn.width;
  newBtn.height = btn.height;

  afBuildTemplate(btn, currDiv);
}

function afActionEvents() {
  $('.af-tab-delete').off('click').on('click', function() {
    const id = $(this).data('line');
    afShowDeleteModal(id);
  });

  $('#af_delete_close').off('click').on('click', function() {
    afHideDeleteModal();
  });

  $('.af-tab-view').off('click').on('click', async function() {
    const id = $(this).data('line');
    let btn = afAllBtn.filter(btn => btn.id == id);
    if (!btn.length) return;
    btn = btn[0];
    
    const is_activated = 'true' == btn.activated ? true : false;
    const toggleActivate = await afActivateBtn(btn.id, !is_activated); 
    if (toggleActivate) {
      btn.activated = `${!is_activated}`;
      if (is_activated) {
        $(`#af_line_${btn.id} .af-tab-view`).removeClass('icon-eye').addClass('icon-eye-off');
      } else {
        $(`#af_line_${btn.id} .af-tab-view`).removeClass('icon-eye-off').addClass('icon-eye');
      }
    } else {
      console.log('Error when toggle activate');
    }
  });

  $('.af-tab-edit').off('click').on('click', function() {
    const id = $(this).data('line');
    let btn = afAllBtn.filter(btn => btn.id == id);
    if (!btn.length) return;
    btn = btn[0];

    afShowEditModal(btn);
  });

}

// export modal functions
function afShowExportModal() {
  $('#af_export').fadeIn();
  $('#af_export_save').on('click', function () {
    $(this).find('span').removeClass('icon-floppy').addClass('af-icon-spin6 animate-spin');
    afSaveButton();
    $(this).find('span').removeClass('af-icon-spin6 animate-spin').addClass('icon-floppy');
  });
}

function afCloseExportModal() {
  $('#af_export').fadeOut(function () {
    afResetExportModal();
    afExportHideError();
  });
  $('#af_export_save').off('click');
}

function afClearDimExportInput() {
  $('#af_export_radio_both input, #af_export_radio_custom input, #af_input_one_w').val('');
}

function afResetExportModal() {
  $('#export_name').val('');
  $('#export_type').val('');
  $('#export_dimensions').val('one');
  afToggleRadio('one')
  $('#af_export_radio_both input, #af_export_radio_custom input, #af_input_one_w').val('');
  $('#af_export_radio_one_default').trigger('click');

  $('#af_icon').removeClass('icon-green icon-pencil').addClass('icon-blue icon-plus-circled');
  $('#af_export_title').html(str_af_add);
}

function afToggleRadio(selected) {
  $('.af-export-radio-tab').hide();
  afClearDimExportInput();
  $(`#af_export_radio_${selected}`).show();
}

function afToggleRadioOne(selected) {
  const curr = $(`#af_input_${selected}`);
  const previous = selected == 'one_w' ? $('#af_input_one_h') : $('#af_input_one_w');

  curr.removeProp('disabled').attr('placeholder', '');
  previous
    .prop({
      disabled: true,
      placeholder: 'auto'
    })
    .val('');
}

async function afSaveButton(btnId = null) {
  let error = [];
  let data = {};
  const btnName = $('#export_name').val();
  const type = $('#export_type').val();
  const typeOfDimensions = $('#export_dimensions').val();

  if (btnName.length === 0) {
    error.push('name')
  } else {
    data.name = btnName;
  }

  if (type.length === 0) {
    error.push('type')
  } else {
    data.ext = type;
  }

  switch (typeOfDimensions) {
    case 'one':
      data.type = 'one';
      const selected = $('.af-export-radio-one .font-checkbox.selected .af-input input');
      const value = selected.val();
      const currName = selected.attr('name').split('_')[1] == 'w' ? 'width' : 'height';
      if (value.length === 0) {
        error.push(currName)
      } else {
        data[currName] = Number(value);
      }
      break;

    case 'both':
      data.type = 'both';
      const bothWidth = $('#af_export_both_w').val();
      const bothHeight = $('#af_export_both_h').val();
      if (bothWidth.length === 0) {
        error.push('width');
      } else {
        data.width = Number(bothWidth);
      }
      if (bothHeight.length === 0) {
        error.push('height');
      } else {
        data.height = Number(bothHeight);
      }
      break;

    case 'custom':
      data.type = 'custom';
      const customWidth = $('#af_export_custom_w').val();
      const customHeight = $('#af_export_custom_h').val();
      if (customWidth.length === 0) {
        error.push('width');
      } else {
        data.width = Number(customWidth);
      }
      if (customHeight.length === 0) {
        error.push('height');
      } else {
        data.height = Number(customHeight);
      }
      break;
  }

  if (error.length > 0) {
    afExportShowError('Missing: ' + error.toString());
    return;
  }
  afExportHideError();

  const newBtn = btnId ? await afEditBtn({...data, id: btnId}) : await afCreateBtn(data);
  if (!newBtn) {
    afExportShowError('Error when saving');
  } else {
    if (!btnId) {
      afDisplayBtn();
    } else {
      afDisplayEditBtn(newBtn);
    }
    afCloseExportModal();
  }
}

function afExportShowError(msg) {
  afExportError.html(msg).show();
}

function afExportHideError() {
  afExportError.hide();
}

// export edit modal

function afShowEditModal(btn) {
  $('#export_name').val(btn.name);
  $('#export_type').val(btn.ext);
  $('#export_dimensions').val(btn.type);
  afToggleRadio(btn.type);

  switch (btn.type) {
    case 'one':
      const selected = 'auto' == btn.width ? 'af_export_radio_one_h' : 'af_export_radio_one_default';
      const selectedInput = 'auto' == btn.width ? 'af_input_one_h' : 'af_input_one_w';
      $(`#${selected}`).trigger('click');
      $(`#${selectedInput}`).val('auto' == btn.width ? btn.height : btn.width);
      break;

    case 'both':
      $('#af_export_both_w').val(btn.width);
      $('#af_export_both_h').val(btn.height);
      break;
  
    case 'custom':
      $('#af_export_custom_w').val(btn.width);
      $('#af_export_custom_h').val(btn.height);
      break;
  }

  $('#af_icon').removeClass('icon-blue icon-plus-circled').addClass('icon-green icon-pencil');
  $('#af_export_title').html(str_af_edit);

  $('#af_export').fadeIn();
  $('#af_export_save').on('click', function () {
    $(this).find('span').removeClass('icon-floppy').addClass('af-icon-spin6 animate-spin');
    afSaveButton(btn.id);
    $(this).find('span').removeClass('af-icon-spin6 animate-spin').addClass('icon-floppy');
  });
}

// export delete modal

function afShowDeleteModal(id) {
  let btn = afAllBtn.filter(btn => btn.id == id);
  if (!btn.length) return;
  btn = btn[0];
  
  $('#af_delete_name').html(sprintf(str_delete_af_btn, btn.name));
  afLoadDeleteEvent(btn.id);
  $('#af_delete').fadeIn();
}

function afHideDeleteModal() {
  afDestroyDeleteEvent();
  $('#af_delete').fadeOut();
}

function afLoadDeleteEvent(id) {
  $('#af_delete_btn').on('click', async function() {
    const deleted = await afDeleteBtn(id);
    if (deleted) {
      $(`#af_line_${id}`).remove();
      afAllBtn = afAllBtn.filter(btn => btn.id != id);
    } else {
      console.log('Error when deleting export');
    }
    afHideDeleteModal();
  });
}

function afDestroyDeleteEvent() {
  $('#af_delete_btn').off('click');
}

// tab functions
function afShowMessage(msg) {
  afMessage.html(msg).show();
}

function afHideMessage() {
  afMessage.hide();
}

function afShowLoading() {
  afLoading.show();
}

function afHideLoading() {
  afLoading.hide();
}

function afClearLine() {
  $('.af-line').remove();
}

// +-----------------------------------------------------------------------+
// | Definition of ajax functions                                          |
// +-----------------------------------------------------------------------+

async function afGetExportBtn() {
  try {
    const res = await $.ajax({
      url: 'ws.php?format=json&method=autoformats.getAdminExportBtn',
      method: 'GET',
      dataType: 'json'
    });
    if ('ok' !== res.stat) return null;
    return res.result;
  } catch (error) {
    return null;
  }
}

async function afCreateBtn(data) {
  try {
    const res = await $.ajax({
      url: 'ws.php?format=json&method=autoformats.createExport',
      method: 'POST',
      dataType: 'json',
      data
    });
    if ('ok' !== res.stat) return null;
    return res.result;
  } catch (error) {
    return null;
  }
}

async function afEditBtn(data) {
  try {
    const res = await $.ajax({
      url: 'ws.php?format=json&method=autoformats.editExport',
      method: 'POST',
      dataType: 'json',
      data
    });
    if ('ok' !== res.stat) return null;
    return res.result;
  } catch (error) {
    return null;
  }
}

async function afDeleteBtn(id) {
  try {
    const res = await $.ajax({
      url: 'ws.php?format=json&method=autoformats.deleteExport',
      method: 'POST',
      dataType: 'json',
      data: {
        id
      }
    });
    if ('ok' !== res.stat) return null;
    return res.result;
  } catch (error) {
    return null;
  }
}

async function afActivateBtn(id, activate) {
  try {
    const res = await $.ajax({
      url: 'ws.php?format=json&method=autoformats.activateExport',
      method: 'POST',
      dataType: 'json',
      data: {
        id,
        activate
      }
    });
    if ('ok' !== res.stat) return null;
    return res.result;
  } catch (error) {
    return null;
  }
}