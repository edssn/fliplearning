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
//$string['txt_hour'] = 'Hora';
//$string['txt_hours'] = 'Horas';
//$string['txt_minut'] = 'Minuto';
//$string['txt_minuts'] = 'Minutos';
//$string['pagination_week'] = 'Semana';
//$string['only_student'] = 'Este reporte es solo para estudiantes';
//$string['sr_hour'] = 'hora';
//$string['sr_hours'] = 'horas';
//$string['sr_minute'] = 'minuto';
//$string['sr_minutes'] = 'minutos';
//$string['sr_second'] = 'segundo';
//$string['sr_seconds'] = 'segundos';
$string['weeks_not_config'] = 'El curso no ha sido configurado por el profesor, por lo que no hay visualizaciones que mostrar.';
$string['pagination_title'] = 'Selección semana';
$string['helplabel'] = 'Ayuda';
$string['exitbutton'] = '¡Entendido!';
$string['hours_unit_time_label'] = 'Número de Horas';
$string['no_data'] = 'No hay datos que mostrar';

/* Menú */
$string['menu_main_title'] = "Dashboard Progreso";
$string['menu_sessions'] = 'Sesiones de Estudio';
$string['menu_setweek'] = "Configurar semanas";
$string['menu_logs'] = "Descargar registros";
$string['menu_time_worked_session_report'] = 'Sesiones de estudio por semana';
$string['menu_activities_performed'] = 'Actividades realizadas';
$string['menu_notes'] = 'Anotaciones';

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
$string['ss_title'] = 'Sesiones de Trabajo';
$string['ss_monday'] = 'Lunes';
$string['ss_tuesday'] = 'Martes';
$string['ss_wednesday'] = 'Miércoles';
$string['ss_thursday'] = 'Jueves';
$string['ss_friday'] = 'Viernes';
$string['ss_saturday'] = 'Sábado';
$string['ss_sunday'] = 'Domingo';

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

$string['ss_axis_x'] = 'Días de la semana';
$string['ss_axis_y'] = 'Horas del día';
$string['ss_url'] = 'URL';
$string['ss_resource_document'] = 'Documento';
$string['ss_resource_image'] = 'Imagen';
$string['ss_resource_audio'] = 'Audio';
$string['ss_resource_video'] = 'Video';
$string['ss_resource_file'] = 'Archivo';
$string['ss_resource_script'] = 'Script/Código';
$string['ss_resource_text'] = 'Texto';
$string['ss_resource_download'] = 'Descargas';
$string['ss_assign'] = 'Tarea';
$string['ss_assignment'] = 'Tarea';
$string['ss_book'] = 'Libro';
$string['ss_choice'] = 'Elección';
$string['ss_feedback'] = 'Retroalimentación';
$string['ss_folder'] = 'Carpeta';
$string['ss_forum'] = 'Foro';
$string['ss_glossary'] = 'Glosario';
$string['ss_label'] = 'Etiqueta';
$string['ss_lesson'] = 'Lección';
$string['ss_page'] = 'Página';
$string['ss_quiz'] = 'Examen';
$string['ss_survey'] = 'Encuesta';
$string['ss_lti'] = 'Herramienta externa';
$string['ss_other'] = 'Otro';
$string['ss_interaction'] = 'Interacción por';
$string['ss_interactions'] = 'Interacciones por';
$string['ss_course_module'] = 'Módulo';
$string['ss_course_modules'] = 'Módulos';
$string['ss_student'] = 'Estudiante.';
$string['ss_students'] = 'Estudiantes.';
$string['ss_average'] = 'Promedio interacciones';
$string['ss_change_timezone'] = 'Zona horaria:';
$string['ss_activity_inside_plataform_student'] = 'Mi actividad en la plataforma';
$string['ss_activity_inside_plataform_teacher'] = 'Actividad de los estudiantes en la plataforma';
$string['ss_time_inside_plataform_student'] = 'Mi tiempo en la plataforma';
$string['ss_time_inside_plataform_teacher'] = 'Tiempo invertido en promedio de los estudiantes en la plataforma en esta semana';
$string['ss_time_inside_plataform_description_teacher'] = 'Tiempo que el estudiante ha invertido en la semana seleccionada, en comparación al tiempo que el/la docente planificó que se debería invertir. El tiempo invertido que se visualiza corresponde al promedio de todos los estudiantes. El tiempo planificado por el/la docente es el asignado en por el/la docente en <i>Configurar Semanas</i>.';
$string['ss_time_inside_plataform_description_student'] = 'Tiempo que ha invertido esta semana en relación al tiempo que el profesor planificó que se debería invertir.';
$string['ss_activity_inside_plataform_description_teacher'] = 'En el eje Y se indican las las horas del día y en el eje X los días de la semana. Dentro del gráfico podrá encontrar múltiples puntos, los cuales, al pasar el cursor sobre estos, ofrecen información detallada sobre las interacciones de los estudiantes, agrupadas por tipo de recurso (número de interacciones, número de estudiantes que interactuaron con el recurso y promedio de interacciones). <br/><br/><b>Al hacer click en las etiquetas, podrá filtrar por tipo de recurso, dejando visible sólo aquellos que no se encuentren tachados.</b>';
$string['ss_activity_inside_plataform_description_student'] = 'Presenta las interacciones por tipo de recurso y horario. Al pasar el cursor sobre un punto visible en el gráfico, verá el número de interacciones agrupadas por tipo de recurso. Al hacer click en las etiquetas, podrá filtrar por tipo de recurso.';
$string['ss_to'] = 'al';
$string['ss_time_spend'] = 'Tiempo invertido';
$string['ss_time_spend_teacher'] = 'Tiempo promedio invertido';
$string['ss_time_should_spend'] = 'Tiempo que deberías invertir';
$string['ss_time_should_spend_teacher'] = 'Tiempo promedio que se debería invertir';

/* Goups */
$string['group_allstudent'] = 'Todos los estudiantes';

/* General Errors */
$string['api_error_network'] = "Ha ocurrido un error en la comunicación con el servidor.";
$string['api_invalid_data'] = 'Datos incorrectos';
$string['api_save_successful'] = 'Se han guardado los datos correctamente en el servidor';
$string['api_cancel_action'] = 'Has cancelado la acción';
//$string['pluginname'] = 'Note My Progress';
//$string['previous_days_to_create_report'] = 'Días considerados para la construcción del reporte';
//$string['previous_days_to_create_report_description'] = 'Días anteriores a la fecha actual que se tendrán en cuenta para generar el reporte.';
//$string['student_reports:usepluggin'] = 'Utilizar el pluggin';
//$string['student_reports:downloadreport'] = 'Descargar actividad de los estudiantes';
//$string['student_reports:setweeks'] = 'Configurar semanas del curso';
//$string['student_reports:activities_performed'] = 'Ver el reporte "Actividades realizadas"';
//$string['student_reports:metareflexion'] = 'Ver el reporte "Metareflexión"';
//$string['student_reports:notes'] = 'Utilizar las notas';

/* Admin Task Screen*/
$string['generate_data_task'] = 'Proceso para generar datos para Flip my Learning Plugin';
