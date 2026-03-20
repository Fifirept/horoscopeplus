<?php
if (!isConnect('admin')) {
  throw new Exception('{{401 - Accès non autorisé}}');
}

$plugin = plugin::byId('horoscopeplus');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>
<div id="div_alert"></div>

<div class="row row-overflow">

  <!-- ================= PAGE LISTE DES ÉQUIPEMENTS ================= -->
  <div class="col-xs-12 eqLogicThumbnailDisplay">

    <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>

    <div class="eqLogicThumbnailContainer">
      <div class="cursor eqLogicAction logoPrimary" data-action="add">
        <i class="fas fa-plus-circle"></i><br>
        <span>{{Ajouter}}</span>
      </div>
      <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
        <i class="fas fa-wrench"></i><br>
        <span>{{Configuration}}</span>
      </div>
    </div>

    <legend><i class="fas fa-table"></i> {{Mes horoscopes}}</legend>

    <div class="eqLogicThumbnailContainer">
      <?php foreach ($eqLogics as $eqLogic): ?>
        <div class="eqLogicDisplayCard cursor <?= !$eqLogic->getIsEnable() ? 'disableCard' : '' ?>"
             data-eqLogic_id="<?= $eqLogic->getId() ?>">
          <img src="plugins/horoscopeplus/plugin_info/horoscopeplus_icon.png" class="imgEqLogicType" />
          <br>
          <span class="name"><?= $eqLogic->getHumanName(true, true) ?></span>
          <span class="hiddenAsCard label label-primary pull-right" style="font-size:0.6em;margin-top:2px;">
            <?= $eqLogic->getConfiguration('type', 'chinois') ?>
          </span>
        </div>
      <?php endforeach; ?>
      <?php if (count($eqLogics) == 0): ?>
        <div class="text-center" style="margin-top:20px;font-weight:bold;width:100%;">
          {{Aucun équipement, cliquez sur "Ajouter" pour commencer}}
        </div>
      <?php endif; ?>
    </div>

  </div>

  <!-- ================= PAGE ÉQUIPEMENT ================= -->
  <div class="col-xs-12 eqLogic" style="display:none;">

    <!-- Barre de gestion -->
    <div class="input-group pull-right" style="display:inline-flex;">
      <span class="input-group-btn">
        <a class="btn btn-sm btn-default eqLogicAction" data-action="configure">
          <i class="fas fa-cogs"></i> {{Configuration avancée}}
        </a>
        <a class="btn btn-sm btn-success eqLogicAction" data-action="save">
          <i class="fas fa-check-circle"></i> {{Sauvegarder}}
        </a>
        <a class="btn btn-sm btn-danger eqLogicAction" data-action="remove">
          <i class="fas fa-minus-circle"></i> {{Supprimer}}
        </a>
      </span>
    </div>

    <!-- Onglets -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
      <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Équipement}}</a></li>
      <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list"></i> {{Commandes}}</a></li>
    </ul>

    <div class="tab-content">

      <!-- ============ ONGLET ÉQUIPEMENT ============ -->
      <div role="tabpanel" class="tab-pane active" id="eqlogictab">
        <form class="form-horizontal">
          <fieldset>

            <input type="hidden" class="eqLogicAttr" data-l1key="id">

            <div class="col-lg-6">

              <legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>

              <div class="form-group">
                <label class="col-sm-4 control-label">{{Nom de l'équipement}}</label>
                <div class="col-sm-6">
                  <input class="eqLogicAttr form-control" data-l1key="name">
                </div>
              </div>

              <!-- ✅ OBJET PARENT -->
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Objet parent}}</label>
                <div class="col-sm-6">
                  <select class="eqLogicAttr form-control" data-l1key="object_id">
                    <option value="">{{Aucun}}</option>
                    <?php
                    foreach (jeeObject::buildTree(null, false) as $object) {
                      echo '<option value="' . $object->getId() . '">'
                        . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber'))
                        . $object->getName()
                        . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>

              <!-- ✅ CATÉGORIE -->
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Catégorie}}</label>
                <div class="col-sm-6">
                  <?php
                  foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                    echo '<label class="checkbox-inline">';
                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '">';
                    echo $value['name'];
                    echo '</label>';
                  }
                  ?>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-4 control-label">{{Options}}</label>
                <div class="col-sm-6">
                  <label class="checkbox-inline">
                    <input type="checkbox" class="eqLogicAttr" data-l1key="isEnable">
                    {{Activer}}
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" class="eqLogicAttr" data-l1key="isVisible">
                    {{Visible}}
                  </label>
                </div>
              </div>

              <legend><i class="fas fa-cogs"></i> {{Paramètres spécifiques}}</legend>

              <div class="form-group">
                <div class="col-sm-offset-4 col-sm-6">
                  <button type="button" class="btn btn-sm btn-info" id="bt_refreshHoroscope">
                    <i class="fas fa-sync-alt"></i> {{Rafraîchir l'horoscope}}
                  </button>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-4 control-label">{{Type d'horoscope}}</label>
                <div class="col-sm-6">
                  <select class="eqLogicAttr form-control"
                          data-l1key="configuration"
                          data-l2key="type"
                          id="hp_type">
                    <option value="chinois">Chinois</option>
                    <option value="occidental">Occidental</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-4 control-label">{{Signe}}</label>
                <div class="col-sm-6">
                  <select class="eqLogicAttr form-control"
                          data-l1key="configuration"
                          data-l2key="signe"
                          id="hp_signe">
                    <optgroup label="Occidental" class="hp-opts-occidental">
                      <option value="belier">Bélier</option>
                      <option value="taureau">Taureau</option>
                      <option value="gemeaux">Gémeaux</option>
                      <option value="cancer">Cancer</option>
                      <option value="lion">Lion</option>
                      <option value="vierge">Vierge</option>
                      <option value="balance">Balance</option>
                      <option value="scorpion">Scorpion</option>
                      <option value="sagittaire">Sagittaire</option>
                      <option value="capricorne">Capricorne</option>
                      <option value="verseau">Verseau</option>
                      <option value="poissons">Poissons</option>
                    </optgroup>
                    <optgroup label="Chinois" class="hp-opts-chinois">
                      <option value="rat">Rat</option>
                      <option value="boeuf">Bœuf</option>
                      <option value="tigre">Tigre</option>
                      <option value="lievre">Lièvre</option>
                      <option value="dragon">Dragon</option>
                      <option value="serpent">Serpent</option>
                      <option value="cheval">Cheval</option>
                      <option value="chevre">Chèvre</option>
                      <option value="singe">Singe</option>
                      <option value="coq">Coq</option>
                      <option value="chien">Chien</option>
                      <option value="cochon">Cochon</option>
                    </optgroup>
                  </select>
                </div>
              </div>

              <legend><i class="fas fa-paint-brush"></i> {{Apparence du tableau}}</legend>

              <!-- COL 1 : Logo -->
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Largeur du logo}}</label>
                <div class="col-sm-4">
                  <input type="text" class="eqLogicAttr form-control"
                         data-l1key="configuration" data-l2key="col1_width"
                         placeholder="110px">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Arrière plan du logo}}</label>
                <div class="col-sm-2">
                  <input type="color" class="eqLogicAttr form-control"
                         data-l1key="configuration" data-l2key="col1_bg">
                </div>
              </div>

              <!-- COL 2 : Thème -->
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Couleur des domaines de prévisions}}</label>
                <div class="col-sm-2">
                  <input type="color" class="eqLogicAttr form-control"
                         data-l1key="configuration" data-l2key="col2_color">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Taille police des domaines de prévisions}}</label>
                <div class="col-sm-4">
                  <input type="text" class="eqLogicAttr form-control"
                         data-l1key="configuration" data-l2key="col2_size"
                         placeholder="10px">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Alignement des domaines de prévisions}}</label>
                <div class="col-sm-4">
                  <select class="eqLogicAttr form-control"
                          data-l1key="configuration" data-l2key="col2_align">
                    <option value="">{{Défaut (gauche)}}</option>
                    <option value="left">{{Gauche}}</option>
                    <option value="center">{{Centré}}</option>
                    <option value="right">{{Droite}}</option>
                  </select>
                </div>
              </div>

              <!-- COL 3 : Texte -->
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Couleur des textes de prévisions}}</label>
                <div class="col-sm-2">
                  <input type="color" class="eqLogicAttr form-control"
                         data-l1key="configuration" data-l2key="col3_color">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Taille police des textes de prévisions}}</label>
                <div class="col-sm-4">
                  <input type="text" class="eqLogicAttr form-control"
                         data-l1key="configuration" data-l2key="col3_size"
                         placeholder="10px">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Alignement des textes de prévisions}}</label>
                <div class="col-sm-4">
                  <select class="eqLogicAttr form-control"
                          data-l1key="configuration" data-l2key="col3_align">
                    <option value="">{{Défaut (gauche)}}</option>
                    <option value="left">{{Gauche}}</option>
                    <option value="center">{{Centré}}</option>
                    <option value="right">{{Droite}}</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Arrière plan des prévisions}}</label>
                <div class="col-sm-2">
                  <input type="color" class="eqLogicAttr form-control"
                         data-l1key="configuration" data-l2key="col23_bg">
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-offset-4 col-sm-6">
                  <button type="button" class="btn btn-default" id="bt_resetAppearance">
                    <i class="fas fa-undo"></i> {{Valeurs par défaut}}
                  </button>
                </div>
              </div>

            </div>

          </fieldset>
        </form>
      </div>

      <!-- ONGLET COMMANDES -->
      <div role="tabpanel" class="tab-pane" id="commandtab">
        <table class="table table-bordered" id="table_cmd">
          <thead>
            <tr>
              <th>{{Nom}}</th>
              <th>{{Type}}</th>
              <th>{{Action}}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php include_file('desktop', 'horoscopeplus', 'js', 'horoscopeplus'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>