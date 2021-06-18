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
require_capability('local/fliplearning:view_as_teacher', $context);
require_capability('local/fliplearning:teacher_sessions', $context);

\local_fliplearning\logs::create("teacher_sessions","view", $USER->id, $COURSE->id);
$reports = new \local_fliplearning\teacher($COURSE->id, $USER->id);

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if(!$configweeks->is_set()){
    $message = get_string("fml_weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' =>[
        "section_help_title" => get_string("ts_section_help_title", "local_fliplearning"),
        "section_help_description" => get_string("ts_section_help_description", "local_fliplearning"),
        "inverted_time_help_title" => get_string("ts_inverted_time_help_title", "local_fliplearning"),
        "inverted_time_help_description_p1" => get_string("ts_inverted_time_help_description_p1", "local_fliplearning"),
        "inverted_time_help_description_p2" => get_string("ts_inverted_time_help_description_p2", "local_fliplearning"),
        "hours_sessions_help_title" => get_string("ts_hours_sessions_help_title", "local_fliplearning"),
        "hours_sessions_help_description_p1" => get_string("ts_hours_sessions_help_description_p1", "local_fliplearning"),
        "hours_sessions_help_description_p2" => get_string("ts_hours_sessions_help_description_p2", "local_fliplearning"),
        "sessions_count_help_title" => get_string("ts_sessions_count_help_title", "local_fliplearning"),
        "sessions_count_help_description_p1" => get_string("ts_sessions_count_help_description_p1", "local_fliplearning"),
        "sessions_count_help_description_p2" => get_string("ts_sessions_count_help_description_p2", "local_fliplearning"),

        "title" => get_string("fml_menu_sessions", "local_fliplearning"),
        "chart" => $reports->get_chart_langs(),
        "days" => $reports->get_short_weekdays(),
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
        "about" => get_string("fml_about", "local_fliplearning"),

        "module_label" => get_string("fml_module_label", "local_fliplearning"),
        "modules_label" => get_string("fml_modules_label", "local_fliplearning"),
        "of_conector" => get_string("fml_of_conector", "local_fliplearning"),
        "finished_label" => get_string("fml_finished_label", "local_fliplearning"),
        "finisheds_label" => get_string("fml_finisheds_label", "local_fliplearning"),

        "session_count_title" => get_string("ts_session_count_chart_title", "local_fliplearning"),
        "session_count_yaxis_title" => get_string("ts_session_count_yaxis_title", "local_fliplearning"),
        "session_count_tooltip_suffix" => get_string("ts_session_count_tooltip_suffix", "local_fliplearning"),

        "hours_sessions_title" => get_string("ts_hours_sessions_chart_title", "local_fliplearning"),
        "weeks_sessions_title" => get_string("ts_weeks_sessions_chart_title", "local_fliplearning"),

        "change_timezone" => get_string("fml_change_timezone", "local_fliplearning"),
        "graph_generating" => get_string("fml_graph_generating", "local_fliplearning"),
        "api_error_network" => get_string("fml_api_error_network", "local_fliplearning"),
        "pagination" => get_string("fml_pagination", "local_fliplearning"),
        "pagination_name" => get_string("fml_pagination_component_name","local_fliplearning"),
        "pagination_separator" => get_string("fml_pagination_component_to","local_fliplearning"),
        "pagination_title" => get_string("fml_pagination_title","local_fliplearning"),
        "helplabel" => get_string("fml_helplabel","local_fliplearning"),
        "exitbutton" => get_string("fml_exitbutton","local_fliplearning"),

        "session_text" => get_string("fml_session_text","local_fliplearning"),
        "sessions_text" => get_string("fml_sessions_text","local_fliplearning"),

        "time_inverted_title" => get_string("ts_time_inverted_chart_title","local_fliplearning"),
        "time_inverted_x_axis" => get_string("ts_time_inverted_xaxis","local_fliplearning"),
        "inverted_time" => get_string("ts_time_inverted_inverted_label","local_fliplearning"),
        "expected_time" => get_string("ts_time_inverted_expected_label","local_fliplearning"),

        "hours_short" => get_string("fml_hours_short", "local_fliplearning"),
        "minutes_short" => get_string("fml_minutes_short", "local_fliplearning"),
        "seconds_short" => get_string("fml_seconds_short", "local_fliplearning"),
    ],
    'hours_sessions_colors' => array("#E0E0E0", '#118AB2'),
    'inverted_time_colors' => array('#118AB2', '#06D6A0'),
    'sessions_count_colors' => array('#FFD166', '#06D6A0', '#118AB2'),
    'courseid' => $COURSE->id,
    'userid' => $USER->id,
    'indicators' => $reports->get_sessions(),
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