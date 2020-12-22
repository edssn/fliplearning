<?php

    define('AJAX_SCRIPT', true);

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once(dirname(__FILE__) . '/locallib.php');

    $action = optional_param('action', false ,PARAM_ALPHA);
    $weeks = optional_param('weeks', false, PARAM_RAW);
    $courseid = optional_param('courseid', false, PARAM_INT);
    $userid = optional_param('userid', false, PARAM_INT);

    $newinstance = optional_param('newinstance', false, PARAM_BOOL);

    $params = array();
    $func = null;

    if($courseid){
        global $COURSE;
        $COURSE = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    }

    if($userid){
        global $USER;
        $USER = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
    }

    if($action == 'saveconfigweek'){
        array_push($params, $weeks);
        array_push($params, $courseid);
        array_push($params, $userid);
        array_push($params, $newinstance);
        if($weeks && $courseid && $userid){
            $func = "local_fliplearning_save_weeks_config";
        }
    }

    if(isset($params) && isset($func)){
        call_user_func_array($func, $params);
    } else {
        $message = get_string('api_invalid_data', 'local_fliplearning');
        local_fliplearning_ajax_response(array(), $message, false, 400);
    }

    function local_fliplearning_save_weeks_config($weeks, $courseid, $userid, $newinstance){
        local_fliplearning_log::create("setweeks", "change_config", $userid, $courseid);
        $weeks = json_decode($weeks);
        $configweeks = new local_fliplearning_configweeks($courseid, $userid);
        if($newinstance){
            $configweeks->create_instance();
        }
        $configweeks->last_instance();
        $configweeks->save_weeks($weeks);
        $configweeks = new local_fliplearning_configweeks($courseid, $userid);
        local_fliplearning_ajax_response(["settings" => $configweeks->get_settings()]);
    }