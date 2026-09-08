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
 * PHPUnit tests for the [[iframe]] castext block.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

use api\util\StackIframeHolder;
use qtype_stack_testcase;
use stack_cas_castext2_iframe;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/castext2/blocks/iframe.block.php');
require_once(__DIR__ . '/../stack/cas/castext2/processor.class.php');

/**
 * Tests for {@link stack_cas_castext2_iframe}.
 *
 * @group qtype_stack
 * @covers \stack_cas_castext2_iframe
 */
final class iframe_block_test extends qtype_stack_testcase {
    public function setUp(): void {
        parent::setUp();
        StackIframeHolder::$iframes = [];
        StackIframeHolder::$islibrary = true;
    }

    public function tearDown(): void {
        StackIframeHolder::$iframes = [];
        StackIframeHolder::$islibrary = false;
        parent::tearDown();
    }

    public function test_scrolling_string_false_is_treated_as_false(): void {
        $block = new stack_cas_castext2_iframe([], []);
        $processor = new \castext2_default_processor();
        $holder = new \castext2_placeholder_holder();

        $block->postprocess(
            ['iframe', json_encode(['scrolling' => 'false']), '<p>Test</p>'],
            $processor,
            $holder
        );

        $this->assertCount(1, StackIframeHolder::$iframes);
        $this->assertFalse(StackIframeHolder::$iframes[0][4]);
    }
}
