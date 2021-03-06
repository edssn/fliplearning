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
 * Plugin strings are defined here.
 *
 * @package     local_fliplearning
 * @category    string
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Flip My Learning';

/* Global */
$string['fml_graph_generating'] = 'Estamos construyendo el reporte, por favor espere un momento.';
$string['fml_weeks_not_config'] = 'El curso no ha sido configurado por el profesor, por lo que no hay visualizaciones que mostrar.';
$string['fml_helplabel'] = 'Ayuda';
$string['fml_exitbutton'] = '¡Entendido!';
$string['fml_only_student'] = 'Este reporte es solo para estudiantes';
$string["fml_send_mail"] = "(Clic para enviar correo)";
$string["fml_about"] = "Acerca de este Gráfico";
$string["fml_about_table"] = "Acerca de esta Tabla";
$string["fml_not_configured"] = "No Configurado";
$string["fml_activated"] = "Activado";
$string["fml_disabled"] = "Desactivado";
$string["fml_sessions"] = "Sesiones";
$string["fml_resources"] = "Recursos";
$string['fml_finished_resources'] = 'Recursos Finalizados';
$string["fml_session_text"] = "sesión";
$string["fml_sessions_text"] = "sesiones";
$string["fml_student_text"] = "estudiante";
$string["fml_students_text"] = "estudiantes";
$string['fml_change_timezone'] = 'Zona horaria:';
$string['fml_of_conector'] = 'de';
$string['fml_module_label'] = 'recurso';
$string['fml_modules_label'] = 'recursos';
$string['fml_finished_label'] = 'finalizado';
$string['fml_finisheds_label'] = 'finalizados';

/* Admin Settings */
$string['fml_use_navbar_menu'] = 'Habilitar menú desplegable';
$string['fml_use_navbar_menu_desc'] = 'Desplegar el menú del plugin en la barra de navegación (parte superior derecha). De lo contrario, el menú del plugin estará en el bloque de navegación.';

/* Menú */
$string['fml_menu_sessions'] = 'Sesiones de Estudio';
$string['fml_menu_setweek'] = "Configurar Semanas";
$string['fml_menu_assignments'] = 'Tareas';
$string['fml_menu_grades'] = 'Calificaciones';
$string['fml_menu_quiz'] = 'Evaluaciones';
$string['fml_menu_dropout'] = 'Deserción';
$string['fml_menu_logs'] = "Descargar Registros";
$string['fml_menu_general'] = "Indicadores Generales";

/* Nav Bar Menu */
$string['fml_togglemenu'] = 'Mostrar/Ocultar menú de FML';

/* Pagination component */
$string['fml_pagination'] = 'Semana:';
$string['fml_pagination_title'] = 'Selección semana';
$string['fml_pagination_component_to'] = 'al';
$string['fml_pagination_component_name'] = 'Semana';

/* Student Goups */
$string['fml_group_allstudent'] = 'Todos los estudiantes';

/* General Errors */
$string['fml_api_error_network'] = "Ha ocurrido un error en la comunicación con el servidor.";
$string['fml_api_invalid_data'] = 'Datos incorrectos';
$string['fml_api_save_successful'] = 'Se han guardado los datos correctamente en el servidor';
$string['fml_api_cancel_action'] = 'Has cancelado la acción';

/* Admin Schedule Task Screen*/
$string['fml_generate_dropout_data_task'] = 'Proceso para generar datos de Deserción para Flip My Learning';

/* Chart*/
$string['fml_chart_loading'] = 'Cargando...';
$string['fml_chart_exportButtonTitle'] = "Exportar";
$string['fml_chart_printButtonTitle'] = "Imprimir";
$string['fml_chart_rangeSelectorFrom'] = "De";
$string['fml_chart_rangeSelectorTo'] = "Hasta";
$string['fml_chart_rangeSelectorZoom'] = "Rango";
$string['fml_chart_downloadPNG'] = 'Descargar imagen PNG';
$string['fml_chart_downloadJPEG'] = 'Descargar imagen JPEG';
$string['fml_chart_downloadPDF'] = 'Descargar documento PDF';
$string['fml_chart_downloadSVG'] = 'Descargar imagen SVG';
$string['fml_chart_downloadCSV'] = 'Descargar CSV';
$string['fml_chart_downloadXLS'] = 'Descargar XLS';
$string['fml_chart_exitFullscreen'] = 'Salir de Pantalla Completa';
$string['fml_chart_hideData'] = 'Ocultar Tabla de Datos';
$string['fml_chart_noData'] = 'No hay datos que mostrar';
$string['fml_chart_printChart'] = 'Imprimir Gráfico';
$string['fml_chart_viewData'] = 'Ver Tabla de Datos';
$string['fml_chart_viewFullscreen'] = 'Ver en Pantalla Completa';
$string['fml_chart_resetZoom'] = 'Reiniciar zoom';
$string['fml_chart_resetZoomTitle'] = 'Reiniciar zoom nivel 1:1';

/* Email */
$string['fml_validation_subject_text'] = 'Asunto es requerido';
$string['fml_validation_message_text'] = 'Mensaje es requerido';
$string['fml_subject_label'] = 'Agrega un asunto';
$string['fml_message_label'] = 'Agrega un mensaje';
$string['fml_submit_button'] = 'Enviar';
$string['fml_cancel_button'] = 'Cancelar';
$string['fml_close_button'] = 'Cerrar';
$string['fml_emailform_title'] = 'Enviar Correo';
$string['fml_sending_text'] = 'Enviando Correos';
$string['fml_recipients_label'] = 'Para';
$string['fml_mailsended_text'] = 'Correos Enviados';
$string['fml_email_footer_text'] = 'Este es un correo electrónico enviado con Fliplearning.';
$string['fml_email_footer_prefix'] = 'Ve a';
$string['fml_email_footer_suffix'] = 'para más información.';
$string['fml_assign_url'] = '/mod/assign/view.php?id=';
$string['fml_assignment_url'] = '/mod/assignment/view.php?id=';
$string['fml_book_url'] = '/mod/book/view.php?id=';
$string['fml_chat_url'] = '/mod/chat/view.php?id=';
$string['fml_choice_url'] = '/mod/choice/view.php?id=';
$string['fml_data_url'] = '/mod/data/view.php?id=';
$string['fml_feedback_url'] = '/mod/feedback/view.php?id=';
$string['fml_folder_url'] = '/mod/folder/view.php?id=';
$string['fml_forum_url'] = '/mod/forum/view.php?id=';
$string['fml_glossary_url'] = '/mod/glossary/view.php?id=';
$string['fml_h5pactivity_url'] = '/mod/h5pactivity/view.php?id=';
$string['fml_imscp_url'] = '/mod/imscp/view.php?id=';
$string['fml_label_url'] = '/mod/label/view.php?id=';
$string['fml_lesson_url'] = '/mod/lesson/view.php?id=';
$string['fml_lti_url'] = '/mod/lti/view.php?id=';
$string['fml_page_url'] = '/mod/page/view.php?id=';
$string['fml_quiz_url'] = '/mod/quiz/view.php?id=';
$string['fml_resource_url'] = '/mod/resource/view.php?id=';
$string['fml_scorm_url'] = '/mod/scorm/view.php?id=';
$string['fml_survey_url'] = '/mod/survey/view.php?id=';
$string['fml_url_url'] = '/mod/url/view.php?id=';
$string['fml_wiki_url'] = '/mod/wiki/view.php?id=';
$string['fml_workshop_url'] = '/mod/workshop/view.php?id=';
$string['fml_course_url'] = '/course/view.php?id=';

