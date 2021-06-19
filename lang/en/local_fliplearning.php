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
$string['fml_graph_generating'] = 'We are building the report, please wait a moment.';
$string['fml_weeks_not_config'] = 'The course has not been configured, so there are no visualizations to show.';
$string['fml_helplabel'] = 'Help';
$string['fml_exitbutton'] = 'Got it!';
$string['fml_only_student'] = 'The report is for students only';
$string["fml_send_mail"] = "(Click to send mail )";
$string["fml_about"] = "About this Chart";
$string["fml_about_table"] = "About this Table";
$string["fml_not_configured"] = "Not Configured";
$string["fml_activated"] = "Activated";
$string["fml_disabled"] = "Disabled";
$string["fml_sessions"] = "Sessions";
$string["fml_resources"] = "Resources";
$string['fml_finished_resources'] = 'Completed Resources';
$string["fml_session_text"] = "session";
$string["fml_sessions_text"] = "sessions";
$string["fml_student_text"] = "student";
$string["fml_students_text"] = "students";
$string['fml_change_timezone'] = 'Time zone:';
$string['fml_of_conector'] = 'of';
$string['fml_module_label'] = 'resource';
$string['fml_modules_label'] = 'resources';
$string['fml_finished_label'] = 'completed';
$string['fml_finisheds_label'] = 'completed';

/* Admin Settings */
$string['fml_use_navbar_menu'] = 'Enable dropdown menu';
$string['fml_use_navbar_menu_desc'] = 'Display the plugin menu in the navigation bar (upper right). Otherwise, the plugin menu will be in the navigation block.';

/* Menú */
$string['fml_menu_sessions'] = 'Study Sessions';
$string['fml_menu_setweek'] = "Set Weeks";
$string['fml_menu_assignments'] = 'Assignments';
$string['fml_menu_grades'] = 'Grades';
$string['fml_menu_quiz'] = 'Quizzes';
$string['fml_menu_dropout'] = 'Dropout';
$string['fml_menu_dropout'] = 'Dropout';
$string['fml_menu_logs'] = "Download Records";
$string['fml_menu_general'] = "General Indicators";

/* Nav Bar Menu */
$string['fml_togglemenu'] = 'Show/Hide FML menu';

/* Pagination component */
$string['fml_pagination'] = 'Week:';
$string['fml_pagination_title'] = 'Week selection';
$string['fml_pagination_component_to'] = 'to';
$string['fml_pagination_component_name'] = 'Week';

/* Student Goups */
$string['fml_group_allstudent'] = 'All students';

/* General Errors */
$string['fml_api_error_network'] = "An error has occurred in communication with the server.";
$string['fml_api_invalid_data'] = 'Incorrect data';
$string['fml_api_save_successful'] = 'The data has been successfully saved on the server';
$string['fml_api_cancel_action'] = 'You have canceled the action';

/* Admin Schedule Task Screen*/
$string['fml_generate_dropout_data_task'] = 'Process to generate Dropout data for Flip My Learning';

/* Chart*/
$string['fml_chart_loading'] = 'Loading...';
$string['fml_chart_exportButtonTitle'] = "Export";
$string['fml_chart_printButtonTitle'] = "Print";
$string['fml_chart_rangeSelectorFrom'] = "From";
$string['fml_chart_rangeSelectorTo'] = "To";
$string['fml_chart_rangeSelectorZoom'] = "Range";
$string['fml_chart_downloadPNG'] = 'Download PNG image';
$string['fml_chart_downloadJPEG'] = 'Download JPEG image';
$string['fml_chart_downloadPDF'] = 'Download PDF document';
$string['fml_chart_downloadSVG'] = 'Download SVG Image';
$string['fml_chart_downloadCSV'] = 'Download CSV';
$string['fml_chart_downloadXLS'] = 'Download XLS';
$string['fml_chart_exitFullscreen'] = 'Exit Full Screen';
$string['fml_chart_hideData'] = 'Hide Data Table';
$string['fml_chart_noData'] = 'No data to show';
$string['fml_chart_printChart'] = 'Print Chart';
$string['fml_chart_viewData'] = 'See Data Table';
$string['fml_chart_viewFullscreen'] = 'View in Full Screen';
$string['fml_chart_resetZoom'] = 'Reset zoom';
$string['fml_chart_resetZoomTitle'] = 'Reset zoom level 1:1';

