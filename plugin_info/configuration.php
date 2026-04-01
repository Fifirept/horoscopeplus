<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}

$data = json_decode(file_get_contents(dirname(__FILE__) . '/info.json'), true);
$pluginVersion = isset($data['pluginVersion']) ? $data['pluginVersion'] : '?';
?>

<form class="form-horizontal">
    <fieldset>

        <legend><i class="fas fa-info-circle"></i> {{Version}}</legend>
        <div class="form-group">
            <label class="col-sm-4 control-label">{{Version du plugin}}</label>
            <div class="col-sm-6">
                <span class="form-control" style="background:transparent;border:none;font-weight:bold;"><?php echo htmlspecialchars($pluginVersion); ?></span>
            </div>
        </div>

        <legend><i class="fas fa-clock"></i> {{Planification du rafraîchissement}}</legend>

        <div class="form-group">
            <label class="col-sm-4 control-label">
                {{Heure de rafraîchissement}}
                <sup><i class="fas fa-question-circle" title="{{Expression cron définissant la fréquence de mise à jour de tous les horoscopes. Par défaut : tous les jours à 02:30.}}"></i></sup>
            </label>
            <div class="col-sm-6">
                <div class="input-group">
                    <input type="text"
                           id="inp_cron_refresh"
                           class="configKey form-control roundedLeft"
                           data-l1key="cron_refresh"
                           placeholder="30 2 * * *" />
                    <span class="input-group-btn">
                        <a class="btn btn-default cursor jeeHelper roundedRight" data-helper="cron" title="{{Assistant cron}}">
                            <i class="fas fa-question-circle"></i>
                        </a>
                    </span>
                </div>
                <span class="help-block">{{Laisser vide pour utiliser le cron quotidien par défaut (02:30). Exemple : "30 7 * * *" pour tous les jours à 7h30.}}</span>
            </div>
        </div>

        <legend><i class="fas fa-link"></i> {{Source des données}}</legend>

        <div class="form-group">
            <label class="col-sm-4 control-label">{{Site officiel}}</label>
            <div class="col-sm-6">
                <a href="https://www.mon-horoscope-du-jour.com/" target="_blank" class="form-control" style="background:transparent;border:none;">
                    https://www.mon-horoscope-du-jour.com/
                </a>
            </div>
        </div>

    </fieldset>
</form>