/* Course Modules Types */
$string['fml_assign'] = 'Tarea';
$string['fml_assignment'] = 'Tarea';
$string['fml_attendance'] = 'Asistencia';
$string['fml_book'] = 'Libro';
$string['fml_chat'] = 'Chat';
$string['fml_choice'] = 'Elección';
$string['fml_data'] = 'Base de Datos';
$string['fml_feedback'] = 'Retroalimentación';
$string['fml_folder'] = 'Carpeta';
$string['fml_forum'] = 'Foro';
$string['fml_glossary'] = 'Glosario';
$string['fml_h5pactivity'] = 'H5P';
$string['fml_imscp'] = 'Contenido IMS';
$string['fml_label'] = 'Etiqueta';
$string['fml_lesson'] = 'Lección';
$string['fml_lti'] = 'Contenido LTI';
$string['fml_page'] = 'Página';
$string['fml_quiz'] = 'Evaluación';
$string['fml_resource'] = 'Recurso';
$string['fml_scorm'] = 'Paquete SCORM';
$string['fml_survey'] = 'Encuesta';
$string['fml_url'] = 'Url';
$string['fml_wiki'] = 'Wiki';
$string['fml_workshop'] = 'Taller';


/* Time */
$string['fml_year'] = 'año';
$string['fml_years'] = 'años';
$string['fml_month'] = 'mes';
$string['fml_months'] = 'meses';
$string['fml_day'] = 'día';
$string['fml_days'] = 'días';
$string['fml_hour'] = 'hora';
$string['fml_hours'] = 'horas';
$string['fml_hours_short'] = 'h';
$string['fml_minute'] = 'minuto';
$string['fml_minutes'] = 'minutos';
$string['fml_minutes_short'] = 'm';
$string['fml_second'] = 'segundo';
$string['fml_seconds'] = 'segundos';
$string['fml_seconds_short'] = 's';
$string['fml_ago'] = 'atrás';
$string['fml_now'] = 'justo ahora';

$string['fml_mon'] = 'Lunes';
$string['fml_tue'] = 'Martes';
$string['fml_wed'] = 'Miércoles';
$string['fml_thu'] = 'Jueves';
$string['fml_fri'] = 'Viernes';
$string['fml_sat'] = 'Sábado';
$string['fml_sun'] = 'Domingo';
$string['fml_mon_short'] = 'Lun';
$string['fml_tue_short'] = 'Mar';
$string['fml_wed_short'] = 'Mié';
$string['fml_thu_short'] = 'Jue';
$string['fml_fri_short'] = 'Vie';
$string['fml_sat_short'] = 'Sáb';
$string['fml_sun_short'] = 'Dom';

$string['fml_jan'] = 'Enero';
$string['fml_feb'] = 'Febrero';
$string['fml_mar'] = 'Marzo';
$string['fml_apr'] = 'Abril';
$string['fml_may'] = 'Mayo';
$string['fml_jun'] = 'Junio';
$string['fml_jul'] = 'Julio';
$string['fml_aug'] = 'Agosto';
$string['fml_sep'] = 'Septiembre';
$string['fml_oct'] = 'Octubre';
$string['fml_nov'] = 'Noviembre';
$string['fml_dec'] = 'Diciembre';
$string['fml_jan_short'] = 'Ene';
$string['fml_feb_short'] = 'Feb';
$string['fml_mar_short'] = 'Mar';
$string['fml_apr_short'] = 'Abr';
$string['fml_may_short'] = 'May';
$string['fml_jun_short'] = 'Jun';
$string['fml_jul_short'] = 'Jul';
$string['fml_aug_short'] = 'Ago';
$string['fml_sep_short'] = 'Sep';
$string['fml_oct_short'] = 'Oct';
$string['fml_nov_short'] = 'Nov';
$string['fml_dec_short'] = 'Dic';

$string['fml_week1'] = 'Sem 1';
$string['fml_week2'] = 'Sem 2';
$string['fml_week3'] = 'Sem 3';
$string['fml_week4'] = 'Sem 4';
$string['fml_week5'] = 'Sem 5';
$string['fml_week6'] = 'Sem 6';

$string['fml_00'] = '12am';
$string['fml_01'] = '1am';
$string['fml_02'] = '2am';
$string['fml_03'] = '3am';
$string['fml_04'] = '4am';
$string['fml_05'] = '5am';
$string['fml_06'] = '6am';
$string['fml_07'] = '7am';
$string['fml_08'] = '8am';
$string['fml_09'] = '9am';
$string['fml_10'] = '10am';
$string['fml_11'] = '11am';
$string['fml_12'] = '12pm';
$string['fml_13'] = '1pm';
$string['fml_14'] = '2pm';
$string['fml_15'] = '3pm';
$string['fml_16'] = '4pm';
$string['fml_17'] = '5pm';
$string['fml_18'] = '6pm';
$string['fml_19'] = '7pm';
$string['fml_20'] = '8pm';
$string['fml_21'] = '9pm';
$string['fml_22'] = '10pm';
$string['fml_23'] = '11pm';

/* Teacher Set Weeks */
$string['tw_section_title'] = 'Configuración de las Semanas del Curso';
$string['tw_section_help_title'] = 'Configuración de las Semanas del Curso';
$string['tw_section_help_description'] = 'Para comenzar, debe configurar el curso por semanas y definir una fecha de inicio para la primera semana (el resto de semanas se realizará de forma automática a partir de esta fecha. A continuación, debe asociar las actividades o módulos relacionadas a cada semana arrastrándolas de la columna de la derecha a la semana correspondiente.  No es necesario asignar todas las actividades o módulos a las semanas, simplemente aquellas que se quieran considerar para hacer el seguimiento de los estudiantes. Finalmente, debe clicar sobre el botón Guardar para conservar su configuración.';
$string['tw_sections'] = "Secciones disponibles en el curso";
$string['tw_weeks_of_course'] = "Planificación de semanas";
$string['tw_add_new_week'] = "Agregar semana";
$string['tw_start'] = "Comienza:";
$string['tw_end'] = "Termina:";
$string['tw_week'] = "Semana";
$string['tw_save'] = "Guardar configuración";
$string['tw_time_dedication'] = "¿Cuántas horas de trabajo espera que los estudiantes dediquen a su curso esta semana?";
$string['tw_enable_scroll'] = "Activar el modo desplazamiento para semanas y temas";
$string['tw_label_section_removed'] = "Eliminado del curso";
$string['tw_error_section_removed'] = "Una sección asignada a una semana se ha eliminado del curso, debe eliminarla de tu planificación para poder continuar.";
$string['tw_save_warning_title'] = "¿Está seguro/a que desea guardar los cambios?";
$string['tw_save_warning_content'] = "Si modifica la configuración de las semanas cuando el curso ya ha comenzado es posible que se pierdan datos...";
$string['tw_confirm_ok'] = "Guardar";
$string['tw_confirm_cancel'] = "Cancelar";
$string['tw_error_empty_week'] = "No puede guardar los cambios con una semana vacía. Por favor, elimínela y inténtelo de nuevo.";
$string['tw_new_group_title'] = "Nueva instancia de configuración";
$string['tw_new_group_text'] = "Hemos detectado que su curso ha finalizado, si desea configurar las semanas para trabajar con nuevos estudiantes, debe activar el botón de más abajo. Esto permitirá separar los datos de los estudiantes actuales de los de cursos anteriores, evitando mezclarlos.";
$string['tw_new_group_button_label'] = "Guardar configuración como nueva instancia";
$string['tw_course_format_weeks'] = 'Semana';
$string['tw_course_format_topics'] = 'Tema';
$string['tw_course_format_social'] = 'Social';
$string['tw_course_format_singleactivity'] = 'Actividad única';
$string['tw_plugin_requirements_title'] = 'Estado:';
$string['tw_plugin_requirements_descriptions'] = 'El plugin será visible y mostrará los reportes para estudiantes y profesores cuando se cumplan las siguientes condiciones...';
$string['tw_plugin_requirements_has_users'] = 'El curso debe poseer al menos un estudiante matriculado';
$string['tw_plugin_requirements_course_start'] = 'La fecha actual debe ser mayor a la fecha de inicio de la primera semana configurada.';
$string['tw_plugin_requirements_has_sections'] = 'Las semanas configuradas poseen al menos una sección.';
$string['tw_plugin_visible'] = 'Reportes visibles.';
$string['tw_plugin_hidden'] = 'Reportes ocultos.';
$string['tw_title_conditions'] = 'Condiciones de uso';