/* Email */
$string['fml_validation_subject_text'] = 'Subject is required';
$string['fml_validation_message_text'] = 'Message is required';
$string['fml_subject_label'] = 'Add a subject';
$string['fml_message_label'] = 'Add a message';
$string['fml_submit_button'] = 'Send';
$string['fml_cancel_button'] = 'Cancel';
$string['fml_close_button'] = 'Close';
$string['fml_emailform_title'] = 'Send mail';
$string['fml_sending_text'] = 'Sending Emails';
$string['fml_recipients_label'] = 'Mail to';
$string['fml_mailsended_text'] = 'Emails sended';
$string['fml_email_footer_text'] = 'This is an email sent with Fliplearning.';
$string['fml_email_footer_prefix'] = 'Go to';
$string['fml_email_footer_suffix'] = 'for more information.';
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
$string['fml_assign'] = 'Assign';
$string['fml_assignment'] = 'Assignment';
$string['fml_attendance'] = 'Attendance';
$string['fml_book'] = 'Book';
$string['fml_chat'] = 'Chat';
$string['fml_choice'] = 'Choice';
$string['fml_data'] = 'Database';
$string['fml_feedback'] = 'Feedback';
$string['fml_folder'] = 'Folder';
$string['fml_forum'] = 'Forum';
$string['fml_glossary'] = 'glossary';
$string['fml_h5pactivity'] = 'H5P';
$string['fml_imscp'] = 'IMS Content Package';
$string['fml_label'] = 'Label';
$string['fml_lesson'] = 'Lesson';
$string['fml_lti'] = 'LTI Content Package';
$string['fml_page'] = 'Page';
$string['fml_quiz'] = 'Quiz';
$string['fml_resource'] = 'Resource';
$string['fml_scorm'] = 'SCORM Package';
$string['fml_survey'] = 'Survey';
$string['fml_url'] = 'Url';
$string['fml_wiki'] = 'Wiki';
$string['fml_workshop'] = 'Workshop';


/* Time */
$string['fml_year'] = 'year';
$string['fml_years'] = 'years';
$string['fml_month'] = 'month';
$string['fml_months'] = 'months';
$string['fml_day'] = 'day';
$string['fml_days'] = 'days';
$string['fml_hour'] = 'hour';
$string['fml_hours'] = 'hours';
$string['fml_hours_short'] = 'h';
$string['fml_minute'] = 'minute';
$string['fml_minutes'] = 'minutes';
$string['fml_minutes_short'] = 'm';
$string['fml_second'] = 'second';
$string['fml_seconds'] = 'seconds';
$string['fml_seconds_short'] = 's';
$string['fml_ago'] = 'ago';
$string['fml_now'] = 'just now';

$string['fml_mon'] = 'Monday';
$string['fml_tue'] = 'Tuesday';
$string['fml_wed'] = 'Wednesday';
$string['fml_thu'] = 'Thursday ';
$string['fml_fri'] = 'Friday';
$string['fml_sat'] = 'Saturday';
$string['fml_sun'] = 'Sunday';
$string['fml_mon_short'] = 'Mon';
$string['fml_tue_short'] = 'Tue';
$string['fml_wed_short'] = 'Wed';
$string['fml_thu_short'] = 'Thu';
$string['fml_fri_short'] = 'Fri';
$string['fml_sat_short'] = 'Sat';
$string['fml_sun_short'] = 'Sun';

$string['fml_jan'] = 'January';
$string['fml_feb'] = 'February';
$string['fml_mar'] = 'March';
$string['fml_apr'] = 'April';
$string['fml_may'] = 'May';
$string['fml_jun'] = 'June';
$string['fml_jul'] = 'July';
$string['fml_aug'] = 'August';
$string['fml_sep'] = 'September';
$string['fml_oct'] = 'October';
$string['fml_nov'] = 'November';
$string['fml_dec'] = 'December';
$string['fml_jan_short'] = 'Jan';
$string['fml_feb_short'] = 'Feb';
$string['fml_mar_short'] = 'Mar';
$string['fml_apr_short'] = 'Apr';
$string['fml_may_short'] = 'May';
$string['fml_jun_short'] = 'Jun';
$string['fml_jul_short'] = 'Jul';
$string['fml_aug_short'] = 'Aug';
$string['fml_sep_short'] = 'Sep';
$string['fml_oct_short'] = 'Oct';
$string['fml_nov_short'] = 'Nov';
$string['fml_dec_short'] = 'Dec';

