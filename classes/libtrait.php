<?php
require_once(dirname(__FILE__) . '/../../../config.php');

trait lib_trait{

    public function get_course($course){
        if(gettype($course) == "string"){
            $course = (int) $course;
        }
        if(gettype($course) == "integer"){
            $course = self::get_course_from_id($course);
        }
        return $course;
    }

    public static function get_course_from_id($courseid){
        global $DB;
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        return $course;
    }

    public function get_user($user){
        if(gettype($user) == "string"){
            $user = (int) $user;
        }
        if(gettype($user) == "integer"){
            $user = self::get_user_from_id($user);
        }
        return $user;
    }

    public static function get_user_from_id($userid){
        global $DB;
        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        return $user;
    }

    public function get_course_sections(){
        $modinfo  = get_fast_modinfo($this->course->id);
        $sections = $modinfo->get_section_info_all();
        $sections = self::format_sections($sections);
        return $sections;
    }

    private function format_sections($sections){
        $full_sections = array();
        foreach ($sections as $index => $section){
            $full_section = [
                'sectionid' => $section->id,
                'section' => $section->section,
                'name' => self::get_section_name($section, $index),
                'visible' => $section->visible,
                'availability' =>  $section->availability,
            ];
            $full_sections[] = $full_section;
        }
        return $full_sections;
    }

    private function get_section_name($section, $current_index){
        if(isset($section->name) ){
            return $section->name;
        }
        $build_name = get_string("course_format_{$this->course->format}", 'local_fliplearning');
        $name = "$build_name $current_index";
        return $name;
    }

    public function to_format($format, $timestamp){
        $tz = self::get_timezone();
        date_default_timezone_set($tz);
        if(gettype($timestamp) == "string"){
            $timestamp = (int) $timestamp;
        }
        $date = date($format, $timestamp);
        return $date;
    }

    public function now_timestamp(){
        $tz = self::get_timezone();
        date_default_timezone_set($tz);
        $now = new DateTime();
        $now = $now->format('U');
        return $now;
    }

    public function get_timezone(){
        $timezone = usertimezone($this->user->timezone);
        $timezone = self::accent_remover($timezone);
        if(!self::is_valid_timezone($timezone)){
            $timezone = self::get_server_timezone();
        }
        return $timezone;
    }

    public function accent_remover($cadena){
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );
        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena );
        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena );
        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena );
        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena );
        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $cadena
        );
        return $cadena;
    }

    public function is_valid_timezone($timezone) {
        return in_array($timezone, timezone_identifiers_list());
    }

    public function get_student_ids(){
        $roles = array(5);
        global $DB;
        $students = array();
        $users = array();
        $context = context_course::instance($this->course->id);
        foreach($roles as $role){
            $users = array_merge($users, get_role_users($role, $context));
        }
        foreach($users as $user){
            if(!in_array($user->id, $students)){
                $students[] = $user->id;
            }
        }
        $students = self::filter_users_by_selected_group($students);
        return $students;
    }

    protected function filter_users_by_selected_group ($users) {
        global $COURSE, $USER;
        $group_manager = new local_fliplearning_group_manager($COURSE, $USER);
        $participants = new local_fliplearning_course_participant($USER->id, $COURSE->id);
        $groups = $participants->all_groups_with_members($COURSE->groupmode);
        $selectedgroup = $group_manager->selected_group();
        if(!isset($selectedgroup->groupid) || $selectedgroup->groupid == 0 ){
            return $users;
        }
        foreach ($groups as $group) {
            if($selectedgroup->groupid == $group->id){
                $users = self::extract_users_in_group($users, $group->members);
            }
        }
        return $users;
    }

    private function extract_users_in_group($allusers, $ingroup){
        $extracted = array();
        foreach($allusers as $userid){
            if(isset($ingroup[$userid]) && !in_array($userid, $extracted)){
                array_push($extracted, $userid);
            }
        }
        return $extracted;
    }
}