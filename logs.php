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

        "changeTimezone" => get_string("fml_change_timezone", "local_fliplearning"),
        "about" => get_string("fml_about", "local_fliplearning"),
        "graphGenerating" => get_string("fml_graph_generating", "local_fliplearning"),
        "apiErrorNetwork" => get_string("fml_api_error_network", "local_fliplearning"),
        "helpLabel" => get_string("fml_helplabel","local_fliplearning"),
        "exitButton" => get_string("fml_exitbutton","local_fliplearning"),
        "successDownload" => "Descarga completa",
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