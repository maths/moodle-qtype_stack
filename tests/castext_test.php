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

use castext2_evaluatable;
use castext2_parser_utils;
use qtype_stack_testcase;
use stack_ast_container;
use stack_cas_keyval;
use stack_cas_security;
use stack_cas_session2;
use stack_maths;
use stack_options;
use function stack_ast_container_silent\is_int;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/castext2/castext2_evaluatable.class.php');
require_once(__DIR__ . '/../stack/cas/castext2/utils.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');


// Unit tests for {@link stack_cas_text}.
// Castext2 has no such class the repalcement is castext2_evaluatable
// which instead of being given a cassession is expected to be placed
// into a cassession, allowing evaluation of multiple evaluatables in
// the same session.

/**
 * @group qtype_stack
 * @group qtype_stack_castext_module
 */
class castext_test extends qtype_stack_testcase {

    public function basic_castext_instantiation($strin, $sa, $val, $disp) {

        if (is_array($sa)) {
            $s1 = array();
            foreach ($sa as $s) {
                $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            }
            $cs1 = new stack_cas_session2($s1, null, 0);
        } else {
            // Now we need a session no matter what.
            $cs1 = new stack_cas_session2([]);
        }

        $at1 = castext2_evaluatable::make_from_source($strin, 'stack_cas_text_test::basic_castext_instantiation');
        if ($at1->get_valid()) {
            // Currently (4.4), you need to be sure that it is valid before you instantiate it.
            // The error that happens happens in a slightly different way now.
            $cs1->add_statement($at1);
            $this->assertEquals($val, $cs1->get_valid());
            if ($cs1->get_valid()) {
                $cs1->instantiate();
                $this->assertEquals($disp, $at1->get_rendered());
            }
        } else {
            $this->assertEquals($val, $at1->get_valid());
        }
    }

    /**
     * @covers \qtype_stack\castext2_evaluatable::make_from_source
     */
    public function test_basic_castext_instantiation() {

        $a1 = array('a:x^2', 'b:(x+1)^2');
        $a2 = array('a:x^2)', 'b:(x+1)^2');

        $cases = array(
                array('', null, true, ''),
                array('Hello world', null, true, 'Hello world'),
                array('$x^2$', null, true, '$x^2$'),
                array('\(x^2\)', null, true, '\(x^2\)'),
                array('{@x*x^2@}', null, true, '\({x^3}\)'),
                array('{@1+2@}', null, true, '\({3}\)'),
                array('\[{@x^2@}\]', null, true, '\[{x^2}\]'),
                array('\[{@a@}\]', $a1, true, '\[{x^2}\]'),
                array('{@a@}', $a1, true, '\({x^2}\)'),
                array('{@sin(x)@}', $a1, true, '\({\sin \left( x \right)}\)'),
                array('\[{@a*b@}\]', $a1, true, '\[{x^2\cdot {\left(x+1\right)}^2}\]'),
                array('{@', null, true, '{@'), // The new parser allwos use of this text fragemt everywhere.
                array('{@(x^2@}', null, false, false),
                array('{@1/0@}', null, true,
                    '<h3>Rendering of text content failed.</h3><ul><li>Division by zero.</li></ul>'),
                array('\(1+{@1/0@}\)', null, true,
                    '<h3>Rendering of text content failed.</h3><ul><li>Division by zero.</li></ul>'),
                array('{@x^2@}', $a2, false, null),
                array('\(\frac{@"0.10"@}{@"0.10"@}\)', null, true, '\(\frac{0.10}{0.10}\)'),
                // This last one looks very odd.  It records a change in v4.0 where we stop supporting dollars.
                array('$${@x^2@}$$', null, true, '$$\({x^2}\)$$'),
                // Generate a maxima error other than division by zero.
                array('{@matrix([a],[b]).matrix([1,2],[3,4])@}', null, true,
                    '<h3>Rendering of text content failed.</h3><ul><li>MULTIPLYMATRICES: ' .
                    'attempt to multiply nonconformable matrices.</li></ul>'),
                );

        foreach ($cases as $case) {
            $this->basic_castext_instantiation($case[0], $case[1], $case[2], $case[3]);
        }
    }

    /**
     * @covers \qtype_stack\castext2_evaluatable::make_from_source
     */
    public function test_validation_error_castext_text() {
        $a = array();
        $cs = array();
        foreach ($a as $var) {
            $cs[] = stack_ast_container::make_from_teacher_source($var, '', new stack_cas_security(), array());
        }
        $cs1 = new stack_cas_session2($cs, null, 0);

        $c = '{@x+1)^2@}';
        $ct = castext2_evaluatable::make_from_source($c, 'test-case');

        $cs1->add_statement($ct);
        $this->assertFalse($cs1->get_valid());
        // Hence do not try to instantiate, or get the rendered text.

        $errmsg = 'You have a missing left bracket <span class="stacksyntaxexample">(</span> in the expression: ' .
            '<span class="stacksyntaxexample">x+1)^2</span>.';
        $this->assertEquals($errmsg, $cs1->get_errors());
        // The castext here is not valid because one of the casstrings inside is invalid.
        $this->assertFalse($ct->get_valid());
        // The same error occurs in the castext as the session.
        $this->assertEquals($errmsg, $ct->get_errors());
    }

    /**
     * @covers \qtype_stack\castext2_evaluatable::make_from_source
     */
    public function test_runtime_error_castext_text() {
        $a = array();
        $cs = array();
        foreach ($a as $var) {
            $cs[] = stack_ast_container::make_from_teacher_source($var, '', new stack_cas_security(), array());
        }
        $cs1 = new stack_cas_session2($cs, null, 0);

        $c = '{@1/0@}';
        $ct = castext2_evaluatable::make_from_source($c, 'test-case');

        $cs1->add_statement($ct);
        $this->assertTrue($cs1->get_valid());
        $cs1->instantiate();

        $this->assertEquals('Division by zero.', $cs1->get_errors());
        $this->assertEquals('<h3>Rendering of text content failed.</h3><ul><li>Division by zero.</li></ul>',
            $ct->get_rendered());
        $this->assertTrue($ct->get_valid());
        $this->assertEquals('Division by zero.',
            $ct->get_errors());
    }

    /**
     * @covers \qtype_stack\castext2_evaluatable::make_from_source
     * @covers \qtype_stack\stack_cas_keyval::get_valid
     */
    public function test_validation_error_castext_session() {
        $a = array('a:x+1)^2');
        $cs = array();
        foreach ($a as $var) {
            $cs[] = stack_ast_container::make_from_teacher_source($var, '', new stack_cas_security(), array());
        }
        $cs1 = new stack_cas_session2($cs, null, 0);

        $c = '{@a@}';
        $ct = castext2_evaluatable::make_from_source($c, 'test-case');

        $cs1->add_statement($ct);
        $this->assertFalse($cs1->get_valid());
        // Hence do not try to instantiate, or get the rendered text.

        $errmsg = 'You have a missing left bracket <span class="stacksyntaxexample">(</span> in the expression: ' .
            '<span class="stacksyntaxexample">a:x+1)^2</span>.';
        $this->assertEquals($errmsg, $cs1->get_errors());

        // The castext here is valid because no errors occur in the vastext itself.
        $this->assertTrue($ct->get_valid());
        // There is no error in the castext itself (any more).
        $this->assertEquals('', $ct->get_errors());
    }

