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
 * local_fliplearning
 *
 * @package     local_fliplearning
 * @autor       Edisson Sigua, Bryan Aguilar
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fliplearning;

use context_course;

use stdClass;

class course_participant{
    const  USER_FIELDS = ["id", "username", "firstname", "lastname", "email"];
    const ALL_STUDENT = 0;
    protected $course;
    protected $user;
    protected $context;

    public function __construct($userid, $courseid){
        $this->course = self::get_course_from_id($courseid);
        $this->user = self::get_user_from_id($userid);
        $this->context = context_course::instance($this->course->id);
    }

    protected function get_course_from_id($courseid, $strictness = MUST_EXIST){
        global $DB;
        $course = $DB->get_record("course", array("id" => $courseid), '*', $strictness);
        return $course;
    }

    protected function get_user_from_id($userid, $strictness = MUST_EXIST){
        global $DB;
        $user = $DB->get_record("user", array("id" => $userid), '*', $strictness);
        return $user;
    }

    public function is_student(){
        $student_role = 5;
        $is_student = false;
        $users_with_role = get_role_users($student_role, $this->context);
        if(!empty($users_with_role) && isset($users_with_role[$this->user->id])){
            $is_student = true;
        }
        return $is_student;
    }

    public function group_has_student($groupid){
        $has_student = false;
        $student_role = 5;
        $course_students = get_role_users($student_role, $this->context);
        $group_members = groups_get_members($groupid);
        foreach($group_members as $member){
            if(isset($course_students[$member->id])){
                $has_student = true;
                break;
            }
        }
        return $has_student;
    }

    public function current_user_groups_with_all_group($groupmode, $exclude_without_student = true){
        $context = context_course::instance($this->course->id);
        $groups = new stdClass();
        $groups->all = groups_get_all_groups($this->course->id);
        if(self::current_user_is_admin() || self::current_user_is_teacher()){
            if(has_capability('local/fliplearning:seegroupwithallstudent', $context)){
                array_unshift($groups->all, self::default_group());
            }
            if($exclude_without_student){
                $groups->all = self::exclude_without_student($groups->all);
            }
            return $groups->all;
        }
        $groups->current_user_is_member = array();
        foreach($groups->all as $group){
            $prefix = "u.";
            $members = groups_get_members($group->id, self::sql_query_user_fields($prefix));
            if(isset($members[$this->user->id])){
                $groups->current_user_is_member[$group->id] = $group;
            }
        }
        if(has_capability('local/fliplearning:seegroupwithallstudent', $context)){
            array_unshift($groups->current_user_is_member, self::default_group());
        }
        if((self::is_student() && $groupmode != SEPARATEGROUPS) || (self::is_student() && $groupmode == SEPARATEGROUPS && empty($groups->current_user_is_member))){
            $groups->current_user_is_member = array(self::default_group());
        }
        if($exclude_without_student){
            $groups->current_user_is_member = self::exclude_without_student($groups->current_user_is_member);
        }
        return $groups->current_user_is_member;
    }

    private function default_group(){
        $group = new stdClass();
        $group->id = self::ALL_STUDENT;
        $group->courseid = $this->course->id;
        $group->idnumber = 'allstudent';
        $group->name = get_string("fml_group_allstudent", "local_fliplearning");
        $group->description = "";
        $group->descriptionformat = 1;
        $group->enrolmentkey = "";
        $group->picture = 0;
        $group->hidepicture = 0;
        $group->timecreated = time();
        $group->timemodified = time();
        return $group;
    }

    public function all_groups_with_members(){
        $groups = array();
        foreach(self::all_groups() as $group){
            $prefix = "u.";
            $group->members = groups_get_members($group->id, self::sql_query_user_fields($prefix));
            $groups[$group->id] = $group;
        }
        return $groups;
    }

    public function all_groups(){
        $groups = groups_get_all_groups($this->course->id);
        $groups = self::filter_groups_by_group_mode($groups);
        $groups = self::arrayCopy($groups);
        return $groups;
    }

    private function filter_groups_by_group_mode($all_groups){
        $groups = new stdClass();
        $groups->all = $all_groups;
        $groups->is_member = array();
        if(self::current_user_is_admin() || self::current_user_is_teacher()){
            return $groups->all;
        }
        if(self::separated_group()){
            $groups->is_member = self::current_user_groups();
        }
        return $groups->is_member;
    }

    private function current_user_is_admin(){
        $admins = get_admins();
        $is_admin = isset($admins[$this->user->id]);
        return $is_admin;
    }

    private function current_user_is_teacher(){
        $editingteachers_role = 3;
        $teacher_role = 4;
        $is_teacher = false;
        $editingteachers = get_role_users($editingteachers_role, $this->context);
        $teachers = get_role_users($teacher_role, $this->context);
        if(!empty($editingteachers) && isset($editingteachers[$this->user->id])){
            $is_teacher = true;
        }
        if(!empty($teachers) && isset($teachers[$this->user->id])){
            $is_teacher = true;
        }
        return $is_teacher;
    }

    private function separated_group(){
        $enabled = $this->course->groupmode == SEPARATEGROUPS;
        return $enabled;
    }

    public function current_user_groups(){
        $groups = new stdClass();
        $groups->all = groups_get_all_groups($this->course->id);
        if(self::current_user_is_admin()){
            return $groups->all;
        }
        $groups->current_user_is_member = array();
        foreach($groups->all as $group){
            $prefix = "u.";
            $members = groups_get_members($group->id, self::sql_query_user_fields($prefix));
            if(isset($members[$this->user->id])){
                $groups->current_user_is_member[$group->id] = $group;
            }
        }
        return $groups->current_user_is_member;
    }

    protected function sql_query_user_fields ($prefix = null, $renameid = null){
        $query = "";
        $fields = self::USER_FIELDS;
        $last = count($fields) - 1;
        foreach ($fields as $iteration => $field) {
            if ($iteration == 0 && $renameid) {
                $query .= "$prefix$renameid";
            } else {
                $query .= "$prefix$field";
            }
            if ($iteration < $last) {
                $query .= ", ";
            }
        }
        return $query;
    }

    protected function arrayCopy(array $array){
        $result = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $result[$key] = self::arrayCopy($val);
            } elseif (is_object($val)) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
    }

    private function exclude_without_student($groups){
        $filtered = array();
        foreach($groups as $group){
            if($group->id == 0){
                array_push($filtered, $group);
            }
            if(self::group_has_student($group->id)){
                array_push($filtered, $group);
            }
        }
        return $filtered;
    }
}