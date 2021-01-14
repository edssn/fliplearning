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

class teacher extends report {

    function __construct($courseid, $userid){
        parent::__construct($courseid, $userid);
        self::set_profile();
        self::set_users();
    }

    /**
     * Almacena el perfil de visualización de la clase en la variable $profile de clase
     */
    public function set_profile(){
        $this->profile = "teacher";
    }

    /**
     * Almacena los ids de los estudiantes en la variable $users de la clase
     */
    public function set_users(){
        $this->users = self::get_student_ids();
        return $this->users;
    }

    /**
     * Obtiene un objeto con los datos para la visualizacion del gráfico
     * sesiones de estudiantes
     *
     * @param string $weekcode identificador de la semana de la que se debe obtener las semanas
     *                         si no se especifica, se toma la semana configurada como la actual
     *
     * @return object objeto con los datos para la visualizacion
     */
    public function hours_sessions($weekcode = null){
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
        $work_sessions = array_map(function($user_sessions){ return $user_sessions->sessions;}, $work_sessions);
        $sessions = self::get_sessions_by_hours($work_sessions);
        $response = self::get_sessions_by_hours_summary($sessions);
        return $response;
    }

    private function get_sessions_by_hours($user_sessions) {
        $schedules = array();
        foreach($user_sessions as $sessions){
            foreach($sessions as $session){
                $day = strtolower(date("D", (int) $session->start));
                $hour = date("G", (int) $session->end);

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

    private function get_sessions_by_hours_summary($schedules) {
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

    public function weeks_sessions(){
        if(!self::course_in_transit()){
            return null;
        }
        if(!self::course_has_users()){
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
        $work_sessions = self::get_work_sessions($start, $end);
        $work_sessions = array_map(function($user_sessions){ return $user_sessions->sessions;}, $work_sessions);
        $months = self::get_sessions_by_weeks($work_sessions);
        $response = self::get_sessions_by_weeks_summary($months);
        return $response;
    }

    private function get_sessions_by_weeks($user_sessions) {
        $months = array();
        foreach($user_sessions as $sessions){
            foreach($sessions as $session){
                $month = strtolower(date("M", (int) $session->start));
                $week = self::get_week_number($session->end);

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

    private function get_sessions_by_weeks_summary($months) {
        $summary = array();
        if (!empty($months)) {
            for ($y = 0; $y <= 11; $y++) {
                $month_code = self::get_month_code($y);
                if (isset($months[$month_code])) {
                    $weeks = $months[$month_code];
                }
                for ($x = 0; $x <= 5; $x++) {
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
            }
        }
        return $summary;
    }

    private function get_month_code($key) {
        $months = array("jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec");
        return $months[$key];
    }

    private function get_week_number($date) {
        $day = date('j', $date);
        $time = date("c", $date);
        $first_sunday = date('j', strtotime("first sunday of this month", $date));
        $week_number = 0;
        while ($first_sunday < $day) {
            $first_sunday+=7;
            $week_number++;
        }
        return $week_number;
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

    protected function get_work_sessions($start, $end){
        $conditions = self::conditions_for_work_sessions($start, $end);
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
        if (isset($start)) {
            $condition = new stdClass();
            $condition->field = "timecreated";
            $condition->value = $start;
            $condition->operator = ">=";
            $conditions[] = $condition;
        }
        if (isset($start) && isset($end)) {
            $condition = new stdClass();
            $condition->field = "timecreated";
            $condition->value = $end;
            $condition->operator = "<=";
            $conditions[] = $condition;
        }
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
            $user->time_format = "minutes";
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

    public function progress_table(){
        if(!self::course_in_transit()){
            return null;
        }
        if(!self::course_has_users()){
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

        $work_sessions = self::get_work_sessions($start, $end);
        $all_course_modules = self::get_course_modules();
        $visible_modules = array_filter($all_course_modules, function($module){ return $module['visible'] == 1;});
//        $visible_modules_ids = self::extract_ids($visible_modules);
        $table = self::get_course_modules_completion($work_sessions, $visible_modules, $enable_completion);
        return $table;
    }

    private function get_course_modules_completion($users_sessions, $course_modules, $enable_completion) {
        $table = array();
        $total_cms = count($course_modules);
        if ($total_cms > 0) {
            foreach ($users_sessions as $index => $user) {
                $complete_cms = self::count_complete_course_module($course_modules, $user->userid, $enable_completion);
                $progress_percentage = (int)(($complete_cms * 100)/$total_cms);
                $inverted_time_label = self::convert_time($user->time_format, $user->summary->added);
                $user_record = self::get_user($user->userid);

                $record = new stdClass();
                $record->id = $user_record->id;
                $record->firstname = $user_record->firstname;
                $record->lastname = $user_record->lastname;
                $record->email = $user_record->email;
                $record->progress_percentage = $progress_percentage;
                $record->total_cms = $total_cms;
                $record->complete_cms = $complete_cms;
                $record->sessions = $user->summary->count;
                $record->inverted_time = $user->summary->added;
                $record->inverted_time_label = $inverted_time_label;

                array_push($table, $record);
            }
        }
        return $table;

    }

    private function count_complete_course_module($course_modules, $userid, $enable_completion){
        $complete_cms = 0;
        foreach ($course_modules as $module) {
            if ($enable_completion) {
                $module_completion_configure = $module['completion'] != 0;
                if ($module_completion_configure) {
                    $finished = self::get_finished_course_module_by_conditions($userid, $module['id']);
                    if ($finished) {
                        $complete_cms++;
                    }
                } else {
                    $finished = self::get_finished_course_module_by_view($userid, $module['id']);
                    if ($finished) {
                        $complete_cms++;
                    }
                }
            } else {
                $finished = self::get_finished_course_module_by_view($userid, $module['id']);
                if ($finished) {
                    $complete_cms++;
                }
            }
        }
        return $complete_cms;
    }

    private function get_finished_course_module_by_view($userid, $cm_id){
        global $DB;
        $complete = false;
        $sql = "select id from {logstore_standard_log} where courseid = {$this->course->id} AND userid = {$userid} AND contextinstanceid = {$cm_id}";
        $logs = $DB->get_records_sql($sql);
        if (isset($logs) && count($logs)>0) {
            $complete = true;
        }
        return $complete;
    }

    private function get_finished_course_module_by_conditions($userid, $cm_id){
        global $DB;
        $complete = false;
        $sql = "select id from {course_modules_completion} where coursemoduleid = {$cm_id} AND userid = {$userid}";
        $logs = $DB->get_records_sql($sql);
        if (isset($logs) && count($logs)>0) {
            $complete = true;
        }
        return $complete;
    }

}