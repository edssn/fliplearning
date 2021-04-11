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
 * Trait con funciones comunes para todas las clases
 * Las clases que usen este trait requieren de una propiedad $course y $user con el objeto respectivo
 *
 * @package     local_fliplearning
 * @autor       Edisson Sigua, Bryan Aguilar
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');

trait lib_trait {

    /**
     * Obtiene el registro de un curso en base al parámetro $course.
     *
     * Si el parámetro $course no es string ni entero, se retorma el mismo valor del
     * parámetro recibido
     *
     * @param string $course id del curso que se desea buscar en formato string, entero u objeto
     *
     * @return mixed un objeto fieldset que contiene el primer registro que hace match a la consulta
     */
    public function get_course($course){
        if(gettype($course) == "string"){
            $course = (int) $course;
        }
        if(gettype($course) == "integer"){
            $course = self::get_course_from_id($course);
        }
        return $course;
    }

    /**
     * Obtiene el registro de un curso dado su id
     *
     * @param int $courseid id del curso a obtener
     *
     * @return mixed un objeto fieldset que contiene el primer registro que hace match a la consulta
     */
    public static function get_course_from_id($courseid){
        global $DB;
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        return $course;
    }

    /**
     * Obtiene el registro de un usuario en base al parámetro $user.
     *
     * Si el parámetro $user no es string ni entero, se retorma el mismo valor del
     * parámetro recibido
     *
     * @param string $user id del curso que se desea buscar en formato string, entero u objeto
     *
     * @return mixed un objeto fieldset que contiene el primer registro que hace match a la consulta
     */
    public function get_user($user){
        if(gettype($user) == "string"){
            $user = (int) $user;
        }
        if(gettype($user) == "integer"){
            $user = self::get_user_from_id($user);
        }
        return $user;
    }

    /**
     * Obtiene el registro de un usuario dado su id
     *
     * @param int $userid id del usuario a obtener
     *
     * @return mixed un objeto fieldset que contiene el primer registro que hace match a la consulta
     */
    public static function get_user_from_id($userid){
        global $DB;
        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        return $user;
    }

    protected function get_full_users(){
        global $DB;
        $users = [];
        list($in, $invalues) = $DB->get_in_or_equal($this->users);
        $fields = self::USER_FIELDS;
        $sql = "SELECT $fields FROM {user} WHERE id $in ORDER BY lastname ASC";
        $rows = $DB->get_recordset_sql($sql, $invalues);
        foreach($rows as $key => $row){
            $users[$row->id] = $row;
        }
        $rows->close();
        return $users;
    }

    protected function get_users_last_access(){
        global $DB;
        $user_access = [];
        list($in, $invalues) = $DB->get_in_or_equal($this->users);
        $sql = "SELECT *  FROM {user_lastaccess} WHERE courseid = {$this->course->id} AND userid $in";
        $rows = $DB->get_recordset_sql($sql, $invalues);
        foreach($rows as $row){
            $user_access[$row->userid] = $row;
        }
        $rows->close();
        return $user_access;
    }

    /**
     * Obtiene un conjunto de campos (sectionid, section, name, visibility, availability) de las secciones del
     * curso almacenado en la variable $course de esta clase
     *
     * @return array con las secciones del curso
     */
    public function get_course_sections(){
        $modinfo  = get_fast_modinfo($this->course->id);
        $sections = $modinfo->get_section_info_all();
        $sections = self::format_sections($sections);
        return $sections;
    }

    /**
     * Obtiene ciertos un conjunto menor de campos (sectionid, section, name, visibility, availability) de cada
     * seccion en un vector con las secciones de un curso (parámetro $sections)
     *
     * @param array $sections vector con las secciones de un curso
     *
     * @return array un vector que contiene las secciones de un curso con un grupo de campos reducido
     */
    private function format_sections($sections){
        $full_sections = array();
        foreach ($sections as $index => $section){
            $full_section = [
                'sectionid' => $section->id,
                'section' => $section->section,
                'name' => self::get_section_name($section, $index),
                'visible' => $section->visible,
                'availability' =>  $section->availability,
            ];
            $full_sections[] = $full_section;
        }
        return $full_sections;
    }

