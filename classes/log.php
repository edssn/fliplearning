<?php
require_once dirname(__FILE__) . '/../../../course/lib.php';

class local_fliplearning_log {

    public static function create($component, $action, $userid, $courseid){
        global $DB;
        $user = self::get_user($userid);
        $course = self::get_course($courseid);
        $log = new stdClass();
        $log->userid = $user->id;
        $log->username = $user->username;
        $log->name = $user->firstname;
        $log->lastname = $user->lastname;
        $log->email = $user->email;
        $log->current_roles = self::get_user_roles($courseid, $userid);
        $log->courseid = $course->id;
        $log->coursename = $course->fullname;
        $log->courseshortname = $course->shortname;
        $log->component = $component;
        $log->action = $action;
        $log->timecreated = time();
        $id = $DB->insert_record("fliplearning_logs", $log, true);
        $log->id = $id;
        return $log;
    }

    public static function get_user($userid){
        global $DB;
        $sql = "select * from {user} where id = ?";
        $user = $DB->get_record_sql($sql, array($userid));
        return $user;
    }

    public static function get_course($courseid){
        global $DB;
        $sql = "select * from {course} where id = ?";
        $user = $DB->get_record_sql($sql, array($courseid));
        return $user;
    }

    public static function get_user_roles($courseid, $userid){
        $user_roles = array();
        $admins = array_values(get_admins());
        foreach($admins as $admin){
            if($admin->id == $userid){
                $user_roles[] = 'admin';
            }
        }
        $context = context_course::instance($courseid);
        $roles = get_user_roles($context, $userid);
        foreach ($roles as $role) {
            $user_roles[] = $role->shortname;
        }
        $user_roles = implode(', ', $user_roles);
        return $user_roles;
    }
}