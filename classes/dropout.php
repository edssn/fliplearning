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

require_once("lib_trait.php");

use stdClass;

class dropout {
    use \lib_trait;

    const MINUTES_TO_NEW_SESSION = 30;
    const USER_FIELDS = "id, username, firstname, lastname, email, lastaccess, picture, deleted";
    public $course;
    public $user;
    public $current_sections;
    protected $users;

    function __construct($course, $userid){
        $this->course = self::get_course($course);
        $this->user = self::get_user($userid);
        $this->current_sections = self::get_course_sections();
        $this->users = self::get_student_ids();;
    }

    public function hello(){
        return "hola";
    }

    public function generate_data(){
        $start = $this->course->startdate;
        $end = null;
        if(isset($this->course->enddate) && ((int)$this->course->enddate) > 0) {
            $end = $this->course->enddate;
        }
        $conditions = self::conditions_for_work_sessions($start, $end);
        $users_sessions = self::get_sessions_from_logs($conditions);
        $cms = self::get_course_modules(false);
        //$cms = array_filter($cms, function($module){ return $module['visible'] == 1 && $module['modname'] != 'label';});
        $cms = array_filter($cms, function($module){ return $module->visible == 1 && $module->modname != 'label';});
        $cm = self::calculate_indicator($cms, $users_sessions);
        return $response = [];
    }

    private function calculate_indicator($cms, $users){

        foreach ($cms as $cm) {
            if ($cm->modname == 'assign' || $cm->modname == 'assignment') {
                $users = self::get_assign_indicators($cm, $users);
            } else if ($cm->modname == 'book') {
                $users = self::get_book_indicators($cm, $users);
            } else if ($cm->modname == 'chat') {

            } else if ($cm->modname == 'choice') {
                $users = self::get_choice_indicators($cm, $users);
            } else if ($cm->modname == 'data') {
                $users = self::get_data_indicators($cm, $users);
            } else if ($cm->modname == 'feedback') {
                $users = self::get_feedback_indicators($cm, $users);
            } else if ($cm->modname == 'folder') {
                $users = self::get_folder_indicators($cm, $users);
            } else if ($cm->modname == 'forum') {

            } else if ($cm->modname == 'glossary') {
                $users = self::get_glossary_indicators($cm, $users);
            } else if ($cm->modname == 'h5pactivity') {

            } else if ($cm->modname == 'imscp') {
                $users = self::get_imscp_indicators($cm, $users);
            } else if ($cm->modname == 'lesson') {

            } else if ($cm->modname == 'lti') {
                $users = self::get_lti_indicators($cm, $users);
            } else if ($cm->modname == 'page') {
                $users = self::get_page_indicators($cm, $users);
            } else if ($cm->modname == 'quiz') {

            } else if ($cm->modname == 'resource') {
                $users = self::get_resource_indicators($cm, $users);
            } else if ($cm->modname == 'scorm') {

            } else if ($cm->modname == 'survey') {
                $users = self::get_survey_indicators($cm, $users);
            } else if ($cm->modname == 'url') {
                $users = self::get_url_indicators($cm, $users);
            } else if ($cm->modname == 'wiki') {
                $users = self::get_wiki_indicators($cm, $users);
            } else if ($cm->modname == 'workshop') {

            }

        }

        return $response = [];
    }