$string['fml_week1'] = 'Week 1';
$string['fml_week2'] = 'Week 2';
$string['fml_week3'] = 'Week 3';
$string['fml_week4'] = 'Week 4';
$string['fml_week5'] = 'Week 5';
$string['fml_week6'] = 'Week 6';

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
$string['tw_section_title'] = 'Course Weeks Configuration';
$string['tw_section_help_title'] = 'Course Weeks Configuration';
$string['tw_section_help_description'] = 'To begin, you must configure the course by weeks and define a starting date for the first week (the rest of the weeks will be carried out automatically from this date. Next, you must associate the activities or modules related to each week by dragging them from the right column to the corresponding week. It is not necessary to assign all the activities or modules to the weeks, simply those that you want to consider to follow up the students. Finally, you must click on the Save button to keep your settings.';
$string['tw_sections'] = "Available sections in the course";
$string['tw_weeks_of_course'] = "Weeks planning";
$string['tw_add_new_week'] = "Add week";
$string['tw_start'] = "Start:";
$string['tw_end'] = "End:";
$string['tw_week'] = "Week";
$string['tw_save'] = "Save configuration";
$string['tw_time_dedication'] = "How many hours of work do you expect students to put into your course this week?";
$string['tw_enable_scroll'] = "Activate scrolling mode for weeks";
$string['tw_label_section_removed'] = "Eliminado del curso";
$string['tw_error_section_removed'] = "A section assigned to a week has been removed from the course, you must remove it from your schedule in order to continue.";
$string['tw_save_warning_title'] = "Are you sure you want to save the changes?";
$string['tw_save_warning_content'] = "If you change the settings for the weeks when the course has already started, data may be lost...";
$string['tw_confirm_ok'] = "Save";
$string['tw_confirm_cancel'] = "Cancel";
$string['tw_error_empty_week'] = "You cannot save changes with an empty week. Please delete it and try again.";
$string['tw_new_group_title'] = "New configuration instance";
$string['tw_new_group_text'] = "We have detected that your course has ended, if you want to configure the weeks to work with new students, you must activate the button below. This will allow the data of current students to be separated from those of previous courses, avoiding mixing them.";
$string['tw_new_group_button_label'] = "Save configuration as new instance";
$string['tw_course_format_weeks'] = 'Week';
$string['tw_course_format_topics'] = 'Theme';
$string['tw_course_format_social'] = 'Social';
$string['tw_course_format_singleactivity'] = 'Single activity';
$string['tw_plugin_requirements_title'] = 'Status:';
$string['tw_plugin_requirements_descriptions'] = 'The plugin will be visible and will show the reports for students and teachers when the following conditions are met...';
$string['tw_plugin_requirements_has_users'] = 'The course must have at least one enrolled student';
$string['tw_plugin_requirements_course_start'] = 'The current date must be greater than the start date of the first configured week.';
$string['tw_plugin_requirements_has_sections'] = 'The configured weeks have at least one section.';
$string['tw_plugin_visible'] = 'Visible graphs.';
$string['tw_plugin_hidden'] = 'Graphs hidden.';
$string['tw_title_conditions'] = 'Terms of use';


/* Teacher General Indicators */
$string['tg_section_help_title'] = 'General Indicators';
$string['tg_section_help_description'] = 'This section contains visualizations with general indicators related to course configuration, resources assigned by weeks, study sessions, and student progress throughout the course. The visualizations in this section show the indicators from the start date to the end of the course (or to the current date in case the course has not finished yet).';

$string['tg_indicators_students'] = 'Students';
$string['tg_indicators_weeks'] = 'Weeks';
$string['tg_indicators_grademax'] = 'Rating';
$string['tg_indicators_course_start'] = 'Start';
$string['tg_indicators_course_end'] = 'End';
$string['tg_indicators_course_format'] = 'Format';
$string['tg_indicators_course_completion'] = 'Modules Completion';
$string["tg_indicators_student_progress"] = "Students Progress";

$string['tg_week_resources_help_title'] = 'Resources by Weeks';
$string['tg_week_resources_help_description_p1'] = 'This graph displays the amount of resources for each of the course sections assigned to each study week configured in the <i> Configure Weeks </i> section. If a week has two or more course sections assigned, the resources in those sections are added together to calculate the total resources for a week.';
$string['tg_week_resources_help_description_p2'] = 'On the x-axis of the graph are the total resources and activities of the sections assigned to each configured week of Flip My Learning. On the y axis are the configured study weeks.';
$string["tg_week_resources_chart_title"] = "Resources by Weeks";
$string["tg_week_resources_yaxis_title"] = "Resources Quantity";

$string['tg_weeks_sessions_help_title'] = 'Sessions per Week';
$string['tg_week_sessions_help_description_p1'] = 'This graph shows the number of study sessions completed by students each week from the course start date. Access to the course by the student is considered the beginning of a study session. A session is considered finished when the time elapsed between two interactions of a student exceeds 30 minutes.';
$string['tg_week_sessions_help_description_p2'] = 'On the x-axis of the graph are the weeks of each month. The y-axis of the graph shows the different months of the year starting from the month of creation of the course. To maintain the symmetry of the graph, a total of five weeks has been placed for each month, however, not every month has that many weeks. These months will only add sessions until week four.';
$string['ts_weeks_sessions_chart_title'] = 'Sessions per Week';

$string['tg_progress_table_help_title'] = 'Students Progress';
$string['tg_progress_table_help_description'] = 'This table shows a list of all students enrolled in the course along with their progress, number of sessions and time spent. To calculate progress, all course resources have been considered, except for those of the <i> Label </i> type. To determine if a student has completed a resource, it is first checked to see if the resource has the completeness setting enabled. If so, it is searched if the student has already completed the activity based on that configuration. Otherwise, the activity is considered complete if the student has seen it at least once.';
$string["tg_progress_table_table_title"] = "Course Progress";
$string['tg_progress_table_thead_name'] = 'Name';
$string['tg_progress_table_thead_lastname'] = 'Last Name';
$string['tg_progress_table_thead_email'] = 'Email';
$string['tg_progress_table_thead_progress'] = 'Progress (%)';
$string['tg_progress_table_thead_sessions'] = 'Sessions';
$string['tg_progress_table_thead_time'] = 'Inverted Time';