$string['tw_time_frame'] = 'Período de Tiempo';
$string['tw_estimated_time'] = 'Tiempo Estimado';
$string['tw_activities_list_dialog_title'] = 'Actividades de la Sección';
$string['tw_activities_list_dialog_description'] = 'Indique el tiempo para las distintas actividades';


/* Teacher General Indicators */
$string['tg_section_help_title'] = 'Indicadores Generales';
$string['tg_section_help_description'] = 'Esta sección contiene visualizaciones con indicadores generales relacionados a la configuración del curso, recursos asignados por semanas, sesiones de estudio y progreso de los estudiantes a lo largo del curso. Las visualizaciones de esta sección muestran los indicadores desde la fecha de inicio hasta la de finalización del curso (o hasta la fecha actual en caso de que el curso aún no ha terminado).';

$string['tg_indicators_students'] = 'Estudiantes';
$string['tg_indicators_weeks'] = 'Semanas';
$string['tg_indicators_grademax'] = 'Calificación';
$string['tg_indicators_course_start'] = 'Inicio';
$string['tg_indicators_course_end'] = 'Fin';
$string['tg_indicators_course_format'] = 'Formato';
$string['tg_indicators_course_completion'] = 'Completitud de Módulos';
$string["tg_indicators_student_progress"] = "Progreso de los Estudiantes";

$string['tg_week_resources_help_title'] = 'Recursos por Semanas';
$string['tg_week_resources_help_description_p1'] = 'Este gráfico visualiza la cantidad de recursos de cada una de las secciones del curso asignadas a cada semana de estudio configurada en la sección <i>Configurar Semanas</i>. Si una semana tiene asignada dos o más secciones del curso, los recursos de dichas secciones se suman para el cálculo del total de recursos de una semana.';
$string['tg_week_resources_help_description_p2'] = 'En el eje x del gráfico se encuentran el total de recursos y actividades de las secciones asignadas a cada semana configurada de Flip My Learning. En el eje y se encuentran las semanas de estudio configuradas.';
$string["tg_week_resources_chart_title"] = "Recursos por Semanas";
$string["tg_week_resources_yaxis_title"] = "Cantidad de Recursos";

$string['tg_weeks_sessions_help_title'] = 'Sesiones por Semana';
$string['tg_week_sessions_help_description_p1'] = 'Este gráfico muestra la cantidad de sesiones de estudio realizadas por los estudiantes en cada semana a partir de la fecha de inicio del curso. Se considera el acceso al curso por parte del estudiante como el inicio de una sesión de estudio. Una sesión se considera finalizada cuando el tiempo transcurrido entre dos interacciones de un estudiante supera los 30 minutos.';
$string['tg_week_sessions_help_description_p2'] = 'En el eje x del gráfico se encuentran las semanas de cada mes. En el eje y del gráfico se encuentran los diferentes meses del año partiendo del mes de creación del curso. Para mantener la simetría del gráfico se ha colocado un total de cinco semanas para cada mes, sin embargo, no todos los meses tiene tal cantidad de semanas. Dichos meses, solo sumarán sesiones hasta la semana cuatro.';
$string['ts_weeks_sessions_chart_title'] = 'Sesiones por Semana';

$string['tg_progress_table_help_title'] = 'Progreso de los estudiantes';
$string['tg_progress_table_help_description'] = 'Este tabla muestra una lista con todos los estudiantes matriculados en el curso junto con su progreso, cantidad de sesiones y tiempo invertido. Para el cálculo del progreso se han considerado todos los recursos del curso a excepción de los de tipo <i>Label</i>. Para determinar si un estudiante ha finalizado un recurso se verifica en primer lugar si el recurso tiene habilitada la configuración de completitud. En caso de ser así, se busca si el estudiante ya ha completado la actividad en base a esa configuración. De lo contrario, la actividad se considera completa si el estudiante la ha visto al menos una vez.';
$string["tg_progress_table_table_title"] = "Progreso del Curso";
$string['tg_progress_table_thead_name'] = 'Nombre';
$string['tg_progress_table_thead_lastname'] = 'Apellidos';
$string['tg_progress_table_thead_email'] = 'Correo';
$string['tg_progress_table_thead_progress'] = 'Progreso (%)';
$string['tg_progress_table_thead_sessions'] = 'Sesiones';
$string['tg_progress_table_thead_time'] = 'Tiempo Invertido';


/* Teacher Sessions */
$string['ts_section_help_title'] = 'Sesiones de Estudio';
$string['ts_section_help_description'] = 'Esta sección contiene visualizaciones con indicadores relacionados a la actividad de los estudiantes en el curso medida en términos de sesiones realizadas, tiempo promedio invertido en el curso por semana y sesiones de estudio en intervalos de tiempo. Los datos presentados en esta sección varían dependiendo de la semana de estudio seleccionada.';

$string['ts_inverted_time_help_title'] = 'Tiempo Invertido de los Estudiantes';
$string['ts_inverted_time_help_description_p1'] = 'Este gráfico muestra el tiempo promedio invertido por parte de los estudiantes en la semana en comparación del tiempo promedio planificado por parte del docente.';
$string['ts_inverted_time_help_description_p2'] = 'En el eje x del gráfico se encuentra el número de horas que el docente ha planificado para una semana específica. En el eje y se encuentran las etiquetas de tiempo promedio invertido y tiempo promedio que se debería invertir.';
$string['ts_time_inverted_chart_title'] = 'Tiempo Invertido de los Estudiantes';
$string['ts_time_inverted_xaxis'] = 'Número de Horas';
$string['ts_time_inverted_inverted_label'] = 'Tiempo Promedio Invertido';
$string['ts_time_inverted_expected_label'] = 'Tiempo Promedio que se debería Invertir';

