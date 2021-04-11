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

$url = '/local/fliplearning/student_sessions.php';
local_fliplearning_set_page($course, $url);

require_capability('local/fliplearning:usepluggin', $context);
require_capability('local/fliplearning:view_as_student', $context);
require_capability('local/fliplearning:student_sessions', $context);

if(is_siteadmin()){
    print_error(get_string("only_student","local_fliplearning"));
}

\local_fliplearning\log::create("student_sessions", "view", $USER->id, $COURSE->id);
$reports = new \local_fliplearning\student($COURSE->id, $USER->id);

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if (!$configweeks->is_set()) {
    $message = get_string("weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' => [
        "section_help_title" => get_string("ss_section_help_title", "local_fliplearning"),
        "section_help_description" => get_string("ss_section_help_description", "local_fliplearning"),
        "inverted_time_help_title" => get_string("ss_inverted_time_help_title", "local_fliplearning"),
        "inverted_time_help_description_p1" => get_string("ss_inverted_time_help_description_p1", "local_fliplearning"),
        "inverted_time_help_description_p2" => get_string("ss_inverted_time_help_description_p2", "local_fliplearning"),
        "hours_session_help_title" => get_string("ss_hours_session_help_title", "local_fliplearning"),
        "hours_session_help_description_p1" => get_string("ss_hours_session_help_description_p1", "local_fliplearning"),
        "hours_session_help_description_p2" => get_string("ss_hours_session_help_description_p2", "local_fliplearning"),
        "resources_access_help_title" => get_string("ss_resources_access_help_title", "local_fliplearning"),
        "resources_access_help_description_p1" => get_string("ss_resources_access_help_description_p1", "local_fliplearning"),
        "resources_access_help_description_p2" => get_string("ss_resources_access_help_description_p2", "local_fliplearning"),
        "resources_access_help_description_p3" => get_string("ss_resources_access_help_description_p3", "local_fliplearning"),

        "title" => get_string("fml_title", "local_fliplearning"),
        "chart" => $reports->get_chart_langs(),
        "days" => array(
            get_string("fml_mon_short", "local_fliplearning"),
            get_string("fml_tue_short", "local_fliplearning"),
            get_string("fml_wed_short", "local_fliplearning"),
            get_string("fml_thu_short", "local_fliplearning"),
            get_string("fml_fri_short", "local_fliplearning"),
            get_string("fml_sat_short", "local_fliplearning"),
            get_string("fml_sun_short", "local_fliplearning"),
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
        "modules_names" => array(
            "assign" => get_string("fml_assign", "local_fliplearning"),
            "assignment" => get_string("fml_assignment", "local_fliplearning"),
            "attendance" => get_string("fml_attendance", "local_fliplearning"),
            "book" => get_string("fml_book", "local_fliplearning"),
            "chat" => get_string("fml_chat", "local_fliplearning"),
            "choice" => get_string("fml_choice", "local_fliplearning"),
            "data" => get_string("fml_data", "local_fliplearning"),
            "feedback" => get_string("fml_feedback", "local_fliplearning"),
            "folder" => get_string("fml_folder", "local_fliplearning"),
            "forum" => get_string("fml_forum", "local_fliplearning"),
            "glossary" => get_string("fml_glossary", "local_fliplearning"),
            "h5pactivity" => get_string("fml_h5pactivity", "local_fliplearning"),
            "imscp" => get_string("fml_imscp", "local_fliplearning"),
            "label" => get_string("fml_label", "local_fliplearning"),
            "lesson" => get_string("fml_lesson", "local_fliplearning"),
            "lti" => get_string("fml_lti", "local_fliplearning"),
            "page" => get_string("fml_page", "local_fliplearning"),
            "quiz" => get_string("fml_quiz", "local_fliplearning"),
            "resource" => get_string("fml_resource", "local_fliplearning"),
            "scorm" => get_string("fml_scorm", "local_fliplearning"),
            "survey" => get_string("fml_survey", "local_fliplearning"),
            "url" => get_string("fml_url", "local_fliplearning"),
            "wiki" => get_string("fml_wiki", "local_fliplearning"),
            "workshop" => get_string("fml_workshop", "local_fliplearning"),
        ),
        "modules_strings" => array(
            "title" => get_string("fml_modules_access_chart_title","local_fliplearning"),
            "modules_no_viewed" => get_string("fml_modules_no_viewed","local_fliplearning"),
            "modules_viewed" => get_string("fml_modules_viewed","local_fliplearning"),
            "modules_complete" => get_string("fml_modules_complete","local_fliplearning"),
            "close_button" => get_string("fml_close_button","local_fliplearning"),
            "modules_interaction" => get_string("fml_modules_interaction","local_fliplearning"),
            "modules_interactions" => get_string("fml_modules_interactions","local_fliplearning"),
        ),
        "pagination" => get_string("pagination", "local_fliplearning"),
        "ss_change_timezone" => get_string("ss_change_timezone", "local_fliplearning"),
        "graph_generating" => get_string("graph_generating", "local_fliplearning"),
        "api_error_network" => get_string("api_error_network", "local_fliplearning"),
        "pagination_name" => get_string("pagination_component_name","local_fliplearning"),
        "pagination_separator" => get_string("pagination_component_to","local_fliplearning"),
        "pagination_title" => get_string("pagination_title","local_fliplearning"),
        "helplabel" => get_string("helplabel","local_fliplearning"),
        "exitbutton" => get_string("exitbutton","local_fliplearning"),
        "about" => get_string("fml_about", "local_fliplearning"),

        "inverted_time_chart_title" => get_string("fml_student_time_inverted_title","local_fliplearning"),
        "inverted_time_chart_x_axis" => get_string("fml_student_time_inverted_x_axis","local_fliplearning"),
        "inverted_time" => get_string("fml_student_inverted_time","local_fliplearning"),
        "expected_time" => get_string("fml_student_expected_time","local_fliplearning"),

        "resource_access_title" => get_string("fml_resource_access_title", "local_fliplearning"),
        "resource_access_x_axis" => get_string("fml_resource_access_x_axis", "local_fliplearning"),
        "resource_access_y_axis" => get_string("fml_resource_access_y_axis", "local_fliplearning"),
        "resource_access_legend1" => get_string("fml_resource_access_legend1", "local_fliplearning"),
        "resource_access_legend2" => get_string("fml_resource_access_legend2", "local_fliplearning"),

        "hours_sessions_title" => get_string("fml_hours_sessions_title", "local_fliplearning"),
        "week_progress_title" => get_string("fml_week_progress_title", "local_fliplearning"),

        "session_text" => get_string("fml_session_text","local_fliplearning"),
        "sessions_text" => get_string("fml_sessions_text","local_fliplearning"),
        "modules_details" => get_string("fml_modules_details", "local_fliplearning"),

        "hours_short" => get_string("fml_hours_short", "local_fliplearning"),
        "minutes_short" => get_string("fml_minutes_short", "local_fliplearning"),
        "seconds_short" => get_string("fml_seconds_short", "local_fliplearning"),

        "modules_access_chart_title" => get_string("fml_modules_access_chart_title", "local_fliplearning"),
        "modules_viewed" => get_string("fml_modules_viewed", "local_fliplearning"),
        "modules_no_viewed" => get_string("fml_modules_no_viewed", "local_fliplearning"),
        "modules_complete" => get_string("fml_modules_complete", "local_fliplearning"),
        "modules_interaction" => get_string("fml_modules_interaction", "local_fliplearning"),
        "modules_interactions" => get_string("fml_modules_interactions", "local_fliplearning"),
        "close_button" => get_string("fml_close_button", "local_fliplearning"),
    ],
    'hours_session_colors' => array('#E0E0E0', '#118AB2'),
    'resources_access_colors' => array('#06D6A0', '#FFD166', '#EF476F'),
    'inverted_time_colors' => array('#118AB2', '#06D6A0'),
    'courseid' => $COURSE->id,
    'userid' => $USER->id,
    'indicators' => $reports->get_sessions(),
    'pages' => $configweeks->get_weeks_paginator(),
    'profile_render' => $reports->render_has(),
    'timezone' => $reports->timezone,
];

$PAGE->requires->js_call_amd('local_fliplearning/student_sessions', 'init', ['content' => $content]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/student_sessions', ['content' => $content]);
echo $OUTPUT->footer();