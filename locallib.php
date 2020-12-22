<?php
    require_once(dirname(__FILE__).'/../../config.php');

    function local_fliplearning_new_menu_item($name, $url){
        $item = new stdClass();
        $item->name = $name;
        $item->url = $url;
        return $item;
    }

    function local_fliplearning_set_page($course, $url){
        global $PAGE;
        require_login($course, false);

        $url = new moodle_url($url);
        $url->param('courseid', $course->id);
        $PAGE->set_url($url);
        $plugin_name = get_string('pluginname', 'local_fliplearning');
        $PAGE->set_title($plugin_name);
        $PAGE->set_pagelayout('standard');
        $PAGE->set_heading($course->fullname);
        local_fliplearning_render_styles();
    }

    function local_fliplearning_render_styles(){
        global $PAGE;
        $PAGE->requires->css('/local/fliplearning/css/googlefonts.css');
        $PAGE->requires->css('/local/fliplearning/css/materialicon.css');
        $PAGE->requires->css('/local/fliplearning/css/materialdesignicons.css');
        $PAGE->requires->css('/local/fliplearning/css/vuetify.css');
        $PAGE->requires->css('/local/fliplearning/css/alertify.css');
        $PAGE->requires->css('/local/fliplearning/css/quill.core.css');
        $PAGE->requires->css('/local/fliplearning/css/quill.snow.css');
        $PAGE->requires->css('/local/fliplearning/css/quill.bubble.css');
        $PAGE->requires->css('/local/fliplearning/styles.css');
    }

    function local_fliplearning_ajax_response($data = array(), $message = null, $valid = true, $code = 200){
        local_fliplearning_set_api_headers();
        $response = [
            'valid' => $valid,
            'message' => $message,
            'data' => $data
        ];
        http_response_code($code);
        echo json_encode($response);
    }

    function local_fliplearning_set_api_headers(){
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
    }
