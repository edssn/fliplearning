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

$url = '/local/fliplearning/prueba.php';
local_fliplearning_set_page($course, $url);


//$samples = [[1, 3], [1, 4], [2, 4], [3, 1], [4, 1], [4, 2]];
//$labels = ['a', 'a', 'a', 'b', 'b', 'b'];
//$classifier = new \local_fliplearning\phpml\Classification\KNearestNeighbors();
//$classifier->train($samples, $labels);


$classifier = new \local_fliplearning\dropout($COURSE->id, $USER->id);
$classifier->generate_data();



echo $OUTPUT->header();
//echo 'hola';
//echo $classifier->predict([3, 2]);
echo $classifier->hello();
echo $OUTPUT->footer();