    /**
     * Verifica que la seccion enviada por parámetro ($section) tenga configurado un nombre. En caso de tenerlo,
     * se retorna tal nombre. En caso de no tenerlo, se configura un nombre genérico y se retorna ese valor.
     *
     * @param $section object objeto que representa una sección de un curso
     * @param $current_index int entero que representa la posicion de la sección en el curso
     *
     * @return string cadena de texto que contiene el nombre de las sección
     */
    private function get_section_name($section, $current_index){
        if(isset($section->name) ){
            return $section->name;
        }
        $build_name = get_string("course_format_{$this->course->format}", 'local_fliplearning');
        $name = "$build_name $current_index";
        return $name;
    }

    public function get_course_modules($include_hidden_cms = false, $formatted = true){
        $modinfo = get_fast_modinfo($this->course->id);
        $modules = $modinfo->get_cms();
        if (!$include_hidden_cms) {
            $modules = array_filter($modules, function($module){ return $module->visible == 1;});
        }
        if ($formatted) {
            $modules = self::format_course_module($modules);
        }
        return $modules;
    }

    public function get_course_module($id){
        $cm = get_course_and_cm_from_cmid($id)[1];
        return $cm;
    }

    private function format_course_module($modules){
        $full_modules = array();
        foreach ($modules as $module){
            $full_module = [
                'id' => $module->id,
                'module' => $module->module,
                'instance' => $module->instance,
                'visible' => $module->visible,
                'modname' =>  $module->modname,
                'module' =>  $module->module,
                'name' =>  $module->name,
                'completion' =>  $module->completion,
                'sectionnum' =>  $module->sectionnum,
                'section' =>  $module->section,
            ];
            $full_modules[] = $full_module;
        }
        return $full_modules;
    }

    protected function get_course_modules_from_sections($sections, $include_hidden_cms = false, $formatted = false){
        $cms = array();
        foreach($sections as $key => $section){
            if($section->visible != 1){
                continue;
            }
            $modules = self::get_sequence_section($section->sectionid);
            $cms = array_merge($cms, $modules);
        }
        if (count($cms)) {
            if (!$include_hidden_cms) {
                $cms = array_filter($cms, function($module){ return ($module && $module->visible == 1);});
            }
            if ($formatted) {
                $cms = self::format_course_module($cms);
            }
        }
        return $cms;
    }

    public function get_sequence_section($sectionid) {
        global $DB;
        $sql =  "select sequence from {course_sections} where id = ?";
        $sequence = $DB->get_record_sql($sql, array($sectionid));
        $course_modules = self::get_course_module_section($sequence->sequence);
        return $course_modules;
    }

    public function get_course_module_section($sequence) {
        $sequence = explode(',', $sequence);
        $course_modules = array();
        foreach ($sequence as $key => $course_module_id) {
            $module = get_coursemodule_from_id( '', $course_module_id, $this->course->id, MUST_EXIST);
            array_push($course_modules, $module);
        }
        return $course_modules;
    }

    /**
     * Retorna un string que representa la fecha ($timestamp) Unix formateada usando el parámetro $format
     * y tomando como referencia la zona horaria obtenida con la función 'get_timezone'
     *
     * @param $format string objeto que representa una sección de un curso
     * @param $timestamp int entero que representa una marca temporal de Unix
     *
     * @return string cadena de texto con la fecha formateada
     */
    public function to_format($format, $timestamp){
        $tz = self::get_timezone();
        date_default_timezone_set($tz);
        if(gettype($timestamp) == "string"){
            $timestamp = (int) $timestamp;
        }
        $date = date($format, $timestamp);
        return $date;
    }

//    protected function course_is_valid(){
//        $in_transit = isset($this->current_week) || isset($this->past_week) ? true : false;
//        $has_users = count($this->users) > 0 ? true : false;
//        return $in_transit && $has_users;
//    }

    /**
     * Retorna un entero que representa la cantidad de segundos desde la Época Unix (January 1 1970 00:00:00 GMT)
     * hasta la fecha actual. La fecha actual se calcula en base a la zona horaria obtenida con la función
     * 'get_timezone'.
     *
     * @return int entero que representa la cantidad de segundos desde la Época Unix hasta la fecha actual
     */
    public function now_timestamp(){
        $tz = self::get_timezone();
        date_default_timezone_set($tz);
        $now = new DateTime();
        $now = $now->format('U');
        return $now;
    }