/* Teacher Sessions */
$string['ts_section_help_title'] = 'Study Sessions';
$string['ts_section_help_description'] = 'This section contains visualizations with indicators related to the activity of the students in the course measured in terms of sessions carried out, average time spent in the course per week and study sessions in time intervals. The data presented in this section varies depending on the study week selected.';

$string['ts_inverted_time_help_title'] = 'Student Time Inverted';
$string['ts_inverted_time_help_description_p1'] = 'This graph shows the average time spent by students for the week compared to the average time planned by the teacher.';
$string['ts_inverted_time_help_description_p2'] = 'On the x-axis of the graph is the number of hours that the teacher has planned for a specific week. On the y-axis are the labels for the average time spent and the average time that should be spent.';
$string['ts_time_inverted_chart_title'] = 'Student Time Inverted';
$string['ts_time_inverted_xaxis'] = 'Number of Hours';
$string['ts_time_inverted_inverted_label'] = 'Average Time Inverted';
$string['ts_time_inverted_expected_label'] = 'Average time that should be inverted';

$string['ts_hours_sessions_help_title'] = 'Sessions by Day and Hour';
$string['ts_hours_sessions_help_description_p1'] = 'This graph shows study sessions by day and time for the selected week. Access to the course by the student is considered the beginning of a study session. A session is considered finished when the time elapsed between two interactions of a student exceeds 30 minutes.';
$string['ts_hours_sessions_help_description_p2'] = 'On the x-axis of the graph are the days of the week. On the y axis are the hours of the day starting at 12am and ending at 11pm or 11pm.';
$string['ts_hours_sessions_chart_title'] = 'Sessions by Day and Hour';

$string['ts_sessions_count_help_title'] = 'Sessions of the Week';
$string['ts_sessions_count_help_description_p1'] = 'This graph shows the number of sessions classified by their duration in time ranges: less than 30 minutes, greater than 30 minutes and greater than 60 minutes. Access to the course by the student is considered the beginning of a study session. A session is considered finished when the time elapsed between two interactions of a student exceeds 30 minutes.';
$string['ts_sessions_count_help_description_p2'] = 'On the x-axis of the graph are the days of the week set. On the y-axis is the number of sessions performed.';
$string['ts_session_count_chart_title'] = 'Sessions of the Week';
$string['ts_session_count_yaxis_title'] = 'Number of Sessions';
$string['ts_session_count_tooltip_suffix'] = ' sessions';
$string['ts_sessions_count_smaller30'] = 'Less than 30 minutes';
$string['ts_sessions_count_greater30'] = 'Greater than 30 minutes';
$string['ts_sessions_count_greater60'] = 'Greater than 60 minutes';


/*Teacher Assignments*/
$string['ta_section_help_title'] = 'Assigns';
$string['ta_section_help_description'] = 'This section contains indicators related to task delivery and access to resources. The data presented in this section varies depending on the study week selected.';

$string['ta_assigns_submissions_help_title'] = 'Assigns Submissions';
$string['ta_assigns_submissions_help_description_p1'] = 'This graph shows the distribution of the number of students, with respect to the delivery status of an assignment.';
$string['ta_assigns_submissions_help_description_p2'] = 'On the x-axis of the graph are the tasks of the sections assigned to the week along with the date and time of delivery. On the y-axis is the distribution of the number of students according to the delivery status. The graph has the option to send an email to the students in some distribution (delivery on time, delivery late, no delivery) clicking on the graph.';
$string['ta_assigns_submissions_intime_sub'] = 'On time Submissions';
$string['ta_assigns_submissions_late_sub'] = 'Late Submissions';
$string['ta_assigns_submissions_no_sub'] = 'No Submissions';
$string['ta_assigns_submissions_assign_nodue'] = 'No deadline';
$string['ta_assigns_submissions_title'] = 'Assigns Submissions';
$string['ta_assigns_submissions_yaxis'] = 'Number of students';
$string['ta_assigns_submissions_xaxis_access'] = 'Accessed';
$string['ta_assigns_submissions_xaxis_no_access'] = 'No access';

$string['ta_access_content_help_title'] = 'Access to Course Contents';
$string['ta_access_content_help_description_p1'] = 'This graph presents the number of students who have accessed and have not accessed the course resources. At the top are the different types of Moodle resources, with the possibility of filtering the information in the graph according to the type of resource selected.';
$string['ta_access_content_help_description_p2'] = 'On the x-axis of the graph are the number of students enrolled in the course. The y-axis of the graph shows the resources of the sections assigned to the week. In addition, this graphic allows you to send an email to students who have accessed the resource or to those who have not accessed clicking on the graphic.';
$string['ta_access_content_chart_title'] = 'Access to Course Contents';
$string['ta_access_content_yaxis_title'] = 'Number of Students';
$string['ta_access_content_tooltip_suffix'] = ' students';


