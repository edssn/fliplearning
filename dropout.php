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
require_capability('local/fliplearning:dropout', $context);

\local_fliplearning\log::create("time", "view", $USER->id, $COURSE->id);

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

        "cluster_label" => get_string("fml_cluster_label", "local_fliplearning"),
        "thead_name" => get_string("thead_name", "local_fliplearning"),
        "thead_lastname" => get_string("thead_lastname", "local_fliplearning"),
        "thead_progress" => get_string("thead_progress", "local_fliplearning"),
        "table_title" => get_string("fml_dropout_table_title", "local_fliplearning"),
        "see_profile" => get_string("fml_dropout_see_profile", "local_fliplearning"),


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