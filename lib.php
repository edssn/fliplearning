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
 * @autor       Edisson Sigua, Bryan Aguilar
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/local/fliplearning/locallib.php');

function local_fliplearning_extend_navigation($root) {
    $use_navbar = get_config("local_fliplearning", "use_navbar_menu");
    if($use_navbar){
        return;
    }

    global $CFG, $COURSE, $PAGE, $SESSION, $SITE, $USER;
    $items = [];

    if (isset($COURSE) && $COURSE->id <= 1 ) {
        return null;
    }

    $context = context_course::instance($COURSE->id);

    $configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
    $configuration_is_set = $configweeks->is_set();

    if(!has_capability('local/fliplearning:usepluggin', $context)){
        return null;
    }

    $hidden_for_student = !$configuration_is_set && !is_siteadmin();
    if(has_capability('local/fliplearning:view_as_student', $context) && $hidden_for_student){
        return null;
    }

    $main_title = get_string('pluginname', 'local_fliplearning');
    $node = navigation_node::create($main_title, null, navigation_node::TYPE_COURSE);
    $child = $root->add_node($node, 'mycourses');
    $child->add_class('mail_root');

    if(has_capability('local/fliplearning:setweeks', $context)){
        $text = get_string('fml_menu_setweek', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/setweeks.php', array('courseid'=>$COURSE->id));
        $child = $node->add(s($text), $url);
    }

    if(has_capability('local/fliplearning:teacher_general', $context) && $configuration_is_set){
        $text = get_string('fml_menu_general', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/teacher.php', array('courseid'=>$COURSE->id));
        $child = $node->add(s($text), $url);
    }

    if(has_capability('local/fliplearning:teacher_sessions', $context) && $configuration_is_set){
        $text = get_string('fml_menu_sessions', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/sessions.php', array('courseid'=>$COURSE->id));
        $child = $node->add(s($text), $url);
    }

    if(has_capability('local/fliplearning:assignments', $context) && $configuration_is_set){
        $text = get_string('fml_menu_assignments', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/assignments.php', array('courseid'=>$COURSE->id));
        $child = $node->add(s($text), $url);
    }

    if(has_capability('local/fliplearning:grades', $context) && $configuration_is_set){
        $text = get_string('fml_menu_grades', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/grades.php', array('courseid'=>$COURSE->id));
        $child = $node->add(s($text), $url);
    }

    if(has_capability('local/fliplearning:quiz', $context) && $configuration_is_set){
        $text = get_string('fml_menu_quiz', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/quiz.php', array('courseid'=>$COURSE->id));
        $child = $node->add(s($text), $url);
    }

    if(has_capability('local/fliplearning:dropout', $context) && $configuration_is_set){
        $text = get_string('fml_menu_dropout', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/dropout.php', array('courseid'=>$COURSE->id));
        $child = $node->add(s($text), $url);
    }


    if(has_capability('local/fliplearning:student_general', $context) && !is_siteadmin() && $configuration_is_set){
        $text = get_string('fml_menu_general', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/student.php', array('courseid'=>$COURSE->id));
        $child = $node->add(s($text), $url);
    }

    if(has_capability('local/fliplearning:student_sessions', $context) && !is_siteadmin() && $configuration_is_set){
        $text = get_string('fml_menu_sessions', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/student_sessions.php', array('courseid'=>$COURSE->id));
        $child = $node->add(s($text), $url);
    }
}

function local_fliplearning_render_navbar_output(\renderer_base $renderer) {
    $use_navbar = get_config("local_fliplearning", "use_navbar_menu");
    if(!$use_navbar){
        return;
    }

    global $CFG, $COURSE, $PAGE, $SESSION, $SITE, $USER;
    $items = [];

    if (isset($COURSE) && $COURSE->id <= 1 ) {
        return null;
    }

    $context = context_course::instance($COURSE->id);

    $configweeks = new \local_fliplearning\configweeks($COURSE, $USER);
    $configuration_is_set = $configweeks->is_set();

    if(!has_capability('local/fliplearning:usepluggin', $context)){
        return null;
    }

    $hidden_for_student = !$configuration_is_set && !is_siteadmin();
    if(has_capability('local/fliplearning:view_as_student', $context) && $hidden_for_student){
        return null;
    }

    if(has_capability('local/fliplearning:setweeks', $context)){
        $text = get_string('fml_menu_setweek', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/setweeks.php', array('courseid'=>$COURSE->id));
        array_push($items, local_fliplearning_new_menu_item(s($text), $url));
    }

    if(has_capability('local/fliplearning:teacher_general', $context) && $configuration_is_set){
        $text = get_string('fml_menu_general', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/teacher.php', array('courseid'=>$COURSE->id));
        array_push($items, local_fliplearning_new_menu_item(s($text), $url));
    }

    if(has_capability('local/fliplearning:teacher_sessions', $context) && $configuration_is_set){
        $text = get_string('fml_menu_sessions', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/sessions.php', array('courseid'=>$COURSE->id));
        array_push($items, local_fliplearning_new_menu_item(s($text), $url));
    }

    if(has_capability('local/fliplearning:assignments', $context) && $configuration_is_set){
        $text = get_string('fml_menu_assignments', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/assignments.php', array('courseid'=>$COURSE->id));
        array_push($items, local_fliplearning_new_menu_item(s($text), $url));
    }

    if(has_capability('local/fliplearning:grades', $context) && $configuration_is_set){
        $text = get_string('fml_menu_grades', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/grades.php', array('courseid'=>$COURSE->id));
        array_push($items, local_fliplearning_new_menu_item(s($text), $url));
    }

    if(has_capability('local/fliplearning:quiz', $context) && $configuration_is_set){
        $text = get_string('fml_menu_quiz', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/quiz.php', array('courseid'=>$COURSE->id));
        array_push($items, local_fliplearning_new_menu_item(s($text), $url));
    }

    if(has_capability('local/fliplearning:dropout', $context) && $configuration_is_set){
        $text = get_string('fml_menu_dropout', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/dropout.php', array('courseid'=>$COURSE->id));
        array_push($items, local_fliplearning_new_menu_item(s($text), $url));
    }


    if(has_capability('local/fliplearning:student_general', $context) && !is_siteadmin() && $configuration_is_set){
        $text = get_string('fml_menu_general', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/student.php', array('courseid'=>$COURSE->id));
        array_push($items, local_fliplearning_new_menu_item(s($text), $url));
    }

    if(has_capability('local/fliplearning:student_sessions', $context) && !is_siteadmin() && $configuration_is_set){
        $text = get_string('fml_menu_sessions', 'local_fliplearning');
        $url = new moodle_url('/local/fliplearning/student_sessions.php', array('courseid'=>$COURSE->id));
        array_push($items, local_fliplearning_new_menu_item(s($text), $url));
    }

    $params = [
        "title" => get_string('pluginname', 'local_fliplearning'),
        "items" => $items];
    return $renderer->render_from_template('local_fliplearning/navbar_popover', $params);
}

function local_fliplearning_get_fontawesome_icon_map() {
    return [
        'local_fliplearning:icon' => 'fa-pie-chart',
    ];
}