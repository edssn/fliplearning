<?php
require_once(dirname(__FILE__).'/../../config.php');
global $COURSE, $USER;

$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$context = context_course::instance($course->id);

$url = new moodle_url('/local/fliplearning/graph1.php?courseid='.$COURSE->id);
$PAGE->set_url($url);
require_login($course, false);
$PAGE->set_title('Gráfico 1');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);


$chart = array(
    "chart" => array(
        "type" => "pie",
        "options3d" => array(
            "enabled" => true,
            "alpha" => 45,
            "beta" => 0,
        ),
    ),
    "title" => array(
        "text" => "Titulo de Gráfico"
    ),
    "subtitle" => array(
        "text" => "Subtitulo de Gráfico"
    ),
    "accessibility" => array(
        "point" => array(
            "valueSuffix" => '%'
        )
    ),
    "tooltip" => array (
        "pointFormat" => "{series.name}: <b>{point.percentage:.1f}%</b>"
    ),
    "plotOptions" => array(
        "pie" => array(
            "allowPointSelect" => true,
            "cursor" => "pointer",
            "depth" => 35,
            "dataLabels" =>  array(
                "enabled" => true,
                "format" => "{point.name}"
            )
        )
    ),
    "series" => array(
        array(
            "type" => "pie",
            "name" => "Browser share",
            "data" => array(
                ["Firefox", 45.0],
                ["IE", 26.8],
                array(
                    "name" => "Chrome",
                    "y" => 12.8,
                    "sliced" => true,
                    "selected" => true
                ),
                ["Safari", 8.5],
                ["Opera", 6.2],
                ["Others", 0.7]
            ),
        ),
    ),
    "credits" => array(
        "enabled" => false
    ),
);

$content = array(
    "chart" => $chart,
    "options" => [],
);

$PAGE->requires->js_call_amd('local_fliplearning/graph1','init', ['content' => $content]);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fliplearning/graph1', ['content' => $content]);
echo $OUTPUT->footer();
