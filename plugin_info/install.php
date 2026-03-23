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
    log::add('horoscopeplus', 'info', 'Plugin HoroscopePlus mis à jour en version ' . $pluginVersion);
}

function horoscopeplus_remove() {
}
