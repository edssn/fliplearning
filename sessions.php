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

require_capability('local/fliplearning:usepluggin', $context);
require_capability('local/fliplearning:sessions', $context);

$url = '/local/fliplearning/sessions.php';
local_fliplearning_set_page($course, $url);

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
        "months" => array(
            get_string("fml_jan", "local_fliplearning"),
            get_string("fml_feb", "local_fliplearning"),
            get_string("fml_mar", "local_fliplearning"),
            get_string("fml_apr", "local_fliplearning"),
            get_string("fml_may", "local_fliplearning"),
            get_string("fml_jun", "local_fliplearning"),
            get_string("fml_jul", "local_fliplearning"),
            get_string("fml_aug", "local_fliplearning"),
            get_string("fml_sep", "local_fliplearning"),
            get_string("fml_oct", "local_fliplearning"),
            get_string("fml_nov", "local_fliplearning"),
            get_string("fml_dec", "local_fliplearning"),
        ),
        "weeks" => array(
            get_string("fml_week1", "local_fliplearning"),
            get_string("fml_week2", "local_fliplearning"),
            get_string("fml_week3", "local_fliplearning"),
            get_string("fml_week4", "local_fliplearning"),
            get_string("fml_week5", "local_fliplearning"),
            get_string("fml_week6", "local_fliplearning"),
        ),
        "no_data" => get_string("no_data", "local_fliplearning"),
        "axis_x" => get_string("ss_axis_x", "local_fliplearning"),
        "axis_y" => get_string("ss_axis_y", "local_fliplearning"),
        "ss_url" => get_string("ss_url", "local_fliplearning"),
        "ss_resource_document" => get_string("ss_resource_document", "local_fliplearning"),
        "ss_resource_image" => get_string("ss_resource_image", "local_fliplearning"),
        "ss_resource_audio" => get_string("ss_resource_audio", "local_fliplearning"),
        "ss_resource_video" => get_string("ss_resource_video", "local_fliplearning"),
        "ss_resource_file" => get_string("ss_resource_file", "local_fliplearning"),
        "ss_resource_script" => get_string("ss_resource_script", "local_fliplearning"),
        "ss_resource_text" => get_string("ss_resource_text", "local_fliplearning"),
        "ss_resource_download" => get_string("ss_resource_download", "local_fliplearning"),
        "ss_assign" => get_string("ss_assign", "local_fliplearning"),
        "ss_assignment" => get_string("ss_assignment", "local_fliplearning"),
        "ss_book" => get_string("ss_book", "local_fliplearning"),
        "ss_choice" => get_string("ss_choice", "local_fliplearning"),
        "ss_feedback" => get_string("ss_feedback", "local_fliplearning"),
        "ss_folder" => get_string("ss_folder", "local_fliplearning"),
        "ss_forum" => get_string("ss_forum", "local_fliplearning"),
        "ss_glossary" => get_string("ss_glossary", "local_fliplearning"),
        "ss_label" => get_string("ss_label", "local_fliplearning"),
        "ss_lesson" => get_string("ss_lesson", "local_fliplearning"),
        "ss_page" => get_string("ss_page", "local_fliplearning"),
        "ss_quiz" => get_string("ss_quiz", "local_fliplearning"),
        "ss_survey" => get_string("ss_survey", "local_fliplearning"),
        "pagination" => get_string("pagination", "local_fliplearning"),
        "ss_interaction" => get_string("ss_interaction", "local_fliplearning"),
        "ss_interactions" => get_string("ss_interactions", "local_fliplearning"),
        "ss_course_module" => get_string("ss_course_module", "local_fliplearning"),
        "ss_course_modules" => get_string("ss_course_modules", "local_fliplearning"),
        "ss_other" => get_string("ss_other", "local_fliplearning"),
        "ss_student" => get_string("ss_student", "local_fliplearning"),
        "ss_students" => get_string("ss_students", "local_fliplearning"),
        "ss_average" => get_string("ss_average", "local_fliplearning"),
        "ss_change_timezone" => get_string("ss_change_timezone", "local_fliplearning"),
        "graph_generating" => get_string("graph_generating", "local_fliplearning"),
        "time_inside_plataform_teacher" => get_string("ss_time_inside_plataform_teacher", "local_fliplearning"),
        "time_inside_plataform_student" => get_string("ss_time_inside_plataform_student", "local_fliplearning"),
        "activity_inside_plataform_student" => get_string("ss_activity_inside_plataform_student", "local_fliplearning"),
        "activity_inside_plataform_teacher" => get_string("ss_activity_inside_plataform_teacher", "local_fliplearning"),
        "ss_to" => get_string("ss_to", "local_fliplearning"),
        "api_error_network" => get_string("api_error_network", "local_fliplearning"),
        "time_spend" => get_string("ss_time_spend", "local_fliplearning"),
        "time_spend_teacher" => get_string("ss_time_spend_teacher", "local_fliplearning"),
        "time_should_spend" => get_string("ss_time_should_spend", "local_fliplearning"),
        "time_should_spend_teacher" => get_string("ss_time_should_spend_teacher", "local_fliplearning"),
        "pagination_name" => get_string("pagination_component_name","local_fliplearning"),
        "pagination_separator" => get_string("pagination_component_to","local_fliplearning"),
        "time_inside_plataform_description_teacher" => get_string("ss_time_inside_plataform_description_teacher","local_fliplearning"),
        "time_inside_plataform_description_student" => get_string("ss_time_inside_plataform_description_student","local_fliplearning"),
        "activity_inside_plataform_description_teacher" => get_string("ss_activity_inside_plataform_description_teacher","local_fliplearning"),
        "activity_inside_plataform_description_student" => get_string("ss_activity_inside_plataform_description_student","local_fliplearning"),
        "pagination_title" => get_string("pagination_title","local_fliplearning"),
        "helplabel" => get_string("helplabel","local_fliplearning"),
        "exitbutton" => get_string("exitbutton","local_fliplearning"),
        'hours_unit_time_label' => get_string('hours_unit_time_label', 'local_fliplearning'),
    ],
    'courseid' => $COURSE->id,
    'userid' => $USER->id,
    'sessions_by_hours' => $reports->get_sessions_by_hours(),
    'sessions_by_weeks' => $reports->get_sessions_by_weeks(),
    'pages' => $configweeks->get_weeks_paginator(),
    'profile_render' => $reports->render_has(),
    'groups' => local_fliplearning_get_groups($course, $USER),
    'timezone' => $reports->timezone,
];

$PAGE->requires->js_call_amd('local_fliplearning/sessions','init', ['content' => $content]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/sessions', ['content' => $content]);
echo $OUTPUT->footer();