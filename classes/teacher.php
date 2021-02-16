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
                $hour = date("G", (int) $session->start);

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
        $days = array("mon", "tue", "wed", "thu", "fri", "sat", "sun");
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
        $response = self::get_sessions_by_weeks_summary($months, (int) $this->course->startdate);
        return $response;
    }

    private function get_sessions_by_weeks($user_sessions) {
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

    private function get_sessions_by_weeks_summary($months, $startdate) {
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
        $sessions_users = self::get_sessions_from_logs($conditions);
        return $sessions_users;
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

        $users_sessions = self::get_work_sessions($start, $end);
        $cms = self::get_course_modules();
        $table = self::get_progress_table($users_sessions, $cms, $enable_completion);
        return $table;
    }

    private function get_progress_table($users, $cms, $enable_completion, $include_sessions = false) {
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
                array_push($cms_ids, $cm);
            }
        }
        $interaction = new stdClass();
        $interaction->complete = $complete_cms;
        $interaction->viewed = $viewed_cms;
        $interaction->cms = $cms_ids;
        $interaction->total = count($cms);
        return $interaction;
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

    private function get_finished_course_module_by_view($userid, $cm_id){
        global $DB;
        $complete = false;
        $sql = "SELECT id from {logstore_standard_log} 
                WHERE courseid = {$this->course->id} AND contextlevel = 70 
                AND contextinstanceid = {$cm_id} AND userid = {$userid}";
        $logs = $DB->get_records_sql($sql);
        if (isset($logs) && count($logs)>0) {
            $complete = true;
        }
        return $complete;
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

    public function count_sessions($weekcode = null){
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
        $sessions_count = self::count_sessions_by_duration($work_sessions);
        $response = self::count_sessions_by_duration_summary($sessions_count, $week->weekstart, $week->weekend);
        return $response;
    }

    private function count_sessions_by_duration($user_sessions) {
        $summary = array();
        foreach($user_sessions as $sessions){
            foreach($sessions as $session){
                $month = strtolower(date("M", (int) $session->start));
                $day = strtolower(date("j", (int) $session->start));
                $day = "$month $day";

                $session_label = "greater60";
                if ($session->duration < 30) {
                    $session_label='smaller30';
                } elseif ($session->duration < 60) {
                    $session_label='greater60';
                }

                if(!isset($summary[$day])){
                    $summary[$day] = array();
                }
                if (!isset($summary[$day][$session_label])) {
                    $summary[$day][$session_label] = 1;
                } else {
                    $summary[$day][$session_label]++;
                }
            }
        }
        return $summary;
    }

    private function count_sessions_by_duration_summary($sessions_count, $start) {
        $categories = array();

        $data = new stdClass();
        $data->smaller30 = array();
        $data->greater30 = array();
        $data->greater60 = array();

        $names = new stdClass;
        $names->smaller30 = get_string("fml_smaller30", "local_fliplearning");
        $names->greater30 = get_string("fml_greater30", "local_fliplearning");
        $names->greater60 = get_string("fml_greater60", "local_fliplearning");

        for ($i = 0; $i < 7; $i++ ) {
            $month = strtolower(date("M", $start));
            $day = strtolower(date("j", $start));
            $label = "$month $day";

            if (isset($sessions_count[$label])) {
                $count = $sessions_count[$label];
                $value = 0;
                if(isset($count['smaller30'])){
                    $value = $count['smaller30'];
                }
                $data->smaller30[] = $value;

                $value = 0;
                if(isset($count['greater30'])){
                    $value = $count['greater30'];
                }
                $data->greater30[] = $value;

                $value = 0;
                if(isset($count['greater60'])){
                    $value = $count['greater60'];
                }
                $data->greater60[] = $value;
            } else {
                $data->smaller30[] = 0;
                $data->greater30[] = 0;
                $data->greater60[] = 0;
            }

            $month_name = self::get_month_name($month);
            $categories[] = "$month_name $day";
            $start += 86400;
        }

        $data_object[] = array(
            "name" => $names->smaller30,
            "data" => $data->smaller30
        );
        $data_object[] = array(
            "name" => $names->greater30,
            "data" => $data->greater30
        );
        $data_object[] = array(
            "name" => $names->greater60,
            "data" => $data->greater60
        );

        $summary = new stdClass();
        $summary->categories = $categories;
        $summary->data = $data_object;

        return $summary;
    }

    private function get_month_name ($month_code) {
        $text = "fml_$month_code";
        $month_name = get_string($text, "local_fliplearning");
        return $month_name;
    }

    public function inverted_time($weekcode = null){
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
        $inverted_time = array_map(function($user_sessions){ return $user_sessions->summary;}, $work_sessions);
        $inverted_time = self::calculate_average("added", $inverted_time);

        $response = self::get_inverted_time_summary($inverted_time, (int) $week->hours_dedications);
        return $response;
    }

    public function get_inverted_time_summary($inverted_time, $expected_time){
        $response = new stdClass();
        $response->expected_time = $expected_time;
        $response->expected_time_converted = self::convert_time("hours", $expected_time, "string");
        $response->inverted_time = self::minutes_to_hours($inverted_time->average, -1);
        $response->inverted_time_converted = self::convert_time("hours", $response->inverted_time, "string");

        $inverted_time = new stdClass();
        $inverted_time->name = get_string("fml_inverted_time","local_fliplearning");
        $inverted_time->y = $response->inverted_time;
        $data[] = $inverted_time;
        $expected_time = new stdClass();
        $expected_time->name = get_string("fml_expected_time","local_fliplearning");
        $expected_time->y = $response->expected_time;
        $data[] = $expected_time;

        $response->data = $data;
        return $response;
    }

    public function assignments_submissions($weekcode = null){
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

        $week_modules = self::get_course_modules_from_sections($week->sections);
        $assign_modules = array_filter($week_modules, function($module){ return $module->modname == 'assign';});
        $assign_ids = self::extract_elements_field($assign_modules, "instance");
        $valid_assigns = self::get_valid_assigns($assign_ids);
        $assign_ids = self::extract_ids($valid_assigns);
        $submissions = self::get_assigns_submissions($assign_ids, $this->users);
        $response = self::get_submissions($valid_assigns, $submissions, $this->users);
        return $response;
    }

    private function get_valid_assigns($assign_ids){
        global $DB;
        $assigns = array();
        if (count($assign_ids) > 0) {
            list($in, $invalues) = $DB->get_in_or_equal($assign_ids);
            $sql = "SELECT * FROM {assign} WHERE course = {$this->course->id} AND id $in AND nosubmissions <> 1";
            $result = $DB->get_records_sql($sql, $invalues);
            $assigns = array_values($result);
        }
        return $assigns;
    }

    private function get_assigns_submissions($assign_ids, $user_ids){
        global $DB;
        $submissions = array();
        if (!empty($assign_ids)) {
            list($in_assigns, $invalues_assigns) = $DB->get_in_or_equal($assign_ids);
            list($in_users, $invalues_users) = $DB->get_in_or_equal($user_ids);
            $params = array_merge($invalues_assigns, $invalues_users);
            $sql = "
                SELECT s.id, a.id as assign, a.course, a.name, a.duedate, s.userid, s.timemodified as timecreated, s.status 
                FROM {assign} a
                INNER JOIN mdl_assign_submission s ON a.id = s.assignment
                WHERE a.course = {$this->course->id} AND a.id $in_assigns AND a.nosubmissions <> 1 
                AND s.userid $in_users AND s.status = 'submitted'
                ORDER BY a.id;
            ";
            $result = $DB->get_records_sql($sql, $params);
            foreach ($result as $submission) {
                if (!isset($submissions[$submission->assign])) {
                    $submissions[$submission->assign] = array();
                }
                array_push($submissions[$submission->assign], $submission);
            }
        }
        return $submissions;
    }

    private function get_submissions($assigns, $assign_submissions, $users){
        global $DB;

        $categories = array();
        $modules = array();
        $submissions_users = array();
        $assignmoduleid=1;

        $data = new stdClass();
        $data->intime_sub = array();
        $data->late_sub = array();
        $data->no_sub = array();

        $names = new stdClass;
        $names->intime_sub = get_string("fml_intime_sub", "local_fliplearning");
        $names->late_sub = get_string("fml_late_sub", "local_fliplearning");
        $names->no_sub = get_string("fml_no_sub", "local_fliplearning");

        foreach ($assigns as $assign) {
            if (isset($assign_submissions[$assign->id])) {
                $submissions = self::count_submissions($assign_submissions[$assign->id], $users);
            } else {
                $submissions = array();
                $submissions['intime_sub'] = array();
                $submissions['late_sub'] = array();
                $submissions['no_sub'] = $users;
            }

            array_push($data->intime_sub, count($submissions['intime_sub']));
            array_push($data->late_sub, count($submissions['late_sub']));
            array_push($data->no_sub, count($submissions['no_sub']));

            $submissions = self::get_submissions_with_users($submissions);
            array_push($submissions_users, $submissions);

            $date_label = get_string("fml_assign_nodue", 'local_fliplearning');
            if ($assign->duedate != "0") {
                $date_label = self::get_date_label($assign->duedate);
            }
            $category_name = "<b>$assign->name</b><br>$date_label";
            array_push($categories, $category_name);

            $module = $DB->get_field('course_modules', 'id',
                array('course' => $assign->course, 'module' => $assignmoduleid, 'instance' => $assign->id));
            array_push($modules, $module);
        }

        $series = array();

        $obj = new stdClass();
        $obj->name = $names->intime_sub;
        $obj->data = $data->intime_sub;
        array_push($series, $obj);

        $obj = new stdClass();
        $obj->name = $names->late_sub;
        $obj->data = $data->late_sub;
        array_push($series, $obj);

        $obj = new stdClass();
        $obj->name = $names->no_sub;
        $obj->data = $data->no_sub;
        array_push($series, $obj);

        $response = new stdClass();
        $response->data = $series;
        $response->categories = $categories;
        $response->modules = $modules;
        $response->users = $submissions_users;

        return $response;
    }

    private function count_submissions($submissions, $users_ids) {
        $submitted_users = array();
        $data = array();
        $data['intime_sub'] = array();
        $data['late_sub'] = array();
        $data['no_sub'] = array();

        foreach ($submissions as $submission) {
            if ( ($submission->duedate == "0") || ( ((int) $submission->timecreated) <= ((int) $submission->duedate) ) ) {
                array_push($data['intime_sub'], $submission->userid);
            } else {
                array_push($data['late_sub'], $submission->userid);
            }
            array_push($submitted_users, $submission->userid);
        }
        $data['no_sub'] = array_diff($users_ids, $submitted_users);
        return $data;
    }

    private function get_submissions_with_users($submissions) {
        $data = array();
        foreach ($submissions as $index => $users) {
            $values = array();
            if (count($users) > 0) {
                $values = self::get_users_from_ids($users);
            }
            $data[$index]=$values;
        }
        $data = array_values($data);
        return $data;
    }

    public function resources_access($weekcode = null){
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

        $week_modules = self::get_course_modules_from_sections($week->sections);
        $week_modules = array_filter($week_modules, function($module){ return $module->modname != 'label';});
        $week_modules = self::set_resources_access_users($week_modules, $this->users, $this->course->id);
        $response = self::get_access_modules_summary($week_modules);
        $users = self::get_users_from_ids($this->users);
        $response->users = $users;
        return $response;
    }

    private function set_resources_access_users($modules, $user_ids, $course_id){
        foreach ($modules as $module) {
            $access_users = self::get_access_modules($course_id, $module->id, $user_ids);
            $module->users = $access_users;
        }
        return $modules;
    }

    private function get_access_modules($course_id, $module_id, $user_ids){
        global $DB;
        $contextlevel = 70;
        list($in_users, $invalues_users) = $DB->get_in_or_equal($user_ids);
        $sql = "
            SELECT DISTINCT(userid) FROM {logstore_standard_log} a
            WHERE courseid = {$course_id} AND contextlevel = {$contextlevel} 
            AND contextinstanceid = {$module_id} AND userid $in_users
            ORDER BY userid;
        ";
        $result = $DB->get_records_sql($sql, $invalues_users);
        $ids = array();
        foreach ($result as $record) {
            array_push($ids, (int) $record->userid);
        }
        return $ids;
    }

    private function get_access_modules_summary($modules){
        $summary = array();
        $types = array();
        foreach ($modules as $module) {
            $item = new stdClass();
            $item->id = $module->id;
            $item->name = $module->name;
            $item->type = $module->modname;
            $item->users = $module->users;
            array_push($summary, $item);

            if (!isset($types[$module->modname])) {
                $type_name = $module->modname;
                $identifier = "fml_{$module->modname}";
                if (get_string_manager()->string_exists($identifier,"local_fliplearning")) {
                    $type_name = get_string($identifier,"local_fliplearning");
                }
                $element = new stdClass();
                $element->type = $module->modname;
                $element->name = $type_name;
                $element->show = true;
                $types[$module->modname] = $element;
            }
        }
        $types = array_values($types);
        $response = new stdClass();
        $response->types = $types;
        $response->modules = $summary;
        return $response;
    }

    public function grade_items() {

        $categories = $this->get_grade_categories();
        $items = $this->get_grade_items();
        $items = $this->format_items($items);
        $users = $this->get_full_users();
        $items = $this->set_average_max_min_grade($items, $users);
        $categories = $this->get_grade_categories_with_items($categories, $items);

        $response = new stdClass();
        $response->categories = $categories;
        $response->student_count = count($this->users);
        return $response;
    }

    private function get_grade_categories () {
        global $DB;
        $sql = "SELECT * FROM {grade_categories} WHERE courseid = {$this->course->id} ORDER BY path";
        $result = $DB->get_records_sql($sql);
        $result = array_values($result);
        return $result;
    }

    private function get_grade_items () {
        global $DB;
        $sql = "SELECT * FROM {grade_items} WHERE courseid = {$this->course->id} AND itemtype = 'mod' and gradetype = 1";
        $result = $DB->get_records_sql($sql);
        $result = array_values($result);
        return $result;
    }

    private function format_items ($items) {
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

    private function get_course_module_id($item) {
        global $DB;
        $coursemoduleid = false;
        if (isset($item->itemmodule)) {
            $sql = "SELECT id FROM {modules} WHERE name = '{$item->itemmodule}'";
            $result = $DB->get_record_sql($sql);
            $moduleid =  $result->id;
            if (isset($moduleid)) {
                $sql = "SELECT id FROM {course_modules} 
                        WHERE course = {$this->course->id} AND module = {$moduleid} 
                        AND instance = {$item->iteminstance} and visible = 1";
                $result = $DB->get_record_sql($sql);
                if (isset($result->id)) {
                    $coursemoduleid = (int) $result->id;
                }
            }
        }
        return $coursemoduleid;
    }

    private function get_grade_categories_with_items ($categories, $items) {
        $categories_items = array();
        foreach ($categories as $category) {
            $category_items = $this->get_grade_items_from_category($categories, $items, $category->id);

            $name = $category->fullname;
            if (!isset($category->parent)) {
                $name = $this->course->fullname;
            }
            $element = new stdClass();
            $element->name = $name;
            $element->items = $category_items;
            array_push($categories_items, $element);
        }
        return $categories_items;
    }

    private function get_grade_items_from_category($categories, $items, $categoryid) {
        $selected_items = $this->filter_items_by_category($items, $categoryid);
        $child_categories = $this->get_child_categories($categories, $categoryid);
        foreach ($child_categories as $categoryid) {
            $child_items = $this->get_grade_items_from_category($categories, $items, $categoryid);
            $selected_items = array_merge($selected_items, $child_items);
        }
        return $selected_items;
    }

    private function filter_items_by_category ($items, $categoryid) {
        $selected_items = [];
        foreach ($items as $item) {
            if ($item->categoryid == $categoryid) {
                array_push($selected_items, $item);
            }
        }
        return $selected_items;
    }

    private function get_child_categories($categories, $categoryid) {
        $child_categories = array();
        foreach ($categories as $category) {
            if ($category->parent == $categoryid) {
                array_push($child_categories, $category->id);
            }
        }
        return $child_categories;
    }

    private function set_average_max_min_grade ($items, $users) {
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

    public function quiz_attempts($weekcode = null){
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

        $week_modules = self::get_course_modules_from_sections($week->sections);
        $quiz_modules = array_filter($week_modules, function($module){ return $module->modname == 'quiz';});
        $response = $this->get_quiz_attempts_summary($quiz_modules);
        return $response;
    }

    private function get_quiz_attempts_summary($quiz_modules) {
        $quizzes = array();
        foreach ($quiz_modules as $module) {
            $quiz = new stdClass();
            $quiz->id = $module->instance;
            $quiz->moduleid = $module->id;
            $quiz->name = $module->name;
            $quiz->modname = $module->modname;

            $attempts = new stdClass();
            $attempts->details = $this->get_quiz_attempts($module->instance);
            $attempts->questions = $this->get_questions_attempts($module->instance);

            $quiz->attempts = $attempts;
            array_push($quizzes, $quiz);
        }
        return $quizzes;
    }

    private function get_quiz_attempts($quizid) {
        global $DB;
        list($in, $invalues) = $DB->get_in_or_equal($this->users);
        $sql = "SELECT id, quiz, userid, attempt, uniqueid, currentpage, state, sumgrades FROM {quiz_attempts} 
                WHERE state = 'finished' AND sumgrades IS NOT NULL AND quiz = {$quizid} AND userid {$in}
                ORDER BY userid, attempt";
        $rows = $DB->get_records_sql($sql, $invalues);
        $rows = array_values($rows);

        $sql = "SELECT count(*) as count FROM {quiz_slots} WHERE quizid = ?";
        $result = $DB->get_record_sql($sql, array($quizid));

        $attempt = new stdClass();
        $attempt->questions = (int) $result->count;
        $attempt->users = 0;
        $attempt->attempts = 0;
        if (count($rows) > 0) {
            $previoususerid = $rows[0]->userid;
            $previousattempt = $rows[0]->attempt;
            $totalusers = 1;
            $totalattemps = 1;
            foreach ($rows as $row) {
                if ($row->userid != $previoususerid) {
                    $totalusers++;
                    $totalattemps++;
                } else {
                    if ($row->attempt != $previousattempt) {
                        $totalattemps++;
                    }
                }
                $previousattempt = $row->attempt;
                $previoususerid = $row->userid;
            }
            $attempt->users = $totalusers;
            $attempt->attempts = $totalattemps;
        }
        return $attempt;
    }

    private function get_questions_attempts($quizid) {
        global $DB;
        list($in, $invalues) = $DB->get_in_or_equal($this->users);
        $sql = "SELECT qattstep.id as id, quizatt.id as quizattid, quizatt.quiz, quizatt.userid, quizatt.attempt, 
                quizatt.uniqueid, qatt.id as qattid, qatt.questionid, q.name, qattstep.sequencenumber, qattstep.state  
                FROM {quiz_attempts} quizatt
                JOIN {question_attempts} qatt ON quizatt.uniqueid = qatt.questionusageid
                JOIN {question} q ON qatt.questionid= q.id
                JOIN {question_attempt_steps} qattstep ON qatt.id = qattstep.questionattemptid
                WHERE quizatt.quiz = {$quizid} AND quizatt.state = 'finished' AND quizatt.sumgrades IS NOT NULL 
                AND q.qtype != 'description' AND quizatt.userid {$in}
                ORDER BY quizatt.userid, qatt.id, qattstep.sequencenumber DESC";
        $rows = $DB->get_records_sql($sql, $invalues);
        $rows = array_values($rows);

        $questions = array();
        if (count($rows) > 0) {
            $qattempts = array();
            $previousqattid = $rows[0]->qattid;
            $qasteps = array();
            foreach ($rows as $row) {
                if ($row->qattid != $previousqattid) {
                    $qattempts[$previousqattid] = $qasteps[0];
                    $qasteps = array();
                }
                $qasteps[] = $row;
                $previousqattid = $row->qattid;
            }
            $qattempts[$previousqattid] = $qasteps[0];


            foreach ($qattempts as $attempt) {
                if (!isset($questions[$attempt->questionid])) {
                    $question = array();
                    $question["id"] = $attempt->questionid;
                    $question["name"] = $attempt->name;
                    $question["total_attempts"] = 1;
                    $question[$attempt->state] = 1;
                    $questions[$attempt->questionid] = $question;
                } else {
                    if (!isset($questions[$attempt->questionid][$attempt->state])) {
                        $questions[$attempt->questionid][$attempt->state] = 1;
                    } else {
                        $questions[$attempt->questionid][$attempt->state]++;
                    }
                    $questions[$attempt->questionid]["total_attempts"]++;
                }
            }
            $questions = array_values($questions);
        }
        return $questions;
    }

    private function get_questions_attempts2($quizid) {
        global $DB;
        list($in, $invalues) = $DB->get_in_or_equal($this->users);
        $sql = "SELECT qattstep.id as id, quizatt.id as quizattid, quizatt.quiz, quizatt.userid, quizatt.attempt, 
                quizatt.uniqueid, qatt.id as qattid, qatt.questionid, q.name, qattstep.sequencenumber, qattstep.state  
                FROM {quiz_attempts} quizatt
                JOIN {question_attempts} qatt ON quizatt.uniqueid = qatt.questionusageid
                JOIN {question} q ON qatt.questionid= q.id
                JOIN {question_attempt_steps} qattstep ON qatt.id = qattstep.questionattemptid
                WHERE quizatt.quiz = {$quizid} AND quizatt.state = 'finished' AND quizatt.sumgrades IS NOT NULL 
                AND q.qtype != 'description' AND quizatt.userid {$in}
                ORDER BY quizatt.userid, qatt.id, qattstep.sequencenumber DESC";
        $rows = $DB->get_records_sql($sql, $invalues);
        $rows = array_values($rows);

        $questions = array();
        if (count($rows) > 0) {
            $qattempts = array();
            $previousqattid = $rows[0]->qattid;
            $qasteps = array();
            foreach ($rows as $row) {
                if ($row->qattid != $previousqattid) {
                    $qattempts[$previousqattid] = $qasteps[0];
                    $qasteps = array();
                }
                $qasteps[] = $row;
                $previousqattid = $row->qattid;
            }
            $qattempts[$previousqattid] = $qasteps[0];

            foreach ($qattempts as $attempt) {
                if (!isset($questions[$attempt->questionid])) {
                    $question = array();
                    $question["id"] = $attempt->questionid;
                    $question["name"] = $attempt->name;
                    $question["total_attempts"] = 1;
                    $question[$attempt->state] = array($attempt);
                    $questions[$attempt->questionid] = $question;
                } else {
                    if (!isset($questions[$attempt->questionid][$attempt->state])) {
                        $questions[$attempt->questionid][$attempt->state] = array($attempt);
                    } else {
                        array_push($questions[$attempt->questionid][$attempt->state], $attempt);
                    }
                    $questions[$attempt->questionid]["total_attempts"]++;
                }
            }
            $questions = array_values($questions);
        }
        return $questions;
    }

    public function get_dropout_clusters() {
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

        $cms = self::get_course_modules();
        $users = self::get_work_sessions($start, $end);
        $users = self::get_progress_table($users, $cms, $enable_completion, true);

        $clusters = $this->get_clusters();
        $users_access = $this->get_users_last_access();
        $users = $this->get_users_details($users, $cms, $enable_completion, $users_access);
        $users = $this->get_users_grades($users);

        $weeks = $this->get_weeks();

        $response = new stdClass();
        $response->users = $users;
        $response->clusters = $clusters;
        $response->total_cms = count($cms);
        $response->cms = $cms;
        $response->weeks = $weeks;
        return $response;
    }

    public function get_clusters() {
        global $DB;
        $sql = "SELECT * FROM {fliplearning_clustering} WHERE courseid = {$this->course->id} AND active = 1";
        $rows = $DB->get_records_sql($sql);
        $rows = array_values($rows);

        $clusters = array();
        $cluster_text = get_string("fml_cluster_text", "local_fliplearning");
        foreach ($rows as $row) {
            if (!isset($clusters[$row->cluster])) {
                $cluster = new stdClass();
                $cluster->name = $cluster_text." ".$row->cluster;
                $cluster->number = $row->cluster;
                $cluster->users = array();
                array_push($cluster->users, $row->userid);
                array_push($clusters, $cluster);
            } else {
                array_push($clusters[$row->cluster]->users, $row->userid);
            }
        }
        return $clusters;
    }

    private function get_users_details($users, $cms, $enable_completion, $users_access) {
        date_default_timezone_set(self::get_timezone());
        $total_cms = count($cms);
        if ($total_cms > 0) {
            foreach ($users as $user) {
//                $complete_cms = self::cms_interactions($cms, $user->id, $enable_completion);
//                $user->complete_cms = $complete_cms;
//                $user->progress_percentage = (int)(($complete_cms * 100)/$total_cms);
                $user->course_lastaccess = $this->get_user_last_access($user->id, $users_access);
            }
        }
        return $users;
    }

    private function get_user_last_access($userid, $users_access) {
        $access = new stdClass();
        $access->label = get_string("fml_dropout_user_never_access", "local_fliplearning");
        $access->timestamp = 0;
        if (isset($users_access[$userid])) {
            $timestamp = (int)$users_access[$userid]->timeaccess;
            $str = strtolower(date("D", $timestamp));
            $day_text = get_string("fml_$str", "local_fliplearning");
            $month_day = date("d", $timestamp);
            $str = strtolower(date("M", $timestamp));
            $month_text = get_string("fml_$str", "local_fliplearning");
            $year = date("Y", $timestamp);
            $hour = date("h", $timestamp);
            $min = date("i", $timestamp);
            $form = date("A", $timestamp);
            $timeago = $this->time_elapsed_string($timestamp);
            $str = "$day_text, $month_day $month_text $year, $hour:$min $form ($timeago)";
            $access->timestamp = $timestamp;
            $access->label = $str;
        }
        return $access;
    }

    private function time_elapsed_string($timestamp) {
        $now = (int) date("U");
        $diff = $now - $timestamp;
        $ago = get_string("fml_ago", "local_fliplearning");

        $interval = $diff / 86400;
        if ($interval >= 1) {
            $interval = floor($interval);
            $text = get_string("fml_days", "local_fliplearning");
            if ($interval == 1) {
                $text = get_string("fml_day", "local_fliplearning");
            }
            return "$interval $text $ago";
        }

        $interval = $diff / 3600;
        if ($interval >= 1) {
            $interval = floor($interval);
            $text = get_string("fml_hours", "local_fliplearning");
            if ($interval == 1) {
                $text = get_string("fml_hour", "local_fliplearning");
            }
            return "$interval $text $ago";
        }

        $interval = $diff / 60;
        if ($interval >= 1) {
            $interval = floor($interval);
            $text = get_string("fml_minutes", "local_fliplearning");
            if ($interval == 1) {
                $text = get_string("fml_minute", "local_fliplearning");
            }
            return "$interval $text $ago";
        }

        if ($diff >= 1) {
            $text = get_string("fml_seconds", "local_fliplearning");
            if ($diff == 1) {
                $text = get_string("fml_second", "local_fliplearning");
            }
            return "$diff $text $ago";
        }

        $text = get_string("fml_now", "local_fliplearning");
        return "$text";
    }

    private function get_users_grades($users) {
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

    private function get_weeks() {
        $configweeks = new \local_fliplearning\configweeks($this->course->id, $this->user->id);
        $weeks = array();
        foreach ($configweeks->weeks as $item) {
            $week = new stdClass();
            $week->weekstart = $item->weekstart;
            $week->weekend = $item->weekend;
            $week->weekstartlabel = $item->weekstartlabel;
            $week->weekendlabel = $item->weekendlabel;
            $week->position = $item->position;
            $week->name = $item->name;
            $week->setions = $item->sections;
            array_push($weeks, $week);
        }
        return $weeks;
    }
}