$string['ts_hours_sessions_help_title'] = 'Sesiones por Día y Hora';
$string['ts_hours_sessions_help_description_p1'] = 'Este gráfico muestra las sesiones de estudio por día y hora de la semana seleccionada. Se considera el acceso al curso por parte del estudiante como el inicio de una sesión de estudio. Una sesión se considera finalizada cuando el tiempo transcurrido entre dos interacciones de un estudiante supera los 30 minutos.';
$string['ts_hours_sessions_help_description_p2'] = 'En el eje x del gráfico se encuentran los días de la semana. En el eje y se encuentran las horas del día empezando por las 12am y terminando a las 11pm o 23 horas.';
$string['ts_hours_sessions_chart_title'] = 'Sesiones por Día y Hora';

$string['ts_sessions_count_help_title'] = 'Sesiones de la Semana';
$string['ts_sessions_count_help_description_p1'] = 'Este gráfico muestra el número de sesiones clasificadas por su duración en rangos de tiempo: menores a 30 minutos, mayores a 30 minutos y mayores a 60 minutos. Se considera el acceso al curso por parte del estudiante como el inicio de una sesión de estudio. Una sesión se considera finalizada cuando el tiempo transcurrido entre dos interacciones de un estudiante supera los 30 minutos.';
$string['ts_sessions_count_help_description_p2'] = 'En el eje x del gráfico están los días de la semana configurada. En el eje y está la cantidad de sesiones realizadas.';
$string['ts_session_count_chart_title'] = 'Sesiones de la Semana';
$string['ts_session_count_yaxis_title'] = 'Cantidad de Sesiones';
$string['ts_session_count_tooltip_suffix'] = ' sesiones';
$string['ts_sessions_count_smaller30'] = 'Menores que 30 minutos';
$string['ts_sessions_count_greater30'] = 'Mayores que 30 minutos';
$string['ts_sessions_count_greater60'] = 'Mayores que 60 minutos';


/*Teacher Assignments*/
$string['ta_section_help_title'] = 'Tareas';
$string['ta_section_help_description'] = 'Esta sección contiene indicadores relacionados a la entrega de tareas y acceso a recursos. Los datos presentados en esta sección varían dependiendo de la semana de estudio seleccionada.';

$string['ta_assigns_submissions_help_title'] = 'Envíos de Tareas';
$string['ta_assigns_submissions_help_description_p1'] = 'Este gráfico presenta la distribución de la cantidad de estudiantes, respecto al estado de entrega de una tarea.';
$string['ta_assigns_submissions_help_description_p2'] = 'En el eje x del gráfico se encuentran las tareas de las secciones asignadas a la semana junto con la fecha y hora de entrega. En el eje y se encuentra la distribución del número de estudiantes según el estado de entrega. El gráfico cuenta con la opción de enviar un correo electrónico a los estudiantes en alguna distribución (envío a tiempo, envíos tardíos, sin envío) al dar clic sobre el gráfico.';
$string['ta_assigns_submissions_intime_sub'] = 'Envíos a tiempo';
$string['ta_assigns_submissions_late_sub'] = 'Envíos tardíos';
$string['ta_assigns_submissions_no_sub'] = 'Sin envío';
$string['ta_assigns_submissions_assign_nodue'] = 'Sin fecha límite';
$string['ta_assigns_submissions_title'] = 'Envíos de Tareas';
$string['ta_assigns_submissions_yaxis'] = 'Número de Estudiantes';
$string['ta_assigns_submissions_xaxis_access'] = 'Accedido';
$string['ta_assigns_submissions_xaxis_no_access'] = 'Sin Acceso';

$string['ta_access_content_help_title'] = 'Acceso a los contenidos del curso';
$string['ta_access_content_help_description_p1'] = 'Este gráfico presenta la cantidad de estudiantes que han accedido y no han accedido a los recursos del curso. En la parte superior se tienen los distintos tipos de recursos de Moodle, con la posibilidad de filtrar la información del gráfico según el tipo de recurso seleccionado.';
$string['ta_access_content_help_description_p2'] = 'En el eje x del gráfico se encuentran la cantidad de estudiantes matriculados en el curso. En el eje y del gráfico se encuentran los recursos de las secciones asignadas a la semana. Además, este gráfico permite enviar un correo electrónico a los estudiantes que han accedido al recurso o bien a aquellos que no han accedido al dar clic sobre el gráfico.';
$string['ta_access_content_chart_title'] = 'Acceso a los Contenidos Curso';
$string['ta_access_content_yaxis_title'] = 'Cantidad de Estudiantes';
$string['ta_access_content_tooltip_suffix'] = ' estudiantes';


/* Teacher Rating*/
$string['tr_section_help_title'] = 'Calificaciones';
$string['tr_section_help_description'] = 'Esta sección contiene indicadores relacionados a los promedios de calificaciones en las actividades evaluables. Las diferentes unidades didácticas (Categorías de Calificación) creadas por el docente se muestran en el selector <i>Categoría de Calificación</i>. Este selector permitirá cambiar entre las diferentes unidades definidas y mostrar las actividades evaluables en cada una.';
$string['tr_select_label'] = 'Categoría de Calificación';

$string['tr_grade_items_average_help_title'] = 'Promedio de Actividades Evaluables';
$string['tr_grade_items_average_help_description_p1'] = 'Este gráfico presenta el promedio (en porcentaje) de calificaciones de los estudiantes en cada una de las actividades evaluables del curso. El promedio en porcentaje se calcula en base a la calificación máxima de la actividad evaluable (ejemplo: una actividad evaluable con calificación máxima de 80 y calificación promedio de 26 presentará una barra con una altura igual al 33%, ya que 26 es el 33% de la calificación total). Se ha expresado el promedio de calificaciones en base a porcentajes para conservar la simetría del gráfico, puesto que Moodle permite crear actividades y asignar calificaciones personalizadas.';
$string['tr_grade_items_average_help_description_p2'] = 'En el eje x del gráfico se encuentran las distintas actividades evaluables del curso. En el eje y se encuentra el promedio de calificaciones expresado en porcentaje.';
$string['tr_grade_items_average_help_description_p3'] = 'Al hacer clic sobre la barra correspondiente a una actividad evaluable, los datos de los dos gráficos inferiores se actualizarán para mostrar información adicional de la actividad evaluable seleccionada.';
$string['tr_grade_items_average_chart_title'] = 'Promedios de Actividades Evaluables';
$string['tr_grade_items_average_yaxis_title'] = 'Promedio de Calificaciones (%)';
$string['tr_grade_items_average_tooltip_average'] = 'Promedio';
$string['tr_grade_items_average_tooltip_graded_students'] = 'Estudiantes Calificados';

