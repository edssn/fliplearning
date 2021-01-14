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
 * User sessions visualizations
 *
 * @package     local_fliplearning
 * @autor       Edisson Sigua, Bryan Aguilar
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fliplearning;

defined('MOODLE_INTERNAL') || die;

require_once('lib_trait.php');

/**
 * Class report
 *
 * @author      Edisson Sigua
 * @author      Bryan Aguilar
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class report {
    use \lib_trait;

    const MINUTES_TO_NEW_SESSION = 30;
    const USER_FIELDS = "id, username, firstname, lastname, email, lastaccess, picture, deleted";
    protected $course;
    protected $user;
    protected $profile;
    protected $users;
    protected $current_week;
    protected $past_week;
    protected $weeks;
    public $timezone;

    function __construct($courseid, $userid){
        $this->user = self::get_user($userid);
        $this->course = self::get_course($courseid);
        $this->timezone = self::get_timezone($userid);
        date_default_timezone_set($this->timezone);
        $this->users = array();
        $configweeks = new \local_fliplearning\configweeks($this->course->id, $this->user->id);
        $this->weeks = $configweeks->weeks;
        $this->current_week = $configweeks->get_current_week();
        $this->past_week = $configweeks->get_past_week();
    }

    abstract public function set_users();

    abstract public function set_profile();

    public function render_has(){
        return $this->profile;
    }

}