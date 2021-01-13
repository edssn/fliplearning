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
    public function get_student_ids(){
        global $DB;
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
        $students = self::filter_users_by_selected_group($students);
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
}