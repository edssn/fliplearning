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
 * Config Weeks
 *
 * @package     local_fliplearning
 * @autor       Edisson Sigua, Bryan Aguilar
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fliplearning;

require_once("lib_trait.php");

use stdClass;

class configweeks {
    use \lib_trait;

    public $course;
    public $user;
    public $weeks;
    public $instance;
    public $current_sections;
    public $startin;

    function __construct($course, $userid){
        $this->course = self::get_course($course);
        $this->user = self::get_user($userid);
        $this->instance = self::last_instance();
        $this->weeks = self::get_weeks();
        $this->current_sections = self::get_course_sections();
        $this->startin = isset($this->weeks[0]) ? $this->weeks[0]->weekstart : 999999999999;
        $this->weeks = self::get_weeks_with_sections();
    }

    /**
     * Obtiene la última instancia configurada para las semanas de Fliplearning. Si aun no se han
     * configurado semanas en un curso, la última instacia es la que crea por defecto el plugin.
     *
     * @return mixed un objeto fieldset que contiene el primer registro que hace match con la consulta
     */
    public function last_instance(){
        global $DB;
        $sql = "select * from {fliplearning_instances} where courseid = ? order by id desc LIMIT 1";
        $instance = $DB->get_record_sql($sql, array($this->course->id));
        if(!isset($instance) || empty($instance)){
            $instance = self::create_instance($this->course->id);
        }
        return $instance;
    }

    /**
     * Crea una nueva instancia para la configuración de semanas de Fliplearning. Esta es la instancia
     * que se crea por defecto.
     *
     * @return mixed un objeto fieldset que contiene el registro creado
     */
    public function create_instance(){
        global $DB;
        $instance = new stdClass();
        $instance->courseid = $this->course->id;
        $instance->year = date("Y");
        $id = $DB->insert_record("fliplearning_instances", $instance, true);
        $instance->id = $id;
        $this->instance = $instance;
        return $instance;
    }

    /**
     * Obtiene las semanas configuradas en Fliplearning en base a los ids de los atributos de clase
     * $course e $intance. Se puede especificar
     *
     * @return array una lista con las semanas configuradas en un curso
     */
    public function get_weeks(){
        global $DB;
        $sql = "SELECT * FROM {fliplearning_weeks} 
                WHERE courseid = ? AND instanceid = ? AND timedeleted IS NULL ORDER BY POSITION ASC";
        $weeks = $DB->get_records_sql($sql, array($this->course->id, $this->instance->id));
        $weeks = array_values($weeks);
        return $weeks;
    }

    /**
     * Agrega el campo sections a la variable de clase semanas para almacenar las secciones asignadas a cada semana.
     *
     * @return array una lista con las semanas del curso y las secciones asignadas a cada una
     * @throws coding_exception | dml_exception
     */
    public function get_weeks_with_sections(){
        $weeks = $this->weeks;
        if(count($weeks) == 0){
            $weeks[] = self::create_first_week();
            $this->weeks = $weeks;
        }
        $course_sections = self::get_course_sections();
        foreach($weeks as $position => $week){
            $week->removable = true;
            if($position == 0){
                $week->removable = false;
            }
            $week->sections = array();
            $week->name = get_string('setweeks_week', 'local_fliplearning');
            if(!isset($week->date_format)) {
                $week->date_format = "Y-m-d";
                $week->weekstartlabel = self::to_format("Y-m-d", $week->weekstart);
                $week->weekendlabel = self::to_format("Y-m-d", $week->weekend);
            }
            $week->weekstart = intval($week->weekstart);
            $week->weekend = intval($week->weekend);
            $week->position = $position;
            $week->delete_confirm = false;
            $sections = self::get_week_sections($week->weekcode);
            foreach($sections as $key => $section){
                $section->name = $section->section_name;
                $section->visible = self::get_current_visibility($section->sectionid);
                $section = self::validate_section($section, $course_sections);
                $week->sections[] = $section;
            }
        }
        return $weeks;
    }

    /**
     * Crea la primera semana (semana por defecto) de Fliplearning en un curso. Esta funcion se ejecuta
     * de manera automática para cada curso.
     *
     * @return stdClass un objeto con la semana creada
     * @throws dml_exception
     */
    private function create_first_week(){
        global $DB;
        $start = strtotime('next monday');
        $end = strtotime('next monday + 6 day') + 86399;
        $week = new stdClass();
        $week->hours_dedications = 0;
        $week->courseid = $this->course->id;
        $week->weekstart = $start;
        $week->weekend = $end;
        $week->position = 0;
        $week->modified_by = $this->user->id;
        $week->created_by = $this->user->id;
        $week->timecreated = self::now_timestamp();
        $week->timemodified = self::now_timestamp();
        $week->weekcode = self::generate_week_code(0);
        $week->instanceid = $this->instance->id;
        $id = $DB->insert_record("fliplearning_weeks", $week, true);
        $week->id = $id;
        return $week;
    }

