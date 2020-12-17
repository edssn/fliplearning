<?php
require_once("libtrait.php");
class local_fliplearning_configweeks {
    use lib_trait;

    public $course;
    public $user;
    public $weeks;
    public $group;
    public $current_sections;
    public $startin;

    function __construct($course, $userid){
        global $DB;
        $this->course = self::get_course($course);
        $this->user = self::get_user($userid);
        $this->group = self::last_group();
        $this->weeks = self::get_weeks();
        $this->current_sections = self::get_course_sections();
        $this->startin = isset($this->weeks[0]) ? $this->weeks[0]->weekstart : 999999999999;
        self::get_weeks_with_sections();
    }

    public function last_group(){
        global $DB;
        $sql = "select * from {fliplearning_group} where courseid = ? order by id desc LIMIT 1";
        $group = $DB->get_record_sql($sql, array($this->course->id));
        if(!isset($group) || empty($group)){
            $group = self::create_group($this->course->id);
        }
        return $group;
    }

    public function create_group(){
        global $DB;
        $group = new stdClass();
        $group->courseid = $this->course->id;
        $group->year = date("Y");
        $id = $DB->insert_record("fliplearning_group", $group, true);
        $group->id = $id;
        $this->group = $group;
        return $group;
    }

    public function get_weeks($format = null){
        global $DB;
        $sql = "select * from {fliplearning_weeks} where courseid = ? and groupid = ? and timedeleted IS NULL order by position asc";
        $weeks = $DB->get_records_sql($sql, array($this->course->id, $this->group->id));
        $weeks = array_values($weeks);
        return $weeks;
    }

    public function get_weeks_with_sections(){
        $weeks = $this->weeks;
        if(count($weeks) == 0){
            $weeks[] = self::create_first_week();
            $this->weeks = $weeks;
        }
        $course_sections = self::get_course_sections();
        foreach($weeks as $position => $week){
            $week->removable = true;
            if($position == 0){
                $week->removable = false;
            }
            $week->sections = array();
            $week->name = get_string('setweeks_week', 'local_student_reports');
            if(!isset($week->date_format)){ // Evita que la fecha vuelva a convertirse, no estoy feliz con esto, pero es funcional :)
                $week->date_format = "Y-m-d";
                $week->weekstart = self::to_format("Y-m-d", $week->weekstart);
                $week->weekend = self::to_format("Y-m-d", $week->weekend);
            }
            $week->position = $position;
            $week->delete_confirm = false;
            $sections = self::get_week_sections($week->weekcode);
            foreach($sections as $key => $section){
                $section->name = $section->section_name;
                $section->visible = self::get_current_visibility($section->sectionid);
                $section = self::validate_section($section, $course_sections);
                $week->sections[] = $section;
            }
        }
        return $weeks;
    }

    private function create_first_week(){
        global $DB;
        $start = strtotime('next monday');
        $end = strtotime('next monday + 6 day');
        $week = new stdClass();
        $week->hours_dedications = 0;
        $week->courseid = $this->course->id;
        $week->weekstart = $start;
        $week->weekend = $end;
        $week->position = 0;
        $week->modified_by = $this->user->id;
        $week->created_by = $this->user->id;
        $week->timecreated = self::now_timestamp();
        $week->timemodified = self::now_timestamp();
        $week->weekcode = self::generate_week_code(0);
        $week->groupid = $this->group->id;
        $id = $DB->insert_record("fliplearning_weeks", $week, true);
        $week->id = $id;
        return $week;
    }

    private function generate_week_code($weekposition){
        $code = $this->group->year . $this->group->id . $this->course->id . $weekposition;
        $code = (int) $code;
        return $code;
    }

    public function get_week_sections ($weekcode){
        global $DB;
        $sql = "select * from {fliplearning_sections} where weekcode = ? and timedeleted IS NULL order by position asc";
        $week_sections = $DB->get_records_sql($sql, array($weekcode));
        return $week_sections;
    }

    private function get_current_visibility($sectionid){
        foreach($this->current_sections as $section){
            if($section['sectionid'] == $sectionid){
                return $section['visible'];
            }
        }
        return null;
    }

    private function validate_section($section, $course_sections){
        $exist = false;
        foreach($course_sections as $key => $course_section){
            if($section->sectionid == $course_section['sectionid']){
                $exist = true;
                if($section->name != $course_section['name']){
                    self::update_section_name($section->sectionid, $course_section['name']);
                    $section->name = $course_section['name'];
                }
                break;
            }
        }
        $section->exists = $exist;
        return $section;
    }

    private function update_section_name($sectionid, $name){
        global $DB;
        $sql = "update {fliplearning_sections} set section_name = ? where sectionid = ?";
        $DB->execute($sql, array($name, $sectionid));
    }

    public function is_set(){
        $is_set = true;
        $settings = self::get_settings();
        foreach($settings as $configured){
            if(!$configured){
                $is_set = false;
                break;
            }
        }
        return $is_set;
    }

    public function get_settings(){
        $tz = self::get_timezone();
        date_default_timezone_set($tz);
        $course_start = $this->startin;
        $weeks = self::get_weeks_with_sections();
        $settings = [
            "weeks" => false,
            "course_start" => false,
            "has_students" => false
        ];
        $first_week = new stdClass();
        $first_week->has_sections = isset($weeks[0]) && !empty($weeks[0]->sections);
        $first_week->started = time() >= $course_start;
        if($first_week->has_sections){
            $settings['weeks'] = true;
        }
        if($first_week->started){
            $settings['course_start'] = true;
        }
        $students = self::get_student_ids();
        if(!empty($students)){
            $settings['has_students'] = true;
        }
        return $settings;
    }
}
