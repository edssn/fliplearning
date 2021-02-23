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

use stdClass;

class student extends report {

    function __construct($courseid, $userid){
        parent::__construct($courseid, $userid);
        self::set_profile();
        self::set_users();
    }

    /**
     * Almacena el perfil de visualización de la clase en la variable $profile de clase
     */
    public function set_profile(){
        $this->profile = "student";
    }

    /**
     * Almacena el id del estudiante en la variable $users de la clase
     */
    public function set_users(){
        $this->users = array($this->user->id);
        return $this->users;
    }

    public function get_sessions($weekcode = null){
        if(!self::course_in_transit()){
            return null;
        }
        if(!self::course_has_users()){
            return null;
        }
        $week = $this->current_week;
        if(!empty($weekcode)){
            $week = self::find_week($weekcode);
        }

        $work_sessions = self::get_work_sessions($week->weekstart, $week->weekend);
        $sessions = array_map(function($user_sessions){ return $user_sessions->sessions;}, $work_sessions);
        $sessions = self::get_sessions_by_hours($sessions);
        $sessions = self::get_sessions_by_hours_summary($sessions);
        $inverted_time = array_map(function($user_sessions){ return $user_sessions->summary;}, $work_sessions);
        $inverted_time = self::calculate_average("added", $inverted_time);
        $inverted_time = self::get_inverted_time_summary($inverted_time, (int) $week->hours_dedications);

        $response = new stdClass();
        $response->sessions = $sessions;
        $response->time = $inverted_time;
        return $response;
    }
}