/* Teacher Rating*/
$string['tr_section_help_title'] = 'Ratings';
$string['tr_section_help_description'] = 'This section contains indicators related to the grade point averages in the evaluable activities. The different teaching units (Qualification Categories) created by the teacher are shown in the <i> Qualification Category </i> selector. This selector will allow you to switch between the different units defined and show the activities that can be evaluated in each one.';
$string['tr_select_label'] = 'Rating Category';

$string['tr_grade_items_average_help_title'] = 'Average of Evaluable Activities';
$string['tr_grade_items_average_help_description_p1'] = "This graph presents the average (percentage) of students' grades in each of the assessable activities in the course. The average in percentage is calculated based on the maximum grade of the evaluable activity (example: an evaluable activity with a maximum score of 80 and an average score of 26 will present a bar with a height equal to 33%, since 26 is 33% of the total rating). The grade point average has been expressed based on percentages to preserve the symmetry of the graph, since Moodle allows you to create activities and assign custom grades.";
$string['tr_grade_items_average_help_description_p2'] = 'On the x-axis of the graph are the different assessable activities of the course. On the y-axis is the grade point average expressed as a percentage.';
$string['tr_grade_items_average_help_description_p3'] = 'Clicking on the bar corresponding to an assessable activity, the data in the two lower graphs will be updated to show additional information about the selected assessable activity.';
$string['tr_grade_items_average_chart_title'] = 'Averages of Evaluable Activities';
$string['tr_grade_items_average_yaxis_title'] = 'Ratings Average (%)';
$string['tr_grade_items_average_tooltip_average'] = 'Avergafe';
$string['tr_grade_items_average_tooltip_graded_students'] = 'Graded Students';

$string['tr_item_grades_details_help_title'] = 'Best, Worst and Average Rating';
$string['tr_item_grades_details_help_description_p1'] = 'This chart shows the best grade, average grade, and worst grade on an assessable activity (the activity selected from the Average Assessable Activities chart).';
$string['tr_item_grades_details_help_description_p2'] = 'On the x-axis of the graph is the score for the qualification of the activity, with the maximum mark for the activity being the maximum value on this axis. On the y axis are the labels for Best Rating, Average Rating, and Worst Rating.';
$string['tr_item_grades_details_chart_subtitle'] = 'Best, Worst and Average Rating';
$string['tr_item_grades_details_yaxis_best_grade'] = 'Best Rating';
$string['tr_item_grades_details_yaxis_average_grade'] = 'Average Rating';
$string['tr_item_grades_details_yaxis_worst_grade'] = 'Worst Rating';

$string['tr_item_grades_distribution_help_title'] = 'Ratings Distribution';
$string['tr_item_grades_distribution_help_description_p1'] = 'This graph shows the distribution of students in different grade ranges. Grade ranges are calculated based on percentages. The following ranges are taken into account: less than 50%, greater than 50%, greater than 60%, greater than 70%, greater than 80% and greater than 90%. These ranges are calculated based on the maximum weight that the teacher assigned to an evaluable activity.';
$string['tr_item_grades_distribution_help_description_p2'] = 'On the x-axis are the rating ranges for the activity. On the y-axis is the number of students who belong to a certain rank.';
$string['tr_item_grades_distribution_help_description_p3'] = 'Clicking on the bar corresponding to a rank, an email can be sent to the students within the grading rank.';
$string['tr_item_grades_distribution_chart_subtitle'] = 'Ratings Distribution';
$string['tr_item_grades_distribution_xaxis_greater_than'] = 'greater than';
$string['tr_item_grades_distribution_xaxis_smaller_than'] = 'less than';
$string['tr_item_grades_distribution_yaxis_title'] = 'Number of Students';
$string['tr_item_grades_distribution_tooltip_prefix'] = 'Range';
$string["tr_item_grades_distribution_tooltip_view_details"] = "(Click to see details)";


/* Teacher Quiz  */
$string['tq_section_help_title'] = 'Quizzes';
$string['tq_section_help_description'] = 'This section contains indicators related to the summary of attempts in the different evaluations of the course and analysis of the questions of an evaluation. The data presented in this section varies depending on the selected study week and on a selector that contains all the Assessment-type activities of the course sections assigned to the selected week.';

$string["tq_quiz_number_questions"] = "Number of Questions";
$string["tq_quiz_attempts_done"] = "Attempts Done";
$string["tq_quiz_students_attempts"] = 'Students Who Tried the Assessment';
$string["fml_select_quiz"] = "Quizzes";

