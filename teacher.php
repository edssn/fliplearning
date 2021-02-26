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

\local_fliplearning\log::create("teacher_general", "view", $USER->id, $COURSE->id);
$reports = new \local_fliplearning\teacher($COURSE->id, $USER->id);

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if (!$configweeks->is_set()) {
    $message = get_string("weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' => [
        "title" => get_string("fml_teacher_indicators_title", "local_fliplearning"),
        "chart" => $reports->get_chart_langs(),
        "helplabel" => get_string("helplabel","local_fliplearning"),
        "exitbutton" => get_string("exitbutton","local_fliplearning"),
        "ss_change_timezone" => get_string("ss_change_timezone", "local_fliplearning"),
        "graph_generating" => get_string("graph_generating", "local_fliplearning"),

        "table_title" => get_string("table_title", "local_fliplearning"),
        "thead_name" => get_string("thead_name", "local_fliplearning"),
        "thead_lastname" => get_string("thead_lastname", "local_fliplearning"),
        "thead_email" => get_string("thead_email", "local_fliplearning"),
        "thead_progress" => get_string("thead_progress", "local_fliplearning"),
        "thead_sessions" => get_string("thead_sessions", "local_fliplearning"),
        "thead_time" => get_string("thead_time", "local_fliplearning"),
        "of_conector" => get_string("fml_of_conector", "local_fliplearning"),

        "teacher_indicators_student" => get_string("fml_teacher_indicators_student", "local_fliplearning"),
        "teacher_indicators_students" => get_string("fml_teacher_indicators_students", "local_fliplearning"),
        "teacher_indicators_week" => get_string("fml_teacher_indicators_week", "local_fliplearning"),
        "teacher_indicators_weeks" => get_string("fml_teacher_indicators_weeks", "local_fliplearning"),
        "teacher_indicators_module" => get_string("fml_module_label", "local_fliplearning"),
        "teacher_indicators_modules" => get_string("fml_modules_label", "local_fliplearning"),
        "teacher_indicators_finalized" => get_string("fml_finished_label", "local_fliplearning"),
        "teacher_indicators_finished" => get_string("fml_finisheds_label", "local_fliplearning"),
        "teacher_indicators_session" => get_string("fml_session_text","local_fliplearning"),
        "teacher_indicators_sessions" => get_string("fml_sessions_text","local_fliplearning"),
        "teacher_indicators_student_progress" => get_string("fml_teacher_indicators_student_progress", "local_fliplearning"),

        "teacher_indicators_week_resources_chart_title" => get_string("fml_teacher_indicators_week_resources_chart_title", "local_fliplearning"),
        "teacher_indicators_week_resources_yaxis_title" => get_string("fml_teacher_indicators_week_resources_yaxis_title", "local_fliplearning"),

        "weeks_sessions_title" => get_string("fml_weeks_sessions_title", "local_fliplearning"),
        "weeks" => array(
            get_string("fml_week1", "local_fliplearning"),
            get_string("fml_week2", "local_fliplearning"),
            get_string("fml_week3", "local_fliplearning"),
            get_string("fml_week4", "local_fliplearning"),
            get_string("fml_week5", "local_fliplearning"),
            get_string("fml_week6", "local_fliplearning"),
        ),
    ],
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