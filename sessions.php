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
 * local fliplearning
 *
 * @package     local_fliplearning
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('locallib.php');
global $COURSE, $USER;

$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

$url = '/local/fliplearning/sessions.php';
local_fliplearning_set_page($course, $url);

require_capability('local/fliplearning:usepluggin', $context);
require_capability('local/fliplearning:sessions', $context);

\local_fliplearning\log::create("sessions","view", $USER->id, $COURSE->id);

if(has_capability('local/fliplearning:view_as_teacher', $context)){
    $reports = new \local_fliplearning\teacher($COURSE->id, $USER->id);
}else{
    $reports = new \local_fliplearning\student($COURSE->id, $USER->id);
}

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if(!$configweeks->is_set()){
    $message = get_string("weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' =>[
        "title" => get_string("fml_title", "local_fliplearning"),
        "days" => array(
            get_string("fml_mon", "local_fliplearning"),
            get_string("fml_tue", "local_fliplearning"),
            get_string("fml_wed", "local_fliplearning"),
            get_string("fml_thu", "local_fliplearning"),
            get_string("fml_fri", "local_fliplearning"),
            get_string("fml_sat", "local_fliplearning"),
            get_string("fml_sun", "local_fliplearning"),
        ),
        "hours" => array(
            get_string("fml_00", "local_fliplearning"),
            get_string("fml_01", "local_fliplearning"),
            get_string("fml_02", "local_fliplearning"),
            get_string("fml_03", "local_fliplearning"),
            get_string("fml_04", "local_fliplearning"),
            get_string("fml_05", "local_fliplearning"),
            get_string("fml_06", "local_fliplearning"),
            get_string("fml_07", "local_fliplearning"),
            get_string("fml_08", "local_fliplearning"),
            get_string("fml_09", "local_fliplearning"),
            get_string("fml_10", "local_fliplearning"),
            get_string("fml_11", "local_fliplearning"),
            get_string("fml_12", "local_fliplearning"),
            get_string("fml_13", "local_fliplearning"),
            get_string("fml_14", "local_fliplearning"),
            get_string("fml_15", "local_fliplearning"),
            get_string("fml_16", "local_fliplearning"),
            get_string("fml_17", "local_fliplearning"),
            get_string("fml_18", "local_fliplearning"),
            get_string("fml_19", "local_fliplearning"),
            get_string("fml_20", "local_fliplearning"),
            get_string("fml_21", "local_fliplearning"),
            get_string("fml_22", "local_fliplearning"),
            get_string("fml_23", "local_fliplearning"),
        ),
        "weeks" => array(
            get_string("fml_week1", "local_fliplearning"),
            get_string("fml_week2", "local_fliplearning"),
            get_string("fml_week3", "local_fliplearning"),
            get_string("fml_week4", "local_fliplearning"),
            get_string("fml_week5", "local_fliplearning"),
            get_string("fml_week6", "local_fliplearning"),
        ),
        "table_title" => get_string("table_title", "local_fliplearning"),
        "thead_name" => get_string("thead_name", "local_fliplearning"),
        "thead_lastname" => get_string("thead_lastname", "local_fliplearning"),
        "thead_email" => get_string("thead_email", "local_fliplearning"),
        "thead_progress" => get_string("thead_progress", "local_fliplearning"),
        "thead_sessions" => get_string("thead_sessions", "local_fliplearning"),
        "thead_time" => get_string("thead_time", "local_fliplearning"),

        "module_label" => get_string("fml_module_label", "local_fliplearning"),
        "modules_label" => get_string("fml_modules_label", "local_fliplearning"),
        "of_conector" => get_string("fml_of_conector", "local_fliplearning"),
        "finished_label" => get_string("fml_finished_label", "local_fliplearning"),
        "finisheds_label" => get_string("fml_finisheds_label", "local_fliplearning"),

        "session_count_title" => get_string("fml_session_count_title", "local_fliplearning"),
        "session_count_yaxis_title" => get_string("fml_session_count_yaxis_title", "local_fliplearning"),
        "session_count_tooltip_suffix" => get_string("fml_session_count_tooltip_suffix", "local_fliplearning"),

        "hours_sessions_title" => get_string("fml_hours_sessions_title", "local_fliplearning"),
        "weeks_sessions_title" => get_string("fml_weeks_sessions_title", "local_fliplearning"),

        "no_data" => get_string("no_data", "local_fliplearning"),
        "pagination" => get_string("pagination", "local_fliplearning"),
        "ss_change_timezone" => get_string("ss_change_timezone", "local_fliplearning"),
        "graph_generating" => get_string("graph_generating", "local_fliplearning"),
        "time_inside_plataform_teacher" => get_string("ss_time_inside_plataform_teacher", "local_fliplearning"),
        "time_inside_plataform_student" => get_string("ss_time_inside_plataform_student", "local_fliplearning"),
        "activity_inside_plataform_student" => get_string("ss_activity_inside_plataform_student", "local_fliplearning"),
        "activity_inside_plataform_teacher" => get_string("ss_activity_inside_plataform_teacher", "local_fliplearning"),
        "api_error_network" => get_string("api_error_network", "local_fliplearning"),
        "pagination_name" => get_string("pagination_component_name","local_fliplearning"),
        "pagination_separator" => get_string("pagination_component_to","local_fliplearning"),
        "time_inside_plataform_description_teacher" => get_string("ss_time_inside_plataform_description_teacher","local_fliplearning"),
        "time_inside_plataform_description_student" => get_string("ss_time_inside_plataform_description_student","local_fliplearning"),
        "activity_inside_plataform_description_teacher" => get_string("ss_activity_inside_plataform_description_teacher","local_fliplearning"),
        "activity_inside_plataform_description_student" => get_string("ss_activity_inside_plataform_description_student","local_fliplearning"),
        "pagination_title" => get_string("pagination_title","local_fliplearning"),
        "helplabel" => get_string("helplabel","local_fliplearning"),
        "exitbutton" => get_string("exitbutton","local_fliplearning"),
    ],
    'courseid' => $COURSE->id,
    'userid' => $USER->id,
    'sessions_by_hours' => $reports->hours_sessions(),
    'sessions_by_weeks' => $reports->weeks_sessions(),
    'progress_table' => $reports->progress_table(),
    'session_count' => $reports->count_sessions(),
    'pages' => $configweeks->get_weeks_paginator(),
    'profile_render' => $reports->render_has(),
    'groups' => local_fliplearning_get_groups($course, $USER),
    'timezone' => $reports->timezone,
];

$PAGE->requires->js_call_amd('local_fliplearning/sessions','init', ['content' => $content]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/sessions', ['content' => $content]);
echo $OUTPUT->footer();