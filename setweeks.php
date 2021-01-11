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
 * Set weeks page
 *
 * @package     local_fliplearning
 * @autor       Edisson Sigua, Bryan Aguilar
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('locallib.php');
global $COURSE, $USER;

$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_capability('local/fliplearning:usepluggin', $context);
require_capability('local/fliplearning:setweeks', $context);

$url = '/local/fliplearning/setweeks.php';
local_fliplearning_set_page($course, $url);

\local_fliplearning\log::create("setweeks","view", $USER->id, $COURSE->id);
$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);

$content = [
    'strings' =>[
        'title' => get_string('setweeks_title', 'local_fliplearning'),
        'description' => get_string('setweeks_description', 'local_fliplearning'),
        'sections' => get_string('setweeks_sections', 'local_fliplearning'),
        'weeks_of_course' => get_string('setweeks_weeks_of_course', 'local_fliplearning'),
        'add_new_week' => get_string('setweeks_add_new_week', 'local_fliplearning'),
        'start' => get_string('setweeks_start', 'local_fliplearning'),
        'week' => get_string('setweeks_week', 'local_fliplearning'),
        'end' => get_string('setweeks_end', 'local_fliplearning'),
        'save' => get_string('setweeks_save', 'local_fliplearning'),
        'error_empty_week' => get_string('setweeks_error_empty_week', 'local_fliplearning'),
        'error_network' => get_string('api_error_network', 'local_fliplearning'),
        'save_successful' => get_string('api_save_successful', 'local_fliplearning'),
        'enable_scroll' => get_string('setweeks_enable_scroll', 'local_fliplearning'),
        'cancel_action' => get_string('api_cancel_action', 'local_fliplearning'),
        'save_warning_title' => get_string('setweeks_save_warning_title', 'local_fliplearning'),
        'save_warning_content' => get_string('setweeks_save_warning_content', 'local_fliplearning'),
        'confirm_ok' => get_string('setweeks_confirm_ok', 'local_fliplearning'),
        'confirm_cancel' => get_string('setweeks_confirm_cancel', 'local_fliplearning'),
        'error_section_removed' => get_string('setweeks_error_section_removed', 'local_fliplearning'),
        'label_section_removed' => get_string('setweeks_label_section_removed', 'local_fliplearning'),
        'new_group_title' => get_string('setweeks_new_group_title', 'local_fliplearning'),
        'new_group_text' => get_string('setweeks_new_group_text', 'local_fliplearning'),
        'new_group_button_label' => get_string('setweeks_new_group_button_label', 'local_fliplearning'),
        'time_dedication' => get_string('setweeks_time_dedication', 'local_fliplearning'),
        'requirements_title' => get_string('plugin_requirements_title', 'local_fliplearning'),
        'requirements_descriptions' => get_string('plugin_requirements_descriptions', 'local_fliplearning'),
        'requirements_has_users' => get_string('plugin_requirements_has_users', 'local_fliplearning'),
        'requirements_course_start' => get_string('plugin_requirements_course_start', 'local_fliplearning'),
        'requirements_has_sections' => get_string('plugin_requirements_has_sections', 'local_fliplearning'),
        'plugin_visible' => get_string('plugin_visible', 'local_fliplearning'),
        'plugin_hidden' => get_string('plugin_hidden', 'local_fliplearning'),
        "helplabel" => get_string("helplabel","local_fliplearning"),
        "exitbutton" => get_string("exitbutton","local_fliplearning"),
        "title_conditions" => get_string("title_conditions","local_fliplearning"),
    ],
    'sections' => $configweeks->get_sections_without_week(),
    'userid' => $USER->id,
    'courseid' => $courseid,
    'weeks' => $configweeks->get_weeks_with_sections(),
    'settings' => $configweeks->get_settings(),
    'timezone' => $configweeks->get_timezone(),
];

$PAGE->requires->js_call_amd('local_fliplearning/setweeks','init', ['content' => $content]);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/setweeks', ['content' => $content]);
echo $OUTPUT->footer();