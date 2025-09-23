<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

namespace qtype_stack\check;
defined('MOODLE_INTERNAL') || die();

use core\check\check;
use core\check\result;
require_once($CFG->dirroot . '/question/type/stack/stack/cas/connectorhelper.class.php');

/**
 * Check STACK and maxima versions match.
 *
 * @package    qtype_stack
 * @copyright  2025 University of Edinbiurgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class stack_version_check extends check {

    /**
     * Get check link.
     * @return \action_link
     */
    public function get_action_link(): ?\action_link {
        $url = new \moodle_url('/admin/settings.php', ['section' => 'qtypesettingstack']);
        return new \action_link($url, stack_string('pluginname'));
    }

    /**
     * Run check.
     * @return result
     */
    public function get_result(): result {
        try {
            list($message, $details, $result) = \stack_connection_helper::stackmaxima_version_healthcheck();
            $message = stack_string($message, $details);
        } catch (\Exception $e) {
            $message = stack_string('healthchecksstackmaximaversion') . ': ' . $e->getMessage();
            $result = false;
        }
        $details = '';
        $status = $result ? result::OK : result::ERROR;
        return new result($status, $message, $details);
    }
}
