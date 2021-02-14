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
$string['pagination'] = 'Semana:';
$string['graph_generating'] = 'Estamos construyendo el reporte, por favor espere un momento.';
$string['weeks_not_config'] = 'El curso no ha sido configurado por el profesor, por lo que no hay visualizaciones que mostrar.';
$string['pagination_title'] = 'Selección semana';
$string['helplabel'] = 'Ayuda';
$string['exitbutton'] = '¡Entendido!';
$string['no_data'] = 'No hay datos que mostrar';
$string["fml_send_mail"] = "(Clic para enviar correo)";

/* Menú */
$string['menu_main_title'] = "Dashboard Progreso";
$string['menu_sessions'] = 'Sesiones de Estudio';
$string['menu_setweek'] = "Configurar semanas";
$string['menu_time'] = 'Seguimiento de Tiempo';
$string['menu_assignments'] = 'Seguimiento de Tareas';
$string['menu_grades'] = 'Seguimiento de Calificaciones';
$string['menu_quiz'] = 'Seguimiento de Evaluaciones';
$string['menu_dropout'] = 'Deserción';
$string['menu_logs'] = "Descargar registros";

/* Nav Bar Menu */
$string['togglemenu'] = 'Mostrar/Ocultar menú de FML';

/* Pagination component */
$string['pagination_component_to'] = 'al';
$string['pagination_component_name'] = 'Semana';

/* Set weeks */
$string['setweeks_title'] = 'Configuración de las Semanas del Curso';
$string['setweeks_description'] = 'Para comenzar, debe configurar el curso por semanas y definir una fecha de inicio para la primera semana (el resto de semanas se realizará de forma automática a partir de esta fecha. A continuación, debe asociar las actividades o módulos relacionadas a cada semana arrastrándolas de la columna de la derecha a la semana correspondiente.  No es necesario asignar todas las actividades o módulos a las semanas, simplemente aquellas que se quieran considerar para hacer el seguimiento de los estudiantes. Finalmente, debe clicar sobre el botón Guardar para conservar su configuración.';
$string['setweeks_sections'] = "Secciones disponibles en el curso";
$string['setweeks_weeks_of_course'] = "Planificación de semanas";
$string['setweeks_add_new_week'] = "Agregar semana";
$string['setweeks_start'] = "Comienza:";
$string['setweeks_end'] = "Termina:";
$string['setweeks_week'] = "Semana";
$string['setweeks_save'] = "Guardar configuración";
$string['setweeks_time_dedication'] = "¿Cuántas horas de trabajo espera que los estudiantes dediquen a su curso esta semana?";
$string['setweeks_enable_scroll'] = "Activar el modo desplazamiento para semanas y temas";
$string['setweeks_label_section_removed'] = "Eliminado del curso";
$string['setweeks_error_section_removed'] = "Una sección asignada a una semana se ha eliminado del curso, debe eliminarla de tu planificación para poder continuar.";
$string['setweeks_save_warning_title'] = "¿Está seguro/a que desea guardar los cambios?";
$string['setweeks_save_warning_content'] = "Si modifica la configuración de las semanas cuando el curso ya ha comenzado es posible que se pierdan datos...";
$string['setweeks_confirm_ok'] = "Guardar";
$string['setweeks_confirm_cancel'] = "Cancelar";
$string['setweeks_error_empty_week'] = "No puede guardar los cambios con una semana vacía. Por favor, elimínela y inténtelo de nuevo.";
$string['setweeks_new_group_title'] = "Nueva instancia de configuración";
$string['setweeks_new_group_text'] = "Hemos detectado que su curso ha finalizado, si desea configurar las semanas para trabajar con nuevos estudiantes, debe activar el botón de más abajo. Esto permitirá separar los datos de los estudiantes actuales de los de cursos anteriores, evitando mezclarlos.";
$string['setweeks_new_group_button_label'] = "Guardar configuración como nueva instancia";
$string['course_format_weeks'] = 'Semana';
$string['course_format_topics'] = 'Tema';
$string['course_format_social'] = 'Social';
$string['course_format_singleactivity'] = 'Actividad única';
$string['plugin_requirements_title'] = 'Estado:';
$string['plugin_requirements_descriptions'] = 'El plugin será visible y mostrará los reportes para estudiantes y profesores cuando se cumplan las siguientes condiciones...';
$string['plugin_requirements_has_users'] = 'El curso debe poseer al menos un estudiante matriculado';
$string['plugin_requirements_course_start'] = 'La fecha actual debe ser mayor a la fecha de inicio de la primera semana configurada.';
$string['plugin_requirements_has_sections'] = 'Las semanas configuradas poseen al menos una sección.';
$string['plugin_visible'] = 'Reportes visibles.';
$string['plugin_hidden'] = 'Reportes ocultos.';
$string['title_conditions'] = 'Condiciones de uso';