    /**
     * @covers \qtype_stack\stack_cas_session2
     */
    public function test_runtime_error_castext_session() {
        $a = array('a:1/0');
        $cs = array();
        foreach ($a as $var) {
            $cs[] = stack_ast_container::make_from_teacher_source($var, '', new stack_cas_security(), array());
        }
        $cs1 = new stack_cas_session2($cs, null, 0);

        $c = '{@a@}';
        $ct = castext2_evaluatable::make_from_source($c, 'test-case');

        $cs1->add_statement($ct);
        $this->assertTrue($cs1->get_valid());
        $cs1->instantiate();
        $this->assertEquals('Division by zero.', $cs1->get_errors());

        // It is correct to return the un-evaluated variable name here.
        $this->assertEquals('\({a}\)', $ct->get_rendered());
        $this->assertTrue($ct->get_valid());
        // There is no error in the castext itself (any more).
        $this->assertEquals('', $ct->get_errors());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_if
     */
    public function test_if_block() {
        $a1 = array('a:true', 'b:is(1>2)', 'c:false');
        // From iss309.
        $c = '[[ if test="false" ]]Alpha[[ elif test="true"]]Beta[[ elif test="false"]]Gamma'
                . '[[ else ]]Delta[[/ if]]';

        $cases = array(
                array('[[if test="a"]]ok1[[/ if]]', $a1, true, "ok1"),
                array('[[ if test="a" ]]ok1s[[/ if ]]', $a1, true, "ok1s"),
                array('[[ if test="b" ]]ok2[[/ if ]]', $a1, true, ""),
                array('[[ if test="b" ]]ok3[[else]]OK3[[/ if ]]', $a1, true, "OK3"),
                array('[[ if test="b" ]]ok4[[elif test="c"]]Ok4[[ else ]]OK4[[/ if ]]', $a1, true, "OK4"),
                array('[[ if test="b" ]]ok4s[[ elif test="c" ]]Ok4s[[ else ]]OK4S[[/ if ]]', $a1, true, "OK4S"),
                array('[[ if test="b" ]]ok5[[elif test="false"]]oK5[[elif test="a"]]Ok5[[else]]OK5[[/ if ]]', $a1, true, "Ok5"),
                array('[[ if test="a" ]][[ if test="a" ]]ok6[[/ if ]][[/ if ]]', $a1, true, "ok6"),
                array('[[ if test="a" ]][[ if test="b" ]]ok7[[/ if ]][[/ if ]]', $a1, true, ""),
                array('[[ if test="a" ]][[ if test="b" ]]ok8[[else]]OK8[[/ if ]][[/ if ]]', $a1, true, "OK8"),
                array('[[if test="is(5>3)"]]OK9[[/if]]', $a1, true, "OK9"),
                array($c . ' ' . $c, $a1, true, "Beta Beta"),
                array($c . $c, $a1, true, "BetaBeta"),
        );

        foreach ($cases as $case) {
            $this->basic_castext_instantiation($case[0], $case[1], $case[2], $case[3], 't');
        }

        foreach ($cases as $case) {
            $this->basic_castext_instantiation($case[0], $case[1], $case[2], $case[3], 's');
        }
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_if
     */
    public function test_if_block_error() {
        // NOTE... These syntax error tests should be removed. All blocks either work or they do not
        // if they do not work you see them not being replaced in the output. The old parser
        // spent way too much time figuring out if a opening block closed or not and that
        // made it unable to deal with comments and other details. Now we have comments and
        // much less complexity in the parser.
        $a = array('a:true', 'b:is(1>2)');
        $cs = array();
        foreach ($a as $var) {
            $cs[] = stack_ast_container::make_from_teacher_source($var, '', new stack_cas_security(), array());
        }
        $session = new stack_cas_session2($cs, null, 0);

        $c = '[[ if test="a" ]][[ if ]]ok[[/ if ]][[/ if ]]';
        $ct = castext2_evaluatable::make_from_source($c, 'test-case');
        $this->assertFalse($ct->get_valid());
        // TODO: better error trapping?
        $this->assertEquals('', $ct->get_errors());

        $c = '[[ if test="a" ]][[else]]a[[elif test="b"]]b[[/ if ]]';
        $ct = castext2_evaluatable::make_from_source($c, 'test-case');
        $this->assertTrue($ct->get_valid());
        $this->assertEquals('', $ct->get_errors());

        $c = '[[ if test="a" ]][[else]]a[[else]]b[[/ if ]]';
        $ct = castext2_evaluatable::make_from_source($c, 'test-case');
        $this->assertTrue($ct->get_valid());
        $this->assertEquals('', $ct->get_errors());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_if
     */
    public function test_broken_block_error() {
        $a = array('a:true', 'b:is(1>2)');
        $cs = array();
        foreach ($a as $var) {
            $cs[] = stack_ast_container::make_from_teacher_source($var, '', new stack_cas_security(), array());
        }
        $session = new stack_cas_session2($cs, null, 0);

        $c = '[[ if test="a" ]][[ if ]]ok[[/ if ]]';
        $ct = castext2_evaluatable::make_from_source($c, 'test-case');
        $this->assertFalse($ct->get_valid());
        // TODO: better error trapping?
        $this->assertEquals('', $ct->get_errors());
    }

    /**
     * @covers \qtype_stack\CTP_Parser
     */
    public function test_broken_block_error2() {
        $a = array('a:true', 'b:is(1>2)');
        $cs = array();
        foreach ($a as $var) {
            $cs[] = stack_ast_container::make_from_teacher_source($var, '', new stack_cas_security(), array());
        }
        $session = new stack_cas_session2($cs, null, 0);

        // None of these should match not even that last bar.
        $c = '[[ foo ]][[/bar]][[bar]][[/foo]][[/bar]]';
        $ct = castext2_evaluatable::make_from_source($c, 'test-case');
        $this->assertFalse($ct->get_valid());
        $this->assertEquals('Expected "comment", "define", "escape", "if" or identifier but "\/" found.',
            $ct->get_errors());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_define
     */
    public function test_define_block() {
        $a1 = array('a:2');

        $cases = array(
                array('{#a#} [[ define a="1" /]]{#a#}', $a1, true, "2 1"),
                array('{#a#} [[ define a="a^2" /]]{#a#}', $a1, true, "2 4"),
        );

        foreach ($cases as $case) {
            $this->basic_castext_instantiation($case[0], $case[1], $case[2], $case[3]);
        }
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_foreach
     */
    public function test_foreach_block() {
        $a1 = array('a:[1,2,3]', 'b:{4,5,6,7}');

        $cases = array(
                // The first one is a tricky one it uses the same variable name.
                array('{#a#} [[ foreach a="a" ]]{#a#},[[/foreach]]', $a1, true, "[1,2,3] 1,2,3,"),
                array('[[ foreach a="b" ]]{#a#},[[/foreach]]', $a1, true, "4,5,6,7,"),
                array('[[ foreach I="a" K="b" ]]{#I#},{#K#},[[/foreach]]', $a1, true, "1,4,2,5,3,6,"),
                array('[[ foreach o="[[1,2],[3,4]]" ]]{[[ foreach k="o" ]]{#k#},[[/ foreach ]]}[[/foreach]]',
                          $a1, true, "{1,2,}{3,4,}"),
        );

        foreach ($cases as $case) {
            $this->basic_castext_instantiation($case[0], $case[1], $case[2], $case[3]);
        }
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_comment
     */
    public function test_comment_block_define() {
        $a1 = array('a:2');

        $cases = array(
                array('{#a#} [[ define a="1" /]][[ comment ]] Ignore comment. [[/ comment]]{#a#}', $a1, true, "2 1"),
                array('{#a#} [[ define a="a^2" /]][[ comment ]]Ignore[[/ comment]]{#a#}', $a1, true, "2 4"),
        );

        foreach ($cases as $case) {
            $this->basic_castext_instantiation($case[0], $case[1], $case[2], $case[3]);
        }
    }

    /**
     * These @@PLUGINFILE@@ tests check that we don't break those
     * placeholders when we are nto actively trying to evalaute them.
     *
     * @covers \qtype_stack\castext2_evaluatable
     */
    public function test_not_confused_by_pluginfile() {
        $c = 'Here {@x@} is some @@PLUGINFILE@@ {@x + 1@} some input';
        $ct = castext2_evaluatable::make_from_source($c, 'test-case');
        $this->assertTrue($ct->get_valid());
        $session = new stack_cas_session2([$ct]);
        $session->instantiate();
        $this->assertEquals('Here \({x}\) is some @@PLUGINFILE@@ \({x+1}\) some input', $ct->get_rendered());
    }

    /**
     * @covers \qtype_stack\castext2_evaluatable
     */
    public function test_not_confused_by_pluginfile_real_example() {
        $realexample = '<p><img style="display: block; margin-left: auto; margin-right: auto;" ' .
                'src="@@PLUGINFILE@@/inclined-plane.png" alt="" width="164" height="117" /></p>';
        $ct = castext2_evaluatable::make_from_source($realexample, 'test-case');
        $session = new stack_cas_session2([$ct]);
        $session->instantiate();
        $this->assertTrue($ct->get_valid());
        $this->assertEquals($realexample, $ct->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_session2::get_keyval_representation
     */
    public function test_get_all_raw_casstrings() {
        $raw = 'Take {@x^2+2*x@} and then {@sin(z^2)@}.';
        $raws = castext2_parser_utils::get_casstrings($raw);
        $session = new stack_cas_session2($raws);
        $kv = $session->get_keyval_representation();
        $val = "x^2+2*x;\nsin(z^2);";
        $this->assertEquals($val, $kv);
    }

    /**
     * @covers \qtype_stack\stack_cas_session2::get_keyval_representation
     */
    public function test_get_all_raw_casstrings_if() {
        $raw = 'Take {@x^2+2*x@} and then [[ if test="true"]]{@sin(z^2)@}[[/if]].';
        $raws = castext2_parser_utils::get_casstrings($raw);
        $session = new stack_cas_session2($raws);
        $kv = $session->get_keyval_representation();
        // The era of conditionla casstrings is over. The casstring extraction does
        // not build conditions.
        $val = "x^2+2*x;\ntrue;\nsin(z^2);";
        $this->assertEquals($val, $kv);
    }

    /**
     * @covers \qtype_stack\stack_cas_session2::get_keyval_representation
     */
    public function test_get_all_raw_casstrings_foreach() {
        $raw = 'Take {@x^2+2*x@} and then[[ foreach t="[1,2,3]"]] {@t@}[[/foreach]].';
        $raws = castext2_parser_utils::get_casstrings($raw);
        $session = new stack_cas_session2($raws);
        $kv = $session->get_keyval_representation();
        // Now we only receive the values before evaluation and the evaluation is single pass.
        $val = "x^2+2*x;\nt:[1,2,3];\nt;";
        $this->assertEquals($val, $kv);

        // Render it for completenes sake.
        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
        $cs1 = new stack_cas_session2([$at1]);
        $cs1->instantiate();

        $text = 'Take \({x^2+2\cdot x}\) and then \({1}\) \({2}\) \({3}\).';
        $this->assertEquals($text, $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_session2::get_keyval_representation
     */
    public function test_get_all_raw_casstrings_empty() {
        $raw = 'Take some text without cas commands.';
        $raws = castext2_parser_utils::get_casstrings($raw);
        $session = new stack_cas_session2($raws);
        $kv = $session->get_keyval_representation();
        $val = '';
        $this->assertEquals($val, $kv);
    }

    /**
     * @covers \qtype_stack\stack_cas_session2::get_keyval_representation
     */
    public function test_get_all_raw_casstrings_session() {

        $sa = array('p:diff(sans,x)', 'q = int(tans,x)');
        foreach ($sa as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs1 = new stack_cas_session2($s1, null, 0);

        $raw = 'Take {@ 1/(1+x^2) @} and then {@sin(z^2)@}.';
        $raws = castext2_parser_utils::get_casstrings($raw);
        $session = new stack_cas_session2($raws);
        // Note the session has the compiled castext and extracting the individual
        // statements from it is not going to happen, so lets merge stuff.
        $kv = $cs1->get_keyval_representation() . "\n" . $session->get_keyval_representation();
        // Note the equation is no longer missing from the keyval representation here.
        $val = "p:diff(sans,x);\nq = int(tans,x);\n1/(1+x^2);\nsin(z^2);";
        $this->assertEquals($val, $kv);
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_redefine_variables() {
        // Notice this means that within a session the value of n has to be returned at every stage....
        $at1 = castext2_evaluatable::make_from_source(
                'Let \(n\) be defined by \({@n:3@}\). Now add one to get \({@n:n+1@}\) and square the result \({@n:n^2@}\).',
                'test-case');
        $session = new stack_cas_session2([$at1]);
        $session->instantiate();
        $this->assertEquals('Let \(n\) be defined by \({3}\). Now add one to get \({4}\) and square the result \({16}\).',
                $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_maths::process_display_castext
     */
    public function test_fact_sheets() {
        $cs2 = new stack_cas_session2(array(), null, 0);
        $at1 = castext2_evaluatable::make_from_source("[[facts:calc_diff_linearity_rule]]", 'test-case');
        $cs2->add_statement($at1);
        $cs2->instantiate();
        $output = stack_maths::process_display_castext($at1->get_rendered());

        $this->assertStringContainsString(stack_string('calc_diff_linearity_rule_name'), $output);
        $this->assertStringContainsString(stack_string('calc_diff_linearity_rule_fact'), $output);
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_assignmatrixelements() {
        // Assign a value to matrix entries.
        $cs = array('A:matrix([1,2],[1,1])', 'A[1,2]:3');

        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs1 = new stack_cas_session2($s1, null, 0);

        $at1 = castext2_evaluatable::make_from_source("{@A@}", 'test-case');
        $cs1->add_statement($at1);
        $cs1->instantiate();

        $this->assertEquals('\({\left[\begin{array}{cc} 1 & 3 \\\\ 1 & 1 \end{array}\right]}\)', $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_assignmatrixelements_p1() {
        // Assign a value to matrix entries.
        $cs = array('A:matrix([1,2],[1,1])', 'A[1,2]:3');

        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $options = new stack_options();
        $options->set_option('matrixparens', '(');
        $cs1 = new stack_cas_session2($s1, $options, 0);

        $at1 = castext2_evaluatable::make_from_source("{@A@}", 'test-case');
        $cs1->add_statement($at1);
        $cs1->instantiate();

        $this->assertEquals('\({\left(\begin{array}{cc} 1 & 3 \\\\ 1 & 1 \end{array}\right)}\)', $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_assignmatrixelements_p2() {
        // Assign a value to matrix entries.
        $cs = array('A:matrix([1,2],[1,1])', 'A[1,2]:3');

        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $options = new stack_options();
        $options->set_option('matrixparens', '');
        $cs1 = new stack_cas_session2($s1, $options, 0);

        $at1 = castext2_evaluatable::make_from_source("{@A@}", 'test-case');
        $cs1->add_statement($at1);
        $cs1->instantiate();

        $this->assertEquals('\({\begin{array}{cc} 1 & 3 \\\\ 1 & 1 \end{array}}\)', $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_plot() {
        $a2 = array('p:x^3');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source("<p>This is some text</p><p>{@plot(p, [x,-2,3])@}</p>", 'test-case');
        $cs2->add_statement($at1);
        $cs2->instantiate();
        $this->assertTrue($at1->get_valid());

        $rendered = $at1->get_rendered();
        $expected = "<p>This is some text</p><p><div class='stack_plot'><img src='!ploturl!";
        // Make sure the curly brackets are not left within rendered castext with plots.
        $this->assertEquals($expected, substr($rendered, 0, 70));

        $msg = '/STACK auto-generated plot of x\^3 with parameters/';
        $this->assertMatchesRegularExpression($msg, $rendered);
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_plot_alttext() {

        $a2 = array('p:sin(x)');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        // Note, since we have spaces in the string we currently need to validate this as the teacher....
        $at1 = castext2_evaluatable::make_from_source('This is some text {@plot(p, [x,-2,3], [alt,"Hello World!"])@}', 'test-case');
        $cs2->add_statement($at1);
        $cs2->instantiate();
        $this->assertTrue($at1->get_valid());

        $msg = "/.svg' alt='Hello World!'/";
        $this->assertMatchesRegularExpression($msg, $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_plot_alttext_html() {
        $s2 = array();
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('This {@plot(x^2, [x,-2,3], [alt,"Hello < World!"])@} has < ' .
            'in the alt text.', 'test-case');
        $cs2->add_statement($at1);
        $cs2->instantiate();
        $msg = "/.svg' alt='Hello &lt; World!'/";
        $this->assertMatchesRegularExpression($msg, $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_plot_alttext_error() {

        $a2 = array('p:sin(x)');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        // Alt tags must be a string.
        $at1 = castext2_evaluatable::make_from_source('This is some text {@plot(p,[x,-2,3],[alt,x])@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        // This is another runtime errror.
        $msg = "/Plot error: the alt tag definition must be a string, but it is not./";
        $this->assertMatchesRegularExpression($msg, $cs2->get_errors());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_plot_small() {

        $a2 = array('p:sin(x)');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('A small plot: {@plot(p, [x,-2,3], [size,200,100])@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals(57, strpos($at1->get_rendered(), "stackplot-"));
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_plot_nottags() {

        $a2 = array('p:sin(x)');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('A tag-less plot: {@plot(p, [x,-2,3], [plottags,false])@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $msg = "/div class='stack_plot/";
        $this->assertDoesNotMatchRegularExpression($msg, $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_plot_option_error() {

        $cs2 = new stack_cas_session2(array(), null, 0);

        // Alt tags must be a string.
        $at1 = castext2_evaluatable::make_from_source('This is some text {@plot(x^2,[x,-2,3],[notoption,""])@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $msg = "/Plot error: STACK does not currently support the following plot2d options:/";
        $this->assertMatchesRegularExpression($msg, $at1->get_errors());
    }

    /**
     * @covers \qtype_stack\castext2_evaluatable::make_from_source
     */
    public function test_currency_1() {

        $at1 = castext2_evaluatable::make_from_source('This is system cost \$100,000 to create.', 'test-case');
        $this->assertTrue($at1->get_valid());
    }

    /**
     * @covers \qtype_stack\castext2_evaluatable::make_from_source
     */
    public function test_forbidden_words() {

        $at1 = castext2_evaluatable::make_from_source('This is system cost {@system("rm /tmp/test")@} to create.',
            'test-case');
        $this->assertFalse($at1->get_valid());
        $this->assertEquals('Forbidden function: <span class="stacksyntaxexample">system</span>.', $at1->get_errors());
    }

    /**
     * @covers \qtype_stack\castext2_parser_utils::math_paint
     */
    public function test_mathdelimiters1() {
        $a2 = array('a:2');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('\begin{align*} x & = {@a@}+1 \\ & = {@a+1@} \end{align*}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\begin{align*} x & = {2}+1 \ & = {3} \end{align*}', $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\castext2_parser_utils::math_paint
     */
    public function test_mathdelimiters2() {
        $a2 = array('a:x^2/(1+x^2)^3', 'p:diff(a,x)');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('\begin{multline*} {@a@} \\\\ {@p@} \end{multline*}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals(
                '\begin{multline*} {\frac{x^2}{{\left(x^2+1\right)}^3}} \\\\ ' .
                '{\frac{2\cdot x}{{\left(x^2+1\right)}^3}-\frac{6\cdot x^3}{{\left(x^2+1\right)}^4}} \end{multline*}',
                $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_decimalplaces() {
        // The function dispdp only holds the number of decimal places to display.  It does not do rounding.
        // Use dispsf for rounding.
        $a2 = array('a:float(%e)', 'b:3.99999');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('{@dispdp(a,2)@}, {@dispdp(b,3)@}, {@dispsf(b,4)@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({2.72}\), \({4.000}\), \({4.000}\)', $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_decimalplaces2() {
        $a2 = array('a:float(%e)', 'b:-3.99999');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('{@dispdp(a,0)*x^2@}, {@dispdp(b,3)@}, {@dispsf(b,4)@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({3\cdot x^2}\), \({-4.000}\), \({-4.000}\)', $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_decimalplaces_error() {
        $a2 = array('a:float(%e)', 'b:-3.99999');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('{@dispdp(a1,0)*x^2@}, {@dispdp(b,3)@}, {@dispsf(b,4)@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $err = array('dispdp requires a real number argument.');
        $this->assertEquals($err, $at1->get_errors(false));
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_mult_blank() {
        $a2 = array('make_multsgn("blank")', 'b:x*y');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);
        $this->assertTrue($cs2->get_valid());

        $at1 = castext2_evaluatable::make_from_source('{@b@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({x\, y}\)', $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_mult_dot() {
        $a2 = array('make_multsgn("dot")', 'b:x*y');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);
        $this->assertTrue($cs2->get_valid());

        $at1 = castext2_evaluatable::make_from_source('{@b@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({x\cdot y}\)', $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_mult_cross() {
        $a2 = array('make_multsgn("cross")', 'b:x*y');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);
        $this->assertTrue($cs2->get_valid());

        $at1 = castext2_evaluatable::make_from_source('{@b@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({x\times y}\)', $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_mult_switch() {
        $a2 = array('make_multsgn("dot")');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);
        $this->assertTrue($cs2->get_valid());

        $at1 = castext2_evaluatable::make_from_source('Default: {@a*b@}. Switch: {@(make_multsgn("cross"), a*b)@}. ' .
                'Cross remains: {@a*b@}.', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('Default: \({a\cdot b}\). Switch: \({a\times b}\). Cross remains: \({a\times b}\).',
                $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_equiv_natural_domain() {
        $a2 = array('ta:[1/(x-1)+1/(x+1),2*x/(x^2-1)]');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);
        $this->assertTrue($cs2->get_valid());

        $cs1 = '\[ {@stack_disp_arg(ta)@} \] \[ {@stack_disp_arg(ta,false)@} \] ' .
            '\[ {@stack_disp_arg(ta,true,false)@} \] \[ {@stack_disp_arg(ta,false,false)@} \]';
        $at1 = castext2_evaluatable::make_from_source($cs1, 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\[ {\begin{array}{lll} &\frac{1}{x+1}+\frac{1}{x-1}&' .
            '{\color{blue}{{x \not\in {\left \{-1 , 1 \right \}}}}}\cr \color{green}' .
            '{\Leftrightarrow}&\frac{2\cdot x}{x^2-1}&{\color{blue}{{x \not\in {\left \{-1 , 1 \right \}}}}}' .
            '\cr \end{array}} \] \[ {\begin{array}{lll}\frac{1}{x+1}+\frac{1}{x-1}&' .
            '{\color{blue}{{x \not\in {\left \{-1 , 1 \right \}}}}}\cr \frac{2\cdot x}{x^2-1}&{\color{blue}' .
            '{{x \not\in {\left \{-1 , 1 \right \}}}}}\cr \end{array}} \] ' .
            '\[ {\begin{array}{lll} &\frac{1}{x+1}+\frac{1}{x-1}& \cr \color{green}{\Leftrightarrow}&' .
            '\frac{2\cdot x}{x^2-1}& \cr \end{array}} \] ' .
            '\[ {\begin{array}{lll}\frac{1}{x+1}+\frac{1}{x-1}& \cr \frac{2\cdot x}{x^2-1}& \cr \end{array}} \]',
            $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_ode1() {
        $at1 = new stack_cas_keyval("p1:'diff(y,x,2)+2*y = 0;p2:ev('diff(y,x,2),simp)+2*ev('diff(y,x,2,z,3),simp) = 0;",
                null, 123);
        $this->assertTrue($at1->get_valid());
        $cs2 = $at1->get_session();

        $at2 = castext2_evaluatable::make_from_source('\[{@p1@}\] \[{@p2@}\]', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals(
                '\[{\frac{\mathrm{d}^2 y}{\mathrm{d} x^2}+2\cdot y=0}\] ' .
                '\[{2\cdot \left(\frac{\mathrm{d}^5 y}{\mathrm{d} x^2 \mathrm{d} z^3}\right)' .
                '+\frac{\mathrm{d}^2 y}{\mathrm{d} x^2}=0}\]',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_ode2() {
        $vars = "derivabbrev:true;p1:'diff(y,x,2)+2*y = 0;p2:ev('diff(y,x,2),simp)+2*ev('diff(y,x,2,z,3),simp) = 0;";
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());
        $cs2 = $at1->get_session();

        $at2 = castext2_evaluatable::make_from_source('\[{@p1@}\] \[{@p2@}\]', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals(
                '\[{y_{x x}+2\cdot y=0}\] \[{2\cdot y_{x x z z z}+y_{x x}=0}\]',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_int() {
        $vars = "foo:'int(f(x),x)";
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());
        $cs2 = $at1->get_session();

        $at2 = castext2_evaluatable::make_from_source('\[{@foo@}\]', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals(
                '\[{\int {f\left(x\right)}{\;\mathrm{d}x}}\]',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_strings_in_castext() {
        $vars = "st1:[\"\;\sin(x^2)\",\"\;\cos(x^2)\"]\n/* And a comment: with LaTeX \;\sin(x) */
        \n a:3;";
        $at1 = new stack_cas_keyval($vars, null, 123);

        $this->assertTrue($at1->get_valid());
        $cs2 = $at1->get_session();

        $at2 = castext2_evaluatable::make_from_source('\[{@a@}\]', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals('\[{3}\]', $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_strings_in_castext_escaped() {
        $vars = 'st:"This is a string with escaped \" strings...."';
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());
        $cs2 = $at1->get_session();

        $at2 = castext2_evaluatable::make_from_source('\[{@st@}\]', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals('\[{This is a string with escaped " strings....}\]',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_strings_in_castext_translated() {
        $vars = 'st:a and "!AND!"';
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());
        $cs2 = $at1->get_session();

        $at2 = castext2_evaluatable::make_from_source('{@st@}', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        // The !AND! string is translated.
        $this->assertEquals('\({a\,{\mbox{ and }}\, \mbox{and}}\)', $at2->get_rendered());

        $at2 = castext2_evaluatable::make_from_source('{@"!AND!"@}', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals('and', $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_strings_only() {
        $s = '{@"This is a string"@} whereas this is empty |{@""@}|. Not quite empty |{@" "@}|.';

        $at2 = castext2_evaluatable::make_from_source($s, 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2 = new stack_cas_session2([$at2]);
        $cs2->instantiate();

        $this->assertEquals(
                'This is a string whereas this is empty ||. Not quite empty | |.',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_strings_only_latex() {
        // Remember the quotes below are escaped!
        $s = '{@"This is a string with LaTeX in it \\\\(\\\\pi\\\\)."@}';

        $at2 = castext2_evaluatable::make_from_source($s, 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2 = new stack_cas_session2([$at2]);
        $cs2->instantiate();

        $this->assertEquals(
                'This is a string with LaTeX in it \\(\\pi\\).',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_strings_embeded() {
        $s = '{@"This is a string"+x^2@}.';

        $at2 = castext2_evaluatable::make_from_source($s, 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2 = new stack_cas_session2([$at2]);
        $cs2->instantiate();

        $this->assertEquals(
                '\({x^2+\mbox{This is a string}}\).',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_numerical_display_float_default() {
        // The number 0.000001 used to be tested, but it was giving weird results.
        // On some versions of Maxima, including the latest, it comes back as
        // 10.0e-7, instead of 1.0e-6. Other versions get it right. I did not like
        // a testcase that asserted weird behaviour (10.0e-7) so I removed it.
        $s = 'Decimal numbers {@0.1@}, {@0.01@}, {@0.001@}, {@0.0001@}, {@0.00001@}.';

        $at2 = castext2_evaluatable::make_from_source($s, 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2 = new stack_cas_session2([$at2]);
        $cs2->instantiate();

        $this->assert_content_with_maths_equals(
            'Decimal numbers \({0.1}\), \({0.01}\), \({0.001}\), \({1.0E-4}\), \({1.0E-5}\).',
            $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_numerical_display_float_decimal() {
        $st = 'Decimal numbers {@0.1@}, {@0.01@}, {@0.001@}, {@0.0001@}, {@0.00001@}, {@0.000001@}.';

        $a2 = array('stackfltfmt:"~f"');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at2 = castext2_evaluatable::make_from_source($st, 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals(
                'Decimal numbers \({0.1}\), \({0.01}\), \({0.001}\), \({0.0001}\), \({0.00001}\), \({0.000001}\).',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_numerical_display_float_scientific() {
        // The number 0.000001 is handled below, so we can skip on old Maxima where it fails.
        $st = 'Decimal numbers {@0.1@}, {@0.01@}, {@0.001@}, {@0.0001@}, {@0.00001@}.';

        $a2 = array('stackfltfmt:"~e"');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at2 = castext2_evaluatable::make_from_source($st, 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assert_content_with_maths_equals(
                'Decimal numbers \({1.0E-1}\), \({1.0E-2}\), \({1.0E-3}\), \({1.0E-4}\), \({1.0E-5}\).',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_numerical_display_float_scientific_small() {
        // On old Maxima, you get back \(9.999999999999999e-7\).
        $this->skip_if_old_maxima('5.32.1');

        $st = 'Decimal number {@0.000001@}.';

        $a2 = array('stackfltfmt:"~e"');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at2 = castext2_evaluatable::make_from_source($st, 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assert_content_with_maths_equals(
                'Decimal number \({1.0E-6}\).',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_numerical_display_1() {
        $s = 'The decimal number {@n:73@} is written in base \(2\) as {@(stackintfmt:"~2r",n)@}, in base \(7\) ' .
            'as {@(stackintfmt:"~7r",n)@}, in scientific notation as {@(stackintfmt:"~e",n)@} ' .
            'and in rhetoric as {@(stackintfmt:"~r",n)@}.';

        $at2 = castext2_evaluatable::make_from_source($s, 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2 = new stack_cas_session2([$at2]);
        $cs2->instantiate();

        $this->assert_content_with_maths_equals(
                'The decimal number \({73}\) is written in base \(2\) as \({1001001}\), in base \(7\) as \({133}\), ' .
                'in scientific notation as \({7.3E+1}\) and in rhetoric as \({\mbox{seventy-three}}\).',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_numerical_display_binary() {
        $st = 'The number {@73@} is written in base \(2\).';

        $a2 = array('stackintfmt:"~b"');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at2 = castext2_evaluatable::make_from_source($st, 'test-case');

        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals(
                'The number \({1001001}\) is written in base \(2\).',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_numerical_display_ampersand_roman() {
        // This test makes sure the castext parser to allows @ within strings within the castext.
        $st = 'The number {@(stackintfmt:"~@r",14)@} is written in Roman numerals.';
        $s2 = array();
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at2 = castext2_evaluatable::make_from_source($st, 'test-case');

        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals(
            'The number \({XIV}\) is written in Roman numerals.',
            $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_numerical_display_roman() {
        $st = 'The number {@14@} is written in Roman numerals.';

        $a2 = array('stackintfmt:"~@r"');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at2 = castext2_evaluatable::make_from_source($st, 'test-case');

        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals(
            'The number \({XIV}\) is written in Roman numerals.',
            $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_inline_fractions() {
        $s = '{@(stack_disp_fractions("i"), 1/x)@} {@(stack_disp_fractions("d"), 1/x)@} {@(stack_disp_fractions("i"), 1/x)@}';

        $at2 = castext2_evaluatable::make_from_source($s, 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2 = new stack_cas_session2([$at2]);
        $cs2->instantiate();

        $this->assertEquals($at2->get_rendered(),
                '\({{1}/{x}}\) \({\frac{1}{x}}\) \({{1}/{x}}\)');
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_inline_fractions_all() {
        $st = '{@1/x@}, {@1/x^2@}, {@1/(a+x)@}, {@1/(2*a)@}, {@1/sin(x+y)@}.';

        $a2 = array('stack_disp_fractions("i")');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at2 = castext2_evaluatable::make_from_source($st, 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals('\({{1}/{x}}\), \({{1}/{x^2}}\), \({{1}/{\left(x+a\right)}}\), \({{1}/{\left(2\cdot a\right)}}\),'
              . ' \({{1}/{\sin \left( y+x \right)}}\).', $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_disp_greek() {
        $a2 = array('a:Delta', 'b:sin(Delta^2)', 'c:delta', 't:theta');
        $s2 = array();
        foreach ($a2 as $s) {
            // 4.3 Change from student validation to teacher.  Students can't create castext.
            $cs = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('{@a@}, {@b@}, {@c@}, {@t@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({\Delta}\), \({\sin \left( \Delta^2 \right)}\), ' .
                '\({\delta}\), \({\theta}\)', $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_subscripts() {
        $a2 = array('a:texsub(v, 2*alpha)', 'b:texsub(v, texsub(m, n))');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('{@a@}, {@b@}, {@beta47@}, {@beta_47@}',
            'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $expected = '\({{v}_{2\cdot \alpha}}\), \({{v}_{{m}_{n}}}\), '.
            '\({\beta_{47}}\), \({{\beta}_{47}}\)';
        $this->assertEquals($expected, $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_maxima_arrays() {
        $a2 = array('p1:a[2]', 'p2:a[n+1]', 'p3:a[b_c]');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('{@p1@}, {@p2@}, {@p3@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({a_{2}}\), \({a_{n+1}}\), \({a_{{b}_{c}}}\)',
                $at1->get_rendered());

        $this->assertEquals('a[n+1]',
            $cs2->get_by_key('p2')->get_value());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_ast_container
     */
    public function test_length() {
        $a2 = array('f(x):=length(x)', 'b:[1,2,3]', 'c:f(b)');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('{@c@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({3}\)', $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_ast_container
     */
    public function test_lambda() {
        $a2 = array('sfc: lambda([x,n],significantfigures(x,n))',
            'n:[3.1234,1]', 'm:apply(sfc,n)');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $st = '{@sfc@}, {@m@}';
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source($st, 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assert_equals_ignore_spaces_and_e('\({\lambda\left(\left[ x , n \right]  , ' .
                '{\it significantfigures}\left(x , n\right)\right)}\), \({3}\)',
            $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_ast_container
     */
    public function test_apply() {
        $a2 = array('p1:apply("+",[x,y,z]);');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $st = '{@p1@}, {@apply("*",[a,b,c])@}';
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at2 = castext2_evaluatable::make_from_source($st, 'test-case');

        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assert_equals_ignore_spaces_and_e('\({z+y+x}\), \({a\cdot b\cdot c}\)',
            $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_ast_container
     */
    public function test_stackintfmt() {
        // Note, we have set up one pattern as CAS strings because we cannot have @ symbols in CAStext at this point.
        // This will be fixed in castext2 (stateful).
        $a2 = array('n:1234', 'str1:"~@R"');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        // Further examples.
        // Tabs instead of commas doesn't work: {@(stackintfmt:"~,,' ,3:d",n)@}.
        // Tabs instead of commas doesn't work: {@(stackintfmt:"~,,' ,3d",n)@}.
        $at1 = castext2_evaluatable::make_from_source('Standard: {@n@}. ' .
            'Scientific notation: {@(stackintfmt:"~e",n)@}. ' .
            'With commas: {@(stackintfmt:"~:d",n)@}. ' .
            'Ordinal rethoric: {@(stackintfmt:"~:r",n)@}. ' .
            // Roman numerals don't work with very large numbers!
            'Roman numerals: {@(stackintfmt:str1,n)@}.', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $expected = 'Standard: \({1234}\). ' .
                'Scientific notation: \({1.234E+3}\). With commas: \({1,234}\). ' .
                'Ordinal rethoric: \({\mbox{one thousand two hundred thirty-fourth}}\). ' .
                'Roman numerals: \({MCCXXXIV}\).';
        $actual = $at1->get_rendered();
        // Some Maxima/Lisp combos output a comma. Other's don't.
        // So, normalise before we compare.
        $actual = str_replace('one thousand, two hundred',
            'one thousand two hundred', $actual);
        $this->assert_equals_ignore_spaces_and_e($expected, $actual);
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_ast_container
     */
    public function test_stack_disp_comma_separate() {
        $st = '{@stack_disp_comma_separate([a,b,c])@} and {#stack_disp_comma_separate([a,b,c])#}.';

        $s2 = array();
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at2 = castext2_evaluatable::make_from_source($st, 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals('a, b, c and "a, b, c".', $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_jsxgraph
     */
    public function test_stack_jsxgraph_statestore() {
        $st = '[[jsxgraph input-ref-stateStore="stateRef"]]' .
              'var board = JXG.JSXGraph.initBoard(divid, {axis: true, showCopyright: false});' .
              'var p = board.create(\'point\', [4, 3]);' .
              'stack_jxg.bind_point(stateRef, p);' .
              'stateInput.style.display = \'none\';' .
              '[[/jsxgraph]]';

        $at2 = castext2_evaluatable::make_from_source($st, 'test-case');
        $this->assertTrue($at2->get_valid());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_ast_container
     */
    public function test_stack_var_makelist() {
        $a2 = array('vars0:stack_var_makelist(k, 5)',
            'vars1:rest(stack_var_makelist(k, 6))');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('{@vars0@} and {#vars1#}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({\left[ k_{0} , k_{1} , k_{2} , k_{3} , k_{4} , k_{5} \right]}\) ' .
                'and [k1,k2,k3,k4,k5,k6]',
            $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_ast_container
     */
    public function test_stack_simp_false_true() {
        $a2 = array('simp:false',
            'p1:1+1',
            'simp:true',
            'p2:1+1');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        // Simp:true at the end so subsequent expressions are simplified.
        $at1 = castext2_evaluatable::make_from_source('{@p1@}, {@p2@}.', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({2}\), \({2}\).',
            $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_ast_container
     */
    public function test_stack_simp_false_true_false() {
        // In STACK v<4.3 authors often control simp within a session.
        $a2 = array('simp:false',
                'p1:1+1',
                'simp:true',
                'p2:1+1',
                'simp:false',
                'p3:1+1');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('{@p1@}, {@p2@}, {@p3@}.', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({1+1}\), \({2}\), \({1+1}\).',
            $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_ast_container
     */
    public function test_stack_beta_function_arg() {
        $a2 = array('n:1932;',
                'f(alfa):=block(x:ifactors(alfa), y:makelist(0,length(x)), ' .
                'for i from 1 thru length(x) do (y[i] : first(x[i])), return(y));',
                'g(alfa,beta):=block(x:alfa*(1-1/beta[1]), ' .
                'for i from 2 thru length(beta) do (x:x*(1-1/beta[i])), return(x));'
            );
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session2($s2, null, 0);

        $at1 = castext2_evaluatable::make_from_source('{@f(n)@}, {@g(n,f(n))@}', 'test-case');
        $this->assertTrue($at1->get_valid());
        $cs2->add_statement($at1);
        $cs2->instantiate();

        $this->assertEquals('\({\left[ 2 , 3 , 7 , 23 \right]}\), \({528}\)',
            $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_cas_keyval
     */
    public function test_orderless() {
        // The unorder() function is not supported due to the way STACK interacts with Maxima.
        $vars = "orderless(b);\np1:a+b+c;";
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('\[{@p1@}\]', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals('\[{c+a+b}\]', $at2->get_rendered());

        // Simplification is needed to reorder expressions.
        $vars = "simp:false;\norderless(b);\np1:a+b+c;";
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('\[{@p1@}\]', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals('\[{a+b+c}\]', $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_cas_keyval
     */
    public function test_display_complex_numbers() {
        // Typically with simp:true this does not display as a+bi.
        $vars = "p1:a+b*%i;";
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('\[{@p1@}\]', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals('\[{\mathrm{i}\cdot b+a}\]', $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_cas_keyval
     */
    public function test_display_logic() {
        $vars = 'make_logic("lang");';
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('{@A and B@}, {@A nounand B@}. ' .
                '{@A or B@}, {@A nounor B@}. ' .
                '{@(A or B) and C@}; {@(A and B) or C@}. ' .
                '{@(A nounor B) nounand C@}; {@(A nounand B) nounor C@}. {@not A@}. ' .
                '{@A nand B and C nor D xor E or F implies G xnor H@}.',
            'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals('\({A\,{\mbox{ and }}\, B}\), \({A\,{\mbox{ and }}\, B}\). ' .
                '\({A\,{\mbox{ or }}\, B}\), \({A\,{\mbox{ or }}\, B}\). ' .
                '\({\left(A\,{\mbox{ or }}\, B\right)\,{\mbox{ and }}\, C}\); ' .
                '\({A\,{\mbox{ and }}\, B\,{\mbox{ or }}\, C}\). ' .
                '\({\left(A\,{\mbox{ or }}\, B\right)\,{\mbox{ and }}\, C}\); ' .
                '\({A\,{\mbox{ and }}\, B\,{\mbox{ or }}\, C}\). ' .
                '\({{\rm not}\left( A \right)}\). ' .
                '\({A\,{\mbox{ nand }}\, B\,{\mbox{ and }}\, C\,{\mbox{ nor }}\, ' .
                'D\,{\mbox{ xor }}\, E\,{\mbox{ or }}\, F\,{\mbox{ implies }}\, G\,{\mbox{ xnor }}\, H}\).',
                $at2->get_rendered());

        $vars = 'make_logic("symbol");';
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('{@A and B@}, {@A nounand B@}. ' .
                '{@A or B@}, {@A nounor B@}. ' .
                '{@(A or B) and C@}; {@(A and B) or C@}. ' .
                '{@(A nounor B) nounand C@}; {@(A nounand B) nounor C@}. {@not A@}. ' .
                '{@A nand B and C nor D xor E or F implies G xnor H@}.',
            'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals('\({A\land B}\), \({A\land B}\). \({A\lor B}\), \({A\lor B}\). ' .
                '\({\left(A\lor B\right)\land C}\); \({A\land B\lor C}\). ' .
                '\({\left(A\lor B\right)\land C}\); \({A\land B\lor C}\). \({\neg \left( A \right)}\). ' .
                '\({A\overline{\land}B\land C\underline{\lor}D\oplus E\lor F\rightarrow G\leftrightarrow H}\).',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_cas_keyval
     */
    public function test_display_tables() {
        $vars = 'T0:table([x,x^3],[-1,-1],[0,0],[1,1],[2,8],[3,27]);';
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('{@T0@}',
                'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals('\({\begin{array}{c|c} x & x^3\\\\ \hline -1 & -1 \\\\ ' .
                '0 & 0 \\\\ 1 & 1 \\\\ 2 & 8 \\\\ 3 & 27\end{array}}\)',
                $at2->get_rendered());

        $vars = '';
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('{@truth_table(a implies b)@}',
                'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals('\({\begin{array}{c|c|c} a & b & a\,{\mbox{ implies }}\, b\\\\ ' .
                '\hline \mathbf{F} & \mathbf{F} & \mathbf{T} \\\\ \mathbf{F} & \mathbf{T} & ' .
                '\mathbf{T} \\\\ \mathbf{T} & \mathbf{F} & \mathbf{F} \\\\ ' .
                '\mathbf{T} & \mathbf{T} & \mathbf{T} \end{array}}\)',
                $at2->get_rendered());

        $vars = 'table_bool_abbreviate:false;';
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('{@truth_table(a xnor b)@}',
                'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals('\({\begin{array}{c|c|c} a & b & a\,{\mbox{ xnor }}\, b\\\\ \hline \mathbf{False} ' .
                '& \mathbf{False} & \mathbf{True} \\\\ \mathbf{False} & \mathbf{True} & \mathbf{False} \\\\ ' .
                '\mathbf{True} & \mathbf{False} & \mathbf{False} \\\\ \mathbf{True} & \mathbf{True} & ' .
                '\mathbf{True}\end{array}}\)', $at2->get_rendered());

        $vars = '';
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('{@table_difference(truth_table(a xor b), truth_table(a implies b))@}',
                'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals('\({\begin{array}{c|c|c} a & b & \color{red}{\underline{a\,{\mbox{ xor }}\, b}}\\\\ ' .
                '\hline \mathbf{F} & \mathbf{F} & \color{red}{\underline{\mathbf{F} }} \\\\ \mathbf{F} & \mathbf{T} ' .
                '& \mathbf{T} \\\\ \mathbf{T} & \mathbf{F} & \color{red}{\underline{\mathbf{T} }} \\\\ \mathbf{T} & ' .
                '\mathbf{T} & \color{red}{\underline{\mathbf{F} }}\end{array}}\)',
                $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_cas_keyval
     */
    public function test_display_equation_match_brackets() {
        $vars = "p1:x+1;\np2:expand(p1^3);";
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('\[ \left({@p1@}\right)^3 = {@p2@}.\]', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals('\[ \left({x+1}\right)^3 = {x^3+3\cdot x^2+3\cdot x+1}.\]', $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_cas_keyval
     */
    public function test_display_apply() {
        $vars = "ans1:transpose(matrix([1,1,0,0])); ans2:transpose(matrix([0,1,1,0])); ans3:transpose(matrix([0,0,1,1])); " .
            "ans4:transpose(matrix([1,0,1,1])); /* The standard basis for R^4 */ " .
            "SB:setify(maplist(transpose,apply(matrix,args(ident(4))))); /* Form a matrix of the student's vectors.*/ " .
            "SA1:map(lambda([ex], first(args(transpose(ex)))), [ans1,ans2,ans3,ans4]); SA2:transpose(apply(matrix,SA1)); " .
            "SA3:ev(determinant(SA2),simp); /* Find any standard basis vectors. */ ".
            "SB1:ev(sublist([ans1,ans2,ans3,ans4],lambda([ex],elementp(ex,SB))),simp); /* Sort out feedback. */ " .
            "SA4:SA2.matrix([w],[x],[y],[z]); SA5:ev(stack_strip_percent(first(solve(flatten(args(SA4)))),[a,b,c,d]),simp); " .
            "SA6:ev(maplist(lambda([ex1], rhs(first(sublist(SA5, lambda([ex2],is(lhs(ex2)=ex1)))))), [w,x,y,z]),simp); " .
            "SA7:zip_with(\"*\",SA6,[ans1,ans2,ans3,ans4]); SA8:apply(\"+\",SA7);";
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('\[ {@SA8@}.\]', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals('\[ {\left[\begin{array}{c} 0 \\\\ 0 \\\\ 0 \\\\ 0 \end{array}\right]}.\]', $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_cas_keyval
     */
    public function test_texput_complex_function() {
        $vars = 'texput(u,lambda([ex],if length(ex)<4 then return("\\bigcup_{?=?}^{?} ? ") else' .
            'sconcat("\\bigcup_{" ,tex1(second(ex)), " = ", tex1(third(ex)), "}^{", tex1(fourth(ex)), ' .
            '"} ", tex1(first(ex)))));';
        $at1 = new stack_cas_keyval($vars, null, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('{@u(A_k,k,1,inf)@}', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals('\({bigcup_{k = 1}^{\infty } {A}_{k}}\)', $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_cas_keyval
     */
    public function test_display_tree() {
        $options = new stack_options();
        $options->set_option('simplify', false);

        $vars = "p1:x=a nounor b;\np2:x=(a nounor b);";
        $at1 = new stack_cas_keyval($vars, $options, 123);
        $this->assertTrue($at1->get_valid());
        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('{@p1@}: {@disptree(p1)@} <br/> {@p2@}: {@disptree(p2)@}', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals("\({x=a\,{\mbox{ or }}\, b}\): <ul class='tree'><li><span class='op'>\(\,{\mbox{ or }}\, \)" .
            "</span><ul><li><code>=</code><ul><li><span class='atom'>\(x\)</span></li><li><span class='atom'>\(a\)</span>" .
            "</li></ul></li><li><span class='atom'>\(b\)</span></li></ul></li></ul> <br/> " .
            "\({x=\left(a\,{\mbox{ or }}\, b\\right)}\): <ul class='tree'><li><code>=</code><ul><li><span class='atom'>" .
            "\(x\)</span></li><li><span class='op'>\(\,{\mbox{ or }}\, \)</span><ul><li><span class='atom'>\(a\)</span>" .
            "</li><li><span class='atom'>\(b\)</span></li></ul></li></ul></li></ul>", $at2->get_rendered());

        // An example needing some bespoke style in the output.
        $vars = "p1:1+'diff(sin(x*y),x,2);";
        $at1 = new stack_cas_keyval($vars, $options, 123);
        $this->assertTrue($at1->get_valid());
        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('{@p1@}: {@disptree(p1)@}', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals("\({1+\left(\\frac{\mathrm{d}^2}{\mathrm{d} x^2} \sin \left( x\cdot y \\right)\\right)}\): " .
            "<ul class='tree'><li><code>+</code><ul><li><span class='atom'>\(1\)</span></li><li><span class='op'>" .
            "\(\\frac{\mathrm{d}^2 }{\mathrm{d} x^2} \)</span><ul><li><code>sin</code><ul><li><code>*</code><ul><li>" .
            "<span class='atom'>\(x\)</span></li><li><span class='atom'>\(y\)</span></li></ul></li></ul></li></ul></li>" .
            "</ul></li></ul>", $at2->get_rendered());

        $at2 = castext2_evaluatable::make_from_source('{@disptree(a+-gamma(x))@}', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals("<ul class='tree'><li><span class='op'>\({ \pm }\)</span><ul><li><span class='atom'>\(a\)" .
            "</span></li><li><span class='op'>\(\Gamma\)</span><ul><li><span class='atom'>\(x\)</span></li></ul></li>" .
            "</ul></li></ul>", $at2->get_rendered());

        $at2 = castext2_evaluatable::make_from_source('{@disptree(sqrt(x))@}', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals("<ul class='tree'><li><span class='op'>\(\sqrt{}\)</span><ul><li>" .
            "<span class='atom'>\(x\)</span></li></ul></li></ul>", $at2->get_rendered());

        $at2 = castext2_evaluatable::make_from_source("{@disptree('limit(1/(x+1),x,0))@}", 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals("<ul class='tree'><li><span class='op'>\(\lim_{x\\rightarrow{0}} \cdots \)</span><ul>" .
            "<li><code>/</code><ul><li><span class='atom'>\(1\)</span></li><li><code>+</code><ul>" .
            "<li><span class='atom'>\(x\)</span></li><li><span class='atom'>\(1\)</span></li></ul></li></ul>" .
            "</li></ul></li></ul>", $at2->get_rendered());

        $at2 = castext2_evaluatable::make_from_source("{@disptree(2*matrix([a,b],[c,d]))@}", 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();
        $this->assertEquals("<ul class='tree'><li><code>*</code><ul><li><span class='atom'>\(2\)</span></li>" .
            "<li><span class='atom'>\(\left[\begin{array}{cc} a & b \\\\ c & d \\end{array}\\right]\)</span></li>" .
            "</ul></li></ul>", $at2->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_cas_keyval
     */
    public function test_display_tree_texput() {
        $options = new stack_options();
        $options->set_option('simplify', false);

        $vars = 'tree_texlist:union(tree_texlist,{"boo"});'.
                "\ntexput(boo, \"\\\\diamond\");";
        $at1 = new stack_cas_keyval($vars, $options, 123);
        $this->assertTrue($at1->get_valid());

        $cs2 = $at1->get_session();
        $at2 = castext2_evaluatable::make_from_source('{@disptree(boo(a,b))@}', 'test-case');
        $this->assertTrue($at2->get_valid());
        $cs2->add_statement($at2);
        $cs2->instantiate();

        $this->assertEquals("<ul class='tree'><li><span class='op'>\(\diamond\)</span><ul><li>" .
            "<span class='atom'>\(a\)</span></li><li><span class='atom'>\(b\)</span></li></ul></li></ul>",
            $at2->get_rendered());
    }
}
