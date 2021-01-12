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

    /**
     * Obtiene un objeto con los datos para la visualizacion del gráfico
     * sesiones de estudiantes
     *
     * @param string $weekcode identificador de la semana de la que se debe obtener las semanas
     *                         si no se especifica, se toma la semana configurada como la actual
     *
     * @return object objeto con los datos para la visualizacion
     */
    public function get_week_sessions($weekcode = null){
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

        $work_sessions = self::work_sessions($week->weekcode);
        $work_sessions = array_map(function($user_sessions){ return $user_sessions->sessions;}, $work_sessions);
        $response = self::get_sessions_schedules_summary($work_sessions);
        return $response;
    }

    public function render_has(){
        return $this->profile;
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
     * Busca la semana con codigo igal al parametro $weekcode y lo retorna. En caso de no encontrar
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

    protected function work_sessions($weekcode = null){
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
        $conditions = self::conditions_for_work_sessions($week->weekstart, $week->weekend);
        $sessions_users = self::get_session_from_logs($conditions);
        return $sessions_users;
    }

    /**
     * Obtiene un objeto con las condiciones de busqueda para obtener los logs de interacciones en
     * la semana configurada
     *
     * @param int $start cantidad de segundos desde de la fecha que representa el inicio de la semana
     * @param int $end cantidad de segundos desde de la fecha que representa el fin de la semana
     *
     * @return object objecto con las condiciones de busqueda para los logs de interacciones
     */
    private function conditions_for_work_sessions($start, $end){
        $conditions = array();
        $condition = new stdClass();
        $condition->field = "timecreated";
        $condition->value = $start;
        $condition->operator = ">=";
        $conditions[] = $condition;

        $condition = new stdClass();
        $condition->field = "timecreated";
        $condition->value = $end;
        $condition->operator = "<=";
        $conditions[] = $condition;
        return $conditions;
    }

    private function get_session_from_logs($conditions){
        $users = array();
        $user_logs = self::get_logs($conditions);
        foreach($user_logs as $userid => $logs){
            $sessions = self::get_sessions($logs);
            $summary = self::calculate_average("duration", $sessions);
            $user = new stdClass();
            $user->userid = $userid;
            $user->count_logs = count($logs);
            $user->time_format = "minuts";
            $user->summary = $summary;
            $user->sessions = $sessions;
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
        $sql = "select * from {logstore_standard_log} where courseid = {$this->course->id} {$conditions} AND userid $in order by timecreated asc";
        $logs = $DB->get_recordset_sql($sql, $invalues);
        foreach($logs as $key => $log){
            if(isset($users[$log->userid])){
                $users[$log->userid][] = $log;
            }else{
                $users[$log->userid] = array();
                $users[$log->userid][] = $log;
            }
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
        /*
          When there is no other record to finish the current one, we define the current time,
          if it is longer than the session delimiter, we assign the session delimiter as maximum
        */
        if(!isset($session->end)){
            $session->end = $previous->timecreated;
            $time_difference = self::diff_in_minutes($session->end, $session->start);
            $session->duration = $time_difference;
            $sessions[] = $session;
        }
        return $sessions;
    }

    protected function diff_in_minutes($timestamp1, $timestamp2){
        if(gettype($timestamp1) == "string"){
            $timestamp1 = (int) $timestamp1;
        }
        if(gettype($timestamp2) == "string"){
            $timestamp2 = (int) $timestamp2;
        }
        $interval = ($timestamp1 - $timestamp2) / 60;
        return $interval;
    }

    protected function get_end_by_time_limit($startsession){
        $tz = self::get_timezone();
        date_default_timezone_set($tz);
        $end = 0;
        $now = time();
        $time_difference = self::diff_in_minutes($now, $startsession);
        if($time_difference >= self::MINUTES_TO_NEW_SESSION){
            $time_to_add = "+" . self::MINUTES_TO_NEW_SESSION . "minutes";
            $end = strtotime($time_to_add, (int) $startsession);
        }else{
            $end = $now;
        }
        return $end;
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

    private function get_sessions_schedules_summary($user_sessions) {
        $schedules = array();
        foreach($user_sessions as $sessions){
            foreach($sessions as $session){
                $day = strtolower(date("D", (int) $session->start));
                $hour = (int) date("H", (int) $session->end);

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

    private function get_day_code($key) {
        $days = array("mon", "tue", "wed", "thu", "fri", "dat", "sun");
        return $days[$key];

    }

}