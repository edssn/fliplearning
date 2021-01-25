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
 * User sessions visualizations
 *
 * @package     local_fliplearning
 * @autor       Edisson Sigua, Bryan Aguilar
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fliplearning;

defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__) . '/../../../config.php');

use stdClass;

/**
 * Class report
 *
 * @author      Edisson Sigua
 * @author      Bryan Aguilar
 * @copyright   2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email {

    protected $course;
    protected $user;

    function __construct($course, $user){
        $this->user = $user;
        $this->course = $course;
    }

    public function sendmail($subject, $recipients, $text, $moduleid, $modulename) {
        global $DB, $CFG;
        $recipients = explode(',', $recipients);

        $sender = new stdClass();
        $sender->id = $this->user->id;
        $sender->firstname = $this->user->firstname;
        $sender->lastname = $this->user->lastname;
        $sender->email = $this->user->email;

        $footer_prefix = get_string("fml_email_footer_prefix","local_fliplearning");
        $footer_suffix = get_string("fml_email_footer_suffix","local_fliplearning");
        $footer_text = get_string("fml_email_footer_text","local_fliplearning");
        $url="";
        if (isset($modulename) && strlen(trim($modulename))>0) {
            $identifier = "fml_{$modulename}_url";
            if (get_string_manager()->string_exists($identifier, "local_fliplearning")) {
                $module_url = get_string($identifier, "local_fliplearning");
                $url = "{$CFG->wwwroot}{$module_url}{$moduleid}";
            }
        }
        $footer = "\n\n---------------------------------------------------------------------------------\n";
        if (strlen($url)){
            $footer = $footer."{$footer_prefix} {$CFG->wwwroot}{$module_url}{$moduleid} {$footer_suffix}.\n";
        }
        $footer = $footer."{$footer_text}";
        $text = $text.$footer;

        foreach ($recipients as $id) {
            if(!empty($id)) {
                $recipient = new stdClass();
                $recipient->id = $id;
                $recipient->email = $DB->get_field('user', 'email', array('id' => $id));
                email_to_user($recipient, $sender, $subject, $text, $text, '', '', true);

            }
        }
    }

}