$string['tr_item_grades_details_help_title'] = 'Mejor, Peor y Calificación Promedio';
$string['tr_item_grades_details_help_description_p1'] = 'Este gráfico muestra la mejor calificación, la calificación promedio y la peor calificación en una actividad evaluable (la actividad seleccionada del gráfico Promedio de Actividades Evaluables).';
$string['tr_item_grades_details_help_description_p2'] = 'En el eje x del gráfico se encuentra el puntaje para la calificación de la actividad, siendo la nota máxima de la actividad el máximo valor en este eje. En el eje y se encuentran las etiquetas de Mejor Calificación, Calificación Promedio y Peor Calificación.';
$string['tr_item_grades_details_chart_subtitle'] = 'Mejor, Peor y Calificación Promedio';
$string['tr_item_grades_details_yaxis_best_grade'] = 'Mejor Calificación';
$string['tr_item_grades_details_yaxis_average_grade'] = 'Calificación Promedio';
$string['tr_item_grades_details_yaxis_worst_grade'] = 'Peor Calificación';

$string['tr_item_grades_distribution_help_title'] = 'Distribución de Calificaciones';
$string['tr_item_grades_distribution_help_description_p1'] = 'Este gráfico muestra la distribución de los estudiantes en diferentes rangos de calificación. Los rangos de calificación se calculan en base a porcentajes. Se toman en cuenta los siguientes rangos: menor al 50%, mayor al 50%, mayor al 60%, mayor al 70%, mayor al 80% y mayor al 90%. Estos rangos se calculan en base a la ponderación máxima que el docente asignó a una actividad evaluable.';
$string['tr_item_grades_distribution_help_description_p2'] = 'En el eje x están los rangos de calificación de la actividad. En el eje y está la cantidad de estudiantes que pertenecen a un determinado rango.';
$string['tr_item_grades_distribution_help_description_p3'] = 'Al hacer clic sobre la barra correspondiente a un rango se puede enviar un correo electrónico a los estudiantes dentro del rango de calificación.';
$string['tr_item_grades_distribution_chart_subtitle'] = 'Distribución de Calificaciones';
$string['tr_item_grades_distribution_xaxis_greater_than'] = 'mayor al';
$string['tr_item_grades_distribution_xaxis_smaller_than'] = 'menor al';
$string['tr_item_grades_distribution_yaxis_title'] = 'Número de Estudiantes';
$string['tr_item_grades_distribution_tooltip_prefix'] = 'Rango';
$string["tr_item_grades_distribution_tooltip_view_details"] = "(Clic para ver detalles)";


/* Teacher Quiz  */
$string['tq_section_help_title'] = 'Evaluaciones';
$string['tq_section_help_description'] = 'Esta sección contiene indicadores relacionados al resumen de intentos en las diferentes evaluaciones del curso y análisis de las preguntas de una evaluación. Los datos presentados en esta sección varían dependiendo de la semana de estudio seleccionada y de un selector que contiene todas las actividades de tipo Evaluación de las secciones del curso asignadas a la semana seleccionada.';

$string["tq_quiz_number_questions"] = "Cantidad de Preguntas";
$string["tq_quiz_attempts_done"] = "Intentos Realizados";
$string["tq_quiz_students_attempts"] = 'Estudiantes que Intentaron la Evaluación';
$string["fml_select_quiz"] = "Evaluaciones";

$string['tq_questions_attempts_help_title'] = 'Intentos de Preguntas';
$string['tq_questions_attempts_help_description_p1'] = 'Este gráfico muestra la distribución de intentos de resolución de cada una de las preguntas de una evaluación junto con el estado de revisión en el que se encuentran.';
$string['tq_questions_attempts_help_description_p2'] = 'En el eje x del gráfico se encuentran las preguntas de la evaluación. En el eje y se encuentra la cantidad de intentos de resolución para cada una de dichas preguntas. La simetría del gráfico se verá afectada por la configuración de la evaluación (ejemplo: en una evaluación que tenga siempre las mismas preguntas, el gráfico presentará la misma cantidad de intentos para cada barra correspondiente a una pregunta. En una evaluación que tenga preguntas aleatorias (de un banco de preguntas), el gráfico presentará en la barra de cada pregunta la suma de los intentos de evaluaciones en los que apareció, pudiendo no ser la misma para cada pregunta de la evaluación).';
$string['tq_questions_attempts_help_description_p3'] = 'Al hacer clic en alguna de las barras correspondiente a una pregunta es posible ver la pregunta de la evaluación en una ventana emergente.';
$string["tq_questions_attempts_chart_title"] = "Intentos de Preguntas";
$string["tq_questions_attempts_yaxis_title"] = "Número de Intentos";
$string["tq_questions_attempts_xaxis_correct_attempt"] = "Correctos";
$string["tq_questions_attempts_xaxis_partcorrect_attempt"] = "Parcialmente Correctos";
$string["tq_questions_attempts_xaxis_incorrect_attempt"] = "Incorrectos";
$string["tq_questions_attempts_xaxis_blank_attempt"] = "En Blanco";
$string["tq_questions_attempts_xaxis_needgraded_attempt"] = "Sin Calificar";
$string["tq_questions_attempts_tooltip_review_question"] = "(Clic para revisar la pregunta)";

$string['tq_hardest_questions_help_title'] = 'Preguntas más difíciles';
$string['tq_hardest_questions_help_description_p1'] = 'Este gráfico muestra las preguntas de la evaluación ordenadas por su nivel de dificultad. Se considera incorrecto a un intento de resolución de una pregunta con el estado de Parcialmente Correcto, Incorrecto o En Blanco, de manera que la cantidad total de intentos incorrectos de una pregunta es la suma de los intentos con los estados antes mencionados. El nivel de dificultad se representa en porcentaje calculado en base a la cantidad total de intentos.';
$string['tq_hardest_questions_help_description_p2'] = 'En el eje x del gráfico se encuentran las preguntas de la evaluación identificadas por el nombre. En el eje y se encuentran el porcentaje de intentos incorrectos del total de intentos de la pregunta. Este eje permite identificar cuáles han sido las preguntas que han representado mayor dificultad para los estudiantes que rindieron la evaluación.';
$string['tq_hardest_questions_help_description_p3'] = 'Al hacer clic en alguna de las barras correspondiente a una pregunta es posible ver la pregunta de la evaluación en una ventana emergente.';
$string["tq_hardest_questions_chart_title"] = "Preguntas mas Difíciles";
$string["tq_hardest_questions_yaxis_title"] = "Intentos Incorrectos";


/* Teacher Dropout */
$string['td_section_help_title'] = 'Deserción';
$string['td_section_help_description'] = 'Esta sección contiene indicadores relacionados a la predicción de abandono de estudiantes de un curso. La información se muestra en base a grupos de estudiantes calculados por un algoritmo que analiza el comportamiento de cada estudiante en base al tiempo invertido, la cantidad de sesiones del estudiante, la cantidad de días activo y las interacciones que ha realizado con cada recurso y con los demás estudiantes del curso. El algoritmo coloca en el mismo grupo a estudiantes con similar comportamiento, de manera que se puede identificar a los estudiantes más y menos comprometidos con el curso. Los datos presentados en esta sección varían dependiendo del grupo seleccionado en el selector que contiene los grupos identificados en el curso.';

