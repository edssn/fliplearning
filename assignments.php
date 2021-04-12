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

$url = '/local/fliplearning/assignments.php';
local_fliplearning_set_page($course, $url);

require_capability('local/fliplearning:usepluggin', $context);
require_capability('local/fliplearning:view_as_teacher', $context);
require_capability('local/fliplearning:assignments', $context);

\local_fliplearning\log::create("assignments", "view", $USER->id, $COURSE->id);
$reports = new \local_fliplearning\teacher($COURSE->id, $USER->id);

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if (!$configweeks->is_set()) {
    $message = get_string("fml_weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' => [
        "section_help_title" => get_string("ta_section_help_title", "local_fliplearning"),
        "section_help_description" => get_string("ta_section_help_description", "local_fliplearning"),
        "assigns_submissions_help_title" => get_string("ta_assigns_submissions_help_title", "local_fliplearning"),
        "assigns_submissions_help_description_p1" => get_string("ta_assigns_submissions_help_description_p1", "local_fliplearning"),
        "assigns_submissions_help_description_p2" => get_string("ta_assigns_submissions_help_description_p2", "local_fliplearning"),
        "access_content_help_title" => get_string("ta_access_content_help_title", "local_fliplearning"),
        "access_content_help_description_p1" => get_string("ta_access_content_help_description_p1", "local_fliplearning"),
        "access_content_help_description_p2" => get_string("ta_access_content_help_description_p2", "local_fliplearning"),

        "title" => get_string("menu_assignments","local_fliplearning"),
        "chart" => $reports->get_chart_langs(),
        "change_timezone" => get_string("fml_change_timezone", "local_fliplearning"),
        "graph_generating" => get_string("fml_graph_generating", "local_fliplearning"),
        "api_error_network" => get_string("fml_api_error_network", "local_fliplearning"),
        "pagination" => get_string("fml_pagination", "local_fliplearning"),
        "pagination_title" => get_string("fml_pagination_title","local_fliplearning"),
        "pagination_separator" => get_string("fml_pagination_component_to","local_fliplearning"),
        "pagination_name" => get_string("fml_pagination_component_name","local_fliplearning"),
        "helplabel" => get_string("fml_helplabel","local_fliplearning"),
        "exitbutton" => get_string("fml_exitbutton","local_fliplearning"),
        "about" => get_string("fml_about", "local_fliplearning"),
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
            "api_error_network" => get_string("fml_api_error_network", "local_fliplearning"),
        ),

        "access" => get_string("ta_assigns_submissions_xaxis_access", "local_fliplearning"),
        "no_access" => get_string("ta_assigns_submissions_xaxis_no_access", "local_fliplearning"),
        "access_chart_title" => get_string("ta_access_content_chart_title", "local_fliplearning"),
        "access_chart_yaxis_label" => get_string("ta_access_content_yaxis_title", "local_fliplearning"),
        "access_chart_suffix" => get_string("ta_access_content_tooltip_suffix", "local_fliplearning"),
        "send_mail" => get_string("fml_send_mail", "local_fliplearning"),
        "student_text" => get_string("fml_student_text", "local_fliplearning"),
        "students_text" => get_string("fml_students_text", "local_fliplearning"),

        "assignsubs_chart_title" => get_string("ta_assigns_submissions_title", "local_fliplearning"),
        "assignsubs_chart_yaxis" => get_string("ta_assigns_submissions_yaxis", "local_fliplearning"),
    ],
    'assigns_submissions_colors' => array('#06D6A0', '#FFD166', '#EF476F'),
    'access_content_colors' => array('#06D6A0', '#EF476F'),
    'courseid' => $COURSE->id,
    'userid' => $USER->id,
    'submissions' => $reports->assignments_submissions(),
    'access' => $reports->resources_access(),
    'pages' => $configweeks->get_weeks_paginator(),
    'profile_render' => $reports->render_has(),
    'groups' => local_fliplearning_get_groups($course, $USER),
    'timezone' => $reports->timezone,
];

$PAGE->requires->js_call_amd('local_fliplearning/assignments', 'init', ['content' => $content]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/assignments', ['content' => $content]);
echo $OUTPUT->footer();