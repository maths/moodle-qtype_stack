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
 * Unit tests for stack_json_input.
 *
 * @package    qtype_stack
 * @copyright  2018 The University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @group qtype_stack
 * @covers \stack_string_input
 */
final class input_json_test extends qtype_stack_testcase {

    public function test_render_blank(): void {

        $el = stack_input_factory::make('json', 'ans1', '""');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" '
                .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="maxima-string" value="" ' .
                'data-stack-input-type="string" />',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_validate_string_input(): void {

        $options = new stack_options();
        $el = stack_input_factory::make('json', 'sans1', '"{}"');
        $el->set_parameter('sameType', true);
        // TODO: when we drop support for PHP7.4 we should reinstate examples below with floats.
        // These cause rounding errors.
        $state = $el->validate_student_response(['sans1' => '{"x":37, "y":30, "type":"Sphere", "err":null}'],
            $options, '"{}"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"{\\"x\\":37, \\"y\\":30, \\"type\\":\\"Sphere\\", \\"err\\":null}"',
            $state->contentsmodified);
        $this->assertEquals("<pre>{\n    \"x\": 37,\n    \"y\": 30,\n    " .
            "\"type\": \"Sphere\",\n    \"err\": null\n}</pre>",
            $state->contentsdisplayed);
    }
}