    /**
     * Retorna un entero que representa la cantidad de segundos desde la Época Unix (January 1 1970 00:00:00 GMT)
     * hasta la fecha enviada por parámetro ($date). La fecha se calcula en base a la zona horaria obtenida con
     * la función 'get_timezone'
     *
     * @param $date string cadena de texto que representa una fecha
     *
     * @return int entero que representa la cantidad de segundos desde la Época Unix hasta la fecha enviada
     * @throws Exception
     */
    public function to_timestamp($date){
        $tz = self::get_timezone();
        date_default_timezone_set($tz);
        $fecha = new DateTime($date);
        $date = $fecha->format('U');
        return $date;
    }

    /**
     * Retorna una cadena de texto con la zona horaria del usuario. En caso de que el usuario no tenga una
     * zona horaria configurada, se retorna la del servidor.
     *
     * @return string cadena de texto con una zona horaria
     */
    public function get_timezone(){
        $timezone = usertimezone($this->user->timezone);
        $timezone = self::accent_remover($timezone);
        if(!self::is_valid_timezone($timezone)){
            $timezone = self::get_server_timezone();
        }
        return $timezone;
    }

    public function get_server_timezone(){
        $date = new DateTime();
        $timeZone = $date->getTimezone();
        return $timeZone->getName();
    }

