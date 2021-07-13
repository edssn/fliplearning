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
global $DB, $COURSE, $USER, $PAGE, $OUTPUT;

$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

$url = '/local/fliplearning/setweeks.php';
local_fliplearning_set_page($course, $url);

require_capability('local/fliplearning:usepluggin', $context);
require_capability('local/fliplearning:view_as_teacher', $context);
require_capability('local/fliplearning:setweeks', $context);

// Guardar log
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https"
        : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
\local_fliplearning\logs::create(
    "setweeks",
    "setweeks",
    "viewed",
    "section",
    $url,
    2,
    $USER->id,
    $COURSE->id
);

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);

$content = [
    'strings' =>[
        'title' => get_string('tw_section_title', 'local_fliplearning'),
        'help_title' => get_string('tw_section_help_title', 'local_fliplearning'),
        'help_description' => get_string('tw_section_help_description', 'local_fliplearning'),
        'sections' => get_string('tw_sections', 'local_fliplearning'),
        'weeks_of_course' => get_string('tw_weeks_of_course', 'local_fliplearning'),
        'add_new_week' => get_string('tw_add_new_week', 'local_fliplearning'),
        'start' => get_string('tw_start', 'local_fliplearning'),
        'week' => get_string('tw_week', 'local_fliplearning'),
        'end' => get_string('tw_end', 'local_fliplearning'),
        'save' => get_string('tw_save', 'local_fliplearning'),
        'error_empty_week' => get_string('tw_error_empty_week', 'local_fliplearning'),
        'enable_scroll' => get_string('tw_enable_scroll', 'local_fliplearning'),
        'error_network' => get_string('fml_api_error_network', 'local_fliplearning'),
        'save_successful' => get_string('fml_api_save_successful', 'local_fliplearning'),
        'cancel_action' => get_string('fml_api_cancel_action', 'local_fliplearning'),
        'save_warning_title' => get_string('tw_save_warning_title', 'local_fliplearning'),
        'save_warning_content' => get_string('tw_save_warning_content', 'local_fliplearning'),
        'confirm_ok' => get_string('tw_confirm_ok', 'local_fliplearning'),
        'confirm_cancel' => get_string('tw_confirm_cancel', 'local_fliplearning'),
        'error_section_removed' => get_string('tw_error_section_removed', 'local_fliplearning'),
        'label_section_removed' => get_string('tw_label_section_removed', 'local_fliplearning'),
        'new_group_title' => get_string('tw_new_group_title', 'local_fliplearning'),
        'new_group_text' => get_string('tw_new_group_text', 'local_fliplearning'),
        'new_group_button_label' => get_string('tw_new_group_button_label', 'local_fliplearning'),
        'time_dedication' => get_string('tw_time_dedication', 'local_fliplearning'),
        'requirements_title' => get_string('tw_plugin_requirements_title', 'local_fliplearning'),
        'requirements_descriptions' => get_string('tw_plugin_requirements_descriptions', 'local_fliplearning'),
        'requirements_has_users' => get_string('tw_plugin_requirements_has_users', 'local_fliplearning'),
        'requirements_course_start' => get_string('tw_plugin_requirements_course_start', 'local_fliplearning'),
        'requirements_has_sections' => get_string('tw_plugin_requirements_has_sections', 'local_fliplearning'),
        'plugin_visible' => get_string('tw_plugin_visible', 'local_fliplearning'),
        'plugin_hidden' => get_string('tw_plugin_hidden', 'local_fliplearning'),
        "helplabel" => get_string("fml_helplabel","local_fliplearning"),
        "exitbutton" => get_string("fml_exitbutton","local_fliplearning"),
        "title_conditions" => get_string("tw_title_conditions","local_fliplearning"),

        "minutes" => get_string("fml_minutes", "local_fliplearning"),
        "timeFrame" => get_string("tw_time_frame", "local_fliplearning"),
        "estimatedTime" => get_string("tw_estimated_time", "local_fliplearning"),
        "activitiesListDialogTitle" => get_string("tw_activities_list_dialog_title", "local_fliplearning"),
        "activitiesListDialogDescription" => get_string("tw_activities_list_dialog_description", "local_fliplearning"),
    ],
    'sections' => $configweeks->get_sections_without_week(),
    'sectionsWithCms' => $configweeks->get_sections_with_course_modules(),
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