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
 * Plugin upgrade steps are defined here.
 *
 * @package     local_fliplearning
 * @category    upgrade
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/upgradelib.php');

/**
 * Execute local_fliplearning upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_fliplearning_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // For further information please read the Upgrade API documentation:
    // https://docs.moodle.org/dev/Upgrade_API
    //
    // You will also have to create the db/install.xml file by using the XMLDB Editor.
    // Documentation for the XMLDB Editor can be found at:
    // https://docs.moodle.org/dev/XMLDB_editor

    if ($oldversion < 2021062300) {

        // Define field email to be dropped from fliplearning_logs.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('email');

        // Conditionally launch drop field email.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }



        // Define field coursename to be added to fliplearning_logs.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('coursename', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'courseid');

        // Conditionally launch add field coursename.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }



        // Define field pluginsection to be added to fliplearning_logs.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('pluginsection', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'coursename');

        // Conditionally launch add field pluginsection.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }



        // Define field target to be added to fliplearning_logs.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('target', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'action');

        // Conditionally launch add field target.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }



        // Define field url to be added to fliplearning_logs.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('url', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'target');

        // Conditionally launch add field url.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }



        // Define field interactiontype to be added to fliplearning_logs.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('interactiontype', XMLDB_TYPE_INTEGER, '2', null, null, null, null, 'url');

        // Conditionally launch add field interactiontype.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }




        // Changing type of field username on table fliplearning_logs to char.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('username', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'userid');

        // Launch change of type for field username.
        $dbman->change_field_type($table, $field);



        // Changing type of field name on table fliplearning_logs to char.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'username');

        // Launch change of type for field name.
        $dbman->change_field_type($table, $field);



        // Changing type of field lastname on table fliplearning_logs to char.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('lastname', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'name');

        // Launch change of type for field lastname.
        $dbman->change_field_type($table, $field);



        // Changing type of field current_roles on table fliplearning_logs to char.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('current_roles', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'lastname');

        // Launch change of type for field current_roles.
        $dbman->change_field_type($table, $field);



        // Changing type of field component on table fliplearning_logs to char.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'pluginsection');

        // Launch change of type for field component.
        $dbman->change_field_type($table, $field);



        // Changing type of field action on table fliplearning_logs to char.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('action', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'component');

        // Launch change of type for field action.
        $dbman->change_field_type($table, $field);



        // Changing nullability of field courseid on table fliplearning_logs to null.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'current_roles');

        // Launch change of nullability for field courseid.
        $dbman->change_field_notnull($table, $field);



        // Rename field name on table fliplearning_logs to firstname.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'username');

        // Launch rename field firstname.
        $dbman->rename_field($table, $field, 'firstname');



        // Rename field current_roles on table fliplearning_logs to currentroles.
        $table = new xmldb_table('fliplearning_logs');
        $field = new xmldb_field('current_roles', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'lastname');

        // Launch rename field current_roles.
        $dbman->rename_field($table, $field, 'currentroles');



        // Fliplearning savepoint reached.
        upgrade_plugin_savepoint(true, 2021062300, 'local', 'fliplearning');
    }

    if ($oldversion < 2021071200) {

        // Rename field hours_dedications on table fliplearning_weeks to minutes_dedication.
        $table = new xmldb_table('fliplearning_weeks');
        $field = new xmldb_field('hours_dedications', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '0', 'weekcode');

        // Launch rename field hours_dedications.
        $dbman->rename_field($table, $field, 'minutes_dedication');

        // Fliplearning savepoint reached.
        upgrade_plugin_savepoint(true, 2021071200, 'local', 'fliplearning');
    }

    return true;
}
