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

class logs {

    public static function create($section, $component, $action, $target, $url, $type, $userid, $courseid){
        global $DB;

        // Informarcion de usuario
        $user = self::get_user($userid);

        // Informacion de curso
        $course = self::get_course($courseid);

        // Registro de log
        $log = new stdClass();
        $log->userid = $user->id;
        $log->username = $user->username;
        $log->firstname = $user->firstname;
        $log->lastname = $user->lastname;
        $log->currentroles = self::get_user_roles($courseid, $userid);
        $log->courseid = $course->id;
        $log->coursename = $course->fullname;
        $log->pluginsection = $section;
        $log->component = $component;
        $log->action = $action;
        $log->target = $target;
        $log->url = $url;
        $log->timecreated = time();
        $log->interactiontype = $type;
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
        $user_roles = implode(',', $user_roles);
        return $user_roles;
    }

    public static function create_logs_file ($logstype, $courseid, $start, $end) {
        global $DB;
        $course = $DB->get_record('course', array('id' => $courseid), 'id,fullname,format', MUST_EXIST);

        // Offset para ocultar ids reales
        $offset = 137;

        // Eliminar archivos antiguos de logs
        self::remove_old_logs_from_disk(($course->id * $offset));

        // Calcular fechas para logs
        $start = strtotime($start);
        $end = strtotime("+1 day", strtotime($end));

        // Generar archivo y devolver nombre del archivo
        if ($logstype == "moodle") {
            return self::get_moodle_logs($course, $start, $end, $offset);
        } else {
            return self::get_fliplearning_logs($course, $start, $end, $offset);
        }
    }

    private static function remove_old_logs_from_disk($courseid){
        $path = dirname(__FILE__) . "/../downloads";
        $files = glob($path . '/*');
        foreach($files as $file){
            if(is_file($file)){
                $route_parts = explode("__", $file);
                foreach($route_parts as $route_part){
                    if($route_part == $courseid){
                        unlink($file);
                    }
                }
            }
        }
    }

