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

        if (!defined('QTYPE_STACK_TEST_CONFIG_PLATFORM')) {
            $this->markTestSkipped(
                    'To run the STACK unit tests, you must set up the Maxima configuration in phpunit.xml.');
        }

        set_config('platform',        QTYPE_STACK_TEST_CONFIG_PLATFORM,        'qtype_stack');
        set_config('maximaversion',   QTYPE_STACK_TEST_CONFIG_MAXIMAVERSION,   'qtype_stack');
        set_config('castimeout',      QTYPE_STACK_TEST_CONFIG_CASTIMEOUT,      'qtype_stack');
        set_config('casresultscache', QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE, 'qtype_stack');
        set_config('maximacommand',   QTYPE_STACK_TEST_CONFIG_MAXIMACOMMAND,   'qtype_stack');
        set_config('plotcommand',     QTYPE_STACK_TEST_CONFIG_PLOTCOMMAND,     'qtype_stack');
        set_config('casdebugging',    QTYPE_STACK_TEST_CONFIG_CASDEBUGGING,    'qtype_stack');

        if (QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE == 'otherdb') {
            set_config('cascachedbtype',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBTYPE,    'qtype_stack');
            set_config('cascachedblibrary', QTYPE_STACK_TEST_CONFIG_CASCACHEDBLIBRARY, 'qtype_stack');
            set_config('cascachedbhost',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBHOST,    'qtype_stack');
            set_config('cascachedbname',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBNAME,    'qtype_stack');
            set_config('cascachedbuser',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBUSER,    'qtype_stack');
            set_config('cascachedbpass',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBPASS,    'qtype_stack');
            set_config('cascachedbprefix',  QTYPE_STACK_TEST_CONFIG_CASCACHEDBPREFIX,  'qtype_stack');
        }

        if (stack_cas_configuration::maxima_bat_is_missing()) {
            stack_cas_configuration::create_maximalocal();
        }

        $this->resetAfterTest();
    }
}
