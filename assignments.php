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

    ],
    'courseid' => $COURSE->id,
    'userid' => $USER->id,
    'assignments_submissions' => $reports->assignments_submissions(),
    'pages' => $configweeks->get_weeks_paginator(),
    'profile_render' => $reports->render_has(),
    'groups' => local_fliplearning_get_groups($course, $USER),
    'timezone' => $reports->timezone,
];

//$PAGE->requires->js_call_amd('local_fliplearning/assignments', 'init', ['content' => $content]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/assignments', ['content' => $content]);
echo $OUTPUT->footer();