    private static function get_moodle_logs($course, $start, $end, $offset) {
        global $DB;

        // Metada de archivo
        $headers = array(
            get_string('tl_logs_header_logid', 'local_fliplearning'),
            get_string('tl_logs_header_userid', 'local_fliplearning'),
            get_string('tl_logs_header_username', 'local_fliplearning'),
            get_string('tl_logs_header_firstname', 'local_fliplearning'),
            get_string('tl_logs_header_lastname', 'local_fliplearning'),
            get_string('tl_logs_header_roles', 'local_fliplearning'),
            get_string('tl_logs_header_courseid', 'local_fliplearning'),
            get_string('tl_logs_header_coursename', 'local_fliplearning'),
            get_string('tl_logs_header_contextlevel', 'local_fliplearning'),
            get_string('tl_logs_header_component', 'local_fliplearning'),
            get_string('tl_logs_header_action', 'local_fliplearning'),
            get_string('tl_logs_header_target', 'local_fliplearning'),
            get_string('tl_logs_header_activitytype', 'local_fliplearning'),
            get_string('tl_logs_header_activityname', 'local_fliplearning'),
            get_string('tl_logs_header_sectionnumber', 'local_fliplearning'),
            get_string('tl_logs_header_sectionname', 'local_fliplearning'),
            get_string('tl_logs_header_timecreated', 'local_fliplearning')
        );
        $path = dirname(__FILE__) . "/../downloads/";
        $filename = "logs_moodle_course__" . ($course->id * $offset) . "__.csv";

        // Abrir archivo
        $file = fopen($path . $filename, 'w');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($file, $headers, ";");

        // Condiciones para busqueda de logs
        $conditions = array($course->id, $start, $end);

        // Contar cantidad de registros segun las condiciones
        $sql = "SELECT COUNT(*) COUNT FROM {logstore_standard_log}
                WHERE COURSEID = ? AND TIMECREATED >= ? AND TIMECREATED <= ?";
        $row = $DB->get_record_sql($sql, $conditions);

        // Si hay logs
        if ($row->count > 0){
            // Obtener ids de usuarios admins
            $admins = array_values(get_admins());
            $adminsIds = array();
            foreach ($admins as $admin) {
                array_push($adminsIds, $admin->id);
            }

            // Obtener ids de usuarios de las interacciones
            $sql = "SELECT DISTINCT(USERID) FROM {logstore_standard_log}
                    WHERE COURSEID = ? AND TIMECREATED >= ? AND TIMECREATED <= ? 
                    ORDER BY USERID";
            $rows = $DB->get_records_sql($sql, $conditions);
            $usersIds = array();
            foreach($rows as $row) {
                array_push($usersIds, $row->userid);
            }

            // Obtener detalles y roles de usuarios
            $users = self::get_users_info($usersIds);
            $users_roles = self::get_users_roles($course->id, $usersIds, $adminsIds);

            // Obtener modulos de curso
            $modinfo = get_fast_modinfo($course->id);
            $modules = $modinfo->get_cms();
            $modules = self::format_course_module($modules);

            // Obtener secciones del curso
            $sections = $modinfo->get_section_info_all();
            $sections = self::format_course_sections($sections, $course->format);

            // Obtener logs
            $sql = "SELECT id,component,action,target,contextlevel,contextinstanceid,userid,timecreated 
                    FROM {logstore_standard_log}
                    WHERE COURSEID = ? AND TIMECREATED >= ? AND TIMECREATED <= ? 
                    ORDER BY TIMECREATED ASC";
            $logs = $DB->get_recordset_sql($sql, $conditions);

            // Escribir archivo
            foreach($logs as $log) {
                // Validar datos de usuario
                $userid = "-";
                $username = "-";
                $firstname = "-";
                $lastname = "-";
                $rol = "-";
                $user = $users[$log->userid];
                if (isset($user)) {
                    $userid = $user->id;
                    $username = $user->username;
                    $firstname = $user->firstname;
                    $lastname = $user->lastname;
                    $rol = implode(",", $users_roles[$user->id]);
                }

                // Si la interaccion es con un modulo de curso
                $activitytype = "-";
                $activityname = "-";
                $sectionnumber = "-";
                $sectionname = "-";
                if ($log->contextlevel == 70 && isset($modules[$log->contextinstanceid])) {
                    $module = $modules[$log->contextinstanceid];
                    $activitytype = $module->modname;
                    $activityname = $module->name;
                    $sectionnumber = $module->sectionnum;
                    $sectionname = $sections[$module->section]->name;
                }

                // Registro de interaccion
                $record = new stdClass();
                $record->id = $log->id;
                $record->userid = ($userid * $offset);
                $record->username = $username;
                $record->firstname = $firstname;
                $record->lastname = $lastname;
                $record->rol = $rol;
                $record->courseid = ($course->id * $offset);
                $record->coursename = $course->fullname;
                $record->contextlevel = $log->contextlevel;
                $record->component = $log->component;
                $record->action = $log->action;
                $record->target = $log->target;
                $record->activitytype = $activitytype;
                $record->activityname = $activityname;
                $record->sectionnumber = $sectionnumber;
                $record->sectionname = $sectionname;
                $record->timecreated = $log->timecreated;

                fputcsv($file, (array) $record, ";");
            }

            $logs->close();
        }

        fclose($file);

        return $filename;
    }

    private static function get_users_info($usersIds){
        global $DB;
        $users = array();
        list($in, $invalues) = $DB->get_in_or_equal($usersIds);
        $fields = "id,username,firstname,lastname";
        $sql = "SELECT $fields FROM {user} WHERE ID $in ORDER BY ID";
        $rows = $DB->get_recordset_sql($sql, $invalues);
        foreach($rows as $key => $row){
            $users[$row->id] = $row;
        }
        $rows->close();
        return $users;
    }

    private static function get_users_roles($courseid, $usersIds, $adminsIds){
        global $DB;

        // Obtener id contexto
        $context = context_course::instance($courseid);
        $contextId = $context->id;

        // Obtener roles de moodle
        $roleNames = array();
        $roles = $DB->get_records('role', array(), 'id', 'id,shortname');
        foreach ($roles as $rol) {
            $roleNames[$rol->id] = $rol->shortname;
        }

        // Obtener roles de usuario en el contexto del curso
        $users_roles = [];
        list($in, $invalues) = $DB->get_in_or_equal($usersIds);
        array_push($invalues, $contextId);
        $sql = "SELECT id,roleid,contextid,userid FROM {role_assignments} 
                WHERE USERID $in AND contextid = ? ORDER BY USERID";
        $rows = $DB->get_recordset_sql($sql, $invalues);
        foreach($rows as $row){
            if (!isset($users_roles[$row->userid])) {
                $users_roles[$row->userid] = array();
            }
            $roleName = (isset($roleNames[$row->roleid])) ? $roleNames[$row->roleid] : "";
            array_push($users_roles[$row->userid], $roleName);
        }
        $rows->close();

        // Agregar rol admin a los usuarios admins
        foreach ($usersIds as $userId) {
            if (!isset($users_roles[$userId])) {
                if (in_array($userId, $adminsIds)) {
                    $users_roles[$userId] = array('admin');
                } else {
                    $users_roles[$userId] = array('-');
                }
            } else {
                if (in_array($userId, $adminsIds)) {
                    array_push($users_roles[$userId], 'admin');
                }
            }
        }
        return $users_roles;
    }

