<?php
require_once(dirname(__FILE__).'/../../config.php');
global $COURSE, $USER;

$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$context = context_course::instance($course->id);

$url = new moodle_url('/local/fliplearning/setweeks.php?courseid='.$COURSE->id);
$PAGE->set_url($url);
require_login($course, false);
$PAGE->set_title('Configurar Semanas');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);



echo $OUTPUT->header();
echo 'hola';
echo $OUTPUT->footer();