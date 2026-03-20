<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function horoscopeplus_install() {
    horoscopeplus_update();
}

function horoscopeplus_update() {
    $data = json_decode(file_get_contents(dirname(__FILE__) . '/info.json'), true);
    $pluginVersion = isset($data['pluginVersion']) ? $data['pluginVersion'] : '0.0';
    config::save('version', $pluginVersion, 'horoscopeplus');
    config::save('functionality::cron::enable', 1, 'horoscopeplus');
    horoscopeplus_install_widget();
    log::add('horoscopeplus', 'info', 'Plugin HoroscopePlus mis à jour en version ' . $pluginVersion);
}

function horoscopeplus_install_widget() {
    $src  = dirname(__FILE__) . '/../desktop/template/cmd.info.string.HoroscopePlus_logo.html';
    $dest = __DIR__ . '/../../../data/customTemplates/dashboard/cmd.info.string.HoroscopePlus_logo.html';
    $dir  = dirname($dest);

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Copie uniquement si le fichier source existe
    if (file_exists($src)) {
        copy($src, $dest);
        log::add('horoscopeplus', 'info', 'Widget HoroscopePlus_logo installé dans ' . $dest);
    } else {
        log::add('horoscopeplus', 'warning', 'Widget source introuvable : ' . $src);
    }
}

function horoscopeplus_remove() {
    // Suppression du widget custom à la désinstallation
    $dest = __DIR__ . '/../../../data/customTemplates/dashboard/cmd.info.string.HoroscopePlus_logo.html';
    if (file_exists($dest)) {
        unlink($dest);
        log::add('horoscopeplus', 'info', 'Widget HoroscopePlus_logo supprimé');
    }
}
