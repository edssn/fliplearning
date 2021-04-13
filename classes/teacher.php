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

    public function get_general_indicators(){
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

        $work_sessions = self::get_work_sessions($start, $end);

        $sessions = array_map(function($user_sessions){ return $user_sessions->sessions;}, $work_sessions);
        $sessions = self::get_sessions_by_weeks($sessions);
        $sessions = self::get_sessions_by_weeks_summary($sessions, (int) $this->course->startdate);

        $cms = self::get_course_modules();
        $cms = array_filter($cms, function($cm){ return $cm['modname'] != 'label';});
        $table = self::get_progress_table($work_sessions, $cms, $enable_completion);

        $weeks_cms = $this->count_week_cms($cms);
        $students = $this->get_student_ids(false);

        $response = new stdClass();
        $response->sessions = $sessions;
        $response->table = $table;
        $response->weeks = $weeks_cms;
        $response->course = $this->get_course_details();
        $response->total_cms = count($cms);
        $response->total_weeks = count($weeks_cms);
        $response->total_students = count($students);
        return $response;
    }

    private function count_week_cms ($cms) {
        $sections_cms = array();
        foreach ($cms as $cm) {
            $sectionid = $cm['section'];
            if (!isset($sections_cms[$sectionid])) {
                $sections_cms[$sectionid] = 0;
            }
            $sections_cms[$sectionid]++;
        }
        $weeks = array();
        foreach ($this->weeks as $week) {
            $total_cms = 0;
            foreach ($week->sections as $section) {
                $sectionid = $section->sectionid;
                if (isset($sections_cms[$sectionid])) {
                    $total_cms += $sections_cms[$sectionid];
                }
            }
            $element = new stdClass();
            $element->name = $week->name;
            $element->position = $week->position;
            $element->cms = $total_cms;
            array_push($weeks, $element);
        }
        return $weeks;
    }

    private function get_course_details() {
        $course = new stdClass();
        $course->fullname = $this->course->fullname;
        $course->shortname = $this->course->shortname;

        $startdate = get_string("fml_not_configured", "local_fliplearning");
        if (!empty($this->course->startdate)) {
            $startdate = $this->format_date($this->course->startdate);
        }
        $enddate = get_string("fml_not_configured", "local_fliplearning");
        if (!empty($this->course->enddate)) {
            $enddate = $this->format_date($this->course->enddate);
        }
        $completion = get_string("fml_disabled", "local_fliplearning");
        if ($this->course->enablecompletion == "1") {
            $completion = get_string("fml_activated", "local_fliplearning");
        }
        $format = $this->course->format;
        $identifier = "tw_course_format_$format";
        if (get_string_manager()->string_exists($identifier, "local_fliplearning")) {
            $format = get_string($identifier, "local_fliplearning");
        }

        $course->startdate = $startdate;
        $course->enddate = $enddate;
        $course->completion = $completion;
        $course->format = $format;
        $course->grademax = $this->get_course_grade();
        return $course;
    }

    private function format_date($date) {
        $date = (int) $date;
        $year = date("Y", $date);
        $month = strtolower(date("M", $date));
        $month = get_string("fml_$month", "local_fliplearning");
        $day = strtolower(date("D", $date));
        $day = get_string("fml_$day", "local_fliplearning");
        $day_number = date("j", $date);
        $hour = date("G", $date);
        $min = date("i", $date);
        $date = "$day, $month $day_number $year, $hour:$min";
        return $date;
    }

    protected function get_course_grade() {
        global $DB;
        $item = $DB->get_record('grade_items',
            array('courseid' => $this->course->id, 'itemtype' => 'course'), 'id, grademax');
        $grade = 0;
        if ($item) {
            $grade = $item->grademax;
        }
        return $grade;
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
    public function get_sessions($weekcode = null){
        if(!self::course_is_valid()){
            return null;
        }
        $week = $this->current_week;
        if(!empty($weekcode)){
            $week = self::find_week($weekcode);
        }

        $work_sessions = self::get_work_sessions($week->weekstart, $week->weekend);
        $sessions = array_map(function($user_sessions){ return $user_sessions->sessions;}, $work_sessions);

        $sessions_count = self::count_sessions_by_duration($sessions);
        $sessions_count = self::count_sessions_by_duration_summary($sessions_count, $week->weekstart, $week->weekend);

        $sessions = self::get_sessions_by_hours($sessions);
        $sessions = self::get_sessions_by_hours_summary($sessions);

        $inverted_time = array_map(function($user_sessions){ return $user_sessions->summary;}, $work_sessions);
        $inverted_time = self::calculate_average("added", $inverted_time);
        $inverted_time = self::get_inverted_time_summary($inverted_time, (int) $week->hours_dedications);

        $response = new stdClass();
        $response->count = $sessions_count;
        $response->sessions = $sessions;
        $response->time = $inverted_time;

        return $response;
    }

    public function count_sessions($weekcode = null){
        if(!self::course_is_valid()){
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
        $names->smaller30 = get_string("ts_sessions_count_smaller30", "local_fliplearning");
        $names->greater30 = get_string("ts_sessions_count_greater30", "local_fliplearning");
        $names->greater60 = get_string("ts_sessions_count_greater60", "local_fliplearning");

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

    private function get_month_name($month_code) {
        $text = "fml_".$month_code."_short";
        $month_name = get_string($text, "local_fliplearning");
        return $month_name;
    }

    public function inverted_time($weekcode = null){
        if(!self::course_is_valid()){
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

    public function assignments_submissions($weekcode = null){
        if(!self::course_is_valid()){
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
                AND s.userid $in_users AND s.status = 'submitted' AND s.latest = 1
                ORDER BY a.id, s.userid;
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
        $names->intime_sub = get_string("ta_assigns_submissions_intime_sub", "local_fliplearning");
        $names->late_sub = get_string("ta_assigns_submissions_late_sub", "local_fliplearning");
        $names->no_sub = get_string("ta_assigns_submissions_no_sub", "local_fliplearning");

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

            $date_label = get_string("ta_assigns_submissions_assign_nodue", 'local_fliplearning');
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
        if(!self::course_is_valid()){
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

    public function quiz_attempts($weekcode = null){
        if(!self::course_is_valid()){
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

    public function get_dropout_clusters() {
        if(!self::course_is_valid()){
            return null;
        }

        $clusters = $this->get_clusters();
        $response = new stdClass();
        $response->clusters = $clusters;
        if (count($clusters)) {
            $response->clusters = $clusters;
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

            $users = self::get_work_sessions($start, $end);
            $users = self::get_progress_table($users, $cms, $enable_completion, true);

            $users_access = $this->get_users_last_access();
            $users = $this->get_users_details($users, $cms, $users_access);
            $users = $this->get_users_course_grade($users);
            $users = $this->get_users_items_grades($users);

            $response->users = $users;
            $response->total_cms = count($cms);
            $response->cms = $cms;
            $response->weeks = $this->weeks;
            $response->sections = $this->current_sections;
        }
        return $response;
    }

    public function get_clusters() {
        global $DB;
        $sql = "SELECT * FROM {fliplearning_clustering} WHERE courseid = {$this->course->id} 
                ORDER BY invertedtimeaverage";
        $rows = $DB->get_records_sql($sql);
        $rows = array_values($rows);

        $clusters = array();
        $clusters_names = array();
        array_push($clusters_names, get_string("td_cluster_high_dropout_risk", "local_fliplearning"));
        array_push($clusters_names, get_string("td_cluster_middle_dropout_risk", "local_fliplearning"));
        array_push($clusters_names, get_string("td_cluster_low_dropout_risk", "local_fliplearning"));
        foreach ($rows as $index => $row) {
            $cluster = new stdClass();
            $cluster->name = $clusters_names[$index];
            $cluster->number = $row->cluster;
            $cluster->users = explode(",", $row->userids);
            $cluster->days = $row->activedaysaverage;
            $cluster->time = $row->invertedtimeaverage;
            $cluster->sessiones = $row->sessionsnumberaverage;
            array_push($clusters, $cluster);
        }
        return $clusters;
    }

    private function get_users_details($users, $cms, $users_access) {
        date_default_timezone_set(self::get_timezone());
        $total_cms = count($cms);
        if ($total_cms > 0) {
            foreach ($users as $user) {
                $user->course_lastaccess = $this->get_user_last_access($user->id, $users_access);
            }
        }
        return $users;
    }

    private function get_user_last_access($userid, $users_access) {
        $access = new stdClass();
        $access->label = get_string("td_user_never_access", "local_fliplearning");
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

}
