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
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_fliplearning
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fliplearning\task;

class generate_data extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('fml_generate_dropout_data_task', 'local_fliplearning');
    }

    public function execute() {
        global $DB;
        $sql = "SELECT id, fullname FROM {course} WHERE id > 1 AND VISIBLE = 1 ORDER BY ID DESC";
        $rows = $DB->get_records_sql($sql);
        foreach ($rows as $row) {
            $dropout = new \local_fliplearning\dropout($row->id);
            $dropout->generate_data();
        }
        return true;
    }
}