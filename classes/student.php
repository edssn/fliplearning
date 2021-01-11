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
 * local_fliplearning
 *
 * @package     local_fliplearning
 * @autor       Edisson Sigua, Bryan Aguilar
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fliplearning;

class student extends report {

    /**
     * Almacena el perfil de visualización de la clase en la variable $profile de clase
     */
    public function set_profile(){
        $this->profile = "student";
    }

    /**
     * Almacena los ids de los estudiantes en la variable $users de la clase
     */
    public function set_users(){
        global $DB;
        $this->users = array($this->user->id);
        return $this->users;
    }

}