    /**
     * Reemplaza los acentos de una cadena de texto que contiene una zona horaria
     *
     * @param $cadena string cadena de texto que representa una zona horaria
     *
     * @return string cadena de texto con una zona horaria sin acentos
     */
    public function accent_remover($cadena){
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );
        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena );
        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena );
        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena );
        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena );
        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $cadena
        );
        return $cadena;
    }

    /**
     * Verifica si una cadena con una zona horaria es válida comparandola con una lista de zonas
     * horarias válidas obtenidas del sistema
     *
     * @param $timezone string cadena de texto que representa una zona horaria
     *
     * @return boolean valor booleano que representa si la zona horaria es válida
     */
    public function is_valid_timezone($timezone) {
        return in_array($timezone, timezone_identifiers_list());
    }

    /**
     * Obtiene los ids de todos los usuarios con rol estudiante en el contexto
     *
     * @return array lista con todos los ids de los estudiantes
     */
    public function get_student_ids($filtered_with_groups = true){
        $roles = array(5);
        $students = array();
        $users = array();
        $context = context_course::instance($this->course->id);
        foreach($roles as $role){
            $users = array_merge($users, get_role_users($role, $context));
        }
        foreach($users as $user){
            if(!in_array($user->id, $students)){
                $students[] = $user->id;
            }
        }
        if ($filtered_with_groups) {
            $students = self::filter_users_by_selected_group($students);
        }
        return $students;
    }

    protected function filter_users_by_selected_group($users) {
        global $COURSE, $USER;
        $group_manager = new \local_fliplearning\group_manager($COURSE, $USER);
        $participants = new \local_fliplearning\course_participant($USER->id, $COURSE->id);
        $groups = $participants->all_groups_with_members($COURSE->groupmode);
        $selectedgroup = $group_manager->selected_group();
        if(!isset($selectedgroup->groupid) || $selectedgroup->groupid == 0 ){
            return $users;
        }
        foreach ($groups as $group) {
            if($selectedgroup->groupid == $group->id){
                $users = self::extract_users_in_group($users, $group->members);
            }
        }
        return $users;
    }

    private function extract_users_in_group($allusers, $ingroup){
        $extracted = array();
        foreach($allusers as $userid){
            if(isset($ingroup[$userid]) && !in_array($userid, $extracted)){
                array_push($extracted, $userid);
            }
        }
        return $extracted;
    }

    protected function get_users_from_ids($ids){
        global $DB;
        list($in, $invalues) = $DB->get_in_or_equal($ids);
        $fields = self::USER_FIELDS;
        $sql = "select $fields from {user} where id $in order by lastname asc";
        $rows = $DB->get_records_sql($sql, $invalues);
        $users = array_values($rows);
        return $users;
    }

    public function extract_ids ($elements){
        $ids = array();
        if(gettype($elements) == 'array' && count($elements)>0){
            foreach($elements as $key => $element){
                if(gettype($element) == "array"){
                    if(isset($element['id'])){
                        $ids[] = $element['id'];
                    }
                }elseif(gettype($element) == "object"){
                    if(isset($element->id)){
                        $ids[] = $element->id;
                    }
                }
            }
        }
        return $ids;
    }

    public function extract_elements_field($elements, $field){
        $list = array();
        if(gettype($elements) == 'array'){
            foreach($elements as $key => $element){
                if(gettype($element) == "array"){
                    if(isset($element[$field])){
                        $list[] = $element[$field];
                    }
                }elseif(gettype($element) == "object"){
                    if(isset($element->$field)){
                        $list[] = $element->$field;
                    }
                }
            }
        }
        return $list;
    }

    public function convert_time($measure, $time, $type = "hour"){
        $response = false;
        $valid_params = true;
        if ($measure == 'minutes') {
            $time = $time * 60;
        } elseif ($measure == 'hours') {
            $time = $time * 3600;
        } else {
            $valid_params = false;
        }
        if($valid_params){
            $horas = floor($time / 3600);
            $minutos = floor(($time % 3600) / 60);
            $segundos = $time % 60;
            if ($type == "hour") {
                $response = self::convert_time_as_hour($horas, $minutos, $segundos);
            } else {
                $response = self::convert_time_as_string($horas, $minutos, $segundos);
            }
        }
        return $response;
    }

    protected function convert_time_as_string($hours, $minutes, $seconds = null){
        $text = [
            'minute' => get_string("fml_minute", "local_fliplearning"),
            'minutes' => get_string("fml_minutes", "local_fliplearning"),
            'hour' => get_string("fml_hour", "local_fliplearning"),
            'hours' => get_string("fml_hours", "local_fliplearning"),
            'second' => get_string("fml_second", "local_fliplearning"),
            'seconds' => get_string("fml_seconds", "local_fliplearning")
        ];
        $hour = new stdClass();
        $hour->text = $hours == 1 ? $text['hour'] : $text['hours'];
        $hour->stringify_value = $hours <= 9 ? "0$hours" : $hours ;
        $hour->output = $hours == 0 ? "" : "$hour->stringify_value $hour->text";

        $minute = new stdClass();
        $minute->text = $minutes == 1 ? $text['minute'] : $text['minutes'];
        $minute->stringify_value = $minutes <= 9 ? "0$minutes" : $minutes;
        $minute->output = $minutes == 0 ? "" : "$minute->stringify_value $minute->text";
        $response = "$hour->output $minute->output";

        $hidde_seconds = ($minutes > 0 && $seconds == 0) || ($hours > 0);

        $second = new stdClass();
        $second->text = $seconds == 1 ? $text['second'] : $text['seconds'];
        $second->stringify_value = $seconds <= 9 ? "0$seconds" : $seconds;
        $second->output = $hidde_seconds ? "" : "$second->stringify_value $second->text";

        $response = "$hour->output $minute->output $second->output";
        $response = trim($response);
        return $response;
    }

    protected function convert_time_as_hour($hours, $minutes, $seconds = null){
        $hour = $hours <= 9 ? "0$hours" : $hours ;
        $minute = $minutes <= 9 ? "0$minutes" : $minutes;
        $second = $seconds <= 9 ? "0$seconds" : $seconds;
        $response = "$hour:$minute:$second";
        $response = trim($response);
        return $response;
    }

    public function minutes_to_hours($minutes, $decimals = 2){
        $hours = 0;
        if($minutes <= 0){
            return $hours;
        }else{
            if($decimals > 0){
                $hours = number_format($minutes / 60, 2);
            }else{
                $hours = $minutes / 60;
            }
        }
        return $hours;
    }

    protected function get_date_label($date) {
        $date = (int) $date;
        $tz = self::get_timezone();
        date_default_timezone_set($tz);
        $day_number = date('d', $date);
        $day_code = strtolower(date('D',$date));
        $day_name = get_string("fml_{$day_code}_short", 'local_fliplearning');
        $month_code = strtolower(date('M',$date));
        $month_name = get_string("fml_{$month_code}_short", 'local_fliplearning');
        $year = date('Y', $date);
        $hour = date('g', $date);
        $min = date('i', $date);
        $format = date('A', $date);
        $label = "$day_name, $month_name $day_number $year, $hour:$min $format";
        return $label;
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
    protected function conditions_for_work_sessions($start, $end){
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

    protected function get_query_from_conditions($filters = array(), $prefix = "and"){
        $conditions = "";
        foreach($filters as $filter){
            $operator = isset($filter->operator) ? $filter->operator : "=";
            $conditions .= " {$prefix} {$filter->field} {$operator} '{$filter->value}' ";
        }
        return $conditions;
    }

    protected function get_sessions($logs){
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

    protected function calculate_average($field, $values, $consider_zero_elements = true){
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

    protected function get_active_days($logs){
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
}