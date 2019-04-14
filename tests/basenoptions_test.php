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

defined('MOODLE_INTERNAL') || die();

/** Unit tests for basen_options.
 *
 * @copyright  2017 The University of Birmingham.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @author     Stephen Parry <stephen@edumake.org>
 */

require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../options.class.php');
require_once(__DIR__ . '/../basenoptions.class.php');

/**
 * @group qtype_stack
 */
class stack_basen_options_test extends qtype_stack_testcase {

    public function test() {
        $bno1 = new stack_basen_options(2, self::BASENMODE_SUFFIX);
        $bno2 = new stack_basen_options(16, self::BASENMODE_SUFFIX | self::BASENMODE_CHOICE);
        $bno3 = new stack_basen_options(16, self::BASENMODE_GREEDY);
        $bno4 = new stack_basen_options(16, self::BASENMODE_C | self::BASENMODE_CHOICE);
        $bno5 = new stack_basen_options(16, self::BASENMODE_ZERO_PREFIX);
        $this->assertTrue($bno1->inject_temp_escapes("xxxx01010101_2xxxxx") == 'xxxx![!"01010101_2"!]!xxxxx');
        $this->assertTrue($bno2->inject_temp_escapes("xxxxABCDEF_16xxxxx") == 'xxxx![!"ABCDEF_16"!]!xxxxx');
        $this->assertTrue($bno3->inject_temp_escapes("xxxxABCDEFxxxxx") == 'xxxx![!"ABCDEF"!]!xxxxx');
        $this->assertTrue($bno4->inject_temp_escapes("xxxx0xABCDEFxxxxx") == 'xxxx![!"0xABCDEF"!]!xxxxx');
        $this->assertTrue($bno5->inject_temp_escapes("xxxx0ABCDEFxxxxx") == 'xxxx![!"0ABCDEF"!]!xxxxx');
    }
}