    private function get_assign_indicators ($cm, $users) {
        $cognitive = new \mod_assign\analytics\indicator\cognitive_depth();
        $social = new \mod_assign\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $cognitive_second_level = 0;
            $cognitive_third_level = 0;
            $cognitive_fourth_level = 0;
            $social_first_level = 0;
            $social_second_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->action == "viewed" && $log->target == "course_module" && $log->objecttable == "assign") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                } else if ($log->action == "submitted" && $log->target == "assessable" && $log->objecttable == "assign_submission") {
                    $cognitive_second_level++;
                } else if ($log->action == "viewed" && $log->target == "feedback" && $log->objecttable == "assign_grades") {
                    $cognitive_third_level = 1;
                } else if ($log->action == "created" && $log->target == "comment" && $log->objecttable == "comments") {
                    $social_second_level = 1;
                }
            }
            if ($cognitive_second_level > 1) {
                $cognitive_fourth_level = 1;
                $cognitive_second_level = 1;
            }
            $user_cognitive_level = $cognitive_first_level + $cognitive_second_level + $cognitive_third_level + $cognitive_fourth_level;
            $user_social_breadth = $social_first_level + $social_second_level;
            $user_cognitive_level_value = $user_cognitive_level/$cm_cognitive_depth;
            $user_social_breadth_value = $user_social_breadth/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;

            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_book_indicators ($cm, $users) {
        $cognitive = new \mod_book\analytics\indicator\cognitive_depth();
        $social = new \mod_book\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $social_first_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_book" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                }
            }
            $user_cognitive_level_value = $cognitive_first_level/$cm_cognitive_depth;
            $user_social_breadth_value = $social_first_level/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;
            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_chat_indicators ($cm, $users) {
        $cognitive = new \mod_chat\analytics\indicator\cognitive_depth();
        $social = new \mod_chat\analytics\indicator\social_breadth();
        $g = $cognitive->get_indicator_type();
        $h = $social->get_cognitive_depth_level($cm);
    }

    private function get_choice_indicators ($cm, $users) {
        $cognitive = new \mod_choice\analytics\indicator\cognitive_depth();
        $social = new \mod_choice\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $cognitive_second_level = 0;
            $social_first_level = 0;
            $social_second_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_choice" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                } else if ($log->component == "mod_choice" && $log->action == "created" && $log->target == "answer") {
                    $cognitive_second_level = 1;
                    $social_second_level = 1;
                }
            }
            $user_cognitive_level = $cognitive_first_level + $cognitive_second_level;
            $user_social_breadth = $social_first_level + $social_second_level;
            $user_cognitive_level_value = $user_cognitive_level/$cm_cognitive_depth;
            $user_social_breadth_value = $user_social_breadth/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;
            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_data_indicators ($cm, $users) {
        $cognitive = new \mod_data\analytics\indicator\cognitive_depth();
        $social = new \mod_data\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $cognitive_second_level = 0;
            $social_first_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_data" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                } else if ($log->component == "mod_data" && $log->action == "created" && $log->target == "record") {
                    $cognitive_second_level = 1;
                }
            }
            $user_cognitive_level = $cognitive_first_level + $cognitive_second_level;
            $user_cognitive_level_value = $user_cognitive_level/$cm_cognitive_depth;
            $user_social_breadth_value = $social_first_level/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;

            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_feedback_indicators ($cm, $users) {
        $cognitive = new \mod_feedback\analytics\indicator\cognitive_depth();
        $social = new \mod_feedback\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $cognitive_second_level = 0;
            $social_first_level = 0;
            $social_second_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_feedback" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                } else if ($log->component == "mod_feedback" && $log->action == "submitted" && $log->target == "response") {
                    $cognitive_second_level = 1;
                    $social_second_level = 1;
                }
            }
            $user_cognitive_level = $cognitive_first_level + $cognitive_second_level;
            $user_social_breadth = $social_first_level + $social_second_level;
            $user_cognitive_level_value = $user_cognitive_level/$cm_cognitive_depth;
            $user_social_breadth_value = $user_social_breadth/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;

            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_folder_indicators ($cm, $users) {
        $cognitive = new \mod_folder\analytics\indicator\cognitive_depth();
        $social = new \mod_folder\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $social_first_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_folder" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                }
            }
            $user_cognitive_level_value = $cognitive_first_level/$cm_cognitive_depth;
            $user_social_breadth_value = $social_first_level/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;
            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_forum_indicators ($cm, $users) {
        $cognitive = new \mod_forum\analytics\indicator\cognitive_depth();
        $social = new \mod_forum\analytics\indicator\social_breadth();
        $g = $cognitive->get_indicator_type();
        $h = $social->get_cognitive_depth_level($cm);
    }

    private function get_glossary_indicators ($cm, $users) {
        $cognitive = new \mod_glossary\analytics\indicator\cognitive_depth();
        $social = new \mod_glossary\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $cognitive_second_level = 0;
            $social_first_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_glossary" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                } else if ($log->component == "mod_glossary" && $log->action == "created" && $log->target == "entry") {
                    $cognitive_second_level = 1;
                }
            }
            $user_cognitive_level = $cognitive_first_level + $cognitive_second_level;
            $user_social_breadth = $social_first_level;
            $user_cognitive_level_value = $user_cognitive_level/$cm_cognitive_depth;
            $user_social_breadth_value = $user_social_breadth/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;

            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_h5pactivity_indicators ($cm, $users) {
        $cognitive = new \mod_h5pactivity\analytics\indicator\cognitive_depth();
        $social = new \mod_h5pactivity\analytics\indicator\social_breadth();
        $g = $cognitive->get_indicator_type();
        $h = $social->get_cognitive_depth_level($cm);
    }

    private function get_imscp_indicators ($cm, $users) {
        $cognitive = new \mod_imscp\analytics\indicator\cognitive_depth();
        $social = new \mod_imscp\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $social_first_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_imscp" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                }
            }
            $user_cognitive_level_value = $cognitive_first_level/$cm_cognitive_depth;
            $user_social_breadth_value = $social_first_level/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;
            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_lesson_indicators ($cm, $users) {
        $cognitive = new \mod_lesson\analytics\indicator\cognitive_depth();
        $social = new \mod_lesson\analytics\indicator\social_breadth();
        $g = $cognitive->get_indicator_type();
        $h = $social->get_cognitive_depth_level($cm);
    }

    private function get_lti_indicators ($cm, $users) {
        $cognitive = new \mod_lti\analytics\indicator\cognitive_depth();
        $social = new \mod_lti\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $social_first_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_lti" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                }
            }
            $user_cognitive_level_value = $cognitive_first_level/$cm_cognitive_depth;
            $user_social_breadth_value = $social_first_level/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;
            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_page_indicators ($cm, $users) {
        $cognitive = new \mod_page\analytics\indicator\cognitive_depth();
        $social = new \mod_page\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $social_first_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_page" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                }
            }
            $user_cognitive_level_value = $cognitive_first_level/$cm_cognitive_depth;
            $user_social_breadth_value = $social_first_level/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;
            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_quiz_indicators ($cm, $users) {
        $cognitive = new \mod_quiz\analytics\indicator\cognitive_depth();
        $social = new \mod_quiz\analytics\indicator\social_breadth();
        $g = $cognitive->get_indicator_type();
        $h = $social->get_cognitive_depth_level($cm);
    }

    private function get_resource_indicators ($cm, $users) {
        $cognitive = new \mod_resource\analytics\indicator\cognitive_depth();
        $social = new \mod_resource\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $social_first_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_resource" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                }
            }
            $user_cognitive_level_value = $cognitive_first_level/$cm_cognitive_depth;
            $user_social_breadth_value = $social_first_level/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;
            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_scorm_indicators ($cm, $users) {
        $cognitive = new \mod_scorm\analytics\indicator\cognitive_depth();
        $social = new \mod_scorm\analytics\indicator\social_breadth();
        $g = $cognitive->get_indicator_type();
        $h = $social->get_cognitive_depth_level($cm);
    }

    private function get_survey_indicators ($cm, $users) {
        $cognitive = new \mod_survey\analytics\indicator\cognitive_depth();
        $social = new \mod_survey\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $cognitive_second_level = 0;
            $social_first_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_survey" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                } else if ($log->component == "mod_survey" && $log->action == "submitted" && $log->target == "response") {
                    $cognitive_second_level = 1;
                }
            }
            $user_cognitive_level = $cognitive_first_level + $cognitive_second_level;
            $user_social_breadth = $social_first_level;
            $user_cognitive_level_value = $user_cognitive_level/$cm_cognitive_depth;
            $user_social_breadth_value = $user_social_breadth/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;

            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_url_indicators ($cm, $users) {
        $cognitive = new \mod_url\analytics\indicator\cognitive_depth();
        $social = new \mod_url\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $social_first_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_url" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                }
            }
            $user_cognitive_level_value = $cognitive_first_level/$cm_cognitive_depth;
            $user_social_breadth_value = $social_first_level/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;
            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_wiki_indicators ($cm, $users) {
        $cognitive = new \mod_wiki\analytics\indicator\cognitive_depth();
        $social = new \mod_wiki\analytics\indicator\social_breadth();
        $cm_cognitive_depth = $cognitive->get_cognitive_depth_level($cm);
        $cm_social_breadth = $social->get_social_breadth_level($cm);
        foreach ($users as $user) {
            $cm_logs = array();
            $logs = $user->logs;
            foreach ($logs as $log) {
                if ($log->contextlevel == "70" && $log->contextinstanceid == $cm->id) {
                    array_push($cm_logs, $log);
                }
            }

            $cognitive_first_level = 0;
            $cognitive_second_level = 0;
            $social_first_level = 0;
            foreach ($cm_logs as $log) {
                if ($log->component == "mod_wiki" && $log->action == "viewed" && $log->target == "course_module") {
                    $cognitive_first_level = 1;
                    $social_first_level = 1;
                } else if ($log->component == "mod_wiki" && ($log->action == "created" || $log->action == "updated") && $log->target == "page") {
                    $cognitive_second_level = 1;
                }
            }
            $user_cognitive_level = $cognitive_first_level + $cognitive_second_level;
            $user_social_breadth = $social_first_level;
            $user_cognitive_level_value = $user_cognitive_level/$cm_cognitive_depth;
            $user_social_breadth_value = $user_social_breadth/$cm_social_breadth;
            $indicator = new stdClass();
            $indicator->cognitive =$user_cognitive_level_value;
            $indicator->social =$user_social_breadth_value;

            if (!isset($user->cms)) {
                $user->cms = array();
            }
            $label = $cm->modname.$cm->id;
            $user->cms[$label] = $indicator;
        }
        return $users;
    }

    private function get_workshop_indicators ($cm, $users) {
        $cognitive = new \mod_workshop\analytics\indicator\cognitive_depth();
        $social = new \mod_workshop\analytics\indicator\social_breadth();
        $g = $cognitive->get_indicator_type();
        $h = $social->get_cognitive_depth_level($cm);
    }
}