$string["td_cluster_label"] = "Grupo";
$string["td_cluster_high_dropout_risk"] = "Riesgo Alto de Abandono";
$string["td_cluster_middle_dropout_risk"] = "Riesgo Medio de Abandono";
$string["td_cluster_low_dropout_risk"] = "Riesgo Bajo de Abandono";
$string["td_cluster_select"] = "Grupo de Estudiantes";
$string["td_dropout_table_title"] = "Estudiantes del Grupo";
$string["td_dropout_see_profile"] = "Ver Perfil";
$string["td_user_never_access"] = "Nunca Accedido";
$string["td_student_progress_title"] = "Progreso del Estudiante";
$string["td_student_grade_title"] = "Calificación";
$string['td_no_data'] = "No hay datos de desercion para este curso. Si no puedes generarlos manualmente, es posible que el curso haya terminado.";
$string['td_no_users_cluster'] = "No hay estudiantes de este grupo";
$string['td_generate_data_manually'] = "Generar Manualmente";
$string['td_generating_data'] = "Generando datos...";
$string['td_send_mail_to_user'] = 'Correo a';
$string['td_send_mail_to_group'] = 'Correo al Grupo';
$string["td_modules_amount"] = "Cantidad de Recursos";
$string["td_modules_details"] = "(Clic para ver recursos)";
$string["td_modules_interaction"] = "interacción";
$string["td_modules_interactions"] = "interacciones";
$string["td_modules_viewed"] = "Accedido";
$string["td_modules_no_viewed"] = "No accedido";
$string["td_modules_complete"] = "Completado";

$string['td_group_students_help_title'] = 'Estudiantes del Grupo';
$string['td_group_students_help_description_p1'] = 'En esta tabla están los estudiantes pertenecientes al grupo seleccionado del selector Grupo de Estudiantes. De cada estudiante se lista su foto, nombres y el porcentaje de progreso del curso. Para el cálculo del progreso se han considerado todos los recursos del curso a excepción de los de tipo Label. Para determinar si un estudiante ha finalizado un recurso se verifica en primer lugar si el recurso tiene habilitada la configuración de completitud. En caso de ser así, se busca si el estudiante ya ha completado la actividad en base a esa configuración. De lo contrario, la actividad se considera completa si el estudiante la ha visto al menos una vez.';
$string['td_group_students_help_description_p2'] = 'Al hacer clic sobre un estudiante en esta tabla, se actualizarán los gráficos inferiores con la información del estudiante seleccionado.';

$string['td_modules_access_help_title'] = 'Recursos del Curso';
$string['td_modules_access_help_description_p1'] = 'Este gráfico muestra la cantidad de recursos a los que el estudiante ha accedido y completado. Los datos presentados en este gráfico varían dependiendo del estudiante seleccionado en la tabla Estudiantes del Grupo. Para determinar la cantidad de recursos y actividades completas se hace uso de la configuración de Moodle denominada Finalización de Actividad. En caso de que el docente no realice la configuración de completitud para las actividades del curso, la cantidad de actividades accedidas y completas siempre será la misma, ya que sin tal configuración, un recurso se considera finalizado cuando el estudiante accede a él.';
$string['td_modules_access_help_description_p2'] = 'En el eje x se encuentran la cantidad de recursos del curso. En el eje y se encuentran las etiquetas de Accedidos, Completos y Total de recursos del curso.';
$string['td_modules_access_help_description_p3'] = 'Al hacer clic sobre alguna barra es posible ver los recursos y actividades disponibles en el curso (en una ventana emergente) junto con la cantidad de interacciones del estudiante con cada recurso y una etiqueta de no accedido, accedido o completado.';

$string['td_week_modules_help_title'] = 'Recursos por Semanas';
$string['td_week_modules_help_description_p1'] = 'Este gráfico muestra la cantidad de recursos que el estudiante ha accedido y completado de cada una de las semanas configuradas en el plugin. Los datos presentados en este gráfico varían dependiendo del estudiante seleccionado en la tabla <i>Estudiantes del Grupo</i>.';
$string['td_week_modules_help_description_p2'] = 'En el eje x del gráfico se encuentran las diferentes semanas de estudio configuradas. En el eje y se encuentra la cantidad de recursos y actividades accedidas y completadas del estudiante.';
$string['td_week_modules_help_description_p3'] = 'Al hacer clic sobre alguna barra es posible ver los recursos y actividades disponibles en el curso (en una ventana emergente) junto con la cantidad de interacciones del estudiante con cada recurso y una etiqueta de no accedido, accedido o completado.';
$string["td_week_modules_chart_title"] = "Recursos por Semanas";

$string["td_modules_access_chart_title"] = "Recursos del Curso";
$string["td_modules_access_chart_series_total"] = "Total";
$string["td_modules_access_chart_series_complete"] = "Completos";
$string["td_modules_access_chart_series_viewed"] = "Accedidos";

$string['td_sessions_evolution_help_title'] = 'Sesiones y tiempo invertido';
$string['td_sessions_evolution_help_description_p1'] = 'Este gráfico permite conocer cómo han evolucionado las sesiones de estudio desde que se registró su primera sesión en el curso. Los datos presentados en este gráfico varían dependiendo del estudiante seleccionado en la tabla <i>Estudiantes del Grupo</i>.';
$string['td_sessions_evolution_help_description_p2'] = 'En el eje x del gráfico se muestra una línea temporal con los días que han transcurrido desde que el estudiante realizó la primera sesión de estudio hasta el día de la última sesión registrada. En el eje y muestran 2 valores, en el lado izquierdo el número de sesiones del estudiante y en el lado derecho la cantidad de tiempo invertido en horas. Entre dichos ejes se dibujan la cantidad de sesiones y el tiempo invertido del estudiante como una serie de tiempo.';
$string['td_sessions_evolution_help_description_p3'] = 'Esta visualización permite hacer un acercamiento sobre una región seleccionada. Este acercamiento ayuda a evidenciar de manera clara dicha evolución en diferentes rangos de fechas.';
$string["td_sessions_evolution_chart_title"] = "Sesiones y Tiempo Invertido";
$string["td_sessions_evolution_chart_xaxis1"] = "Número de Sesiones";
$string["td_sessions_evolution_chart_xaxis2"] = "Cantidad de Horas";
$string["td_sessions_evolution_chart_legend1"] = "Sesiones";
$string["td_sessions_evolution_chart_legend2"] = "Tiempo Invertido";

$string['td_user_grades_help_title'] = 'Calificaciones';
$string['td_user_grades_help_description_p1'] = 'Este gráfico muestra una comparación de las calificaciones del estudiante con los promedios de calificaciones (media en porcentaje) de sus compañeros en las distintas actividades evaluables del curso. Los datos presentados en este gráfico varían dependiendo del estudiante seleccionado en la tabla <i>Estudiantes del Grupo</i>.';
$string['td_user_grades_help_description_p2'] = 'En el eje x del gráfico se muestran las diferentes actividades evaluables. En el eje y se encuentra la calificación del estudiante y la media de calificaciones de sus compañeros. Tanto la calificación del estudiante como la media del curso se muestran en porcentaje para mantener la simetría del gráfico.';
$string['td_user_grades_help_description_p3'] = 'Con un clic en la barra correspondiente a alguna actividad es posible dirigirse a dicha analizada.';
$string["td_user_grades_chart_title"] = "Calificaciones";
$string["td_user_grades_chart_yaxis"] = "Calificación en Porcentaje";
$string["td_user_grades_chart_xaxis"] = "Actividades Evaluables";
$string["td_user_grades_chart_legend"] = "Curso (Media)";
$string["td_user_grades_chart_tooltip_no_graded"] = "Sin Calificaciones";
$string["td_user_grades_chart_view_activity"] = "Clic para ver la actividad";


