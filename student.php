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
require_capability('local/fliplearning:student_general', $context);

if (is_siteadmin()) {
    print_error(get_string("fml_only_student", "local_fliplearning"));
}

\local_fliplearning\log::create("student_general", "view", $USER->id, $COURSE->id);
$reports = new \local_fliplearning\student($COURSE->id, $USER->id);

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if (!$configweeks->is_set()) {
    $message = get_string("fml_weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' => [
        "section_help_title" => get_string("sg_section_help_title", "local_fliplearning"),
        "section_help_description" => get_string("sg_section_help_description", "local_fliplearning"),
        "modules_access_help_title" => get_string("sg_modules_access_help_title", "local_fliplearning"),
        "modules_access_help_description_p1" => get_string("sg_modules_access_help_description_p1", "local_fliplearning"),
        "modules_access_help_description_p2" => get_string("sg_modules_access_help_description_p2", "local_fliplearning"),
        "modules_access_help_description_p3" => get_string("sg_modules_access_help_description_p3", "local_fliplearning"),
        "weeks_session_help_title" => get_string("sg_weeks_session_help_title", "local_fliplearning"),
        "weeks_session_help_description_p1" => get_string("sg_weeks_session_help_description_p1", "local_fliplearning"),
        "weeks_session_help_description_p2" => get_string("sg_weeks_session_help_description_p2", "local_fliplearning"),
        "sessions_evolution_help_title" => get_string("sg_sessions_evolution_help_title", "local_fliplearning"),
        "sessions_evolution_help_description_p1" => get_string("sg_sessions_evolution_help_description_p1", "local_fliplearning"),
        "sessions_evolution_help_description_p2" => get_string("sg_sessions_evolution_help_description_p2", "local_fliplearning"),
        "sessions_evolution_help_description_p3" => get_string("sg_sessions_evolution_help_description_p3", "local_fliplearning"),
        "user_grades_help_title" => get_string("sg_user_grades_help_title", "local_fliplearning"),
        "user_grades_help_description_p1" => get_string("sg_user_grades_help_description_p1", "local_fliplearning"),
        "user_grades_help_description_p2" => get_string("sg_user_grades_help_description_p2", "local_fliplearning"),
        "user_grades_help_description_p3" => get_string("sg_user_grades_help_description_p3", "local_fliplearning"),

        "finished_resources" => get_string("fml_finished_resources", "local_fliplearning"),

        "title" => get_string("menu_general", "local_fliplearning"),
        "chart" => $reports->get_chart_langs(),
        "change_timezone" => get_string("fml_change_timezone", "local_fliplearning"),
        "graph_generating" => get_string("fml_graph_generating", "local_fliplearning"),
        "api_error_network" => get_string("fml_api_error_network", "local_fliplearning"),
        "helplabel" => get_string("fml_helplabel", "local_fliplearning"),
        "exitbutton" => get_string("fml_exitbutton", "local_fliplearning"),
        "about" => get_string("fml_about", "local_fliplearning"),
        "weeks" => array(
            get_string("fml_week1", "local_fliplearning"),
            get_string("fml_week2", "local_fliplearning"),
            get_string("fml_week3", "local_fliplearning"),
            get_string("fml_week4", "local_fliplearning"),
            get_string("fml_week5", "local_fliplearning"),
            get_string("fml_week6", "local_fliplearning"),
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

        "student_progress_title" => get_string("td_student_progress_title", "local_fliplearning"),
        "see_profile" => get_string("td_dropout_see_profile", "local_fliplearning"),
        "module_label" => get_string("fml_module_label", "local_fliplearning"),
        "modules_label" => get_string("fml_modules_label", "local_fliplearning"),
        "of_conector" => get_string("fml_of_conector", "local_fliplearning"),
        "finished_label" => get_string("fml_finished_label", "local_fliplearning"),
        "finisheds_label" => get_string("fml_finisheds_label", "local_fliplearning"),
        "session_text" => get_string("fml_session_text", "local_fliplearning"),
        "sessions_text" => get_string("fml_sessions_text", "local_fliplearning"),
        "hours_short" => get_string("fml_hours_short", "local_fliplearning"),
        "minutes_short" => get_string("fml_minutes_short", "local_fliplearning"),
        "seconds_short" => get_string("fml_seconds_short", "local_fliplearning"),
        "inverted_time_title" => get_string("tg_progress_table_thead_time", "local_fliplearning"),
        "count_sessions_title" => get_string("tg_progress_table_thead_sessions", "local_fliplearning"),
        "student_grade_title" => get_string("td_student_grade_title", "local_fliplearning"),
        "modules_access_chart_title" => get_string("fml_modules_access_chart_title", "local_fliplearning"),
        "modules_amount" => get_string("fml_modules_amount", "local_fliplearning"),
        "modules_details" => get_string("fml_modules_details", "local_fliplearning"),
        "modules_interaction" => get_string("fml_modules_interaction", "local_fliplearning"),
        "modules_interactions" => get_string("fml_modules_interactions", "local_fliplearning"),
        "modules_viewed" => get_string("fml_modules_viewed", "local_fliplearning"),
        "modules_no_viewed" => get_string("fml_modules_no_viewed", "local_fliplearning"),
        "modules_complete" => get_string("fml_modules_complete", "local_fliplearning"),
        "close_button" => get_string("fml_close_button", "local_fliplearning"),
        "modules_access_chart_title" => get_string("fml_modules_access_chart_title", "local_fliplearning"),
        "modules_access_chart_series_total" => get_string("fml_modules_access_chart_series_total", "local_fliplearning"),
        "modules_access_chart_series_complete" => get_string("fml_modules_access_chart_series_complete", "local_fliplearning"),
        "modules_access_chart_series_viewed" => get_string("fml_modules_access_chart_series_viewed", "local_fliplearning"),
        "sessions_evolution_chart_title" => get_string("fml_sessions_evolution_chart_title", "local_fliplearning"),
        "sessions_evolution_chart_xaxis1" => get_string("fml_sessions_evolution_chart_xaxis1", "local_fliplearning"),
        "sessions_evolution_chart_xaxis2" => get_string("fml_sessions_evolution_chart_xaxis2", "local_fliplearning"),
        "sessions_evolution_chart_legend1" => get_string("fml_sessions_evolution_chart_legend1", "local_fliplearning"),
        "sessions_evolution_chart_legend2" => get_string("fml_sessions_evolution_chart_legend2", "local_fliplearning"),
        "user_grades_chart_title" => get_string("fml_user_grades_chart_title", "local_fliplearning"),
        "user_grades_chart_yaxis" => get_string("fml_user_grades_chart_yaxis", "local_fliplearning"),
        "user_grades_chart_xaxis" => get_string("fml_user_grades_chart_xaxis", "local_fliplearning"),
        "user_grades_chart_legend" => get_string("fml_user_grades_chart_legend", "local_fliplearning"),
        "user_grades_chart_tooltip_no_graded" => get_string("fml_user_grades_chart_tooltip_no_graded", "local_fliplearning"),
        "user_grades_chart_view_activity" => get_string("fml_user_grades_chart_view_activity", "local_fliplearning"),
        "weeks_sessions_title" => get_string("ts_weeks_sessions_chart_title", "local_fliplearning"),
    ],
    'weeks_session_colors' => array('#E0E0E0', '#118AB2'),
    'modules_access_colors' => array('#FFD166', '#06D6A0', '#118AB2'),
    'sessions_evolution_colors' => array('#118AB2', '#073B4C'),
    'user_grades_colors' => array('#118AB2', '#073B4C'),
    'courseid' => $COURSE->id,
    'userid' => $USER->id,
    'indicators' => $reports->get_general_indicators(),
    'profile_render' => $reports->render_has(),
    'timezone' => $reports->timezone,
];

$PAGE->requires->js_call_amd('local_fliplearning/student', 'init', ['content' => $content]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/student', ['content' => $content]);
echo $OUTPUT->footer();