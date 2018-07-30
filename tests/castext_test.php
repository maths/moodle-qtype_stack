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

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/castext.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');

// Unit tests for {@link stack_cas_text}.

/**
 * @group qtype_stack
 */
class stack_cas_text_test extends qtype_stack_testcase {

    public function basic_castext_instantiation($strin, $sa, $val, $disp, $security = 's') {

        if (is_array($sa)) {
            $s1 = array();
            foreach ($sa as $s) {
                $s1[] = new stack_cas_casstring($s);
            }
            $cs1 = new stack_cas_session($s1, null, 0);
        } else {
            $cs1 = null;
        }

        $at1 = new stack_cas_text($strin, $cs1, 0, $security);
        $at1->get_valid();
        $this->assertEquals($val, $at1->get_valid());
        $this->assertEquals($disp, $at1->get_display_castext());
    }

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
                array('\[{@a*b@}\]', $a1, true, '\[{x^2\cdot \left(x+1\right)^2}\]'),
                array('{@', null, false, false),
                array('{@(x^2@}', null, false, false),
                array('{@1/0@}', null, true, '1/0'),
                array('\(1+{@1/0@}\)', null, true, '\(1+1/0\)'),
                array('{@x^2@}', $a2, false, null),
                // This last one looks very odd.  It records a change in v4.0 where we stop supporting dollars.
                array('$${@x^2@}$$', null, true, '$$\({x^2}\)$$'),
        );

        foreach ($cases as $case) {
            $this->basic_castext_instantiation($case[0], $case[1], $case[2], $case[3]);
        }
    }

    public function test_if_block() {
        $a1 = array('a:true', 'b:is(1>2)', 'c:false');
        // From iss309.
        $c = '[[ if test="false" ]]Alpha[[ elif test="true"]]Beta[[ elif test="false"]]Gamma'
                . '[[ else ]]Delta[[/ if]]';

        $cases = array(
                array('[[ if test="a" ]]ok1[[/ if ]]', $a1, true, "ok1"),
                array('[[ if test="b" ]]ok2[[/ if ]]', $a1, true, ""),
                array('[[ if test="b" ]]ok3[[else]]OK3[[/ if ]]', $a1, true, "OK3"),
                array('[[ if test="b" ]]ok4[[elif test="c"]]Ok4[[ else ]]OK4[[/ if ]]', $a1, true, "OK4"),
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

    public function test_if_block_error() {
        $a = array('a:true', 'b:is(1>2)');
        $cs = array();
        foreach ($a as $var) {
            $cs[] = new stack_cas_casstring($var);
        }
        $session = new stack_cas_session($cs, null, 0);

        $c = '[[ if test="a" ]][[ if ]]ok[[/ if ]][[/ if ]]';
        $ct = new stack_cas_text($c, $session, null);
        $ct->get_display_castext();
        $this->assertFalse($ct->get_valid());
        $this->assertEquals('<span class="error">CASText failed validation. </span> If-block needs a test attribute. ',
                $ct->get_errors(false));

        $c = '[[ if test="a" ]][[else]]a[[elif test="b"]]b[[/ if ]]';
        $ct = new stack_cas_text($c, $session, null);
        $ct->get_display_castext();
        $this->assertFalse($ct->get_valid());
        $this->assertEquals('<span class="error">CASText failed validation. </span> PARSE ERROR: "elif" after '
                . 'an "else" in an if block.', $ct->get_errors(false));

        $c = '[[ if test="a" ]][[else]]a[[else]]b[[/ if ]]';
        $ct = new stack_cas_text($c, $session, null);
        $ct->get_display_castext();
        $this->assertFalse($ct->get_valid());
        $this->assertEquals('<span class="error">CASText failed validation. </span> PARSE ERROR: Multiple else '
                . 'branches in an if block.', $ct->get_errors(false));
    }

    public function test_broken_block_error() {
        $a = array('a:true', 'b:is(1>2)');
        $cs = array();
        foreach ($a as $var) {
            $cs[] = new stack_cas_casstring($var);
        }
        $session = new stack_cas_session($cs, null, 0);

        $c = '[[ if test="a" ]][[ if ]]ok[[/ if ]]';
        $ct = new stack_cas_text($c, $session, null);
        $ct->get_display_castext();
        $this->assertFalse($ct->get_valid());
        $this->assertEquals('<span class="error">CASText failed validation. </span>If-block needs a test attribute. '.
                                                      " PARSE ERROR: '[[ if ]]' has no match. ", $ct->get_errors(false));
    }

    public function test_broken_block_error2() {
        $a = array('a:true', 'b:is(1>2)');
        $cs = array();
        foreach ($a as $var) {
            $cs[] = new stack_cas_casstring($var);
        }
        $session = new stack_cas_session($cs, null, 0);

        // None of these should match not even that last bar.
        $c = '[[ foo ]][[/bar]][[bar]][[/foo]][[/bar]]';
        $ct = new stack_cas_text($c, $session);
        $ct->get_display_castext();
        $this->assertFalse($ct->get_valid());
        $this->assertEquals('<span class="error">CASText failed validation. </span>'.
                "PARSE ERROR: '[[ foo ]]' has no match. <br/>'[[/ bar ]]' has no match. <br/>'[[ bar ]]' has no match. <br/>".
                "'[[/ foo ]]' has no match. <br/>'[[/ bar ]]' has no match. ", $ct->get_errors(false));
    }

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

    public function test_not_confused_by_pluginfile() {
        $ct = new stack_cas_text('Here {@x@} is some @@PLUGINFILE@@ {@x + 1@} some input', null, 0);
        $this->assertTrue($ct->get_valid());
        $this->assertEquals(array('x', 'x + 1'), $ct->get_all_raw_casstrings());
        $this->assertEquals('Here \({x}\) is some @@PLUGINFILE@@ \({x+1}\) some input', $ct->get_display_castext());
    }

    public function test_not_confused_by_pluginfile_real_example() {
        $realexample = '<p><img style="display: block; margin-left: auto; margin-right: auto;" ' .
                'src="@@PLUGINFILE@@/inclined-plane.png" alt="" width="164" height="117" /></p>';
        $ct = new stack_cas_text($realexample);
        $this->assertTrue($ct->get_valid());
        $this->assertEquals(array(), $ct->get_all_raw_casstrings());
        $this->assertEquals($realexample, $ct->get_display_castext());
    }

    public function test_get_all_raw_casstrings() {
        $raw = 'Take {@x^2+2*x@} and then {@sin(z^2)@}.';
        $at1 = new stack_cas_text($raw, null, 0);
        $val = array('x^2+2*x', 'sin(z^2)');
        $this->assertEquals($val, $at1->get_all_raw_casstrings());
    }

    public function test_get_all_raw_casstrings_if() {
        $raw = 'Take {@x^2+2*x@} and then [[ if test="true"]]{@sin(z^2)@}[[/if]].';
        $at1 = new stack_cas_text($raw, null, 0);
        $val = array('x^2+2*x', 'sin(z^2)', 'true');
        $this->assertEquals($val, $at1->get_all_raw_casstrings());
    }

    public function test_get_all_raw_casstrings_foreach() {
        $raw = 'Take {@x^2+2*x@} and then [[ foreach t="[1,2,3]"]]{@t@}[[/foreach]].';
        $at1 = new stack_cas_text($raw, null, 0);
        // Here the list is iterated over and the t-variable appears multiple times.
        $val = array('x^2+2*x', 't', 't:[1,2,3]');
        $this->assertEquals($val, $at1->get_all_raw_casstrings());
    }

    public function test_get_all_raw_casstrings_empty() {
        $raw = 'Take some text without cas commands.';
        $at1 = new stack_cas_text($raw, null, 0);
        $val = array();
        $this->assertEquals($val, $at1->get_all_raw_casstrings());
    }

    public function test_get_all_raw_casstrings_session() {

        $sa = array('p:diff(sans)', 'q=int(tans)');
        foreach ($sa as $s) {
            $cs    = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $cs1 = new stack_cas_session($s1, null, 0);

        $raw = 'Take {@ 1/(1+x^2) @} and then {@sin(z^2)@}.';
        $at1 = new stack_cas_text($raw, $cs1, 0);
        $val = array('p:diff(sans)', 'q=int(tans)', '1/(1+x^2)', 'sin(z^2)');
        $this->assertEquals($val, $at1->get_all_raw_casstrings());

    }

    public function check_external_forbidden_words($ct, $val, $words) {

        $a2 = array('a:x^2)', 'b:(sin(x)+1)^2');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = new stack_cas_casstring($s);
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text($ct, $cs2, 0);
        $this->assertEquals($val, $at1->check_external_forbidden_words($words));

    }

    public function test_auto_generated_key_names() {

        $a2 = array('a:x^2', 'caschat0:x^3');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text("This is some text {@x^2@}, {@x^3@}", $cs2, 0);
        $at1->get_display_castext();
        $session = $at1->get_session();
        $this->assertEquals(array('a', 'caschat0', 'caschat1', 'caschat2'), $session->get_all_keys());
    }

    public function test_redefine_variables() {
        // Notice this means that within a session the value of n has to be returned at every stage....
        $at1 = new stack_cas_text(
                'Let \(n\) be defined by \({@n:3@}\). Now add one to get \({@n:n+1@}\) and square the result \({@n:n^2@}\).',
                null, 0);
        $this->assertEquals('Let \(n\) be defined by \({3}\). Now add one to get \({4}\) and square the result \({16}\).',
                $at1->get_display_castext());
    }

    public function testcheck_external_forbidden_words() {
        $cases = array(
            array('', false, array()),
            array('$\sin(x)$', false, array()),
            array('$\cos(x)$', false, array('cos')),
            array('{@cos(x)@}', true, array('cos')),
            array('$\cos(x)$', true, array('sin')), // The session already has sin(x) above!
        );

        foreach ($cases as $case) {
            $this->check_external_forbidden_words($case[0], $case[1], $case[2]);
        }
    }

    public function test_fact_sheets() {
        $cs2 = new stack_cas_session(array(), null, 0);
        $at1 = new stack_cas_text("[[facts:calc_diff_linearity_rule]]", $cs2, 0);
        $output = stack_maths::process_display_castext($at1->get_display_castext());

        $this->assertContains(stack_string('calc_diff_linearity_rule_name'), $output);
        $this->assertContains(stack_string('calc_diff_linearity_rule_fact'), $output);
    }

    public function test_bad_variablenames() {
        $cs = new stack_cas_session(array(), null, 0);
        $rawcastext = '\[\begin{array}{rcl} & =& {@Ax2@} + {@double_cAx@} + {@c2A@} + {@Bx2@} + {@cBx@} + {@Cx@},\\ & =' .
                '& {@ApBx2@} + {@xterm@} + {@c2A@}. \end{array}\] Matching coefficients \[\begin{array}{rcl} A + B& =' .
                '& {@a@}\,\\ {@double_cA + cB@} + C& =& 0,\\ {@Ac2@}& =& {@b@}. \end{array}\]';
        $at1 = new stack_cas_text($rawcastext, $cs, 0, 't', false, 0);

        $this->assertFalse($at1->get_valid());
        $this->assertEquals('<span class="error">CASText failed validation. </span> ' .
                        'CAS commands not valid.  You seem to be missing * characters. Perhaps you meant to type ' .
                        '<span class="stacksyntaxexample">c2<font color="red">*</font>A</span>.',
                        $at1->get_errors());
    }

    public function test_assignmatrixelements() {
        // Assign a value to matrix entries.
        $cs = array('A:matrix([1,2],[1,1])', 'A[1,2]:3');

        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);

        $at1 = new stack_cas_text("{@A@}", $at1, 0);
        $at1->get_display_castext();

        $this->assertEquals('\({\left[\begin{array}{cc} 1 & 3 \\\\ 1 & 1 \end{array}\right]}\)', $at1->get_display_castext());
    }

    public function test_assignmatrixelements_p1() {
        // Assign a value to matrix entries.
        $cs = array('A:matrix([1,2],[1,1])', 'A[1,2]:3');

        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $options = new stack_options();
        $options->set_option('matrixparens', '(');
        $at1 = new stack_cas_session($s1, $options, 0);

        $at1 = new stack_cas_text("{@A@}", $at1, 0);
        $at1->get_display_castext();

        $this->assertEquals('\({\left(\begin{array}{cc} 1 & 3 \\\\ 1 & 1 \end{array}\right)}\)', $at1->get_display_castext());
    }

    public function test_assignmatrixelements_p2() {
        // Assign a value to matrix entries.
        $cs = array('A:matrix([1,2],[1,1])', 'A[1,2]:3');

        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $options = new stack_options();
        $options->set_option('matrixparens', '');
        $at1 = new stack_cas_session($s1, $options, 0);

        $at1 = new stack_cas_text("{@A@}", $at1, 0);
        $at1->get_display_castext();

        $this->assertEquals('\({\begin{array}{cc} 1 & 3 \\\\ 1 & 1 \end{array}}\)', $at1->get_display_castext());
    }

    public function test_plot() {

        $a2 = array('p:x^3');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text("This is some text {@plot(p, [x,-2,3])@}", $cs2, 0);
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $session = $at1->get_session();
        $this->assertEquals(array('p', 'caschat0'), $session->get_all_keys());

        $this->assertTrue(is_int(strpos($at1->get_display_castext(),
                ".svg' alt='STACK auto-generated plot of x^3 with parameters [[x,-2,3]]'")));
    }

    public function test_plot_alttext() {

        $a2 = array('p:sin(x)');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        // Note, since we have spaces in the string we currently need to validate this as the teacher....
        $at1 = new stack_cas_text('This is some text {@plot(p, [x,-2,3], [alt,"Hello World!"])@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $session = $at1->get_session();
        $this->assertEquals(array('p', 'caschat0'), $session->get_all_keys());
        $this->assertTrue(is_int(strpos($at1->get_display_castext(), ".svg' alt='Hello World!'")));
    }

    public function test_plot_alttext_error() {

        $a2 = array('p:sin(x)');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        // Alt tags must be a string.
        $at1 = new stack_cas_text('This is some text {@plot(p,[x,-2,3],[alt,x])@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $session = $at1->get_session();
        $this->assertEquals(array('p', 'caschat0'), $session->get_all_keys());
        $this->assertTrue(is_int(strpos($at1->get_errors(), "Plot error: the alt tag definition must be a string, but is not.")));
    }

    public function test_plot_small() {

        $a2 = array('p:sin(x)');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text('A small plot: {@plot(p, [x,-2,3], [size,200,100])@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $session = $at1->get_session();
        $this->assertTrue(is_int(strpos($at1->get_display_castext(), "width='200'")));
    }

    public function test_plot_nottags() {

        $a2 = array('p:sin(x)');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text('A tag-less plot: {@plot(p, [x,-2,3], [plottags,false])@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $session = $at1->get_session();
        $this->assertFalse(is_int(strpos($at1->get_display_castext(), "<div class='stack_plot'>")));
    }

    public function test_plot_option_error() {

        $cs2 = new stack_cas_session(array(), null, 0);

        // Alt tags must be a string.
        $at1 = new stack_cas_text('This is some text {@plot(x^2,[x,-2,3],[notoption,""])@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $session = $at1->get_session();
        $this->assertEquals(array('caschat0'), $session->get_all_keys());
        $this->assertTrue(is_int(strpos($at1->get_errors(),
                "Plot error: STACK does not currently support the following plot2d options:")));
    }

    public function test_currency_1() {

        $at1 = new stack_cas_text('This is system cost \$100,000 to create.', null, 0, 't');
        $this->assertTrue($at1->get_valid());
    }

    public function test_forbidden_words() {

        $at1 = new stack_cas_text('This is system cost {@system(rm*)@} to create.', null, 0, 't');
        $this->assertFalse($at1->get_valid());
        $this->assertEquals('<span class="error">CASText failed validation. </span>CAS commands not valid.  ' .
                'The expression <span class="stacksyntaxexample">system</span> is forbidden.', $at1->get_errors());
    }

    public function test_mathdelimiters1() {
        $a2 = array('a:2');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text('\begin{align*} x & = {@a@}+1 \\ & = {@a+1@} \end{align*}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals('\begin{align*} x & = {2}+1 \ & = {3} \end{align*}', $at1->get_display_castext());
    }

    public function test_mathdelimiters2() {
        $a2 = array('a:x^2/(1+x^2)^3', 'p:diff(a,x)');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text('\begin{multline*} {@a@} \\\\ {@p@} \end{multline*}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals(
                '\begin{multline*} {\frac{x^2}{\left(x^2+1\right)^3}} \\\\ ' .
                '{\frac{2\cdot x}{\left(x^2+1\right)^3}-\frac{6\cdot x^3}{\left(x^2+1\right)^4}} \end{multline*}',
                $at1->get_display_castext());
    }

    public function test_disp_decimalplaces() {
        // The function dispdp only holds the number of decimal places to display.  It does not do rounding.
        // Use dispsf for rounding.
        $a2 = array('a:float(%e)', 'b:3.99999');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text('{@dispdp(a,2)@}, {@dispdp(b,3)@}, {@dispsf(b,4)@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals('\({2.72}\), \({4.000}\), \({4.000}\)', $at1->get_display_castext());
    }

    public function test_disp_decimalplaces2() {
        $a2 = array('a:float(%e)', 'b:-3.99999');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text('{@dispdp(a,0)*x^2@}, {@dispdp(b,3)@}, {@dispsf(b,4)@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals('\({3\cdot x^2}\), \({-4.000}\), \({-4.000}\)', $at1->get_display_castext());
    }

    public function test_disp_mult_blank() {
        $a2 = array('make_multsgn("blank")', 'b:x*y');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);
        $this->assertTrue($cs2->get_valid());

        $at1 = new stack_cas_text('{@b@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals('\({x\, y}\)', $at1->get_display_castext());
    }

    public function test_disp_mult_dot() {
        $a2 = array('make_multsgn("dot")', 'b:x*y');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);
        $this->assertTrue($cs2->get_valid());

        $at1 = new stack_cas_text('{@b@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals('\({x\cdot y}\)', $at1->get_display_castext());
    }

    public function test_disp_mult_cross() {
        $a2 = array('make_multsgn("cross")', 'b:x*y');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);
        $this->assertTrue($cs2->get_valid());

        $at1 = new stack_cas_text('{@b@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals('\({x\times y}\)', $at1->get_display_castext());
    }

    public function test_disp_equiv_natural_domain() {
        $a2 = array('ta:[1/(x-1)+1/(x+1),2*x/(x^2-1)]');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);
        $this->assertTrue($cs2->get_valid());

        $cs1 = '\[ {@stack_disp_arg(ta)@} \] \[ {@stack_disp_arg(ta,false)@} \] ' .
            '\[ {@stack_disp_arg(ta,true,false)@} \] \[ {@stack_disp_arg(ta,false,false)@} \]';
        $at1 = new stack_cas_text($cs1, $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals('\[ {\begin{array}{lll} &\frac{1}{x+1}+\frac{1}{x-1}&' .
            '{\color{blue}{{x \not\in {\left \{-1 , 1 \right \}}}}}\cr \color{green}' .
            '{\Leftrightarrow}&\frac{2\cdot x}{x^2-1}&{\color{blue}{{x \not\in {\left \{-1 , 1 \right \}}}}}' .
            '\cr \end{array}} \] \[ {\begin{array}{lll}\frac{1}{x+1}+\frac{1}{x-1}&' .
            '{\color{blue}{{x \not\in {\left \{-1 , 1 \right \}}}}}\cr \frac{2\cdot x}{x^2-1}&{\color{blue}' .
            '{{x \not\in {\left \{-1 , 1 \right \}}}}}\cr \end{array}} \] ' .
            '\[ {\begin{array}{lll} &\frac{1}{x+1}+\frac{1}{x-1}& \cr \color{green}{\Leftrightarrow}&' .
            '\frac{2\cdot x}{x^2-1}& \cr \end{array}} \] ' .
            '\[ {\begin{array}{lll}\frac{1}{x+1}+\frac{1}{x-1}& \cr \frac{2\cdot x}{x^2-1}& \cr \end{array}} \]',
            $at1->get_display_castext());
    }

    public function test_disp_ode1() {
        $at1 = new stack_cas_keyval("p1:'diff(y,x,2)+2*y = 0;p2:ev('diff(y,x,2),simp)+2*ev('diff(y,x,2,z,3),simp) = 0;",
                null, 123, 't', true, 0);
        $this->assertTrue($at1->get_valid());

        $at2 = new stack_cas_text('\[{@p1@}\] \[{@p2@}\]', $at1->get_session(), 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals(
                '\[{\frac{\mathrm{d}^2 y}{\mathrm{d} x^2}+2\cdot y=0}\] ' .
                '\[{2\cdot \left(\frac{\mathrm{d}^5 y}{\mathrm{d} x^2 \mathrm{d} z^3}\right)' .
                '+\frac{\mathrm{d}^2 y}{\mathrm{d} x^2}=0}\]',
                $at2->get_display_castext());
    }

    public function test_disp_ode2() {
        $vars = "derivabbrev:true;p1:'diff(y,x,2)+2*y = 0;p2:ev('diff(y,x,2),simp)+2*ev('diff(y,x,2,z,3),simp) = 0;";
        $at1 = new stack_cas_keyval($vars, null, 123, 't', true, 0);
        $this->assertTrue($at1->get_valid());

        $at2 = new stack_cas_text('\[{@p1@}\] \[{@p2@}\]', $at1->get_session(), 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals(
                '\[{y_{x x}+2\cdot y=0}\] \[{2\cdot y_{x x z z z}+y_{x x}=0}\]',
                $at2->get_display_castext());
    }

    public function test_disp_int() {
        $vars = "foo:'int(f(x),x)";
        $at1 = new stack_cas_keyval($vars, null, 123, 't', true, 0);
        $this->assertTrue($at1->get_valid());

        $at2 = new stack_cas_text('\[{@foo@}\]', $at1->get_session(), 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals(
                '\[{\int {f\left(x\right)}{\;\mathrm{d}x}}\]',
                $at2->get_display_castext());
    }

    public function test_strings_in_castext() {
        $vars = "st1:[\"\;\sin(x^2)\",\"\;\cos(x^2)\"]\n/* And a comment: with LaTeX \;\sin(x) */\n a:3;";
        $at1 = new stack_cas_keyval($vars, null, 123, 't', true, 0);
        $this->assertTrue($at1->get_valid());

        $at2 = new stack_cas_text('\[{@a@}\]', $at1->get_session(), 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals('\[{3}\]', $at2->get_display_castext());
    }

    public function test_strings_in_castext_escaped() {
        $vars = 'st:"This is a string with escaped \" strings...."';
        $at1 = new stack_cas_keyval($vars, null, 123, 't', true, 0);
        $this->assertTrue($at1->get_valid());

        $at2 = new stack_cas_text('\[{@st@}\]', $at1->get_session(), 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals('\[This is a string with escaped " strings....\]',
                $at2->get_display_castext());
    }

    public function test_strings_only() {
        $s = '{@"This is a string"@} whereas this is empty |{@""@}|. Not quite empty |{@" "@}|.';

        $at2 = new stack_cas_text($s, null, 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals(
                'This is a string whereas this is empty ||. Not quite empty | |.',
                $at2->get_display_castext());
    }

    public function test_strings_only_latex() {
        // Remember the quotes below are escaped!
        $s = '{@"This is a string with LaTeX in it \\\\(\\\\pi\\\\)."@}';

        $at2 = new stack_cas_text($s, null, 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals(
                'This is a string with LaTeX in it \\(\\pi\\).',
                $at2->get_display_castext());
    }

    public function test_strings_embeded() {
        $s = '{@"This is a string"+x^2@}.';

        $at2 = new stack_cas_text($s, null, 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals(
                '\({x^2+\mbox{This is a string}}\).',
                $at2->get_display_castext());
    }

    public function test_numerical_display_float_default() {
        // The number 0.000001 used to be tested, but it was giving weird results.
        // On some versions of Maxima, including the latest, it comes back as
        // 10.0e-7, instead of 1.0e-6. Other versions get it right. I did not like
        // a testcase that asserted weird behaviour (10.0e-7) so I removed it.
        $s = 'Decimal numbers {@0.1@}, {@0.01@}, {@0.001@}, {@0.0001@}, {@0.00001@}.';

        $at2 = new stack_cas_text($s, null, 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertContentWithMathsEquals(
            'Decimal numbers \({0.1}\), \({0.01}\), \({0.001}\), \({1.0e-4}\), \({1.0e-5}\).',
            $at2->get_display_castext());
    }

    public function test_numerical_display_float_decimal() {
        $st = 'Decimal numbers {@0.1@}, {@0.01@}, {@0.001@}, {@0.0001@}, {@0.00001@}, {@0.000001@}.';

        $a2 = array('stackfltfmt:"~f"');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at2 = new stack_cas_text($st, $cs2, 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals(
                'Decimal numbers \({0.1}\), \({0.01}\), \({0.001}\), \({0.0001}\), \({0.00001}\), \({0.000001}\).',
                $at2->get_display_castext());
    }

    public function test_numerical_display_float_scientific() {
        // The number 0.000001 is handled below, so we can skip on old Maxima where it fails.
        $st = 'Decimal numbers {@0.1@}, {@0.01@}, {@0.001@}, {@0.0001@}, {@0.00001@}.';

        $a2 = array('stackfltfmt:"~e"');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at2 = new stack_cas_text($st, $cs2, 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertContentWithMathsEquals(
                'Decimal numbers \({1.0e-1}\), \({1.0e-2}\), \({1.0e-3}\), \({1.0e-4}\), \({1.0e-5}\).',
                $at2->get_display_castext());
    }

    public function test_numerical_display_float_scientific_small() {
        // On old Maxima, you get back \(9.999999999999999e-7\).
        $this->skip_if_old_maxima('5.32.1');

        $st = 'Decimal number {@0.000001@}.';

        $a2 = array('stackfltfmt:"~e"');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at2 = new stack_cas_text($st, $cs2, 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertContentWithMathsEquals(
                'Decimal number \({1.0e-6}\).',
                $at2->get_display_castext());
    }

    public function test_numerical_display_1() {
        $s = 'The decimal number {@n:73@} is written in base \(2\) as {@(stackintfmt:"~2r",n)@}, in base \(7\) ' .
            'as {@(stackintfmt:"~7r",n)@}, in scientific notation as {@(stackintfmt:"~e",n)@} ' .
            'and in rhetoric as {@(stackintfmt:"~r",n)@}.';

        $at2 = new stack_cas_text($s, null, 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertContentWithMathsEquals(
                'The decimal number \({73}\) is written in base \(2\) as \({1001001}\), in base \(7\) as \({133}\), ' .
                'in scientific notation as \({7.3e+1}\) and in rhetoric as \({seventy-three}\).',
                $at2->get_display_castext());
    }

    public function test_numerical_display_binary() {
        $st = 'The number {@73@} is written in base \(2\).';

        $a2 = array('stackintfmt:"~b"');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at2 = new stack_cas_text($st, $cs2, 0, 't');

        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals(
                'The number \({1001001}\) is written in base \(2\).',
                $at2->get_display_castext());
    }

    public function test_inline_fractions() {
        $s = '{@(stack_disp_fractions("i"), 1/x)@} {@(stack_disp_fractions("d"), 1/x)@} {@(stack_disp_fractions("i"), 1/x)@}';

        $at2 = new stack_cas_text($s, null, 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals($at2->get_display_castext(),
                '\({{1}/{x}}\) \({\frac{1}{x}}\) \({{1}/{x}}\)');
    }

    public function test_inline_fractions_all() {
        $st = '{@1/x@}, {@1/x^2@}, {@1/(a+x)@}, {@1/(2*a)@}, {@1/sin(x+y)@}.';

        $a2 = array('stack_disp_fractions("i")');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at2 = new stack_cas_text($st, $cs2, 0, 't');
        $this->assertTrue($at2->get_valid());
        $at2->get_display_castext();

        $this->assertEquals('\({{1}/{x}}\), \({{1}/{x^2}}\), \({{1}/{\left(x+a\right)}}\), \({{1}/{\left(2\cdot a\right)}}\),'
              . ' \({{1}/{\sin \left( y+x \right)}}\).', $at2->get_display_castext());
    }

    public function test_disp_greek() {
        $a2 = array('a:Delta', 'b:sin(Delta^2)', 'c:delta', 't:theta');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('s');
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text('{@a@}, {@b@}, {@c@}, {@t@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals($at1->get_display_castext(), '\({\Delta}\), \({\sin \left( \Delta^2 \right)}\), ' .
                '\({\delta}\), \({\theta}\)');
    }

    public function test_subscripts() {
        $a2 = array('a:texsub(v, 2*alpha)', 'b:texsub(v, texsub(m, n))');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text('{@a@}, {@b@}, {@beta47@}, {@beta_47@}',
            $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals('\({{v}_{2\cdot \alpha}}\), \({{v}_{{m}_{n}}}\), '.
            '\({{\it beta_{47}}}\), \({{\beta}_{47}}\)', $at1->get_display_castext());
    }

    public function test_maxima_arrays() {
        $a2 = array('p1:a[2]', 'p2:a[n+1]', 'p3:a[b_c]');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text('{@p1@}, {@p2@}, {@p3@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals('\({a_{2}}\), \({a_{n+1}}\), \({a_{{b}_{c}}}\)',
                $at1->get_display_castext());

        $this->assertEquals('a[n+1]',
            $cs2->get_value_key('p2'));
    }

    public function test_length() {
        $a2 = array('f(x):=length(x)', 'b:[1,2,3]', 'c:f(b)');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text('{@c@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals('\({3}\)', $at1->get_display_castext());
    }

    public function test_lambda() {
        $a2 = array('sf: lambda([x,n],significantfigures(x,n))',
            'n:[3.1234,1]', 'm:apply(sf,n)');
        $s2 = array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $this->assertTrue($cs->get_valid());
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text('{@sf@}, {@m@}', $cs2, 0, 't');
        $this->assertTrue($at1->get_valid());
        $at1->get_display_castext();

        $this->assertEquals('\({\lambda\left(\left[ x , n \right]  , {\it significantfigures}\left(x , n\right)\right)}\), \({3}\)',
            $at1->get_display_castext());
    }


    public function test_odd_logic_eval() {
        // First we have a session. That comes from keyval like question-vars.
        $kv = new stack_cas_keyval('a:true;b:is(1>2);c:false');
        $s = $kv->get_session(); // This does a validation on the side.

        // Take '[[ if test="b" ]]ok4[[elif test="c"]]Ok4[[ else ]]OK4[[/ if ]]' as the castext.
        // Then we start to add some new variables into it as the castext is evaluated.
        // First the conditions that have been extracted from a if-elif-else construct during the "compilation"-step.
        $s->add_vars(array(new stack_cas_casstring('stackparsecond8:b')));
        $s->add_vars(array(new stack_cas_casstring('stackparsecond9:not (stackparsecond8) and (c)')));
        $s->add_vars(array(new stack_cas_casstring('stackparsecond10:not (stackparsecond9)')));

        // After that the if-blocks will map those definitions to their own conditions.
        $s->add_vars(array(new stack_cas_casstring('caschat0:stackparsecond8')));
        $s->add_vars(array(new stack_cas_casstring('caschat1:stackparsecond9')));
        $s->add_vars(array(new stack_cas_casstring('caschat2:stackparsecond10')));

        // Now lets instantiate.
        $s->instantiate();

        $this->assertEquals('false', $s->get_value_key('caschat0'));
        $this->assertEquals('false', $s->get_value_key('caschat1'));
        $this->assertEquals('true', $s->get_value_key('caschat2'));
    }

}