/* Teacher Logs */
$string['tl_section_help_title'] = 'Descargar Registros';
$string['tl_section_help_description'] = 'En esta sección se pueden descargar archivo en formato de valores separados por comas(.cv) de las actividades que han realizado los participantes del curso en el Curso (Moodle) o en el Plugin Flip My Learning (FML)';

$string['tl_moodle_logs_help_title'] = "Registros de Moodle";
$string['tl_moodle_logs_help_description'] = "El archivo de logs de Moodle enumera todas las acciones que se han realizado dentro del curso (ver el curso, ver los recursos, enviar una tarea, etc.)";
$string['tl_moodle_logs_about'] = "Acerca de los Registros de Moodle";
$string['tl_fliplearning_logs_help_title'] = "Registros de Fliplearning";
$string['tl_fliplearning_logs_help_description'] = "El archivo de logs de Fliplearning enumera todas las acciones que se han realizado dentro del plugin Flip My Learning (ver las secciones, ver los tooltips de los graficos, ver las ventanas de ayuda, etc.)";
$string['tl_fliplearning_logs_about'] = "Acerca de los Registros de Fliplearning";

$string['tl_logs_help_table_header_column'] = "Columna";
$string['tl_logs_help_table_header_description'] = "Descripción";
$string['tl_logs_dates_range'] = "Rango de Fechas para Registros";
$string['tl_logs_success_download'] = "Descarga Completa";
$string['tl_logs_without_dates_validation'] = "Por favor, selecciona un rango de fechas";


$string['tl_logs_header_logid'] = "idinteraccion";
$string['tl_logs_header_userid'] = "idusuario";
$string['tl_logs_header_username'] = "username";
$string['tl_logs_header_firstname'] = "nombres";
$string['tl_logs_header_lastname'] = "apellidos";
$string['tl_logs_header_roles'] = "roles";
$string['tl_logs_header_courseid'] = "idcurso";
$string['tl_logs_header_coursename'] = "nombrecurso";
$string['tl_logs_header_contextlevel'] = "nivelcontexto";
$string['tl_logs_header_component'] = "componente";
$string['tl_logs_header_action'] = "accion";
$string['tl_logs_header_target'] = "objeto";
$string['tl_logs_header_activitytype'] = "tipoactividad";
$string['tl_logs_header_activityname'] = "nombreactividad";
$string['tl_logs_header_sectionnumber'] = "numeroseccion";
$string['tl_logs_header_sectionname'] = "nombreseccion";
$string['tl_logs_header_timecreated'] = "fecha";

$string['tl_logs_header_pluginsection'] = "secionplugin";
$string['tl_logs_header_url'] = "url";
$string['tl_logs_header_description'] = "descripcion";

$string['tl_logs_header_logid_help_description'] = "Número de identificación de registro";
$string['tl_logs_header_userid_help_description'] = "Número de identificación del usuario";
$string['tl_logs_header_username_help_description'] = "Nombre de usuario en moodle";
$string['tl_logs_header_firstname_help_description'] = "Primer nombre del usuario";
$string['tl_logs_header_lastname_help_description'] = "Apellidos del usuario";
$string['tl_logs_header_roles_help_description'] = "Roles del usuario que realizó la interacción";
$string['tl_logs_header_courseid_help_description'] = "Número de identificación del curso";
$string['tl_logs_header_coursename_help_description'] = "Nombre del Curso";
$string['tl_logs_header_contextlevel_help_description'] = "Nivel de contexto del objeto de la interacción. (50: nivel de curso, 70: nivel de módulo de curso)";
$string['tl_logs_header_component_help_description'] = "Componente de la interacción";
$string['tl_logs_header_action_help_description'] = "Acción realizada por el usuario";
$string['tl_logs_header_target_help_description'] = "Objetivo de la interacción";
$string['tl_logs_header_activitytype_help_description'] = "Tipo de actividad. (Tarea, Recurso, Evaluación, etc.)";
$string['tl_logs_header_activityname_help_description'] = "Nombre de la Actividad";
$string['tl_logs_header_sectionnumber_help_description'] = "Número de la sección del curso en la que se encuentra la actividad";
$string['tl_logs_header_sectionname_help_description'] = "Nombre de la sección del curso. (Nombre colocado por el creador del curso si está disponible, de lo contrario se genera concatenando el nombre del formato del curso (Semanas, Temas, etc.) con el número de la sección. Ejm: Tema 1, Tema 2, Tema 3, etc.)";
$string['tl_logs_header_timecreated_help_description'] = "Fecha en en formato Unix en la que se realizó la interacción";

$string['tl_logs_header_pluginsection_help_description'] = "Nombre de la sección del plugin. (Indicadores Generales, Sesiones, Tareas, etc.)";
$string['tl_logs_header_url_help_description'] = "Url de la página en la que se realizó la interacción";
$string['tl_logs_header_description_help_description'] = "Descripción de la interacción";

$string['tl_fliplearning_logs_type_1'] = "El usuario seleccionó un grupo en el filtro de grupos";
$string['tl_fliplearning_logs_type_2'] = "El usuario vió la sección (entró al componente o actualizó la pagina)";
$string['tl_fliplearning_logs_type_3'] = "El usuario vió la ventana de ayuda de la seccion";
$string['tl_fliplearning_logs_type_4'] = "El usuario guardó una nueva configuración de semanas";
$string['tl_fliplearning_logs_type_5'] = "El usuario vió el tooltip del gráfico";
$string['tl_fliplearning_logs_type_6'] = "El usuario descargó el gráfico";
$string['tl_fliplearning_logs_type_7'] = "El usuario vió la ventana de ayuda del gráfico";
$string['tl_fliplearning_logs_type_8'] = "El usuario filtró los datos del gráfico";
$string['tl_fliplearning_logs_type_9'] = "El usuario envió un correo a los usuarios del gráfico";
$string['tl_fliplearning_logs_type_10'] = "El usuario seleccionó una semana en el filtro de semanas";
$string['tl_fliplearning_logs_type_11'] = "El usuario seleccionó una categoría de evaluación";
$string['tl_fliplearning_logs_type_12'] = "El usuario seleccionó un ítem evaluable para ver sus detalles";
$string['tl_fliplearning_logs_type_13'] = "El usuario seleccionó una pregunta para ver sus detalles";
$string['tl_fliplearning_logs_type_14'] = "El usuario seleccionó un grupo en el selector de grupos";
$string['tl_fliplearning_logs_type_15'] = "El usuario seleccionó un usuario de la tabla de usuarios para ver sus detalles";
$string['tl_fliplearning_logs_type_16'] = "El usuario envió un correo al usuario seleccionado";
$string['tl_fliplearning_logs_type_17'] = "El usuario envió un correo al grupo de estudiantes seleccionado";
$string['tl_fliplearning_logs_type_18'] = "El usuario vió la ventana con los detalles de acceso a los recursos";
$string['tl_fliplearning_logs_type_19'] = "El usuario seleccionó una actividad evaluable desde el gráfico";
$string['tl_fliplearning_logs_type_20'] = "El usuario descargó un archivo de logs";
$string['tl_fliplearning_logs_type_21'] = "El usuario seleccionó una evaluación para ver sus intentos";