/* Sessions */
$string['fml_title'] = 'Sesiones de Trabajo';
$string['fml_mon'] = 'Lunes';
$string['fml_tue'] = 'Martes';
$string['fml_wed'] = 'Miércoles';
$string['fml_thu'] = 'Jueves';
$string['fml_fri'] = 'Viernes';
$string['fml_sat'] = 'Sábado';
$string['fml_sun'] = 'Domingo';

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

$string['fml_week1'] = 'Semana 1';
$string['fml_week2'] = 'Semana 2';
$string['fml_week3'] = 'Semana 3';
$string['fml_week4'] = 'Semana 4';
$string['fml_week5'] = 'Semana 5';
$string['fml_week6'] = 'Semana 6';

$string['table_title'] = 'Progreso del Curso';
$string['thead_name'] = 'Nombre';
$string['thead_lastname'] = 'Apellidos';
$string['thead_email'] = 'Correo';
$string['thead_progress'] = 'Progreso (%)';
$string['thead_sessions'] = 'Sesiones';
$string['thead_time'] = 'Tiempo Invertido';

$string['fml_module_label'] = 'módulo';
$string['fml_modules_label'] = 'módulos';
$string['fml_of_conector'] = 'de';
$string['fml_finished_label'] = 'finalizado';
$string['fml_finisheds_label'] = 'finalizados';

$string['fml_smaller30'] = 'Menores que 30 minutos';
$string['fml_greater30'] = 'Mayores que 30 minutos';
$string['fml_greater60'] = 'Mayores que 60 minutos';

$string['fml_session_count_title'] = 'Sesiones de la Semana';
$string['fml_session_count_yaxis_title'] = 'Cantidad de Sesiones';
$string['fml_session_count_tooltip_suffix'] = ' sesiones';

$string['fml_hours_sessions_title'] = 'Sesiones por Día y Hora';
$string['fml_weeks_sessions_title'] = 'Sesiones por Semana';

$string["fml_session_text"] = "sesión";
$string["fml_sessions_text"] = "sesiones";

$string['ss_change_timezone'] = 'Zona horaria:';
$string['ss_activity_inside_plataform_student'] = 'Mi actividad en la plataforma';
$string['ss_activity_inside_plataform_teacher'] = 'Actividad de los estudiantes en la plataforma';
$string['ss_time_inside_plataform_student'] = 'Mi tiempo en la plataforma';
$string['ss_time_inside_plataform_teacher'] = 'Tiempo invertido en promedio de los estudiantes en la plataforma en esta semana';
$string['ss_time_inside_plataform_description_teacher'] = 'Tiempo que el estudiante ha invertido en la semana seleccionada, en comparación al tiempo que el/la docente planificó que se debería invertir. El tiempo invertido que se visualiza corresponde al promedio de todos los estudiantes. El tiempo planificado por el/la docente es el asignado en por el/la docente en <i>Configurar Semanas</i>.';
$string['ss_time_inside_plataform_description_student'] = 'Tiempo que ha invertido esta semana en relación al tiempo que el profesor planificó que se debería invertir.';
$string['ss_activity_inside_plataform_description_teacher'] = 'En el eje Y se indican las las horas del día y en el eje X los días de la semana. Dentro del gráfico podrá encontrar múltiples puntos, los cuales, al pasar el cursor sobre estos, ofrecen información detallada sobre las interacciones de los estudiantes, agrupadas por tipo de recurso (número de interacciones, número de estudiantes que interactuaron con el recurso y promedio de interacciones). <br/><br/><b>Al hacer click en las etiquetas, podrá filtrar por tipo de recurso, dejando visible sólo aquellos que no se encuentren tachados.</b>';
$string['ss_activity_inside_plataform_description_student'] = 'Presenta las interacciones por tipo de recurso y horario. Al pasar el cursor sobre un punto visible en el gráfico, verá el número de interacciones agrupadas por tipo de recurso. Al hacer click en las etiquetas, podrá filtrar por tipo de recurso.';

