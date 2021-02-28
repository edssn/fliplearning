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

$url = '/local/fliplearning/dropout.php';
local_fliplearning_set_page($course, $url);

require_capability('local/fliplearning:usepluggin', $context);
require_capability('local/fliplearning:view_as_teacher', $context);
require_capability('local/fliplearning:dropout', $context);

\local_fliplearning\log::create("dropout", "view", $USER->id, $COURSE->id);
$reports = new \local_fliplearning\teacher($COURSE->id, $USER->id);

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if (!$configweeks->is_set()) {
    $message = get_string("weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' => [
        "chart" => $reports->get_chart_langs(),
        "title" => get_string("menu_dropout","local_fliplearning"),
        "no_data" => get_string("no_data", "local_fliplearning"),
        "pagination" => get_string("pagination", "local_fliplearning"),
        "ss_change_timezone" => get_string("ss_change_timezone", "local_fliplearning"),
        "graph_generating" => get_string("graph_generating", "local_fliplearning"),
        "api_error_network" => get_string("api_error_network", "local_fliplearning"),
        "pagination_name" => get_string("pagination_component_name", "local_fliplearning"),
        "pagination_separator" => get_string("pagination_component_to", "local_fliplearning"),
        "pagination_title" => get_string("pagination_title", "local_fliplearning"),
        "helplabel" => get_string("helplabel", "local_fliplearning"),
        "exitbutton" => get_string("exitbutton", "local_fliplearning"),
        "dropout_no_data" => get_string("fml_dropout_no_data", "local_fliplearning"),
        "dropout_no_users_cluster" => get_string("fml_dropout_no_users_cluster", "local_fliplearning"),
        "generate_data_manually" => get_string("fml_dropout_generate_data_manually", "local_fliplearning"),
        "generating_data" => get_string("fml_dropout_generating_data", "local_fliplearning"),
        "about" => get_string("fml_about", "local_fliplearning"),

        "cluster_label" => get_string("fml_cluster_label", "local_fliplearning"),
        "cluster_select" => get_string("fml_cluster_select", "local_fliplearning"),
        "thead_name" => get_string("thead_name", "local_fliplearning"),
        "thead_lastname" => get_string("thead_lastname", "local_fliplearning"),
        "thead_progress" => get_string("thead_progress", "local_fliplearning"),
        "table_title" => get_string("fml_dropout_table_title", "local_fliplearning"),
        "see_profile" => get_string("fml_dropout_see_profile", "local_fliplearning"),
        "send_mail_to_user" => get_string("fml_send_mail_to_user", "local_fliplearning"),
        "send_mail_to_group" => get_string("fml_send_mail_to_group", "local_fliplearning"),
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
        "modules_strings" => array(
            "title" => get_string("fml_modules_access_chart_title","local_fliplearning"),
            "modules_no_viewed" => get_string("fml_modules_no_viewed","local_fliplearning"),
            "modules_viewed" => get_string("fml_modules_viewed","local_fliplearning"),
            "modules_complete" => get_string("fml_modules_complete","local_fliplearning"),
            "close_button" => get_string("fml_close_button","local_fliplearning"),
            "modules_interaction" => get_string("fml_modules_interaction","local_fliplearning"),
            "modules_interactions" => get_string("fml_modules_interactions","local_fliplearning"),
        ),

        "student_progress_title" => get_string("fml_dropout_student_progress_title", "local_fliplearning"),
        "module_label" => get_string("fml_module_label", "local_fliplearning"),
        "modules_label" => get_string("fml_modules_label", "local_fliplearning"),
        "of_conector" => get_string("fml_of_conector", "local_fliplearning"),
        "finished_label" => get_string("fml_finished_label", "local_fliplearning"),
        "finisheds_label" => get_string("fml_finisheds_label", "local_fliplearning"),
        "inverted_time_title" => get_string("thead_time", "local_fliplearning"),
        "count_sessions_title" => get_string("thead_sessions", "local_fliplearning"),
        "student_grade_title" => get_string("fml_dropout_student_grade_title", "local_fliplearning"),
        "modules_access_chart_title" => get_string("fml_modules_access_chart_title", "local_fliplearning"),
        "modules_access_chart_series_total" => get_string("fml_modules_access_chart_series_total", "local_fliplearning"),
        "modules_access_chart_series_complete" => get_string("fml_modules_access_chart_series_complete", "local_fliplearning"),
        "modules_access_chart_series_viewed" => get_string("fml_modules_access_chart_series_viewed", "local_fliplearning"),
        "week_modules_chart_title" => get_string("fml_week_modules_chart_title", "local_fliplearning"),
        "modules_amount" => get_string("fml_modules_amount", "local_fliplearning"),
        "modules_details" => get_string("fml_modules_details", "local_fliplearning"),
//        "modules_interaction" => get_string("fml_modules_interaction", "local_fliplearning"),
//        "modules_interactions" => get_string("fml_modules_interactions", "local_fliplearning"),
//        "modules_viewed" => get_string("fml_modules_viewed", "local_fliplearning"),
//        "modules_no_viewed" => get_string("fml_modules_no_viewed", "local_fliplearning"),
//        "modules_complete" => get_string("fml_modules_complete", "local_fliplearning"),
//        "close_button" => get_string("fml_close_button", "local_fliplearning"),
        "sessions_evolution_chart_title" => get_string("fml_sessions_evolution_chart_title", "local_fliplearning"),
        "sessions_evolution_chart_xaxis1" => get_string("fml_sessions_evolution_chart_xaxis1", "local_fliplearning"),
        "sessions_evolution_chart_xaxis2" => get_string("fml_sessions_evolution_chart_xaxis2", "local_fliplearning"),
        "sessions_evolution_chart_legend1" => get_string("fml_sessions_evolution_chart_legend1", "local_fliplearning"),
        "sessions_evolution_chart_legend2" => get_string("fml_sessions_evolution_chart_legend2", "local_fliplearning"),
        "session_text" => get_string("fml_session_text", "local_fliplearning"),
        "sessions_text" => get_string("fml_sessions_text", "local_fliplearning"),
        "hours_short" => get_string("fml_hours_short", "local_fliplearning"),
        "minutes_short" => get_string("fml_minutes_short", "local_fliplearning"),
        "seconds_short" => get_string("fml_seconds_short", "local_fliplearning"),
        "user_grades_chart_title" => get_string("fml_user_grades_chart_title", "local_fliplearning"),
        "user_grades_chart_yaxis" => get_string("fml_user_grades_chart_yaxis", "local_fliplearning"),
        "user_grades_chart_xaxis" => get_string("fml_user_grades_chart_xaxis", "local_fliplearning"),
        "user_grades_chart_legend" => get_string("fml_user_grades_chart_legend", "local_fliplearning"),
        "user_grades_chart_tooltip_no_graded" => get_string("fml_user_grades_chart_tooltip_no_graded", "local_fliplearning"),
        "user_grades_chart_view_activity" => get_string("fml_user_grades_chart_view_activity", "local_fliplearning"),

    ],
    'courseid' => $COURSE->id,
    'userid' => $USER->id,
    'dropout' => $reports->get_dropout_clusters(),
    'profile_render' => $reports->render_has(),
    'groups' => local_fliplearning_get_groups($course, $USER),
    'timezone' => $reports->timezone,
];

$PAGE->requires->js_call_amd('local_fliplearning/dropout', 'init', ['content' => $content]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/dropout', ['content' => $content]);
echo $OUTPUT->footer();