<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk//
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

defined('MOODLE_INTERNAL') || die();

// Add in all the tests from answertestsfixtures.class into the unit testing framework.
// These are exposed to users as documentation and Travis integration should also run all the tests.
//
// @copyright  2016 The University of Edinburgh.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/answertest/controller.class.php');
require_once(__DIR__ . '/../stack/options.class.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/fixtures/answertestfixtures.class.php');

/**
 * @group qtype_stack
 */
class stack_answertest_fixtures_cas_testcase extends qtype_stack_testcase {

    /**
     * @dataProvider answertest_fixtures
     */
    public function test_answertest($name, $test) {
        list($passed, $error, $rawmark, $feedback, $ansnote, $anomalynote) = stack_answertest_test_data::run_test($test);

        $this->assertEquals($test->ansnote, $ansnote);
        $this->assertTrue($passed, $anomalynote);
    }

    public function answertest_fixtures() {

        $tests = stack_answertest_test_data::get_all();
        $testdata = array();
        foreach ($tests as $test) {
            $testname = 'AT' . $test->name .
                    '( ' . $test->studentanswer . ', ' . $test->teacheranswer. ')';
            if ($test->options != '') {
                $testname .= ' Options: ' . $test->options;
            }
            $testdata[] = array($testname, $test);
        }
        return $testdata;
    }
}