/* Time */
$string['fml_time_inverted_title'] = 'Tiempo invertido de los Estudiantes';
$string['fml_time_inverted_x_axis'] = 'Número de Horas';
$string['fml_inverted_time'] = 'Tiempo Promedio Invertido';
$string['fml_expected_time'] = 'Tiempo Promedio que se debería Invertir';

/* Goups */
$string['group_allstudent'] = 'Todos los estudiantes';

/* General Errors */
$string['api_error_network'] = "Ha ocurrido un error en la comunicación con el servidor.";
$string['api_invalid_data'] = 'Datos incorrectos';
$string['api_save_successful'] = 'Se han guardado los datos correctamente en el servidor';
$string['api_cancel_action'] = 'Has cancelado la acción';

/* Admin Task Screen*/
$string['generate_data_task'] = 'Proceso para generar datos para Flip my Learning Plugin';

/* Time */
$string['fml_hour'] = 'hora';
$string['fml_hours'] = 'horas';
$string['fml_minute'] = 'minuto';
$string['fml_minutes'] = 'minutos';
$string['fml_second'] = 'segundo';
$string['fml_seconds'] = 'segundos';

/* Assign Submissions */
$string['fml_intime_sub'] = 'Envíos a tiempo';
$string['fml_late_sub'] = 'Envíos tardíos';
$string['fml_no_sub'] = 'Sin envío';
$string['fml_assign_nodue'] = 'Sin fecha límite';
$string['fml_assignsubs_title'] = 'Envíos de Tareas';
$string['fml_assignsubs_yaxis'] = 'Número de Estudiantes';

$string['fml_jan_dim'] = 'Ene.';
$string['fml_feb_dim'] = 'Feb.';
$string['fml_mar_dim'] = 'Mar.';
$string['fml_apr_dim'] = 'Abr.';
$string['fml_may_dim'] = 'May.';
$string['fml_jun_dim'] = 'Jun.';
$string['fml_jul_dim'] = 'Jul.';
$string['fml_aug_dim'] = 'Ago.';
$string['fml_sep_dim'] = 'Sep.';
$string['fml_oct_dim'] = 'Oct.';
$string['fml_nov_dim'] = 'Nov.';
$string['fml_dec_dim'] = 'Dic.';

$string['fml_mon_dim'] = 'Lun.';
$string['fml_tue_dim'] = 'Mar.';
$string['fml_wed_dim'] = 'Mié.';
$string['fml_thu_dim'] = 'Jue.';
$string['fml_fri_dim'] = 'Vie.';
$string['fml_sat_dim'] = 'Sáb.';
$string['fml_sun_dim'] = 'Dom.';

/* Content Access */
$string['fml_assign'] = 'Tarea';
$string['fml_assignment'] = 'Tarea';
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
$string['fml_lti'] = 'Contenido IMS';
$string['fml_page'] = 'Página';
$string['fml_quiz'] = 'Examen';
$string['fml_resource'] = 'Recurso';
$string['fml_scorm'] = 'Paquete SCORM';
$string['fml_survey'] = 'Encuesta';
$string['fml_url'] = 'Url';
$string['fml_wiki'] = 'Wiki';
$string['fml_workshop'] = 'Taller';

$string['fml_access'] = 'Accedido';
$string['fml_no_access'] = 'Sin Acceso';
$string['fml_access_chart_title'] = 'Acceso a los Contenidos Curso';
$string['fml_access_chart_yaxis_label'] = 'Cantidad de Estudiantes';
$string['fml_access_chart_suffix'] = ' estudiantes';


