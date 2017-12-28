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

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/answertest/controller.class.php');
require_once(__DIR__ . '/../stack/options.class.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/fixtures/subscriptsfixtures.class.php');

// Add in all the tests from subscriptsfixtures.php into the unit testing framework.
// These are exposed to users as documentation and the Travis integration should also run all the tests.
//
// @copyright 2016 The University of Edinburgh.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_subscript_testcase extends qtype_stack_testcase {

    /**
     * @dataProvider stack_subscripts_test_data::get_raw_test_data
     */
    public function test_subscripts() {
        $this->skip_if_old_maxima('5.40.0');

        $test1 = stack_subscripts_test_data::test_from_raw(func_get_args());
        $resultfalse = stack_subscripts_test_data::run_test($test1, false);

        $test2 = stack_subscripts_test_data::test_from_raw(func_get_args());
        $resulttrue = stack_subscripts_test_data::run_test($test2, true);

        if ('invalid' == $resultfalse->maxima) {
            $this->assertFalse($resultfalse->valid);
        } else {
            $this->assertEquals($resultfalse->maxima, $resultfalse->value);
            $this->assertEquals($resultfalse->tex, $resultfalse->display);
        }

        if ('invalid' == $resulttrue->maxima) {
            $this->assertFalse($resulttrue->valid);
        } else {
            $target = $resulttrue->maxima;
            if ($resulttrue->maximasimp != '!') {
                $target = $resulttrue->maximasimp;
            }
            $this->assertEquals($target, $resulttrue->value);
            $target = $resulttrue->tex;
            if ($resulttrue->maximasimp != '!') {
                $target = $resulttrue->texsimp;
            }
            $this->assertEquals($target, $resulttrue->display);
        }
    }

    /**
     * @dataProvider stack_subscripts_test_data::get_raw_test_data_legacy
     */
    public function test_subscripts_legacy_maxima() {
        $this->skip_if_new_maxima('5.40.0');

        $test1 = stack_subscripts_test_data::test_from_raw(func_get_args());
        $resultfalse = stack_subscripts_test_data::run_test($test1, false);

        $test2 = stack_subscripts_test_data::test_from_raw(func_get_args());
        $resulttrue = stack_subscripts_test_data::run_test($test2, true);

        if ('invalid' == $resultfalse->maxima) {
            $this->assertFalse($resultfalse->valid);
        } else {
            $this->assertEquals($resultfalse->maxima, $resultfalse->value);
            $this->assertEquals($resultfalse->tex, $resultfalse->display);
        }

        if ('invalid' == $resulttrue->maxima) {
            $this->assertFalse($resulttrue->valid);
        } else {
            $target = $resulttrue->maxima;
            if ($resulttrue->maximasimp != '!') {
                $target = $resulttrue->maximasimp;
            }
            $this->assertEquals($target, $resulttrue->value);
            $target = $resulttrue->tex;
            if ($resulttrue->maximasimp != '!') {
                $target = $resulttrue->texsimp;
            }
            $this->assertEquals($target, $resulttrue->display);
        }
    }
}
