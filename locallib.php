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
 * Library of functions for Flip my Learning.
 *
 * @package     local_fliplearning
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @author      Edisson Sigua
 * @author      Bryan Aguilar
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');

/**
 * Retorna un nuevo ítem para el menú de navegación de Fliplearning
 *
 * @param string $name nombre de la página para el ítem
 * @param string $url url de la página para el ítem
 *
 * @return stdClass objeto que contiene el nuevo ítem
 */
function local_fliplearning_new_menu_item($name, $url){
    $item = new stdClass();
    $item->name = $name;
    $item->url = $url;
    return $item;
}

/**
 * Agrega una nueva página a la navegación de Moodle
 *
 * @param object $course objeto con datos del curso curso
 * @param string $url url de la página a poner en la navegación
 */
function local_fliplearning_set_page($course, $url){
    global $PAGE;
    require_login($course, false);

    $url = new moodle_url($url);
    $url->param('courseid', $course->id);
    $PAGE->set_url($url);
    $plugin_name = get_string('pluginname', 'local_fliplearning');
    $PAGE->set_title($plugin_name);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_heading($course->fullname);
    local_fliplearning_render_styles();
}

/**
 * Agrega las importaciones de los archivos css necesarios para Fliplearning
 */
function local_fliplearning_render_styles(){
    global $PAGE;
    $PAGE->requires->css('/local/fliplearning/css/googlefonts.css');
    $PAGE->requires->css('/local/fliplearning/css/materialicon.css');
    $PAGE->requires->css('/local/fliplearning/css/materialdesignicons.css');
    $PAGE->requires->css('/local/fliplearning/css/vuetify.css');
    $PAGE->requires->css('/local/fliplearning/css/alertify.css');
    $PAGE->requires->css('/local/fliplearning/css/quill.core.css');
    $PAGE->requires->css('/local/fliplearning/css/quill.snow.css');
    $PAGE->requires->css('/local/fliplearning/css/quill.bubble.css');
    $PAGE->requires->css('/local/fliplearning/styles.css');
}

/**
 * Envuelve en un contenedor la respuesta a una petición Ajax
 *
 * @param object $data los datos a devolver
 * @param string $message mensaje de la respuesta
 * @param boolean $valid campo opcional para especificar si la respuesta es correcta válida
 * @param string $code campo opcionar para especificar la respuesta http
 */
function local_fliplearning_ajax_response($data = array(), $code = 200){
    local_fliplearning_set_api_headers();
    http_response_code($code);
    echo json_encode($data);
}

/**
 * Coloca cabeceras a la respuesta http de una peticion ajax
 */
function local_fliplearning_set_api_headers(){
    header('Access-Control-Allow-Origin: *');
    header('Content-type: application/json');
}

function local_fliplearning_get_groups($course, $user){
    global $COURSE;
    $group_manager = new \local_fliplearning\group_manager($course, $user);
    $participants = new \local_fliplearning\course_participant($user->id, $course->id);
    $groups = array_values($participants->current_user_groups_with_all_group($COURSE->groupmode));
    $selectedgroupid = $group_manager->selected_group()->groupid;
    $groups = local_fliplearning_add_selected_property($groups, $selectedgroupid);
    return $groups;
}

function local_fliplearning_add_selected_property($groups, $groupid = null){
    foreach ($groups as $group) {
        if(!is_null($groupid) && $group->id == $groupid){
            $group->selected = true;
        }else{
            $group->selected = false;
        }
    }
    return $groups;
}