    /**
     * Genera un código de identificación para una semana de Fliplearning. El código se forma
     * en base al año actual, id de la instancia de Fliplearning en el curso y posición de la semana
     *
     * @return int entero que representa el identificador de la semana
     */
    private function generate_week_code($weekposition){
        $code = $this->instance->year . $this->instance->id . $this->course->id . $weekposition;
        $code = (int) $code;
        return $code;
    }

    /**
     * Obtiene las secciones de una la semana identificada por el parámetro $weekcode
     *
     * @param string $weekcode identificador de la semana de la que se debe obtener las semanas
     *
     * @return array lista con las secciones asignadas a la semana
     */
    public function get_week_sections ($weekcode){
        global $DB;
        $sql = "select * from {fliplearning_sections} where weekcode = ? and timedeleted IS NULL order by position asc";
        $week_sections = $DB->get_records_sql($sql, array($weekcode));
        return $week_sections;
    }

    /**
     * Devuelve un valor booleano que representa si la semana es visible o no
     *
     * @param int $sectionid id de la sección
     *
     * @return boolean valor booleano que representa si la sección es visible o no. Retorna null en caso de
     *                 no encontrar la sección
     */
    private function get_current_visibility($sectionid){
        foreach($this->current_sections as $section){
            if($section['sectionid'] == $sectionid){
                return $section['visible'];
            }
        }
        return null;
    }

    /**
     * Actualiza el nombre de una sección (parámetro $section) si existe en la lista de secciones del
     * parámetro $course_sections. En caso de que la sección ya tenga un nombre, no se actualiza su nombre.
     *
     * @param object $section objeto para verificar existencia
     * @param object $course_sections lista de secciones para validar
     *
     * @return object objeto con la sección actualizada
     */
    private function validate_section($section, $course_sections){
        $exist = false;
        foreach($course_sections as $key => $course_section){
            if($section->sectionid == $course_section['sectionid']){
                $exist = true;
                if($section->name != $course_section['name']){
                    self::update_section_name($section->sectionid, $course_section['name']);
                    $section->name = $course_section['name'];
                }
                break;
            }
        }
        $section->exists = $exist;
        return $section;
    }

    /**
     * Actualiza el nombre de una sección de Fliplearning
     *
     * @param object $sectionid id de la seccion para actualizar
     * @param object $name nuevo nombre para la sección
     *
     * @return void
     */
    private function update_section_name($sectionid, $name){
        global $DB;
        $sql = "update {fliplearning_sections} set section_name = ? where sectionid = ?";
        $DB->execute($sql, array($name, $sectionid));
    }

    /**
     * Verifica si un curso tiene configurada las semanas de Fliplearning
     *
     * @return boolean valor booleano que representa si las semanas han sido configuradas
     */
    public function is_set(){
        $is_set = true;
        $settings = self::get_settings();
        foreach($settings as $configured){
            if(!$configured){
                $is_set = false;
                break;
            }
        }
        return $is_set;
    }

    /**
     * Obtiene las configuraciones de Fliplearning en un curso
     *
     * @return array lista de valores booleanos con las configuraciones del curso
     */
    public function get_settings(){
        $tz = self::get_timezone();
        date_default_timezone_set($tz);
        $course_start = $this->startin;
        $weeks = self::get_weeks_with_sections();
        $settings = [
            "weeks" => false,
            "course_start" => false,
            "has_students" => false
        ];
        $first_week = new stdClass();
        $first_week->has_sections = isset($weeks[0]) && !empty($weeks[0]->sections);
        $first_week->started = time() >= $course_start;
        if($first_week->has_sections){
            $settings['weeks'] = true;
        }
        if($first_week->started){
            $settings['course_start'] = true;
        }
        $students = self::get_student_ids();
        if(!empty($students)){
            $settings['has_students'] = true;
        }
        return $settings;
    }

    /**
     * Obtiene una lista de las secciones de un curso (sin la configuración de
     * las semanas de Fliplearning)
     *
     * @return array lista de secciones del curso
     */
    public function get_sections_without_week(){
        $course_sections = self::get_course_sections();
        $weeks = self::get_weeks_with_sections();
        foreach($weeks as $key => $week){
            foreach($week->sections as $section){
                foreach($course_sections as $index => $course_section){
                    if($course_section['sectionid'] == $section->sectionid){
                        unset($course_sections[$index]);
                    }
                }
            }
        }
        $course_sections = array_values($course_sections);
        return $course_sections;
    }

    /**
     * Guarda las semanas de Fliplearning configuradas en un curso
     *
     * @param array $weeks semanas a guardar
     *
     * @return void
     * @throws Exception
     */
    public function save_weeks($weeks){
        global $DB;
        self::delete_weeks();
        foreach($weeks as $key => $week){
            $week = self::save_week($week, $key);
            self::save_week_sections($week->weekcode, $week->sections);
        }
    }

    /**
     * Elimina las semanas de Fliplearning configuradas en un curso
     *
     * @return void
     * @throws dml_exception
     */
    public function delete_weeks(){
        global $DB;
        $weeks = $this->weeks;
        foreach($weeks as $week){
            self::delete_week_sections($week->weekcode);
            $sql = "update {fliplearning_weeks} set timedeleted = ? where id = ?";
            $DB->execute($sql, array(self::now_timestamp() , $week->id));
        }
    }