/* Email */
$string['fml_validation_subject_text'] = 'Asunto es requerido';
$string['fml_validation_message_text'] = 'Mensaje es requerido';
$string['fml_subject_label'] = 'Agrega un asunto';
$string['fml_message_label'] = 'Agrega un mensaje';

$string['fml_submit_button'] = 'Enviar';
$string['fml_cancel_button'] = 'Cancelar';
$string['fml_emailform_title'] = 'Enviar Correo';
$string['fml_sending_text'] = 'Enviando Correos';

$string['fml_recipients_label'] = 'Para';
$string['fml_mailsended_text'] = 'Correos Enviados';

$string['fml_email_footer_text'] = 'Este es un correo electrónico enviado con Fliplearning.';
$string['fml_email_footer_prefix'] = 'Ve a';
$string['fml_email_footer_suffix'] = 'para más información.';
$string['fml_mailsended_text'] = 'Correos Enviados';

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

/* Grades */
$string['fml_grades_select_label'] = 'Categoría de Evaluación';
$string['fml_grades_chart_title'] = 'Promedios de Actividades Evaluables';
$string['fml_grades_yaxis_title'] = 'Promedio de Calificaciones (%)';
$string['fml_grades_tooltip_average'] = 'Calificación Promedio';
$string['fml_grades_tooltip_grade'] = 'Calificación Máxima';
$string['fml_grades_tooltip_student'] = 'estudiante calificado de';
$string['fml_grades_tooltip_students'] = 'estudiantes calificados de';

$string['fml_grades_best_grade'] = 'Mejor Calificación';
$string['fml_grades_average_grade'] = 'Calificación Promedio';
$string['fml_grades_worst_grade'] = 'Peor Calificación';
$string['fml_grades_details_subtitle'] = 'Mejor, Peor y Calificación Promedio';

$string['fml_grades_distribution_subtitle'] = 'Distribución de Calificaciones';
$string['fml_grades_distribution_greater_than'] = 'mayor al';
$string['fml_grades_distribution_smaller_than'] = 'menor al';
$string['fml_grades_distribution_yaxis_title'] = 'Número de Estudiantes';
$string['fml_grades_distribution_tooltip_prefix'] = 'Rango';
$string['fml_grades_distribution_tooltip_suffix'] = 'en este rango';
$string["fml_view_details"] = "(Clic para ver detalles)";


/* Quiz */
$string["fml_quiz_info_text"] = "Esta Evaluación tiene";
$string["fml_question_text"] = "pregunta";
$string["fml_questions_text"] = "preguntas";
$string["fml_doing_text_singular"] = "intento realizado por";
$string["fml_doing_text_plural"] = "intentos realizados por";
$string["fml_attempt_text"] = "intento";
$string["fml_attempts_text"] = "intentos";
$string["fml_student_text"] = "estudiante";
$string["fml_students_text"] = "estudiantes";
$string["fml_quiz"] = "Evaluaciones";
$string["fml_questions_attempts_chart_title"] = "Intentos de Preguntas";
$string["fml_questions_attempts_yaxis_title"] = "Número de Intentos";
$string["fml_hardest_questions_chart_title"] = "Preguntas mas Difíciles";
$string["fml_hardest_questions_yaxis_title"] = "Intentos Incorrectos";
$string["fml_correct_attempt"] = "Correctos";
$string["fml_partcorrect_attempt"] = "Parcialmente Correctos";
$string["fml_incorrect_attempt"] = "Incorrectos";
$string["fml_blank_attempt"] = "En Blanco";
$string["fml_needgraded_attempt"] = "Sin Calificar";
$string["fml_review_question"] = "(Clic para revisar la pregunta)";


/* Deserción */
$string["fml_cluster_text"] = "Clúster";
$string["fml_cluster_label"] = "Grupo";
$string["fml_dropout_table_title"] = "Estudiantes del Grupo";
$string["fml_dropout_see_profile"] = "Ver Perfil";




