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
global $DB, $COURSE, $USER, $PAGE, $OUTPUT;

$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

$url = '/local/fliplearning/logs.php';
local_fliplearning_set_page($course, $url);

require_capability('local/fliplearning:usepluggin', $context);
require_capability('local/fliplearning:view_as_teacher', $context);
require_capability('local/fliplearning:logs', $context);

// Guardar log
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https"
        : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
\local_fliplearning\logs::create(
    "teacher_download_logs",
    "teacher_download_logs",
    "viewed",
    "section",
    $url,
    2,
    $USER->id,
    $COURSE->id
);

$reports = new \local_fliplearning\teacher($COURSE->id, $USER->id);

$configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
if(!$configweeks->is_set()){
    $message = get_string("fml_weeks_not_config", "local_fliplearning");
    print_error($message);
}

$content = [
    'strings' =>[
        "title" => get_string("fml_menu_logs", "local_fliplearning"),
        "sectionHelpTitle" => get_string("tl_section_help_title", "local_fliplearning"),
        "sectionHelpDescription" => get_string("tl_section_help_description", "local_fliplearning"),

        "moodleLogsHelpTitle" => get_string("tl_moodle_logs_help_title", "local_fliplearning"),
        "moodleLogsHelpDescription" => get_string("tl_moodle_logs_help_description", "local_fliplearning"),
        "moodleLogsAbout" => get_string("tl_moodle_logs_about", "local_fliplearning"),
        "fliplearningLogsHelpTitle" => get_string("tl_fliplearning_logs_help_title", "local_fliplearning"),
        "fliplearningLogsHelpDescription" => get_string("tl_fliplearning_logs_help_description", "local_fliplearning"),
        "fliplearningLogsAbout" => get_string("tl_fliplearning_logs_about", "local_fliplearning"),

        "logsHelpTableHeaderColumn" => get_string("tl_logs_help_table_header_column", "local_fliplearning"),
        "logsHelpTableHeaderDescription" => get_string("tl_logs_help_table_header_description", "local_fliplearning"),
        "logsDatesRange" => get_string("tl_logs_dates_range", "local_fliplearning"),
        "logsSuccessDownload" => get_string("tl_logs_success_download", "local_fliplearning"),
        "logsWithoutDatesValidation" => get_string("tl_logs_without_dates_validation", "local_fliplearning"),

        "logsHeaderLogid" => get_string("tl_logs_header_logid", "local_fliplearning"),
        "logsHeaderUserId" => get_string("tl_logs_header_userid", "local_fliplearning"),
        "logsHeaderUsername" => get_string("tl_logs_header_username", "local_fliplearning"),
        "logsHeaderFirstname" => get_string("tl_logs_header_firstname", "local_fliplearning"),
        "logsHeaderLastname" => get_string("tl_logs_header_lastname", "local_fliplearning"),
        "logsHeaderRoles" => get_string("tl_logs_header_roles", "local_fliplearning"),
        "logsHeaderCourseId" => get_string("tl_logs_header_courseid", "local_fliplearning"),
        "logsHeaderCoursename" => get_string("tl_logs_header_coursename", "local_fliplearning"),
        "logsHeaderContextLevel" => get_string("tl_logs_header_contextlevel", "local_fliplearning"),
        "logsHeaderComponent" => get_string("tl_logs_header_component", "local_fliplearning"),
        "logsHeaderAction" => get_string("tl_logs_header_action", "local_fliplearning"),
        "logsHeaderTarget" => get_string("tl_logs_header_target", "local_fliplearning"),
        "logsHeaderActivitytype" => get_string("tl_logs_header_activitytype", "local_fliplearning"),
        "logsHeaderActivityname" => get_string("tl_logs_header_activityname", "local_fliplearning"),
        "logsHeaderSectionnumber" => get_string("tl_logs_header_sectionnumber", "local_fliplearning"),
        "logsHeaderSectionname" => get_string("tl_logs_header_sectionname", "local_fliplearning"),
        "logsHeaderTimecreated" => get_string("tl_logs_header_timecreated", "local_fliplearning"),

        "logsHeaderPluginsection" => get_string("tl_logs_header_pluginsection", "local_fliplearning"),
        "logsHeaderUrl" => get_string("tl_logs_header_url", "local_fliplearning"),
        "logsHeaderDescription" => get_string("tl_logs_header_description", "local_fliplearning"),

        "logsHeaderLogIdHelpDescription" => get_string("tl_logs_header_logid_help_description", "local_fliplearning"),
        "logsHeaderUserIdHelpDescription" => get_string("tl_logs_header_userid_help_description", "local_fliplearning"),
        "logsHeaderUsernameHelpDescription" => get_string("tl_logs_header_username_help_description", "local_fliplearning"),
        "logsHeaderFirstnameHelpDescription" => get_string("tl_logs_header_firstname_help_description", "local_fliplearning"),
        "logsHeaderLastnameHelpDescription" => get_string("tl_logs_header_lastname_help_description", "local_fliplearning"),
        "logsHeaderRolesHelpDescription" => get_string("tl_logs_header_roles_help_description", "local_fliplearning"),
        "logsHeaderCourseIdHelpDescription" => get_string("tl_logs_header_courseid_help_description", "local_fliplearning"),
        "logsHeaderCoursenameHelpDescription" => get_string("tl_logs_header_coursename_help_description", "local_fliplearning"),
        "logsHeaderContextLevelHelpDescription" => get_string("tl_logs_header_contextlevel_help_description", "local_fliplearning"),
        "logsHeaderComponentHelpDescription" => get_string("tl_logs_header_component_help_description", "local_fliplearning"),
        "logsHeaderActionHelpDescription" => get_string("tl_logs_header_action_help_description", "local_fliplearning"),
        "logsHeaderTargetHelpDescription" => get_string("tl_logs_header_target_help_description", "local_fliplearning"),
        "logsHeaderActivitytypeHelpDescription" => get_string("tl_logs_header_activitytype_help_description", "local_fliplearning"),
        "logsHeaderActivitynameHelpDescription" => get_string("tl_logs_header_activityname_help_description", "local_fliplearning"),
        "logsHeaderSectionnumberHelpDescription" => get_string("tl_logs_header_sectionnumber_help_description", "local_fliplearning"),
        "logsHeaderSectionnameHelpDescription" => get_string("tl_logs_header_sectionname_help_description", "local_fliplearning"),
        "logsHeaderTimecreatedHelpDescription" => get_string("tl_logs_header_timecreated_help_description", "local_fliplearning"),

        "logsHeaderPluginsectionHelpDescription" => get_string("tl_logs_header_pluginsection_help_description", "local_fliplearning"),
        "logsHeaderUrlHelpDescription" => get_string("tl_logs_header_url_help_description", "local_fliplearning"),
        "logsHeaderDescriptionHelpDescription" => get_string("tl_logs_header_description_help_description", "local_fliplearning"),

        "changeTimezone" => get_string("fml_change_timezone", "local_fliplearning"),
        "about" => get_string("fml_about", "local_fliplearning"),
        "graphGenerating" => get_string("fml_graph_generating", "local_fliplearning"),
        "apiErrorNetwork" => get_string("fml_api_error_network", "local_fliplearning"),
        "helpLabel" => get_string("fml_helplabel","local_fliplearning"),
        "exitButton" => get_string("fml_exitbutton","local_fliplearning"),
    ],
    'courseId' => $COURSE->id,
    'userId' => $USER->id,
    'profileRender' => $reports->render_has(),
    'timezone' => $reports->timezone,
];

$PAGE->requires->js_call_amd('local_fliplearning/logs','init', ['content' => $content]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/logs', ['content' => $content]);
echo $OUTPUT->footer();