$string['tq_questions_attempts_help_title'] = 'Question Attempts';
$string['tq_questions_attempts_help_description_p1'] = 'This graph shows the distribution of attempts to solve each question in an assessment, along with their review status.';
$string['tq_questions_attempts_help_description_p2'] = 'On the x-axis of the graph are the assessment questions. The y-axis shows the number of resolution attempts for each of these questions. The symmetry of the graph will be affected by the assessment settings (example: in an assessment that always has the same questions, the graph will present the same number of attempts for each bar corresponding to a question. In an assessment that has random questions ( of a question bank), the graph will present in the bar of each question the sum of the evaluation attempts in which it appeared, and may not be the same for each evaluation question).';
$string['tq_questions_attempts_help_description_p3'] = 'Clicking on any of the bars corresponding to a question, it is possible to see the evaluation question in a pop-up window.';
$string["tq_questions_attempts_chart_title"] = "Question Attempts";
$string["tq_questions_attempts_yaxis_title"] = "Number of Attemps";
$string["tq_questions_attempts_xaxis_correct_attempt"] = "Correct";
$string["tq_questions_attempts_xaxis_partcorrect_attempt"] = "Partially Correct";
$string["tq_questions_attempts_xaxis_incorrect_attempt"] = "Incorrect";
$string["tq_questions_attempts_xaxis_blank_attempt"] = "Blank";
$string["tq_questions_attempts_xaxis_needgraded_attempt"] = "Unrated";
$string["tq_questions_attempts_tooltip_review_question"] = "(Click to review the question)";

$string['tq_hardest_questions_help_title'] = 'Hardest Questions';
$string['tq_hardest_questions_help_description_p1'] = 'This graph shows the assessment questions ordered by their level of difficulty. An attempt to resolve a question with the status of Partially Correct, Incorrect or Blank is considered incorrect, so that the total number of incorrect attempts of a question is the sum of the attempts with the aforementioned statuses. The level of difficulty is represented as a percentage calculated based on the total number of attempts.';
$string['tq_hardest_questions_help_description_p2'] = 'On the x-axis of the graph are the assessment questions identified by name. The y-axis shows the percentage of unsuccessful attempts out of the total number of attempts for the question. This axis allows us to identify which have been the questions that have represented the greatest difficulty for the students who took the evaluation.';
$string['tq_hardest_questions_help_description_p3'] = 'Clicking on any of the bars corresponding to a question, it is possible to see the evaluation question in a pop-up window.';
$string["tq_hardest_questions_chart_title"] = "Hardest Questions";
$string["tq_hardest_questions_yaxis_title"] = "Incorrect Attempts";


/* Teacher Dropout */
$string['td_section_help_title'] = 'Dropout';
$string['td_section_help_description'] = 'This section contains indicators related to the prediction of student dropouts from a course. The information is displayed based on groups of students calculated by an algorithm that analyzes the behavior of each student based on the time inverted, the number of student sessions, the number of days active and the interactions they have made with each resource and with the other students in the course. The algorithm places students with similar behavior in the same group, so that students who are more and less committed to the course can be identified. The data presented in this section varies depending on the group selected in the selector that contains the groups identified in the course.';

$string["td_cluster_label"] = "Group";
$string["td_cluster_high_dropout_risk"] = "High Dropout Risk";
$string["td_cluster_middle_dropout_risk"] = "Middle Dropout Risk";
$string["td_cluster_low_dropout_risk"] = "Low Dropout Risk";
$string["td_cluster_select"] = "Students Group";
$string["td_dropout_table_title"] = "Group Students";
$string["td_dropout_see_profile"] = "View profile";
$string["td_user_never_access"] = "Never accessed";
$string["td_student_progress_title"] = "Student Progress";
$string["td_student_grade_title"] = "Grade";
$string['td_no_data'] = "There is no dropout data for this course. If you can't generate them manually, the course may have ended. ";
$string['td_no_users_cluster'] = "There are no students in this group";
$string['td_generate_data_manually'] = "Generate Manually";
$string['td_generating_data'] = "Generating data...";
$string['td_send_mail_to_user'] = 'Mail to';
$string['td_send_mail_to_group'] = 'Mail to Group';
$string["td_modules_amount"] = "Quantity of Resources";
$string["td_modules_details"] = "(Click to see resources)";
$string["td_modules_interaction"] = "interaction";
$string["td_modules_interactions"] = "interactions";
$string["td_modules_viewed"] = "Accessed";
$string["td_modules_no_viewed"] = "Not accessed";
$string["td_modules_complete"] = "Completed";

$string['td_group_students_help_title'] = 'Group Students';
$string['td_group_students_help_description_p1'] = "In this table are the students belonging to the group selected from the Student Group selector. Each student's photo, names and the percentage of progress in the course are listed. For the calculation of progress, all course resources have been considered, except for those of the Label type. To determine if a student has completed a resource, it is first checked to see if the resource has the completeness setting enabled. If so, it is searched if the student has already completed the activity based on that configuration. Otherwise, the activity is considered complete if the student has seen it at least once.";
$string['td_group_students_help_description_p2'] = 'Clicking on a student in this table will update the graphs below with the information for the selected student.';

