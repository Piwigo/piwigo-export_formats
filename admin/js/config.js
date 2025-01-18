// +-----------------------------------------------------------------------+
// | Definition of constants                                               |
// +-----------------------------------------------------------------------+
let af_config;

// +-----------------------------------------------------------------------+
// | On dom ready                                                          |
// +-----------------------------------------------------------------------+
$(function () {
  afSelectConfig();

  $('#config_group, #config_admin').on('click', async function () {
    const value = $(this).val();
    if (value == 'perm_group') {
      $('#config_group_select').removeAttr('disabled');
    } else if (value == 'perm_admin') {
      $('#config_group_select').attr('disabled', true).val('');
    }
  });

  $('input[name="config_perm"]').on('change', function() {
    const selected = $(this).val();
    if ('perm_admin' == selected) {
      if ('admin' == af_config.permission) return;
      afEditConfigInput();
    }
  });

  $('#config_group_select').on('change', function() {
    const selected = $(this).val();
    if (selected == af_config.permission) return;
    afEditConfigInput();
  });

  $('input[name="config_show"]').on('change', function() {
    const selected = $(this).val();
    if (selected == af_config.show_as) return;
    afEditConfigInput();
  });
});

// +-----------------------------------------------------------------------+
// | Definition of functions                                               |
// +-----------------------------------------------------------------------+

async function afSelectConfig() {
  af_config = await afGetConfig();
  if ('admin' == af_config.permission) {
    $('#config_admin').trigger('click');
  } else {
    $('#config_group').trigger('click');
  }
  $('#config_group_select').val(af_config.permission);
  $(`#show_${af_config.show_as}`).trigger('click');
}

async function afEditConfigInput() {
  let permission = $('.af-config-permissions .font-checkbox.selected input').val();
  const show_as = $('.af-config-show .font-checkbox.selected input').val();

  if (permission == 'perm_admin') {
    permission = 'admin';
  } else {
    permission = $('#config_group_select').val();
  }

  const newConfig = await afEditConfig(permission, show_as);
  if (newConfig) {
    af_config.permission = permission;
    af_config.show_as = show_as;
  } else {
    console.log('Error when saving config');
  }
}

// +-----------------------------------------------------------------------+
// | Definition of ajax functions                                          |
// +-----------------------------------------------------------------------+
async function afGetConfig() {
  try {
    const res = await $.ajax({
      url: 'ws.php?format=json&method=autoformats.getConfig',
      method: 'GET',
      dataType: 'json',
    });
    if ('ok' !== res.stat) return null;
    return res.result;
  } catch (error) {
    return null;
  }
}

async function afEditConfig(permission, show_as) {
  try {
    const res = await $.ajax({
      url: 'ws.php?format=json&method=autoformats.editConfig',
      method: 'POST',
      dataType: 'json',
      data: {
        permission,
        show_as
      }
    });
    if ('ok' !== res.stat) return null;
    return res.result;
  } catch (error) {
    return null;
  }
}