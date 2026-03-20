(function () {

  /**
   * Filtre les options du select #hp_signe selon le type choisi.
   * Jeedom gère nativement la valeur via eqLogicAttr — pas besoin de champ caché.
   */
  function syncSigne() {
    var type  = document.getElementById('hp_type');
    var signe = document.getElementById('hp_signe');
    if (!type || !signe) return;

    var isOcc = type.value === 'occidental';

    // Affiche/masque les groupes d'options
    var grpOcc = signe.querySelector('.hp-opts-occidental');
    var grpChi = signe.querySelector('.hp-opts-chinois');
    if (grpOcc) grpOcc.style.display = isOcc ? '' : 'none';
    if (grpChi) grpChi.style.display = isOcc ? 'none' : '';

    // Si la valeur courante n'appartient pas au bon groupe, on remet la première option visible
    var currentOpt = signe.options[signe.selectedIndex];
    if (currentOpt) {
      var inWrongGroup = isOcc
        ? currentOpt.closest('optgroup') === grpChi
        : currentOpt.closest('optgroup') === grpOcc;

      if (inWrongGroup) {
        var firstValid = isOcc
          ? grpOcc.querySelector('option')
          : grpChi.querySelector('option');
        if (firstValid) signe.value = firstValid.value;
      }
    }
  }

  // Changement du type d'horoscope
  $(document).on('change', '#hp_type', function () {
    syncSigne();
  });

  $(document).on('shown.eqLogic', function () {
    syncSigne();
  });

  // Bouton réinitialisation des valeurs par défaut
  $(document).on('click', '#bt_resetAppearance', function () {
    $('[data-l2key="col1_width"]').val('110px');
    $('[data-l2key="col1_bg"]').val('#000000');
    $('[data-l2key="col2_color"]').val('#ff0000');
    $('[data-l2key="col2_size"]').val('10px');
    $('[data-l2key="col2_align"]').val('');
    $('[data-l2key="col3_color"]').val('#000000');
    $('[data-l2key="col3_size"]').val('10px');
    $('[data-l2key="col3_align"]').val('');
    $('[data-l2key="col23_bg"]').val('#cfcfcf');
  });

  // Bouton "Rafraîchir" manuel via AJAX
  $(document).on('click', '#bt_refreshHoroscope', function () {
    var eqLogicId = $('.eqLogicAttr[data-l1key="id"]').val();
    if (!eqLogicId) {
      $('#div_alert').showAlert({ message: 'Sauvegardez d\'abord l\'équipement.', level: 'warning' });
      return;
    }
    var btn = $(this).prop('disabled', true).html('<i class="fas fa-spin fa-spinner"></i>');
    $.ajax({
      type: 'POST',
      url: 'plugins/horoscopeplus/core/ajax/horoscopeplus.ajax.php',
      data: { action: 'refresh', eqLogic_id: eqLogicId },
      dataType: 'json',
      success: function (data) {
        if (data.state !== 'ok') {
          $('#div_alert').showAlert({ message: data.result, level: 'danger' });
        } else {
          $('#div_alert').showAlert({ message: 'Horoscope rafraîchi avec succès.', level: 'success' });
        }
      },
      error: function () {
        $('#div_alert').showAlert({ message: 'Erreur de communication avec le serveur.', level: 'danger' });
      },
      complete: function () {
        btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Rafraîchir l\'horoscope');
      }
    });
  });

})();
