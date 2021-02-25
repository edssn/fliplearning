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
 * Ajax Script
 *
 * @package     local_fliplearning
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @author      Edisson Sigua, Bryan Aguilar
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('AJAX_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');

global $USER, $COURSE, $DB;

$userid = required_param('userid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

$COURSE = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$USER = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

require_login($COURSE, false);
$context = context_course::instance($courseid);
require_capability('local/fliplearning:ajax', $context);


$action = optional_param('action', false ,PARAM_ALPHA);
$weeks = optional_param('weeks', false, PARAM_RAW);
$profile = optional_param('profile', false, PARAM_RAW);
$weekcode = optional_param('weekcode', false, PARAM_INT);
$groupid = optional_param('groupid',  null,  PARAM_INT);

$subject = optional_param('subject', false ,PARAM_TEXT);
$recipients = optional_param('recipients', false ,PARAM_TEXT);
$text = optional_param('text', false ,PARAM_TEXT);
$moduleid = optional_param('moduleid', false ,PARAM_INT);
$modulename = optional_param('modulename', false ,PARAM_TEXT);

$newinstance = optional_param('newinstance', false, PARAM_BOOL);

$params = array();
$func = null;

if($action == 'saveconfigweek') {
    array_push($params, $weeks);
    array_push($params, $courseid);
    array_push($params, $userid);
    array_push($params, $newinstance);
    if($weeks && $courseid && $userid){
        $func = "local_fliplearning_save_weeks_config";
    }
} elseif ($action == 'changegroup') {
    array_push($params, $courseid);
    array_push($params, $userid);
    array_push($params, $groupid);
    if($courseid && $userid){
        $func = "local_fliplearning_change_group";
    }
} elseif($action == 'worksessions') {
    array_push($params, $weekcode);
    array_push($params, $courseid);
    array_push($params, $userid);
    array_push($params, $profile);
    if($weekcode && $courseid && $userid && $profile){
        $func = "local_fliplearning_get_work_sessions";
    }
} elseif($action == 'time') {
    array_push($params, $weekcode);
    array_push($params, $courseid);
    array_push($params, $userid);
    array_push($params, $profile);
    if($weekcode && $courseid && $userid && $profile){
        $func = "local_fliplearning_get_inverted_time";
    }
} elseif($action == 'assignments') {
    array_push($params, $weekcode);
    array_push($params, $courseid);
    array_push($params, $userid);
    array_push($params, $profile);
    if($weekcode && $courseid && $userid && $profile){
        $func = "local_fliplearning_get_assignments_submissions";
    }
} elseif($action == 'sendmail') {
    array_push($params, $COURSE);
    array_push($params, $USER);
    array_push($params, $subject);
    array_push($params, $recipients);
    array_push($params, $text);
    array_push($params, $moduleid);
    array_push($params, $modulename);
    if($subject && $recipients && $text){
        $func = "local_fliplearning_send_email";
    }
} elseif($action == 'quiz') {
    array_push($params, $weekcode);
    array_push($params, $courseid);
    array_push($params, $userid);
    array_push($params, $profile);
    if($weekcode && $courseid && $userid && $profile){
        $func = "local_fliplearning_get_quiz_attempts";
    }
} elseif($action == 'dropoutdata') {
    array_push($params, $courseid);
    array_push($params, $userid);
    array_push($params, $profile);
    if($courseid && $userid && $profile){
        $func = "local_fliplearning_generate_dropout_data";
    }
} elseif($action == 'studentsessions') {
    array_push($params, $weekcode);
    array_push($params, $courseid);
    array_push($params, $userid);
    array_push($params, $profile);
    if($weekcode && $courseid && $userid && $profile){
        $func = "local_fliplearning_get_student_sessions";
    }
}



if(isset($params) && isset($func)){
    call_user_func_array($func, $params);
}else{
    $message = get_string('api_invalid_data', 'local_fliplearning');
    local_fliplearning_ajax_response(array($message), 400);
}

function local_fliplearning_save_weeks_config($weeks, $courseid, $userid, $newinstance){
    \local_fliplearning\log::create("setweeks", "change_config", $userid, $courseid);
    $weeks = json_decode($weeks);
    $configweeks = new \local_fliplearning\configweeks($courseid, $userid);
    if($newinstance){
        $configweeks->create_instance();
    }
    $configweeks->last_instance();
    $configweeks->save_weeks($weeks);
    $configweeks = new \local_fliplearning\configweeks($courseid, $userid);
    local_fliplearning_ajax_response(["settings" => $configweeks->get_settings()]);
}

function local_fliplearning_change_group($courseid, $userid, $groupid){
    set_time_limit(300);
    global $DB;
    if(is_null($groupid)){
        $groupid = 0;
    }
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
    $group_manager = new \local_fliplearning\group_manager($course, $user);
    $participants = new \local_fliplearning\course_participant($user->id, $course->id);
    $groups = array_values($participants->current_user_groups_with_all_group($course->groupmode));
    $selectedgroupid = $group_manager->selected_group()->groupid;
    $groups = local_fliplearning_add_selected_property($groups, $selectedgroupid);
    $group_manager->set_group($groupid);
    $groups = local_fliplearning_get_groups($course, $user);
    local_fliplearning_ajax_response(array("groups" => $groups));
}

function local_fliplearning_get_work_sessions($weekcode, $courseid, $userid, $profile){
    set_time_limit(300);
    if($profile == "teacher"){
        $reports = new \local_fliplearning\teacher($courseid, $userid);
    }else{
        $reports = new \local_fliplearning\student($courseid, $userid);
    }
    $sessions = $reports->hours_sessions($weekcode);
    $count = $reports->count_sessions($weekcode);
    $body = array(
        "sessions_by_hours" => $sessions,
        "session_count" => $count
    );
    local_fliplearning_ajax_response($body);
}

function local_fliplearning_get_inverted_time($weekcode, $courseid, $userid, $profile){
    set_time_limit(300);
    if($profile == "teacher"){
        $reports = new \local_fliplearning\teacher($courseid, $userid);
    }else{
        $reports = new \local_fliplearning\student($courseid, $userid);
    }
    $inverted_time = $reports->inverted_time($weekcode);
    $body = array(
        "inverted_time" => $inverted_time,
    );
    local_fliplearning_ajax_response($body);
}

function local_fliplearning_get_assignments_submissions($weekcode, $courseid, $userid, $profile){
    set_time_limit(300);
    if($profile == "teacher"){
        $reports = new \local_fliplearning\teacher($courseid, $userid);
    }else{
        $reports = new \local_fliplearning\student($courseid, $userid);
    }
    $submissions = $reports->assignments_submissions($weekcode);
    $access = $reports->resources_access($weekcode);
    $body = array(
        "submissions" => $submissions,
        "access" => $access,
    );
    local_fliplearning_ajax_response($body);
}

function local_fliplearning_send_email($course, $user, $subject, $recipients, $text, $moduleid, $modulename){
    set_time_limit(300);
    $email = new \local_fliplearning\email($course, $user);
    $email->sendmail($subject, $recipients, $text, $moduleid, $modulename);

    $body = array(
        "data" => [$subject, $recipients, $text, $moduleid],
    );
    local_fliplearning_ajax_response($body);
}

function local_fliplearning_get_quiz_attempts($weekcode, $courseid, $userid, $profile){
    set_time_limit(300);
    if($profile == "teacher"){
        $reports = new \local_fliplearning\teacher($courseid, $userid);
    }else{
        $reports = new \local_fliplearning\student($courseid, $userid);
    }
    $quiz = $reports->quiz_attempts($weekcode);
    $body = array(
        "quiz" => $quiz,
    );
    local_fliplearning_ajax_response($body);
}

function local_fliplearning_generate_dropout_data($courseid, $userid, $profile){
    set_time_limit(300);
    if($profile == "teacher"){
        $dropout = new \local_fliplearning\dropout($courseid, $userid);
        $dropout->generate_data();
        local_fliplearning_ajax_response([], "ok", true, 200);
    }else{
        local_fliplearning_ajax_response([], "", false, 400);
    }
}

function local_fliplearning_get_student_sessions($weekcode, $courseid, $userid, $profile){
    set_time_limit(300);
    $reports = new \local_fliplearning\student($courseid, $userid);
    $indicators = $reports->get_sessions($weekcode, false);
    $body = array(
        "indicators" => $indicators,
    );
    local_fliplearning_ajax_response($body);
}