    private static function format_course_module($modules){
        $full_modules = array();
        foreach ($modules as $module){
            $formatted_module = new stdClass();
            $formatted_module->modname =  $module->modname;
            $formatted_module->name =  $module->name;
            $formatted_module->sectionnum =  $module->sectionnum;
            $formatted_module->section =  $module->section;
            $full_modules[$module->id] = $formatted_module;
        }
        return $full_modules;
    }

    private static function format_course_sections($sections, $course_format){
        $formatted_sections = array();
        foreach ($sections as $index => $section){
            $formatted_section = new stdClass();
            $formatted_section->sectionid = $section->id;
            $formatted_section->name = self::get_section_name($section, $index, $course_format);
            $formatted_sections[$section->id] = $formatted_section;
        }
        return $formatted_sections;
    }

    private function get_section_name($section, $current_index, $course_format){
        if(isset($section->name) ){
            return $section->name;
        }
        if (get_string_manager()->string_exists("tw_course_format_{$course_format}", "local_fliplearning")) {
            $course_format = get_string("tw_course_format_{$course_format}", 'local_fliplearning');
        }
        $name = "$course_format $current_index";
        return $name;
    }

    private static function get_fliplearning_logs($course, $start, $end, $offset) {
        global $DB;

        // Condiciones para busqueda de logs
        $conditions = array($course->id, $start, $end);

        // Metada de archivo
        $headers = array(
            get_string('tl_logs_header_logid', 'local_fliplearning'),
            get_string('tl_logs_header_userid', 'local_fliplearning'),
            get_string('tl_logs_header_username', 'local_fliplearning'),
            get_string('tl_logs_header_firstname', 'local_fliplearning'),
            get_string('tl_logs_header_lastname', 'local_fliplearning'),
            get_string('tl_logs_header_roles', 'local_fliplearning'),
            get_string('tl_logs_header_courseid', 'local_fliplearning'),
            get_string('tl_logs_header_coursename', 'local_fliplearning'),
            get_string('tl_logs_header_pluginsection', 'local_fliplearning'),
            get_string('tl_logs_header_component', 'local_fliplearning'),
            get_string('tl_logs_header_action', 'local_fliplearning'),
            get_string('tl_logs_header_target', 'local_fliplearning'),
            get_string('tl_logs_header_url', 'local_fliplearning'),
            get_string('tl_logs_header_timecreated', 'local_fliplearning'),
            get_string('tl_logs_header_description', 'local_fliplearning'),
        );
        $path = dirname(__FILE__) . "/../downloads/";
        $filename = "logs_fliplearning_course__" . ($course->id * $offset) . "__.csv";

        // Abrir archivo
        $file = fopen($path . $filename, 'w');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($file, $headers, ";");

        // Contar cantidad de registros segun las condiciones
        $sql = "SELECT COUNT(*) COUNT FROM {fliplearning_logs}
                WHERE COURSEID = ? AND TIMECREATED >= ? AND TIMECREATED <= ?";
        $row = $DB->get_record_sql($sql, $conditions);

        // Si hay logs
        if ($row->count > 0) {
            // Obtener logs
            $sql = "SELECT 
                        id,userid,username,firstname,lastname,currentroles,courseid,coursename,
                        pluginsection,component,action,target,url,timecreated,interactiontype 
                    FROM {fliplearning_logs}
                    WHERE COURSEID = ? AND TIMECREATED >= ? AND TIMECREATED <= ? 
                    ORDER BY TIMECREATED ASC";
            $logs = $DB->get_recordset_sql($sql, $conditions);

            // Escribir archivo
            foreach($logs as $log) {
                $log->userid = $log->userid * $offset;
                $log->courseid = $log->courseid * $offset;
                fputcsv($file, (array) $log, ";");
            }
            $logs->close();
        }

        fclose($file);

        return $filename;
    }
}