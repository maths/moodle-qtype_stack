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

namespace qtype_stack;

use qtype_stack_testcase;
use stack_cas_security;
use stack_input;
use stack_input_factory;
use stack_input_state;
use stack_options;


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/fixtures/test_base.php');

require_once(__DIR__ . '/../stack/input/factory.class.php');

/**
 * Unit tests for stack_freetext_input.
 *
 * @package    qtype_stack
 * @copyright  2026 The University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @group qtype_stack
 * @covers \stack_string_input
 */
final class input_freetext_test extends qtype_stack_testcase {
    public function test_render_blank(): void {

        $el = stack_input_factory::make('freetext', 'ans1', 'x^2');
        $this->assertEquals(
            '<textarea class="freetextinput" name="stack1__ans1" id="stack1__ans1" autocapitalize="none" ' .
            'spellcheck="false" rows="5" cols="80" data-stack-input-type="freetext" maxlength="5096">' .
            '</textarea>',
            $el->render(
                new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                'stack1__ans1',
                false,
                null
            )
        );
    }

    public function test_render_hello_world(): void {

        $el = stack_input_factory::make('freetext', 'ans1', '"Hello world"');
        $this->assertEquals(
            '<textarea class="freetextinput" name="stack1__ans1" id="stack1__ans1" autocapitalize="none" ' .
            'spellcheck="false" rows="5" cols="80" data-stack-input-type="freetext" maxlength="5096">' .
            '000</textarea>',
            $el->render(
                new stack_input_state(stack_input::VALID, ['000'], '', '', '', '', ''),
                'stack1__ans1',
                false,
                null
            )
        );
        $this->assertEquals(
            'The answer <pre>Hello world</pre> would be correct.',
            $el->get_teacher_answer_display('"Hello world"', '\\text{Hello world}')
        );
    }

    public function test_render_monospace(): void {
        $el = stack_input_factory::make('freetext', 'ans1', '"Hello world"');
        $el->set_parameter('options', 'monospace:true');
        $this->assertEquals(
            '<textarea class="freetextinput input-monospace" name="stack1__ans1" id="stack1__ans1" autocapitalize="none" ' .
            'spellcheck="false" rows="5" cols="80" data-stack-input-type="freetext" maxlength="5096">' .
            '000</textarea>',
            $el->render(
                new stack_input_state(stack_input::VALID, ['000'], '', '', '', '', ''),
                'stack1__ans1',
                false,
                null
            )
        );
    }

    public function test_validate_string_input(): void {

        $options = new stack_options();
        $el = stack_input_factory::make('freetext', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(
            ['sans1' => 'Hello world'],
            $options,
            '"A random string"',
            new stack_cas_security()
        );
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"Hello world"', $state->contentsmodified);
        $this->assertEquals('<p>Hello world</p>', $state->contentsdisplayed);
        $this->assertEquals(
            'The answer <pre>Hello world</pre> would be correct.',
            $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed)
        );
    }
}