/*Student General*/
$string['sg_section_help_title'] = 'Indicadores Generales';
$string['sg_section_help_description'] = 'Esta sección contiene indicadores relacionados a tu información, progreso, indicadores generales, recursos del curso, sesiones a lo largo del curso y calificaciones obtenidas. Las visualizaciones de esta sección muestran los indicadores durante todo el curso (hasta la fecha actual).';

$string['sg_modules_access_help_title'] = 'Recursos del Curso';
$string['sg_modules_access_help_description_p1'] = 'Este gráfico muestra la cantidad de recursos que has accedido y completado. Para determinar la cantidad de recursos que has completado se hace uso de la configuración de Moodle denominada Finalización de Actividad. En caso de que el docente no haya configurado la completitud para las actividades del curso, la cantidad de actividades accedidas y completas siempre será la misma, ya que sin tal configuración, un recurso se considera finalizado cuando accedes a él.';
$string['sg_modules_access_help_description_p2'] = 'En el eje x se encuentran la cantidad de recursos del curso. En el eje y se encuentran las etiquetas de Accedidos, Completos y Total de recursos en referencia a tus interacciones con los recursos del curso.';
$string['sg_modules_access_help_description_p3'] = 'Al hacer clic sobre alguna barra es posible ver los recursos y actividades disponibles en el curso (en una ventana emergente) junto con la cantidad de interacciones que has realizado con cada recurso y una etiqueta de no accedido, accedido o completado.';

$string['sg_weeks_session_help_title'] = 'Sesiones por Semana';
$string['sg_weeks_session_help_description_p1'] = 'Este gráfico muestra la cantidad de sesiones de estudio que has realizado cada semana a partir de la fecha de inicio del curso. Se considera el acceso al curso como el inicio de una sesión de estudio. Una sesión se considera finalizada cuando el tiempo transcurrido entre dos interacciones supera los 30 minutos.';
$string['sg_weeks_session_help_description_p2'] = 'En el eje x del gráfico se encuentran las semanas de cada mes. En el eje y del gráfico se encuentran los diferentes meses del año partiendo del mes de creación del curso. Para mantener la simetría del gráfico se ha colocado un total de cinco semanas para cada mes, sin embargo, no todos los meses tiene tal cantidad de semanas. Dichos meses, solo sumarán sesiones hasta la semana cuatro.';

$string['sg_sessions_evolution_help_title'] = 'Sesiones y Tiempo Invertido';
$string['sg_sessions_evolution_help_description_p1'] = 'Este gráfico permite conocer cómo han evolucionado tus sesiones de estudio desde que se registró tu primera sesión en el curso.';
$string['sg_sessions_evolution_help_description_p2'] = 'En el eje x del gráfico se muestra una línea temporal con los días que han transcurrido desde que realizaste tu primera sesión de estudio hasta el día de tu última sesión registrada. En el eje y muestran 2 valores, en el lado izquierdo tu cantidad de sesiones y en el lado derecho tu cantidad de tiempo invertido en horas. Entre dichos ejes se dibujan tu cantidad de sesiones y tu tiempo invertido del estudiante como una serie de tiempo.';
$string['sg_sessions_evolution_help_description_p3'] = 'Esta visualización permite hacer un acercamiento sobre una región seleccionada.';

$string['sg_user_grades_help_title'] = 'Calificaciones';
$string['sg_user_grades_help_description_p1'] = 'Este gráfico muestra una comparación de tus calificaciones con los promedios de calificaciones (media en porcentaje) de tus compañeros en las distintas actividades evaluables del curso.';
$string['sg_user_grades_help_description_p2'] = 'En el eje x del gráfico se muestran las diferentes actividades evaluables. En el eje y se encuentra tus calificaciones y la media de calificaciones de tus compañeros. Tanto tu calificación como la media del curso se muestran en porcentaje para mantener la simetría del gráfico.';
$string['sg_user_grades_help_description_p3'] = 'Con un clic en la barra correspondiente a alguna actividad es posible dirigirse a dicha analizada.';

/* Student Sessions*/
$string['ss_section_help_title'] = 'Sesiones de Estudio';
$string['ss_section_help_description'] = 'Esta sección contiene visualizaciones con indicadores relacionados a tu actividad en el curso medida en términos de sesiones de estudio, tiempo invertido y progreso en cada una de las semanas configuradas por el docente. Las visualizaciones de esta sección varían dependiendo de la semana de estudio seleccionada.';

$string['ss_inverted_time_help_title'] = 'Tu tiempo invertido';
$string['ss_inverted_time_help_description_p1'] = 'Este gráfico muestra tu tiempo invertido en la semana en comparación del tiempo planificado por parte del docente.';
$string['ss_inverted_time_help_description_p2'] = 'En el eje x del gráfico se encuentra el número de horas que el docente ha planificado para una semana específica. En el eje y se encuentran las etiquetas de tiempo invertido y tiempo que se debería invertir.';
$string['ss_inverted_time_chart_title'] = 'Tu Tiempo Invertido';
$string['ss_inverted_time_xaxis_title'] = 'Número de Horas';
$string['ss_inverted_time_inverted_label'] = 'Tiempo Invertido';
$string['ss_inverted_time_expected_label'] = 'Tiempo que se debería Invertir';

$string['ss_hours_session_help_title'] = 'Sesiones por Día y Hora';
$string['ss_hours_session_help_description_p1'] = 'Este gráfico muestra tus sesiones de estudio por día y hora de la semana seleccionada. Se considera el acceso al curso como el inicio de una sesión de estudio. Una sesión se considera finalizada cuando el tiempo transcurrido entre dos interacciones supera los 30 minutos.';
$string['ss_hours_session_help_description_p2'] = 'En el eje x del gráfico se encuentran los días de la semana. En el eje y se encuentran las horas del día empezando por las 12am y terminando a las 11pm o 23 horas.';

$string['ss_resources_access_help_title'] = 'Interacción por Tipos de Recursos';
$string['ss_resources_access_help_description_p1'] = 'Este gráfico muestra cuántos recursos tienes pendientes y cuáles ya has completado en la semana seleccionada. Los recursos se agrupan por su tipo en este gráfico. Además, en la parte superior se muestra una barra que representa el porcentaje de recursos accedidos del total de recursos asignados a la semana seleccionada.';
$string['ss_resources_access_help_description_p2'] = 'En el eje x del gráfico se encuentran los diferentes tipos de recursos. En el eje y se encuentran la cantidad de recursos accedidos de la semana.';
$string['ss_resources_access_help_description_p3'] = 'Al hacer clic sobre alguna barra es posible ver los recursos y actividades disponibles en el curso (en una ventana emergente) junto con la cantidad de interacciones que has realizado con cada recurso y una etiqueta de no accedido, accedido o completado.';
$string['ss_resource_access_chart_title'] = 'Interacción por Tipos de Recursos';
$string['ss_resource_access_yaxis_title'] = 'Cantidad de Recursos';
$string['ss_resource_access_xaxis_title'] = 'Tipos de Recursos';
$string['ss_resource_access_legend1'] = 'Completos';
$string['ss_resource_access_legend2'] = 'Pendientes';

$string['ss_week_progress_title'] = 'Progreso de la Semana';



