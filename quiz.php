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

$url = '/local/fliplearning/time.php';
local_fliplearning_set_page($course, $url);

require_capability('local/fliplearning:usepluggin', $context);
require_capability('local/fliplearning:view_as_teacher', $context);
require_capability('local/fliplearning:quiz', $context);

\local_fliplearning\log::create("quiz","view", $USER->id, $COURSE->id);
$reports = new \local_fliplearning\teacher($COURSE->id, $USER->id);

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if(!$configweeks->is_set()){
    $message = get_string("weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' =>[
        "section_help_title" => get_string("tq_section_help_title", "local_fliplearning"),
        "section_help_description" => get_string("tq_section_help_description", "local_fliplearning"),
        "questions_attempts_help_title" => get_string("tq_questions_attempts_help_title", "local_fliplearning"),
        "questions_attempts_help_description_p1" => get_string("tq_questions_attempts_help_description_p1", "local_fliplearning"),
        "questions_attempts_help_description_p2" => get_string("tq_questions_attempts_help_description_p2", "local_fliplearning"),
        "questions_attempts_help_description_p3" => get_string("tq_questions_attempts_help_description_p3", "local_fliplearning"),
        "hardest_questions_help_title" => get_string("tq_hardest_questions_help_title", "local_fliplearning"),
        "hardest_questions_help_description_p1" => get_string("tq_hardest_questions_help_description_p1", "local_fliplearning"),
        "hardest_questions_help_description_p2" => get_string("tq_hardest_questions_help_description_p2", "local_fliplearning"),
        "hardest_questions_help_description_p3" => get_string("tq_hardest_questions_help_description_p3", "local_fliplearning"),

        "chart" => $reports->get_chart_langs(),
        "title" => get_string("menu_quiz","local_fliplearning"),
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

        "quiz_info_text" => get_string("fml_quiz_info_text", "local_fliplearning"),
        "question_text" => get_string("fml_question_text", "local_fliplearning"),
        "questions_text" => get_string("fml_questions_text", "local_fliplearning"),
        "doing_text_singular" => get_string("fml_doing_text_singular", "local_fliplearning"),
        "doing_text_plural" => get_string("fml_doing_text_plural", "local_fliplearning"),
        "attempt_text" => get_string("fml_attempt_text", "local_fliplearning"),
        "attempts_text" => get_string("fml_attempts_text", "local_fliplearning"),
        "student_text" => get_string("fml_student_text", "local_fliplearning"),
        "students_text" => get_string("fml_students_text", "local_fliplearning"),
        "of_conector" => get_string("fml_of_conector", "local_fliplearning"),
        "quiz_label" => get_string("fml_quiz", "local_fliplearning"),

        "questions_attempts_chart_title" => get_string("fml_questions_attempts_chart_title", "local_fliplearning"),
        "questions_attempts_yaxis_title" => get_string("fml_questions_attempts_yaxis_title", "local_fliplearning"),
        "hardest_questions_chart_title" => get_string("fml_hardest_questions_chart_title", "local_fliplearning"),
        "hardest_questions_yaxis_title" => get_string("fml_hardest_questions_yaxis_title", "local_fliplearning"),

        "correct_attempt" => get_string("fml_correct_attempt", "local_fliplearning"),
        "partcorrect_attempt" => get_string("fml_partcorrect_attempt", "local_fliplearning"),
        "incorrect_attempt" => get_string("fml_incorrect_attempt", "local_fliplearning"),
        "blank_attempt" => get_string("fml_blank_attempt", "local_fliplearning"),
        "needgraded_attempt" => get_string("fml_needgraded_attempt", "local_fliplearning"),
        "review_question" => get_string("fml_review_question", "local_fliplearning"),

    ],
    'questions_attempts_colors' => array('#06D6A0', '#FFD166', '#EF476F', '#118AB2', '#264653'),
    'hardest_questions_colors' => array('#EF476F'),
    'courseid' => $COURSE->id,
    'userid' => $USER->id,
    'quiz' => $reports->quiz_attempts(),
    'pages' => $configweeks->get_weeks_paginator(),
    'profile_render' => $reports->render_has(),
    'groups' => local_fliplearning_get_groups($course, $USER),
    'timezone' => $reports->timezone,
];

$PAGE->requires->js_call_amd('local_fliplearning/quiz','init', ['content' => $content]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/quiz', ['content' => $content]);
echo $OUTPUT->footer();