    /**
     * Elimina las secciones asignadas a una semana de Fliplearning
     *
     * @param string $weekcode id de la semana a eliminar
     *
     * @return void
     * @throws dml_exception
     */
    public function delete_week_sections($weekcode){
        global $DB;
        $sql = "update {fliplearning_sections} set timedeleted = ? where weekcode = ?";
        $DB->execute($sql, array(self::now_timestamp() , $weekcode));
    }

    /**
     * Guarda una semana de Fliplearning configurada en un curso
     *
     * @param object $week semana a guardar
     * @param int $position posicion de la semana
     *
     * @return void
     * @throws Exception
     */
    private function save_week($week, $position){
        global $DB;
        $week->weekcode = self::generate_week_code($position);
        $week->position = $position;
        $week->weekstart = self::to_timestamp($week->s);
        $week->weekend = self::to_timestamp($week->e) + 86399;
        $week->hours_dedications = $week->h;
        $week->courseid = $this->course->id;
        $week->created_by = $this->user->id;
        $week->modified_by = $this->user->id;
        $week->timecreated = self::now_timestamp();
        $week->timemodified = self::now_timestamp();
        $week->instanceid = $this->instance->id;
        $id = $DB->insert_record("fliplearning_weeks", $week, true);
        $week->id = $id;
        return $week;
    }

    /**
     * Guarda las secciones asignadas a una semana de Fliplearning
     *
     * @param string $weekcode id de la semana a la que pertenece las secciones
     * @param array $sections lista de secciones a guardar
     *
     * @return void
     * @throws dml_exception
     */
    public function save_week_sections($weekcode, $sections){
        self::delete_week_sections($weekcode);
        foreach ($sections as $position => $section){
            self::save_week_section($section, $weekcode, $position);
        }
    }

    /**
     * Guarda una seccion asignada a una semana de Fliplearning
     *
     * @param object $section sección a guardar
     * @param int $weekcode id de la semana a la que pertenece la sección
     * @param int $position posición de la sección
     *
     * @return void
     */
    private function save_week_section($section, $weekcode, $position){
        global $DB;
        $section->sectionid = $section->sid;
        $section->section_name = self::get_section_name_from_id($section->sectionid, $position);
        $section->weekcode = $weekcode;
        $section->position = $position;
        $section->timecreated = self::now_timestamp();
        $section->timemodified = self::now_timestamp();
        $id = $DB->insert_record("fliplearning_sections", $section, true);
        $section->id = $id;
        return $section;
    }

    /**
     * Obtiene el nombre de una sección dado su id
     *
     * @param int $sectionid id de sección
     * @param int $position posición de la sección
     *
     * @return void
     */
    private function get_section_name_from_id($sectionid, $position){
        global $DB;
        $result = $DB->get_record("course_sections", ["id" => $sectionid]);
        $name = self::get_section_name($result, $position);
        return $name;
    }

    /**
     * Devuelve la semana actual de las semanas configuradas de Fliplearning. En caso de que la consulta
     * se realice despues de que el curso haya terminado, se retorna la última semana configurada
     *
     * @param int $last_if_course_finished parámetro entero opcional para devolver la última semana configurada
     *                                     en caso de que el curso haya terminado
     *
     * @return object objeto con la semana actual a la última semana
     */
    public function get_current_week($last_if_course_finished = true){
        $current = null;
        $now = time();
        $lastweek = null;
        foreach($this->weeks as $week){
            $lastweek = $week;
            if($now >= $week->weekstart  && $now <= $week->weekend){
                $current = $week;
                break;
            }
        }
        if($last_if_course_finished){
            $current = $lastweek;
        }
        return $current;
    }

    /**
     * Toma la fecha actual al momento de hacer la llamada a la funcion y le resta 7 días para obtener
     * el día de la peticion de la semana pasada. Si el día obtenido esta dentro de alguna de las semanas
     * configuradas de Fliplearning entonces se retorna esa semana, de lo contratio se retorna null
     *
     * @return object objeto con la semana a la que corresponde la fecha actual menos 7 días. En caso de
     *                no encontrarlo se retorna null
     */
    public function get_past_week(){
        $past = null;
        $day_past_week = strtotime("-7 day", time());
        foreach($this->weeks as $week){
            if($day_past_week >= $week->weekstart && $day_past_week <= $week->weekend){
                $past = $week;
                break;
            }
        }
        return $past;
    }

    public function get_weeks_paginator(){
        $pages = array();
        $current_week = self::get_current_week();
        foreach($this->weeks as $key => $week){
            $page = new stdClass();
            $page->number = $key + 1;
            $page->weekcode = $week->weekcode;
            $page->weekid = $week->id;
            $page->weekstart = $week->weekstartlabel;
            $page->weekend = $week->weekendlabel;
            $page->selected = $week->weekcode == $current_week->weekcode ? true : false;
            $page->is_current_week = $week->weekcode == $current_week->weekcode ? true : false;
            array_push($pages, $page);
        }
        return $pages;
    }
}
