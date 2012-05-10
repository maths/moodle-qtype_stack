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
 * Base class for Stack unit tests.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/stack/stack/cas/installhelper.class.php');

/**
 * Base class for Stack unit tests.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_testcase extends advanced_testcase {
    public function setUp() {
        parent::setUp();
        set_config('platform',        'win',    'qtype_stack');
        set_config('maximaversion',   '5.22.1', 'qtype_stack');
        set_config('castimeout',      '1',      'qtype_stack');
        set_config('casresultscache', 'db',     'qtype_stack');
        set_config('maximacommand',   '',       'qtype_stack');
        set_config('plotcommand',     '',       'qtype_stack');
        set_config('casdebugging',    '0',      'qtype_stack');

        if (stack_cas_configuration::maxima_bat_is_missing()) {
            stack_cas_configuration::create_maximalocal();
        }

        $this->resetAfterTest();
    }
}
