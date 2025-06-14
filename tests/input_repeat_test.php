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
use stack_ast_container;
use stack_cas_session2;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/fixtures/test_base.php');

require_once(__DIR__ . '/../stack/input/factory.class.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');

/**
 * Unit tests for stack_repeat_input.
 *
 * @package    qtype_stack
 * @copyright  2025 The University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @group qtype_stack
 * @covers \stack_repeat_input
 */
final class input_repeat_test extends qtype_stack_testcase {

    public function test_render_blank(): void {

        $el = stack_input_factory::make('repeat', 'ans1', '""');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" '
                .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="maxima-string" value="" ' .
                'data-stack-input-type="string" />',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_validate_broken_json(): void {

        $options = new stack_options();
        $simpleinputs = [];
        $el0 = stack_input_factory::make('algebraic', 'ans1', 'x');
        $simpleinputs['ans1'] = $el0;

        $el = stack_input_factory::make('repeat', 'sans1', '"{}"');
        $el->set_parameter('sameType', true);
        $el->add_simple_inputs($simpleinputs);

        // Broken JSON of some kind.
        $state = $el->validate_student_response(['sans1' => '{"ans1":"x^2"'],
            $options, '"{}"',
            new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('invalid_json', $state->note);
        $this->assertEquals('[]',
            $state->contentsmodified);
    }

    public function test_validate_basic_input(): void {

        $options = new stack_options();
        $simpleinputs = [];
        $el1 = stack_input_factory::make('algebraic', 'ans1', 'x');
        $simpleinputs['ans1'] = $el1;
        $el2 = stack_input_factory::make('numerical', 'ans2', 'x');
        $simpleinputs['ans2'] = $el2;

        $el = stack_input_factory::make('repeat', 'sans1', '"{}"');
        $el->set_parameter('sameType', true);
        $el->add_simple_inputs($simpleinputs);

        $rawinput = '{"data":{"ans1":["x^2","x^3"],"ans2":["5"]}}';
        $state = $el->validate_student_response(['sans1' => $rawinput], $options, '"{}"',
            new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('(repeatedans1:[x^2,x^3],repeatedans2:[5])',
            $state->contentsmodified);
        $expected = "<pre>{\n    \"data\": {\n        \"ans1\": [\n" .
                    "            \"x^2\",\n" .
                    "            \"x^3\"\n" .
                    "        ],\n" .
                    "        \"ans2\": [\n" .
                    "            \"5\"\n" .
                    "        ]\n" .
                    "    }\n" .
                    "}</pre>" .
                    "\\[ \\left(\\left[ x^2 , x^3 \\right] , \\left[ 5 \\right] \\right) \]";

        $this->assertEquals($expected, $state->contentsdisplayed);
    }

    public function test_validate_invalid_input(): void {

        $options = new stack_options();
        $simpleinputs = [];
        $el0 = stack_input_factory::make('algebraic', 'ans1', 'x');
        $simpleinputs['ans1'] = $el0;

        $el = stack_input_factory::make('repeat', 'sans1', '"{}"');
        $el->set_parameter('sameType', true);
        $el->add_simple_inputs($simpleinputs);

        $rawinput = '{"data":{"ans1":["0.33*x^2"]}}';
        $state = $el->validate_student_response(['sans1' => $rawinput], $options, '"{}"',
            new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Illegal_floats', $state->note);
        $this->assertEquals('[]',
            $state->contentsmodified);
    }

    public function test_validate_full_input(): void {

        $options = new stack_options();
        $simpleinputs = [];
        $el1 = stack_input_factory::make('numerical', 'ans1', 'x');
        $simpleinputs['ans1'] = $el1;
        $el2 = stack_input_factory::make('algebraic', 'ans2', 'x');
        $el2->set_parameter('options', 'allowempty');
        $simpleinputs['ans2'] = $el2;
        $el5 = stack_input_factory::make('algebraic', 'ans5', 'x');
        $simpleinputs['ans5'] = $el5;

        $el = stack_input_factory::make('repeat', 'sans1', '"{}"');
        $el->set_parameter('sameType', true);
        $el->add_simple_inputs($simpleinputs);

        $rawinput = '{"num_copies": 3,' .
                        '"data": [' .
                        '{"repeat_id": "1",' .
                        '"inputs": {"ans1": ["1", "2", "3"],"ans2": ["a", "b", ""]}},' .
                        '{"repeat_id": "3",' .
                        '"inputs": {"ans5": ["x^2", "x^4", "x^6"]}' .
                        '}]}';

        $state = $el->validate_student_response(['sans1' => $rawinput], $options, '"{}"',
            new stack_cas_security());
        //var_dump($state);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('(repeatedans1:[1,2,3],repeatedans2:[a,b,EMPTYANSWER],repeatedans5:[x^2,x^4,x^6])',
            $state->contentsmodified);
    }

    public function test_repeat_encode(): void {

        $options = new stack_options();
        $options->set_option('simplify', false);

        $cs = [];
        $cs[] = 'repeat_encode([["ans1",[x^2,x^3]],["ans2",[0.5*x^2]]])';
        $cs[] = 'repeat_encode([[ans1,[x^2,x^3]],["ans2",[0.5*x^2]]])';
        $cs[] = 'repeat_encode([["ans1",x^3],["ans2",[0.5*x^2]]])';
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), []);
        }
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('"{\"data\":{\"ans1\":[\"x^2\",\"x^3\"],\"ans2\":[\"0.5*x^2\"]}}"',
            $s1[0]->get_value());
        $this->assertEquals('repeat_encode: the first element of your input list must be a string, ' .
            'giving the name of one input.',
            $s1[1]->get_errors());
        $this->assertEquals('repeat_encode: the second element of your input list must be a list of expressions, ' .
            'giving the values of the repeated inputs.',
            $s1[2]->get_errors());
    }
}
