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

use stdClass;

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
    protected $current_sections;
    public $timezone;

    function __construct($courseid, $userid){
        $this->user = self::get_user($userid);
        $this->course = self::get_course($courseid);
        $this->timezone = self::get_timezone($userid);
        date_default_timezone_set($this->timezone);
        $this->users = array();
        $configweeks = new \local_fliplearning\configweeks($this->course->id, $this->user->id);
        $this->weeks = $configweeks->weeks;
        $this->current_sections = $configweeks->current_sections;
        $this->current_week = $configweeks->get_current_week();
        $this->past_week = $configweeks->get_past_week();
    }

    abstract public function set_users();

    abstract public function set_profile();

    public function render_has(){
        return $this->profile;
    }

    protected function course_is_valid(){
        $in_transit = isset($this->current_week) || isset($this->past_week) ? true : false;
        $has_users = count($this->users) > 0 ? true : false;
        return $in_transit && $has_users;
    }

    /**
     * Verifica si el curso aún no ha terminado o si el tiempo transcurrido desde que ha terminado las
     * semanas configuradas de Fliplearning es menor a una semana
     *
     * @return boolean valor booleano que indica si el curso aun sigue activo
     */
    protected function course_in_transit(){
        $in_transit = isset($this->current_week) || isset($this->past_week) ? true : false;
        return $in_transit;
    }

    /**
     * Verifica si el curso tiene estudiantes
     *
     * @return boolean valor booleano que indica si el curso tiene estudiantes
     */
    protected function course_has_users(){
        $has_users = count($this->users) > 0 ? true : false;
        return $has_users;
    }

    /**
     * Busca la semana con codigo igual al parametro $weekcode y lo retorna. En caso de no encontrar
     * la semana con el codigo de paramtero, se imprime un error
     *
     * @param string $weekcode identificador de la semana que se desea obtener
     *
     * @return object objecto con la semana que hace match con el parametro
     */
    protected function find_week($weekcode){
        foreach($this->weeks as $week){
            if($weekcode == $week->weekcode){
                return $week;
            }
        }
        print_error("Weekcode not found");
    }

    protected function get_progress_table($users, $cms, $enable_completion, $include_sessions = false) {
        $table = array();
        $total_cms = count($cms);
        if ($total_cms > 0) {
            foreach ($users as $user) {
                $cms_interaction = self::cms_interactions($cms, $user, $enable_completion);
                $progress_percentage = (int)(($cms_interaction->complete * 100)/$total_cms);
                $inverted_time_label = self::convert_time($user->time_format, $user->summary->added, "hour");
                $user_record = self::get_user($user->userid);

                $record = new stdClass();
                $record->id = $user_record->id;
                $record->firstname = $user_record->firstname;
                $record->lastname = $user_record->lastname;
                $record->username = $user_record->username;
                $record->email = $user_record->email;
                $record->progress_percentage = $progress_percentage;
                $record->cms = $cms_interaction;
                $record->sessions_number = $user->summary->count;
                $record->inverted_time = $user->summary->added;
                $record->inverted_time_label = $inverted_time_label;

                if ($include_sessions) {
                    $record->sessions = $user->sessions;
                }
                array_push($table, $record);
            }
        }
        return $table;
    }

    private function cms_interactions($cms, $user, $cms_completion_enabled){
        $complete_cms = 0;
        $cms_ids = array();
        $viewed_cms = 0;
        foreach ($cms as $module) {
            $finished = null;
            if ($cms_completion_enabled) {
                $module_completion_configure = $module['completion'] != 0;
                if ($module_completion_configure) {
                    $finished = self::finished_cm_by_conditions($user->userid, $module['id']);
                }
            }
            $interactions = self::count_cm_interactions($user, $module['id']);
            $viewed = ($interactions > 0);
            $finished = (!isset($finished)) ? $viewed : $finished;

            $cm = new stdClass();
            $cm->id = $module['id'];
            $cm->interactions = $interactions;
            $cm->complete = false;
            $cm->viewed = false;
            if ($viewed) {
                $viewed_cms++;
                $cm->viewed = true;
            }
            if ($finished) {
                $complete_cms++;
                $cm->complete = true;
            }
            if ($viewed || $finished) {
                $cmid = "cm".$module['id'];
                $cms_ids[$cmid] = $cm;
            }
        }
        $interaction = new stdClass();
        $interaction->complete = $complete_cms;
        $interaction->viewed = $viewed_cms;
        $interaction->modules = $cms_ids;
        $interaction->total = count($cms);
        return $interaction;
    }

    private function finished_cm_by_conditions($userid, $cm_id){
        global $DB;
        $complete = false;
        $item = $DB->get_record('course_modules_completion',
            array('coursemoduleid' => $cm_id, 'userid' => $userid), 'id, timemodified');
        if ($item) {
            $complete = true;
        }
        return $complete;
    }

    private function count_cm_interactions($user, $cm_id){
        $cm_logs = 0;
        foreach ($user->logs as $log) {
            if ($log->contextlevel == 70 && $log->contextinstanceid == $cm_id) {
                $cm_logs++;
            }
        }
        return $cm_logs;
    }

    protected function get_sessions_by_weeks($user_sessions) {
        $months = array();
        foreach($user_sessions as $sessions){
            foreach($sessions as $session){
                $resp = self::get_month_and_week_number((int) $session->start);
                $month = $resp->month;
                $week = $resp->week;

                if(!isset($months[$month])){
                    $months[$month] = array();
                }
                if(!isset($months[$month][$week])){
                    $months[$month][$week] = 1;
                } else {
                    $months[$month][$week]++;
                }
            }
        }
        return $months;
    }

    protected function get_sessions_by_weeks_summary($months, $startdate) {
        $startdate = strtotime('first day of this month', $startdate);
        $month_number = ((int) date("n", $startdate)) - 1;

        $summary = array();
        $categories = array();
        $week_dates = array();
        if (!empty($months)) {
            for ($y = 0; $y <= 11; $y++) {
                $month_code = self::get_month_code($month_number);
                if (isset($months[$month_code])) {
                    $weeks = $months[$month_code];
                }
                for ($x = 0; $x <= 4; $x++) {
                    $value = 0;
                    if(isset($weeks)) {
                        if (isset($weeks[$x])) {
                            $value=$weeks[$x];
                        }
                    }
                    $element = array("x" => $x, "y" => $y, "value" => $value);
                    array_push($summary, $element);
                }
                $weeks = null;

                $dates = self::get_weeks_of_month($startdate);
                array_push($week_dates, $dates);

                $month_number++;
                if ($month_number > 11) {
                    $month_number = 0;
                }

                $month_name = get_string("fml_$month_code", "local_fliplearning");
                $year = date("Y", $startdate);
                $category_name = "$month_name $year";
                array_push($categories, $category_name);

                $startdate = strtotime('first day of +1 month',$startdate);
            }
        }
        $response = new stdClass();
        $response->data = $summary;
        $response->categories = $categories;
        $response->weeks = $week_dates;
        return $response;
    }

    private function get_month_code($key) {
        $months = array("jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec");
        return $months[$key];
    }

    private function get_weeks_of_month($date) {
        $weeks = array();
        $month_code = strtolower(date("M", $date));
        $date = strtotime("first monday of this month", $date);
        while (strtolower(date("M", $date)) == $month_code) {

            $day_code = strtolower(date("D", $date));
            $start_day_name = get_string("fml_$day_code", "local_fliplearning");
            $start_day_number = strtolower(date("d", $date));

            $end = strtotime("+ 7 days", $date) - 1;
            $day_code = strtolower(date("D", $end));
            $end_day_name = get_string("fml_$day_code", "local_fliplearning");
            $end_day_number = strtolower(date("d", $end));

            $label = "$start_day_name $start_day_number - $end_day_name $end_day_number";
            array_push($weeks, $label);
            $month_code = strtolower(date("M", $date));
            $date = strtotime("+ 7 days", $date);
        }
        return $weeks;
    }

    private function get_month_and_week_number($date) {
        $monday_of_week = strtotime( 'monday this week', $date);
        $first_monday_month = strtotime("first monday of this month", $monday_of_week);
        $first_sunday_month = strtotime("+ 7 days", $first_monday_month) - 1;
        $week_number = 0;
        while ($first_sunday_month < $date) {
            $first_sunday_month = strtotime("+ 7 days", $first_sunday_month);
            $week_number++;
        }
        $resp = new stdClass();
        $resp->month = strtolower(date("M", $first_monday_month));
        $resp->week = $week_number;
        return $resp;
    }

    protected function get_sessions_by_hours($user_sessions) {
        $schedules = array();
        foreach($user_sessions as $sessions){
            foreach($sessions as $session){
                $start = (int) $session->start;
                $day = strtolower(date("D", $start));
                $hour = date("G", $start);

                if(!isset($schedules[$day])){
                    $schedules[$day] = array();
                }
                if(!isset($schedules[$day][$hour])){
                    $schedules[$day][$hour] = 1;
                } else {
                    $schedules[$day][$hour]++;
                }
            }
        }
        return $schedules;
    }

    protected function get_sessions_by_hours_summary($schedules) {
        $summary = array();
        if (!empty($schedules)) {
            for ($x = 0; $x <= 6; $x++) {
                $day_code = self::get_day_code($x);
                if (isset($schedules[$day_code])) {
                    $hours = $schedules[$day_code];
                }
                for ($y = 0; $y <= 23; $y++) {
                    $value = 0;
                    if(isset($hours)) {
                        if (isset($hours[$y])) {
                            $value=$hours[$y];
                        }
                    }
                    $element = array(
                        "x" => $x,
                        "y" => $y,
                        "value" => $value,
                    );
                    array_push($summary, $element);
                }
                $hours = null;
            }
        }
        return $summary;
    }

    public function get_inverted_time_summary($inverted_time, $expected_time, $average_time = true){
        $response = new stdClass();
        $response->expected_time = $expected_time;
        $response->expected_time_converted = self::convert_time("hours", $expected_time, "string");
        $response->inverted_time = self::minutes_to_hours($inverted_time->average, -1);
        $response->inverted_time_converted = self::convert_time("hours", $response->inverted_time, "string");

        $inverted_time = new stdClass();
        $inverted_time->name = get_string("fml_inverted_time","local_fliplearning");
        $inverted_time->y = $response->inverted_time;

        $expected_time = new stdClass();
        $expected_time->name = get_string("fml_expected_time","local_fliplearning");
        $expected_time->y = $response->expected_time;

        if (!$average_time) {
            $inverted_time->name = get_string("fml_student_inverted_time","local_fliplearning");
            $expected_time->name = get_string("fml_student_expected_time","local_fliplearning");
        }
        $data[] = $inverted_time;
        $data[] = $expected_time;

        $response->data = $data;
        return $response;
    }

    protected function get_day_code($key) {
        $days = array("mon", "tue", "wed", "thu", "fri", "sat", "sun");
        return $days[$key];
    }

    protected function get_work_sessions($start, $end){
        $conditions = self::conditions_for_work_sessions($start, $end);
        $sessions_users = self::get_sessions_from_logs($conditions);
        return $sessions_users;
    }

    protected function get_sessions_from_logs($conditions){
        $users = array();
        $user_logs = self::get_logs($conditions);
        foreach($user_logs as $userid => $logs){
            $sessions = self::get_sessions($logs);
            $summary = self::calculate_average("duration", $sessions);
            $active_days = self::get_active_days($logs);
            $user = new stdClass();
            $user->userid = $userid;
            $user->count_logs = count($logs);
            $user->active_days = $active_days;
            $user->time_format = "minutes";
            $user->summary = $summary;
            $user->sessions = $sessions;
            $user->logs = $logs;
            $users[] = $user;
        }
        return $users;
    }

    /**
     * Obtiene una lista indexada por el id de usuario que contiene en cada posicion los logs
     * del usuario.
     *
     * @param array $filters lista de condiciones para la busqueda de los logs, en caso de no especificarse,
     *                       se toma como una lista vacía
     *
     * @return array lista de usuarios con sus logs
     */
    protected function get_logs($filters = array()){
        global $DB;
        $users = array();
        $conditions = self::get_query_from_conditions($filters);
        list($in, $invalues) = $DB->get_in_or_equal($this->users);
        $sql = "SELECT * FROM {logstore_standard_log} 
                WHERE courseid = {$this->course->id} {$conditions} AND userid $in ORDER BY timecreated ASC";
        $logs = $DB->get_recordset_sql($sql, $invalues);
        foreach($logs as $key => $log){
            if(!isset($users[$log->userid])){
                $users[$log->userid] = array();
            }
            $users[$log->userid][] = $log;
        }
        $logs->close();
        foreach($this->users as $userid){
            if(!isset($users[$userid])){
                $users[$userid] = array();
            }
        }
        return $users;
    }

    /**
     * Obtiene una cadena de texto que representa una condicion 'where' de busqueda en lenguaje sql
     * cuyos campos se concatenan en base al parámetro $filters con el prefijo $prefix
     *
     * @param array $filters lista de condiciones para la cadena de texto que representa la condicion
     * @param string $prefix prefijo con el que se une cada condicion de la variable $filters. Si se
     *                       omite, por defecto toma el valor de and
     *
     * @return string cadena de texto que representa una condicional 'where' el lenguaje sql
     */
    private function get_query_from_conditions($filters = array(), $prefix = "and"){
        $conditions = "";
        foreach($filters as $filter){
            $operator = isset($filter->operator) ? $filter->operator : "=";
            $conditions .= " {$prefix} {$filter->field} {$operator} '{$filter->value}' ";
        }
        return $conditions;
    }

    private function get_sessions($logs){
        $sessions = array();
        if(count($logs) == 0){
            return $sessions;
        }
        $session = new stdClass();
        $session->duration = 0;
        $session->start = $logs[0]->timecreated;
        $session->end = null;;
        $previous = $logs[0];
        foreach($logs as $key => $log){
            $time_difference = self::diff_in_minutes($log->timecreated, $previous->timecreated);
            if($time_difference >= self::MINUTES_TO_NEW_SESSION){
                $session->end = $previous->timecreated;
                $session->duration = self::diff_in_minutes($session->end, $session->start);
                $sessions[] = $session;

                $session = new stdClass();
                $session->duration = 0;
                $session->start = $log->timecreated;
                $session->end = null;
            }
            $previous = $log;
        }
        if(!isset($session->end)){
            $session->end = $previous->timecreated;
            $time_difference = self::diff_in_minutes($session->end, $session->start);
            $session->duration = $time_difference;
            $sessions[] = $session;
        }
        return $sessions;
    }

    private function diff_in_minutes($timestamp1, $timestamp2){
        if(gettype($timestamp1) == "string"){
            $timestamp1 = (int) $timestamp1;
        }
        if(gettype($timestamp2) == "string"){
            $timestamp2 = (int) $timestamp2;
        }
        $interval = ($timestamp1 - $timestamp2) / 60;
        return $interval;
    }

    protected function calculate_average($field , $values, $consider_zero_elements = true){
        $counter = 0;
        $total = 0;
        foreach($values as $value){
            if(gettype($value) == "object"){
                if(isset($value->$field)){
                    if(!$consider_zero_elements && $value->$field == 0){
                        continue;
                    }
                    $counter++;
                    $total += $value->$field;
                }
            }elseif(gettype($value) == "array"){
                if(isset($value[$field])){
                    if(!$consider_zero_elements && $value[$field] == 0){
                        continue;
                    }
                    $counter++;
                    $total += $value[$field];
                }
            }
        }

        $average = $counter > 0 ? ($total / $counter) : 0;
        $result = new stdClass();
        $result->count = $counter;
        $result->added = $total;
        $result->average = $average;
        return $result;
    }

    private function get_active_days($logs){
        $days_count = 0;
        if(count($logs) == 0){
            return $days_count;
        }
        $days = array();
        foreach($logs as $key => $log){
            $year = date("Y", $log->timecreated);
            $month = date("m", $log->timecreated);
            $day = date("d", $log->timecreated);
            $label = $year.$month.$day;
            if (!isset($days[$label])) {
                $days[$label] = 1;
            }
        }
        $days_count = count($days);
        return $days_count;
    }

    protected function get_users_course_grade($users) {
        global $DB;
        $item = $DB->get_record('grade_items',
            array('courseid' => $this->course->id, 'itemtype' => 'course'), 'id, courseid, grademax', MUST_EXIST);
        $sql = "SELECT id, userid, rawgrademax, finalgrade FROM {grade_grades} 
                WHERE itemid = {$item->id} AND finalgrade IS NOT NULL";
        $rows = $DB->get_records_sql($sql);
        $grades = array();
        foreach ($rows as $row) {
            $grades[$row->userid] = $row;
        }

        foreach ($users as $user) {
            $grade = new stdClass();
            $grade->finalgrade = 0;
            $grade->maxgrade = $item->grademax;
            if (isset($grades[$user->id])) {
                $grade->finalgrade = $grades[$user->id]->finalgrade;
            }
            $user->coursegrade = $grade;
        }
        return $users;
    }

    protected function get_users_items_grades($users) {
        global $DB;
        $items = $this->get_grade_items();
        $items = $this->format_items($items);
        $items = $this->set_average_max_min_grade($items, $users);

        $itemsids = $this->extract_elements_field($items, 'id');
        list($in, $invalues) = $DB->get_in_or_equal($itemsids);
        $sql = "SELECT id, itemid, userid, finalgrade FROM {grade_grades} 
                WHERE itemid $in AND finalgrade IS NOT NULL ORDER BY itemid, userid";
        $rows = $DB->get_recordset_sql($sql, $invalues);

        $itemsgraded = array();
        foreach($rows as $row){
            $itemsgraded[$row->itemid][$row->userid] = $row;
        }
        $rows->close();

        foreach ($users as $user) {
            $useritems = array();
            foreach ($items as $item) {
                $useritem = new stdClass();
                $useritem->average = $item->average;
                $useritem->average_percentage = $item->average_percentage;
                $useritem->categoryid = $item->categoryid;
                $useritem->coursemoduleid = $item->coursemoduleid;
                $useritem->finalgrade = 0;
                $useritem->gradecount = $item->gradecount;
                $useritem->grademax = $item->grademax;
                $useritem->grademin = $item->grademin;
                $useritem->id = $item->id;
                $useritem->iteminstance = $item->iteminstance;
                $useritem->itemmodule = $item->itemmodule;
                $useritem->itemname = $item->itemname;
                $useritem->maxrating = $item->maxrating;
                $useritem->minrating = $item->minrating;
                if (isset($itemsgraded[$item->id][$user->id])) {
                    $useritem->finalgrade = $itemsgraded[$item->id][$user->id]->finalgrade;
                }
                array_push($useritems, $useritem);
            }
            $user->gradeitems = $useritems;
        }
        return $users;
    }

    protected function get_grade_categories () {
        global $DB;
        $sql = "SELECT * FROM {grade_categories} WHERE courseid = {$this->course->id} ORDER BY path";
        $result = $DB->get_records_sql($sql);
        $result = array_values($result);
        return $result;
    }

    protected function get_grade_items () {
        global $DB;
        $sql = "SELECT * FROM {grade_items} WHERE courseid = {$this->course->id} AND itemtype = 'mod' and gradetype = 1";
        $result = $DB->get_records_sql($sql);
        $result = array_values($result);
        return $result;
    }

    protected function format_items ($items) {
        $response = array();
        foreach ($items as $item) {
            $format_item = new stdClass();
            $format_item->id = (int) $item->id;
            $format_item->categoryid = (int) $item->categoryid;
            $format_item->itemname = $item->itemname;
            $format_item->itemmodule = $item->itemmodule;
            $format_item->iteminstance = (int) $item->iteminstance;
            $format_item->grademax = (int) $item->grademax;
            $format_item->grademin = (int) $item->grademin;
            $coursemoduleid = $this->get_course_module_id($item);
            $format_item->coursemoduleid = $coursemoduleid;
            array_push($response, $format_item);
        }
        return $response;
    }

    protected function get_course_module_id($item) {
        global $DB;
        $coursemoduleid = false;
        if (isset($item->itemmodule)) {
            $result = $DB->get_record('modules', array('name' => $item->itemmodule), 'id', MUST_EXIST);
            $moduleid =  $result->id;
            $result = $DB->get_record('course_modules',
                array('course' => $this->course->id, 'module' => $moduleid, 'instance' => $item->iteminstance),
                'id', MUST_EXIST);
            $coursemoduleid = (int) $result->id;
        }
        return $coursemoduleid;
    }

    protected function set_average_max_min_grade ($items, $users) {
        foreach ($items as $item) {
            $result = $this->get_average_max_min_grade($item->id);
            $grades = $this->get_item_grades($item->id, $users);
            $item->average_percentage = $this->convert_value_to_percentage($result->avg, $item->grademax);
            $item->average = $result->avg;
            $item->maxrating = $result->max;
            $item->minrating = $result->min;
            $item->gradecount = (int) $result->count;
            $item->grades = $grades;
        }
        return $items;
    }

    private function get_item_grades($itemid, $users) {
        global $DB;
        list($in, $invalues) = $DB->get_in_or_equal($this->users);
        $sql = "SELECT id, rawgrade, rawgrademax, rawgrademin, userid FROM {grade_grades} 
                WHERE itemid = {$itemid} AND rawgrade IS NOT NULL AND userid {$in}";
        $grades = $DB->get_records_sql($sql, $invalues);
        $grades = array_values($grades);
        foreach ($grades as $grade) {
            $grade->rawgrade = (int) $grade->rawgrade;
            $grade->rawgrademax = (int) $grade->rawgrademax;
            $grade->rawgrademin = (int) $grade->rawgrademin;
            $grade->userid = (int) $grade->userid;
            if (isset($users[$grade->userid])) {
                $grade->user = $users[$grade->userid];
            }
        }
        return $grades;
    }

    private function convert_value_to_percentage($value, $maxvalue) {
        $percentage = 0;
        if ($maxvalue > 0) {
            $percentage = ($value * 100)/$maxvalue;
        }
        return $percentage;
    }

    private function get_average_max_min_grade($itemid) {
        global $DB;
        list($in, $invalues) = $DB->get_in_or_equal($this->users);
        $sql = "SELECT COUNT(*) as count, MAX(rawgrade) as max, MIN(rawgrade) as min, AVG(rawgrade) as avg
                FROM {grade_grades} WHERE itemid = {$itemid} AND rawgrade IS NOT NULL AND userid {$in}";
        $result = $DB->get_records_sql($sql, $invalues);
        $result = array_values($result);
        return $result[0];
    }

    public function get_chart_langs() {
        $langs = array(
            "loading" => get_string("chart_loading", "local_fliplearning"),
            "exportButtonTitle" => get_string("chart_exportButtonTitle", "local_fliplearning"),
            "printButtonTitle" => get_string("chart_printButtonTitle", "local_fliplearning"),
            "rangeSelectorFrom" => get_string("chart_rangeSelectorFrom", "local_fliplearning"),
            "rangeSelectorTo" => get_string("chart_rangeSelectorTo", "local_fliplearning"),
            "rangeSelectorZoom" => get_string("chart_rangeSelectorZoom", "local_fliplearning"),
            "downloadPNG" => get_string("chart_downloadPNG", "local_fliplearning"),
            "downloadJPEG" => get_string("chart_downloadJPEG", "local_fliplearning"),
            "downloadPDF" => get_string("chart_downloadPDF", "local_fliplearning"),
            "downloadSVG" => get_string("chart_downloadSVG", "local_fliplearning"),
            "downloadCSV" => get_string("chart_downloadCSV", "local_fliplearning"),
            "downloadXLS" => get_string("chart_downloadXLS", "local_fliplearning"),
            "exitFullscreen" => get_string("chart_exitFullscreen", "local_fliplearning"),
            "hideData" => get_string("chart_hideData", "local_fliplearning"),
            "noData" => get_string("chart_noData", "local_fliplearning"),
            "printChart" => get_string("chart_printChart", "local_fliplearning"),
            "viewData" => get_string("chart_viewData", "local_fliplearning"),
            "viewFullscreen" => get_string("chart_viewFullscreen", "local_fliplearning"),
            "resetZoom" => get_string("chart_resetZoom", "local_fliplearning"),
            "resetZoomTitle" => get_string("chart_resetZoomTitle", "local_fliplearning"),
            "months" => array(
                get_string("fml_jan", "local_fliplearning"),
                get_string("fml_feb", "local_fliplearning"),
                get_string("fml_mar", "local_fliplearning"),
                get_string("fml_apr", "local_fliplearning"),
                get_string("fml_may", "local_fliplearning"),
                get_string("fml_jun", "local_fliplearning"),
                get_string("fml_jul", "local_fliplearning"),
                get_string("fml_aug", "local_fliplearning"),
                get_string("fml_sep", "local_fliplearning"),
                get_string("fml_oct", "local_fliplearning"),
                get_string("fml_nov", "local_fliplearning"),
                get_string("fml_dec", "local_fliplearning"),
            ),
            "shortMonths" => array(
                get_string("fml_jan_short", "local_fliplearning"),
                get_string("fml_feb_short", "local_fliplearning"),
                get_string("fml_mar_short", "local_fliplearning"),
                get_string("fml_apr_short", "local_fliplearning"),
                get_string("fml_may_short", "local_fliplearning"),
                get_string("fml_jun_short", "local_fliplearning"),
                get_string("fml_jul_short", "local_fliplearning"),
                get_string("fml_aug_short", "local_fliplearning"),
                get_string("fml_sep_short", "local_fliplearning"),
                get_string("fml_oct_short", "local_fliplearning"),
                get_string("fml_nov_short", "local_fliplearning"),
                get_string("fml_dec_short", "local_fliplearning"),
            ),
            "weekdays" => array(
                get_string("fml_sun", "local_fliplearning"),
                get_string("fml_mon", "local_fliplearning"),
                get_string("fml_tue", "local_fliplearning"),
                get_string("fml_wed", "local_fliplearning"),
                get_string("fml_thu", "local_fliplearning"),
                get_string("fml_fri", "local_fliplearning"),
                get_string("fml_sat", "local_fliplearning"),
            ),
            "shortWeekdays" => array(
                get_string("fml_sun_short", "local_fliplearning"),
                get_string("fml_mon_short", "local_fliplearning"),
                get_string("fml_tue_short", "local_fliplearning"),
                get_string("fml_wed_short", "local_fliplearning"),
                get_string("fml_thu_short", "local_fliplearning"),
                get_string("fml_fri_short", "local_fliplearning"),
                get_string("fml_sat_short", "local_fliplearning"),
            ),
        );
        return $langs;
    }
}