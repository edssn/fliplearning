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
 * FlipLearning Logs component
 *
 * @package     local_fliplearning
 * @autor       Edisson Sigua, Bryan Aguilar
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fliplearning;

require_once dirname(__FILE__) . '/../../../course/lib.php';

use context_course;
use stdClass;

class log {

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