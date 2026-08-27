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

/**
 * PHPUnit tests for the [[filter]] castext block validation.
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

use castext2_evaluatable;
use qtype_stack_testcase;
use stack_cas_session2;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/castext2/castext2_evaluatable.class.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/castext2/blocks/filter.block.php');
require_once(__DIR__ . '/../stack/cas/castext2/blocks/ascii.block.php');

/**
 * Tests for {@link stack_cas_castext2_filter::validate()}.
 * @group qtype_stack
 * @covers \stack_cas_castext2_filter::validate
 */
final class filter_block_test extends qtype_stack_testcase {
    public function test_filter_accepts_known_type_and_transforms(): void {
        $raw = '[[ascii input="ans1"]]'
            . '[[filter type="markdown" transforms="asciimath,aligneq"]][[/filter]]'
            . '[[/ascii]]';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');

        $this->assertTrue($at1->get_valid());
        $this->assertEquals('', $at1->get_errors());
    }

    public function test_filter_requires_type(): void {
        $raw = '[[ascii input="ans1"]]'
            . '[[filter transforms="asciimath"]][[/filter]]'
            . '[[/ascii]]';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');

        $this->assertFalse($at1->get_valid());
        $this->assertEquals(stack_string('stackBlock_filter_type_required'), $at1->get_errors());
    }

    public function test_filter_rejects_unknown_type(): void {
        $raw = '[[ascii input="ans1"]]'
            . '[[filter type="unknown-filter"]][[/filter]]'
            . '[[/ascii]]';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');

        $this->assertFalse($at1->get_valid());
        $this->assertStringContainsString(
            stack_string('stackBlock_filter_unknown', [
                'type' => 'unknown-filter',
                'filters' => implode(', ', \stack_cas_castext2_filter::$filtertypes),
            ]),
            $at1->get_errors()
        );
    }

    public function test_filter_rejects_unknown_transforms(): void {
        $raw = '[[ascii input="ans1"]]'
            . '[[filter type="markdown" transforms="aligneq,wrong,minwrap,bad"]][[/filter]]'
            . '[[/ascii]]';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');

        $this->assertFalse($at1->get_valid());
        $this->assertStringContainsString(
            stack_string('stackBlock_filter_trans_unknown', [
                'transforms' => 'wrong, bad',
                'valid' => implode(', ', \stack_cas_castext2_filter::$transtypes),
            ]),
            $at1->get_errors()
        );
    }
}
