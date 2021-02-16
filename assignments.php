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
require_capability('local/fliplearning:assignments', $context);

\local_fliplearning\log::create("assignments", "view", $USER->id, $COURSE->id);

if (has_capability('local/fliplearning:view_as_teacher', $context)) {
    $reports = new \local_fliplearning\teacher($COURSE->id, $USER->id);
} else {
    $reports = new \local_fliplearning\student($COURSE->id, $USER->id);
}

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if (!$configweeks->is_set()) {
    $message = get_string("weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' => [
        "title" => get_string("menu_assignments","local_fliplearning"),
        "chart" => array(
            "loading" => get_string("chart_loading", "local_fliplearning"),
            "exportButtonTitle" => get_string("chart_exportButtonTitle", "local_fliplearning"),
            "printButtonTitle" => get_string("chart_printButtonTitle", "local_fliplearning"),
            "rangeSelectorFrom" => get_string("chart_rangeSelectorFrom", "local_fliplearning"),
            "rangeSelectorTo" => get_string("chart_rangeSelectorTo", "local_fliplearning"),
            "rangeSelectorZoom" => get_string("chart_rangeSelectorZoom", "local_fliplearning"),
            "downloadPNG" => get_string("chart_downloadPNG", "local_fliplearning"),
            "downloadJPEG" => get_string("chart_downloadJPEG", "local_fliplearning"),
            "downloadPDF" => get_string("chart_downloadPDF", "local_fliplearning"),
            "downloadSVG" => get_string("chart_downloadSVG", "local_fliplearning"),
            "downloadCSV" => get_string("chart_downloadCSV", "local_fliplearning"),
            "downloadXLS" => get_string("chart_downloadXLS", "local_fliplearning"),
            "exitFullscreen" => get_string("chart_exitFullscreen", "local_fliplearning"),
            "hideData" => get_string("chart_hideData", "local_fliplearning"),
            "noData" => get_string("chart_noData", "local_fliplearning"),
            "printChart" => get_string("chart_printChart", "local_fliplearning"),
            "viewData" => get_string("chart_viewData", "local_fliplearning"),
            "viewFullscreen" => get_string("chart_viewFullscreen", "local_fliplearning"),
            "resetZoom" => get_string("chart_resetZoom", "local_fliplearning"),
            "resetZoomTitle" => get_string("chart_resetZoomTitle", "local_fliplearning"),
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
            "shortMonths" => array(
                get_string("fml_jan_short", "local_fliplearning"),
                get_string("fml_feb_short", "local_fliplearning"),
                get_string("fml_mar_short", "local_fliplearning"),
                get_string("fml_apr_short", "local_fliplearning"),
                get_string("fml_may_short", "local_fliplearning"),
                get_string("fml_jun_short", "local_fliplearning"),
                get_string("fml_jul_short", "local_fliplearning"),
                get_string("fml_aug_short", "local_fliplearning"),
                get_string("fml_sep_short", "local_fliplearning"),
                get_string("fml_oct_short", "local_fliplearning"),
                get_string("fml_nov_short", "local_fliplearning"),
                get_string("fml_dec_short", "local_fliplearning"),
            ),
            "weekdays" => array(
                get_string("fml_sun", "local_fliplearning"),
                get_string("fml_mon", "local_fliplearning"),
                get_string("fml_tue", "local_fliplearning"),
                get_string("fml_wed", "local_fliplearning"),
                get_string("fml_thu", "local_fliplearning"),
                get_string("fml_fri", "local_fliplearning"),
                get_string("fml_sat", "local_fliplearning"),
            ),
            "shortWeekdays" => array(
                get_string("fml_sun_short", "local_fliplearning"),
                get_string("fml_mon_short", "local_fliplearning"),
                get_string("fml_tue_short", "local_fliplearning"),
                get_string("fml_wed_short", "local_fliplearning"),
                get_string("fml_thu_short", "local_fliplearning"),
                get_string("fml_fri_short", "local_fliplearning"),
                get_string("fml_sat_short", "local_fliplearning"),
            ),
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

        "access" => get_string("fml_access", "local_fliplearning"),
        "no_access" => get_string("fml_no_access", "local_fliplearning"),
        "access_chart_title" => get_string("fml_access_chart_title", "local_fliplearning"),
        "access_chart_yaxis_label" => get_string("fml_access_chart_yaxis_label", "local_fliplearning"),
        "access_chart_suffix" => get_string("fml_access_chart_suffix", "local_fliplearning"),
        "send_mail" => get_string("fml_send_mail", "local_fliplearning"),
        "student_text" => get_string("fml_student_text", "local_fliplearning"),
        "students_text" => get_string("fml_students_text", "local_fliplearning"),

        "no_data" => get_string("no_data", "local_fliplearning"),
        "assignsubs_chart_title" => get_string("fml_assignsubs_title", "local_fliplearning"),
        "assignsubs_chart_yaxis" => get_string("fml_assignsubs_yaxis", "local_fliplearning"),
    ],
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