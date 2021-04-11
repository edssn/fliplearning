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

    public function get_general_indicators () {
        if(!self::course_is_valid()){
            return null;
        }

        $start = null;
        if(isset($this->course->startdate) && ((int)$this->course->startdate) > 0) {
            $start = $this->course->startdate;
        }
        $end = null;
        if(isset($this->course->enddate) && ((int)$this->course->enddate) > 0) {
            $end = $this->course->enddate;
        }
        $enable_completion = false;
        if(isset($this->course->enablecompletion) && ((int)$this->course->enablecompletion) == 1) {
            $enable_completion = true;
        }

        $cms = self::get_course_modules();
        $cms = array_filter($cms, function($cm){ return $cm['modname'] != 'label';});
        $cms = array_values($cms);

        $user_sessions = self::get_work_sessions($start, $end);

        $user = self::get_progress_table($user_sessions, $cms, $enable_completion, true);
        $user = $this->get_users_course_grade($user);
        $user = $this->get_users_items_grades($user);

        $sessions = array_map(function($user_sessions){ return $user_sessions->sessions;}, $user_sessions);
        $sessions = self::get_sessions_by_weeks($sessions);
        $sessions = self::get_sessions_by_weeks_summary($sessions, (int) $this->course->startdate);

        $configweeks = new \local_fliplearning\configweeks($this->course->id, $this->user->id);

        $response = new stdClass();
        $response->cms = $cms;
        $response->user = $user[0];
        $response->sessions = $sessions;
        $response->sections = $configweeks->current_sections;

        return $response;
    }

    public function get_sessions($weekcode = null, $include_weeks = true){
        if(!self::course_is_valid()){
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
        $inverted_time = self::get_inverted_time_summary($inverted_time, (int) $week->hours_dedications, false);

        $response = new stdClass();
        $response->hours_sessions = $sessions;
        $response->inverted_time = $inverted_time;

        if ($include_weeks) {
            $start = null;
            if(isset($this->course->startdate) && ((int)$this->course->startdate) > 0) {
                $start = $this->course->startdate;
            }
            $end = null;
            if(isset($this->course->enddate) && ((int)$this->course->enddate) > 0) {
                $end = $this->course->enddate;
            }
            $enable_completion = false;
            if(isset($this->course->enablecompletion) && ((int)$this->course->enablecompletion) == 1) {
                $enable_completion = true;
            }

            $work_sessions = self::get_work_sessions($start, $end);
            $cms = self::get_course_modules();
            $cms = array_filter($cms, function($cm){ return $cm['modname'] != 'label';});
            $cms = array_values($cms);
            $user = self::get_progress_table($work_sessions, $cms, $enable_completion);

            $response->course_cms = $cms;
            $response->user_cms = $user[0]->cms->modules;
//            $response->weeks = $this->weeks;
//            $response->sections = $this->current_sections;
        }
        $response->sections = $week->sections;
        return $response;
    }
}