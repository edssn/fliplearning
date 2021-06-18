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

$url = '/local/fliplearning/teacher.php';
local_fliplearning_set_page($course, $url);

require_capability('local/fliplearning:usepluggin', $context);
require_capability('local/fliplearning:view_as_teacher', $context);
require_capability('local/fliplearning:teacher_general', $context);

\local_fliplearning\logs::create("teacher_general", "view", $USER->id, $COURSE->id);
$reports = new \local_fliplearning\teacher($COURSE->id, $USER->id);

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if (!$configweeks->is_set()) {
    $message = get_string("fml_weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' => [
        "section_help_title" => get_string("tg_section_help_title", "local_fliplearning"),
        "section_help_description" => get_string("tg_section_help_description", "local_fliplearning"),
        "week_resources_help_title" => get_string("tg_week_resources_help_title", "local_fliplearning"),
        "week_resources_help_description_p1" => get_string("tg_week_resources_help_description_p1", "local_fliplearning"),
        "week_resources_help_description_p2" => get_string("tg_week_resources_help_description_p2", "local_fliplearning"),
        "weeks_sessions_help_title" => get_string("tg_weeks_sessions_help_title", "local_fliplearning"),
        "week_sessions_help_description_p1" => get_string("tg_week_sessions_help_description_p1", "local_fliplearning"),
        "week_sessions_help_description_p2" => get_string("tg_week_sessions_help_description_p2", "local_fliplearning"),
        "progress_table_help_title" => get_string("tg_progress_table_help_title", "local_fliplearning"),
        "progress_table_help_description" => get_string("tg_progress_table_help_description", "local_fliplearning"),

        "finished_resources" => get_string("fml_finished_resources", "local_fliplearning"),
        "sessions_text" => get_string("fml_sessions", "local_fliplearning"),

        "title" => get_string("fml_menu_general", "local_fliplearning"),
        "chart" => $reports->get_chart_langs(),
        "helplabel" => get_string("fml_helplabel","local_fliplearning"),
        "exitbutton" => get_string("fml_exitbutton","local_fliplearning"),
        "change_timezone" => get_string("fml_change_timezone", "local_fliplearning"),
        "graph_generating" => get_string("fml_graph_generating", "local_fliplearning"),
        "about" => get_string("fml_about", "local_fliplearning"),
        "about_table" => get_string("fml_about_table", "local_fliplearning"),

        "table_title" => get_string("tg_progress_table_table_title", "local_fliplearning"),
        "thead_name" => get_string("tg_progress_table_thead_name", "local_fliplearning"),
        "thead_lastname" => get_string("tg_progress_table_thead_lastname", "local_fliplearning"),
        "thead_email" => get_string("tg_progress_table_thead_email", "local_fliplearning"),
        "thead_progress" => get_string("tg_progress_table_thead_progress", "local_fliplearning"),
        "thead_sessions" => get_string("tg_progress_table_thead_sessions", "local_fliplearning"),
        "thead_time" => get_string("tg_progress_table_thead_time", "local_fliplearning"),
        "of_conector" => get_string("fml_of_conector", "local_fliplearning"),

        "teacher_indicators_students" => get_string("tg_indicators_students", "local_fliplearning"),
        "teacher_indicators_weeks" => get_string("tg_indicators_weeks", "local_fliplearning"),
        "teacher_indicators_modules" => get_string("fml_resources", "local_fliplearning"),
        "teacher_indicators_grademax" => get_string("tg_indicators_grademax", "local_fliplearning"),
        "teacher_indicators_course_start" => get_string("tg_indicators_course_start", "local_fliplearning"),
        "teacher_indicators_course_end" => get_string("tg_indicators_course_end", "local_fliplearning"),
        "teacher_indicators_course_format" => get_string("tg_indicators_course_format", "local_fliplearning"),
        "teacher_indicators_course_completion" => get_string("tg_indicators_course_completion", "local_fliplearning"),
        "teacher_indicators_finalized" => get_string("fml_finished_label", "local_fliplearning"),
        "teacher_indicators_finished" => get_string("fml_finisheds_label", "local_fliplearning"),
        "teacher_indicators_session" => get_string("fml_session_text","local_fliplearning"),
        "teacher_indicators_sessions" => get_string("fml_sessions_text","local_fliplearning"),
        "teacher_indicators_student_progress" => get_string("tg_indicators_student_progress", "local_fliplearning"),

        "teacher_indicators_week_resources_chart_title" => get_string("tg_week_resources_chart_title", "local_fliplearning"),
        "teacher_indicators_week_resources_yaxis_title" => get_string("tg_week_resources_yaxis_title", "local_fliplearning"),

        "weeks_sessions_title" => get_string("ts_weeks_sessions_chart_title", "local_fliplearning"),
        "weeks" => array(
            get_string("fml_week1", "local_fliplearning"),
            get_string("fml_week2", "local_fliplearning"),
            get_string("fml_week3", "local_fliplearning"),
            get_string("fml_week4", "local_fliplearning"),
            get_string("fml_week5", "local_fliplearning"),
            get_string("fml_week6", "local_fliplearning"),
        ),
    ],
    'weeks_sessions_colors' => array('#E0E0E0', '#118AB2'),
    'week_resources_colors' => array('#118AB2'),
    'courseid' => $COURSE->id,
    'userid' => $USER->id,
    'indicators' => $reports->get_general_indicators(),
    'profile_render' => $reports->render_has(),
    'groups' => local_fliplearning_get_groups($course, $USER),
    'timezone' => $reports->timezone,
];

$PAGE->requires->js_call_amd('local_fliplearning/teacher', 'init', ['content' => $content]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/teacher', ['content' => $content]);
echo $OUTPUT->footer();