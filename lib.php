<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     local_fliplearning
 * @category    admin
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');

function local_fliplearning_render_navbar_output(\renderer_base $renderer) {

    global $CFG, $COURSE, $PAGE, $SESSION, $SITE, $USER;

    $items = [];

    if (isset($COURSE) && $COURSE->id <= 1 ) {
        return null;
    }

    //$configweeks = new local_progress_dashboard_configweeks($COURSE, $USER);

    $url = new moodle_url('/local/fliplearning/graph1.php?courseid='.$COURSE->id);
    //die($url);

    // semanas
    $item = new stdClass();
    $item->name = 'Configurar Semanas';
    $item->url = $url;
    array_push($items, $item);

    // grafico 1
    $item = new stdClass();
    $item->name = 'PD.1.';
    $item->url = $url;
    array_push($items, $item);

    $params = [
        "title" => get_string('menu_main_title', 'local_fliplearning'),
        "items" => $items];
    return $renderer->render_from_template('local_fliplearning/navbar_popover', $params);


}

function local_fliplearning_get_fontawesome_icon_map() {
    return [
        'local_fliplearning:icon' => 'fa-pie-chart',
    ];
}