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
require_capability('local/fliplearning:grades', $context);

\local_fliplearning\log::create("grades", "view", $USER->id, $COURSE->id);
$reports = new \local_fliplearning\teacher($COURSE->id, $USER->id);

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if (!$configweeks->is_set()) {
    $message = get_string("weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' => [
        "section_help_title" => get_string("tr_section_help_title", "local_fliplearning"),
        "section_help_description" => get_string("tr_section_help_description", "local_fliplearning"),
        "grade_items_average_help_title" => get_string("tr_grade_items_average_help_title", "local_fliplearning"),
        "grade_items_average_help_description_p1" => get_string("tr_grade_items_average_help_description_p1", "local_fliplearning"),
        "grade_items_average_help_description_p2" => get_string("tr_grade_items_average_help_description_p2", "local_fliplearning"),
        "grade_items_average_help_description_p3" => get_string("tr_grade_items_average_help_description_p3", "local_fliplearning"),
        "item_grades_details_help_title" => get_string("tr_item_grades_details_help_title", "local_fliplearning"),
        "item_grades_details_help_description_p1" => get_string("tr_item_grades_details_help_description_p1", "local_fliplearning"),
        "item_grades_details_help_description_p2" => get_string("tr_item_grades_details_help_description_p2", "local_fliplearning"),
        "item_grades_distribution_help_title" => get_string("tr_item_grades_distribution_help_title", "local_fliplearning"),
        "item_grades_distribution_help_description_p1" => get_string("tr_item_grades_distribution_help_description_p1", "local_fliplearning"),
        "item_grades_distribution_help_description_p2" => get_string("tr_item_grades_distribution_help_description_p2", "local_fliplearning"),
        "item_grades_distribution_help_description_p3" => get_string("tr_item_grades_distribution_help_description_p3", "local_fliplearning"),

        "graded_students" => get_string("tr_graded_students", "local_fliplearning"),
        "of_conector" => get_string("fml_of_conector", "local_fliplearning"),

        "chart" => $reports->get_chart_langs(),
        "title" => get_string("menu_grades","local_fliplearning"),
        "no_data" => get_string("no_data", "local_fliplearning"),
        "ss_change_timezone" => get_string("ss_change_timezone", "local_fliplearning"),
        "graph_generating" => get_string("graph_generating", "local_fliplearning"),
        "api_error_network" => get_string("api_error_network", "local_fliplearning"),
        "helplabel" => get_string("helplabel", "local_fliplearning"),
        "exitbutton" => get_string("exitbutton", "local_fliplearning"),
        "about" => get_string("fml_about", "local_fliplearning"),

        "grades_select_label" => get_string("fml_grades_select_label", "local_fliplearning"),
        "grades_chart_title" => get_string("fml_grades_chart_title", 'local_fliplearning'),
        "grades_yaxis_title" => get_string("fml_grades_yaxis_title", 'local_fliplearning'),
        "grades_tooltip_average" => get_string("fml_grades_tooltip_average", 'local_fliplearning'),
        "grades_tooltip_grade" => get_string("fml_grades_tooltip_grade", 'local_fliplearning'),
        "grades_tooltip_student" => get_string("fml_grades_tooltip_student", 'local_fliplearning'),
        "grades_tooltip_students" => get_string("fml_grades_tooltip_students", 'local_fliplearning'),
        "grades_greater_than" => get_string("fml_grades_distribution_greater_than", 'local_fliplearning'),
        "grades_smaller_than" => get_string("fml_grades_distribution_smaller_than", 'local_fliplearning'),
        "grades_details_subtitle" => get_string("fml_grades_details_subtitle", 'local_fliplearning'),
        "grades_distribution_subtitle" => get_string("fml_grades_distribution_subtitle", 'local_fliplearning'),
        "grades_distribution_yaxis_title" => get_string("fml_grades_distribution_yaxis_title", 'local_fliplearning'),
        "grades_distribution_tooltip_prefix" => get_string("fml_grades_distribution_tooltip_prefix", 'local_fliplearning'),
        "grades_distribution_tooltip_suffix" => get_string("fml_grades_distribution_tooltip_suffix", 'local_fliplearning'),
        "student_text" => get_string("fml_student_text", "local_fliplearning"),
        "students_text" => get_string("fml_students_text", "local_fliplearning"),

        "email_strings" => array(
            "validation_subject_text" => get_string("fml_validation_subject_text","local_fliplearning"),
            "validation_message_text" => get_string("fml_validation_message_text","local_fliplearning"),
            "subject" => "",
            "subject_prefix" => $COURSE->fullname,
            "subject_label" => get_string("fml_subject_label","local_fliplearning"),
            "message_label" => get_string("fml_message_label","local_fliplearning"),

            "submit_button" => get_string("fml_submit_button","local_fliplearning"),
            "cancel_button" => get_string("fml_cancel_button","local_fliplearning"),
            "emailform_title" => get_string("fml_emailform_title","local_fliplearning"),
            "sending_text" => get_string("fml_sending_text","local_fliplearning"),
            "recipients_label" => get_string("fml_recipients_label","local_fliplearning"),
            "mailsended_text" => get_string("fml_mailsended_text","local_fliplearning"),
            "api_error_network" => get_string("api_error_network", "local_fliplearning"),
        ),

        "grade_item_details_categories" => array(
            get_string("fml_grades_best_grade","local_fliplearning"),
            get_string("fml_grades_average_grade","local_fliplearning"),
            get_string("fml_grades_worst_grade","local_fliplearning"),
        ),
        "grades_best_grade" => get_string("fml_grades_best_grade","local_fliplearning"),
        "grades_average_grade" => get_string("fml_grades_average_grade","local_fliplearning"),
        "grades_worst_grade" => get_string("fml_grades_worst_grade","local_fliplearning"),

        "grade_item_details_title" => "",
        "view_details" => get_string("fml_view_details", "local_fliplearning"),
        "send_mail" => get_string("fml_send_mail", "local_fliplearning"),

    ],
    'grade_items_average_colors' => array('#118AB2'),
    'item_grades_details_colors' => array('#06D6A0', '#FFD166', '#EF476F'),
    'item_grades_distribution_colors' => array('#118AB2', '#073B4C'),
    'courseid' => $COURSE->id,
    'userid' => $USER->id,
    'grades' => $reports->grade_items(),
    'profile_render' => $reports->render_has(),
    'groups' => local_fliplearning_get_groups($course, $USER),
    'timezone' => $reports->timezone,
];

$PAGE->requires->js_call_amd('local_fliplearning/grades', 'init', ['content' => $content]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/grades', ['content' => $content]);
echo $OUTPUT->footer();