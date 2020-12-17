<?php
class local_fliplearning_group_manager{
    public $course;
    public $user;
    public $groupid;
    public $groupname;
    public $groupmode;
    const SELECTED_COURSE_GROUPS = "local_fliplearning_group_selected";
    const ALL_STUDENT = 0;

    function __construct($course, $user){
        $this->user = $user;
        $this->course = $course;
        $this->groupmode = $course->groupmode;
        self::create_course_session_if_not_exist();
        self::set_default_group();
    }

    private function create_course_session_if_not_exist(){
        if(!self::session_was_created()){
            $_SESSION[self::SELECTED_COURSE_GROUPS] = array();
        }
        if(!self::course_session_was_created()){
            $_SESSION[self::SELECTED_COURSE_GROUPS][$this->course->id] = new local_fliplearning_sessiongroup();
        }
    }

    private function session_was_created(){
        $created = isset($_SESSION[self::SELECTED_COURSE_GROUPS]);
        return $created;
    }

    private function course_session_was_created(){
        $created = isset($_SESSION[self::SELECTED_COURSE_GROUPS][$this->course->id]);
        return $created;
    }

    private function set_default_group(){
        if(self::is_valid_group()){
            return true;
        }
        $participants = new local_fliplearning_course_participant($this->user->id, $this->course->id);
        $groups = $participants->current_user_groups_with_all_group($this->groupmode);
        $groups = array_values($groups);
        if($this->course->groupmode == SEPARATEGROUPS && isset($groups[0])){ // use the first group, if user have the capacity the first group will be all students
            self::set_group($groups[0]->id);
        }else{
            self::set_all_student();
        }
    }

    public function set_group($groupid){
        $this->groupid = $groupid;
        $this->groupmode = $this->course->groupmode;
        $default_name = get_string('group_allstudent', 'local_fliplearning');
        $group_name = $groupid > 0 ? groups_get_group_name($groupid) : $default_name;
        $this->groupname = $group_name;
        self::update_course_session();
    }

    private function set_all_student(){
        $this->groupid = self::ALL_STUDENT;
        $this->groupname = get_string('group_allstudent', 'local_fliplearning');
        $this->groupmode = $this->course->groupmode;
        self::update_course_session();
    }

    private function update_course_session(){
        $session = $_SESSION[self::SELECTED_COURSE_GROUPS][$this->course->id];
        $session->courseid = $this->course->id;
        $session->groupid = $this->groupid;
        $session->groupname = $this->groupname;
        $session->groupmode = $this->groupmode;
    }

    private function is_valid_group(){
        $valid = false;
        if(self::session_was_created() && self::course_session_was_created()){
            $group = $_SESSION[self::SELECTED_COURSE_GROUPS][$this->course->id];
            if(!is_null($group->courseid) && !is_null($group->groupid) && !is_null($group->groupmode)){
                $valid = true;
            }
        }

        $participant = new local_fliplearning_course_participant($this->user->id, $this->course->id);
        if($participant->is_student() && self::different_group_mode() || self::group_selection_not_allowed()){
            $valid = false;
        }

        if(self::session_was_created() && self::course_session_was_created()){
            $course_session = $_SESSION[self::SELECTED_COURSE_GROUPS][$this->course->id];
            if(!empty($course_session->groupid) && $course_session->groupid > self::ALL_STUDENT && !$participant->group_has_student($course_session->groupid)){
                $valid = false;
            }
        }
        return $valid;
    }

    private function different_group_mode(){
        $change = true;
        if(self::session_was_created() && self::course_session_was_created()){
            $course_session = $_SESSION[self::SELECTED_COURSE_GROUPS][$this->course->id];
            $change = $this->course->groupmode != $course_session->groupmode;
        }
        return $change;
    }

    private function group_selection_not_allowed(){
        $not_allowed = false;
        if(self::session_was_created() && self::course_session_was_created()){
            $session = $_SESSION[self::SELECTED_COURSE_GROUPS][$this->course->id];
            $participant = new local_fliplearning_course_participant($this->user->id, $this->course->id);
            if($participant->is_student() && $session->groupmode != SEPARATEGROUPS && $session->groupid > self::ALL_STUDENT){
                $not_allowed = true;
            }
        }
        return $not_allowed;
    }

    public function selected_group(){
        $group = $_SESSION[self::SELECTED_COURSE_GROUPS][$this->course->id];
        return $group;
    }
}