$string['td_modules_access_help_title'] = 'Course Resources';
$string['td_modules_access_help_description_p1'] = 'This graph shows the amount of resources the student has accessed and completed. The data presented in this graph varies depending on the student selected in the Group Students table. To determine the amount of resources and complete activities, the Moodle configuration called Activity Completion is used. In case the teacher does not make the completeness configuration for the course activities, the number of activities accessed and completed will always be the same, since without such configuration, a resource is considered finished when the student accesses it.';
$string['td_modules_access_help_description_p2'] = 'On the x-axis are the amount of course resources. On the y axis are the labels of Accessed, Complete and Total of course resources.';
$string['td_modules_access_help_description_p3'] = 'Clicking on any bar it is possible to see the resources and activities available in the course (in a pop-up window) along with the number of student interactions with each resource and a label of not accessed, accessed or completed.';

$string['td_week_modules_help_title'] = 'Resources by Weeks';
$string['td_week_modules_help_description_p1'] = 'This graph shows the amount of resources that the student has accessed and completed for each of the weeks configured in the plugin. The data presented in this graph varies depending on the student selected in the <i> Group Students </i> table.';
$string['td_week_modules_help_description_p2'] = 'On the x-axis of the graph are the different study weeks configured. On the y axis is the amount of resources and activities accessed and completed by the student.';
$string['td_week_modules_help_description_p3'] = 'Clicking on any bar it is possible to see the resources and activities available in the course (in a pop-up window) along with the number of student interactions with each resource and a label of not accessed, accessed or completed.';
$string["td_week_modules_chart_title"] = "Resources by Weeks";

$string["td_modules_access_chart_title"] = "Course Resources";
$string["td_modules_access_chart_series_total"] = "Total";
$string["td_modules_access_chart_series_complete"] = "Completed";
$string["td_modules_access_chart_series_viewed"] = "Accessed";

$string['td_sessions_evolution_help_title'] = 'Sessions and Time Inverted';
$string['td_sessions_evolution_help_description_p1'] = 'This graph allows you to know how the study sessions have evolved since your first session was registered in the course. The data presented in this graph varies depending on the student selected in the <i> Group Students </i> table.';
$string['td_sessions_evolution_help_description_p2'] = 'The x-axis of the graph shows a time line with the days that have elapsed since the student made the first study session until the day of the last recorded session. On the y-axis they show 2 values, on the left side the number of student sessions and on the right side the amount of time spent in hours. Between these axes the number of sessions and the time inverted of the student are drawn as a time series.';
$string['td_sessions_evolution_help_description_p3'] = 'This display allows you to zoom in on a selected region. This approach helps to clearly show this evolution in different date ranges.';
$string["td_sessions_evolution_chart_title"] = "Sessions and Time Inverted";
$string["td_sessions_evolution_chart_xaxis1"] = "Number of Sessions";
$string["td_sessions_evolution_chart_xaxis2"] = "Number of Hours";
$string["td_sessions_evolution_chart_legend1"] = "Sessions";
$string["td_sessions_evolution_chart_legend2"] = "Inverted Time";

$string['td_user_grades_help_title'] = 'Ratings';
$string['td_user_grades_help_description_p1'] = "This graph shows a comparison of the student's grades with the grade point averages (percentage mean) of their peers in the different assessable activities of the course. The data presented in this graph varies depending on the student selected in the <i> Group Students </i> table.";
$string['td_user_grades_help_description_p2'] = "The x-axis of the graph shows the different assessable activities. On the y-axis is the student's grade and the average grade of their peers. Both the student's grade and the course average are displayed as a percentage to maintain the symmetry of the graph.";
$string['td_user_grades_help_description_p3'] = 'With a click on the bar corresponding to any activity, it is possible to go to that analyzed.';
$string["td_user_grades_chart_title"] = "Ratings";
$string["td_user_grades_chart_yaxis"] = "Rating in Percentage";
$string["td_user_grades_chart_xaxis"] = "Evaluable Activities";
$string["td_user_grades_chart_legend"] = "Course (Average)";
$string["td_user_grades_chart_tooltip_no_graded"] = "No Ratings";
$string["td_user_grades_chart_view_activity"] = "Click to see the activity";


/* Teacher Logs */
$string['tl_section_help_title'] = 'Descargar Registros';
$string['tl_section_help_description'] = 'En esta sección se pueden descargar archivo en formato de valores separados por comas(.cv) de las actividades que han realizado los participantes del curso en el Curso (Moodle) o en el Plugin Flip My Learning (FML)';


/* Student General */
$string['sg_section_help_title'] = 'General Indicators';
$string['sg_section_help_description'] = 'This section contains indicators related to your information, progress, general indicators, course resources, sessions throughout the course and grades obtained. The displays in this section show the gauges throughout the course (up to the current date).';

$string['sg_modules_access_help_title'] = 'Course Resources';
$string['sg_modules_access_help_description_p1'] = 'This graph shows the amount of resources that you have accessed and completed. To determine the amount of resources you have completed, use the Moodle configuration called Activity Completion. In case the teacher has not configured the completeness for the course activities, the number of activities accessed and completed will always be the same, since without such configuration, a resource is considered finished when you access it.';
$string['sg_modules_access_help_description_p2'] = 'On the x-axis are the amount of course resources. On the y axis are the labels of Accessed, Complete and Total resources in reference to your interactions with the resources of the course.';
$string['sg_modules_access_help_description_p3'] = 'Clicking on any bar it is possible to see the resources and activities available in the course (in a pop-up window) along with the number of interactions you have made with each resource and a label of not accessed, accessed or completed.';

$string['sg_weeks_session_help_title'] = 'Sessions per Week';
$string['sg_weeks_session_help_description_p1'] = 'This graph shows the number of study sessions you have taken each week from the course start date. Access to the course is considered the beginning of a study session. A session is considered finished when the time elapsed between two interactions exceeds 30 minutes.';
$string['sg_weeks_session_help_description_p2'] = 'On the x-axis of the graph are the weeks of each month. The y-axis of the graph shows the different months of the year starting from the month of creation of the course. To maintain the symmetry of the graph, a total of five weeks has been placed for each month, however, not every month has that many weeks. These months will only add sessions until week four.';

$string['sg_sessions_evolution_help_title'] = 'Sessions and Time Inverted';
$string['sg_sessions_evolution_help_description_p1'] = 'This graph allows you to know how your study sessions have evolved since your first session was registered in the course.';
$string['sg_sessions_evolution_help_description_p2'] = 'The x-axis of the graph shows a time line with the days that have elapsed since you did your first study session until the day of your last recorded session. On the y-axis they show 2 values, on the left side your number of sessions and on the right side your amount of time spent in hours. Between these axes your number of sessions and your time spent as a student are plotted as a time series.';
$string['sg_sessions_evolution_help_description_p3'] = 'This display allows you to zoom in on a selected region.';

$string['sg_user_grades_help_title'] = 'Ratings';
$string['sg_user_grades_help_description_p1'] = 'This graph shows a comparison of your grades with the grade point averages (average in percent) of your classmates in the different assessable activities of the course.';
$string['sg_user_grades_help_description_p2'] = "The x-axis of the graph shows the different assessable activities. On the y-axis you will find your grades and the average of your classmates' grades. Both your grade and the course average are displayed in percentage to maintain the symmetry of the graph.";
$string['sg_user_grades_help_description_p3'] = 'Clicking on the bar corresponding to any activity, it is possible to go to that analyzed.';

/* Student Sessions*/
$string['ss_section_help_title'] = 'Study Sessions';
$string['ss_section_help_description'] = 'This section contains visualizations with indicators related to your activity in the course measured in terms of study sessions, time inverted and progress in each of the weeks configured by the teacher. The displays in this section vary depending on the study week selected.';

$string['ss_inverted_time_help_title'] = 'Your Inverted Time';
$string['ss_inverted_time_help_description_p1'] = 'This graph shows your time spent in the week compared to the time planned by the teacher.';
$string['ss_inverted_time_help_description_p2'] = 'On the x-axis of the graph is the number of hours that the teacher has planned for a specific week. On the y-axis are the labels for time spent and time that should be spent.';
$string['ss_inverted_time_chart_title'] = 'Your Inverted Time';
$string['ss_inverted_time_xaxis_title'] = 'Number of Hours';
$string['ss_inverted_time_inverted_label'] = 'Inverted Time';
$string['ss_inverted_time_expected_label'] = 'Time that should be Inverted';

$string['ss_hours_session_help_title'] = 'Sessions by Day and Hour';
$string['ss_hours_session_help_description_p1'] = 'This graph shows your study sessions by day and time of the selected week. Access to the course is considered the beginning of a study session. A session is considered finished when the time elapsed between two interactions exceeds 30 minutes.';
$string['ss_hours_session_help_description_p2'] = 'On the x-axis of the graph are the days of the week. On the y axis are the hours of the day starting at 12am and ending at 11pm or 11pm.';

$string['ss_resources_access_help_title'] = 'Interaction by Resource Types';
$string['ss_resources_access_help_description_p1'] = 'This graph shows how many resources you have pending and which ones you have already completed in the selected week. Resources are grouped by type in this chart. In addition, a bar is displayed at the top that represents the percentage of resources accessed out of the total resources assigned to the selected week.';
$string['ss_resources_access_help_description_p2'] = 'On the x-axis of the graph are the different types of resources. On the y axis are the amount of resources accessed for the week.';
$string['ss_resources_access_help_description_p3'] = 'Clicking on any bar it is possible to see the resources and activities available in the course (in a pop-up window) along with the number of interactions you have made with each resource and a label of not accessed, accessed or completed.';
$string['ss_resource_access_chart_title'] = 'Interaction by Resource Types';
$string['ss_resource_access_yaxis_title'] = 'Quantity of Resources';
$string['ss_resource_access_xaxis_title'] = 'Types of Resources';
$string['ss_resource_access_legend1'] = 'Completed';
$string['ss_resource_access_legend2'] = 'No Complete';

$string['ss_week_progress_title'] = 'Week Progress';



