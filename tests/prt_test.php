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

use castext2_static_replacer;
use prt_evaluatable;
use qtype_stack_testcase;
use stack_cas_security;
use stack_cas_session2;
use stack_options;
use stack_potentialresponse_tree_lite;
use stack_secure_loader;
use stack_utils;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../stack/cas/secure_loader.class.php');
require_once(__DIR__ . '/../stack/prt.class.php');
require_once(__DIR__ . '/../stack/prt.evaluatable.class.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');

// Unit tests for stack_potentialresponse_tree_lite, including the prt_evaluatable.
//
// @copyright 2022 The University of Edinburgh.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \stack_potentialresponse_tree_lite
 */

class prt_test extends qtype_stack_testcase {

    private function create_default_node() {
        $node = new stdClass();
        $node->id                  = '';
        $node->nodename            = '';
        $node->quiet               = false;
        $node->answertest          = 'AlgEquiv';
        $node->sans                = '';
        $node->tans                = '';
        $node->testoptions         = '';

        $node->falsenextnode       = -1;
        $node->falseanswernote     = '';
        $node->falsescore          = 0;
        $node->falsescoremode      = '=';
        $node->falsepenalty        = null;
        $node->falsefeedback       = '';
        $node->falsefeedbackformat = FORMAT_HTML;

        $node->truenextnode        = -1;
        $node->trueanswernote      = '';
        $node->truescore           = 1;
        $node->truescoremode       = '=';
        $node->truepenalty         = null;
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;

        return $node;
    }

    public function test_single_node_prt() {

        $newprt = new stdClass;
        $newprt->name = 'testprt';
        $newprt->id = '0';
        $newprt->value = 5;
        $newprt->feedbackstyle = 1;
        $newprt->feedbackvariables = null;
        $newprt->firstnodename = '0';
        $newprt->nodes = [];
        $newprt->autosimplify = true;

        $node = $this->create_default_node();
        $node->id              = '0';
        $node->sans            = 'sans';
        $node->tans            = '(x+1)^3/3+c';
        $node->answertest      = 'Int';
        $node->testoptions     = 'x';
        $node->truefeedback    = 'Yeah!';
        $node->trueanswernote  = '1-0-1';
        $node->falsefeedback   = 'Boo!';
        $node->falseanswernote = '1-0-0';
        $newprt->nodes[] = $node;

        $prt = new stack_potentialresponse_tree_lite($newprt, 5);

        $this->assertFalse($prt->is_formative());
        $this->assertEquals(array('Int' => true), $prt->get_answertests());
        $expected = array('NULL' => 'NULL', '1-0-1' => '1-0-1', '1-0-0' => '1-0-0');
        $this->assertEquals($expected, $prt->get_all_answer_notes());

        // For $inputs we only need the names of the inputs, not the full inputs.
        $inputs = array('sans' => true);
        $boundvars = array();
        $defaultpenalty = 0.1;
        $security = new stack_cas_security();
        $pathprefix = '/p/' . '0';
        $sig = $prt->compile($inputs, $boundvars, $defaultpenalty, $security, $pathprefix, null);

        // Test 1 - a correct answer.
        $inputs = array('sans' => '(x+1)^3/3+c');

        $session = new stack_cas_session2([], new stack_options(), 123);
        // Add preamble from PRTs as well.
        if ($sig['be'] != '') {
            $session->add_statement(new stack_secure_loader($sig['be'], 'preamble PRT: ' . $prt->get_name()));
        }
        if ($sig['cv'] != '') {
            $session->add_statement(new stack_secure_loader($sig['cv'], 'contextvariables PRT: ' . $prt->get_name()));
        }
        // The prt definition itself.
        $session->add_statement(new stack_secure_loader($sig['def'], 'definition PRT: ' . $prt->get_name()));
        // Suppress simplification of raw inputs.
        $session->add_statement(new stack_secure_loader('simp:false', 'input-simplification'));
        $is = '_INPUT_STRING:["stack_map"';
        foreach ($inputs as $key => $value) {
            $session->add_statement(new stack_secure_loader($key . ':' . $value, 'input ' . $key));
            $is .= ',[' . stack_utils::php_string_to_maxima_string($key) . ',';
            if (strpos($value, 'ev(') === 0) { // Unpack the value if we have simp...
                $is .= stack_utils::php_string_to_maxima_string(mb_substr($value, 3, -6)) . ']';
            } else {
                $is .= stack_utils::php_string_to_maxima_string($value) . ']';
            }
        }
        $is .= ']';
        $session->add_statement(new stack_secure_loader($is, 'input-strings'));
        $prtev = new prt_evaluatable($sig['sig'], 1, new castext2_static_replacer([]), $prt->get_trace());
        $session->add_statement(new stack_secure_loader('simp:false', 'prt-simplification'));
        $session->add_statement($prtev);
        $session->instantiate();

        $this->assertEquals(1, $prtev->get_score());
        $expected = 'Yeah!';
        $this->assertEquals($expected, $prtev->get_feedback());
        $this->assertEquals(array('ATInt_true.', '1-0-1'), $prtev->get_answernotes());
        $expected = array('ATInt(sans,(x+1)^3/3+c,ev(x,simp));', '/* ------------------- */',
            'prt_testprt(sans);');
        $this->assertEquals($expected, $prtev->get_trace());

        // Test 2 - an incorrect answer.
        $inputs = array('sans' => '(x+1)^3/3');

        $session = new stack_cas_session2([], new stack_options(), 123);
        // Add preamble from PRTs as well.
        if ($sig['be'] != '') {
            $session->add_statement(new stack_secure_loader($sig['be'], 'preamble PRT: ' . $prt->get_name()));
        }
        if ($sig['cv'] != '') {
            $session->add_statement(new stack_secure_loader($sig['cv'], 'contextvariables PRT: ' . $prt->get_name()));
        }
        // The prt definition itself.
        $session->add_statement(new stack_secure_loader($sig['def'], 'definition PRT: ' . $prt->get_name()));
        // Suppress simplification of raw inputs.
        $session->add_statement(new stack_secure_loader('simp:false', 'input-simplification'));
        $is = '_INPUT_STRING:["stack_map"';
        foreach ($inputs as $key => $value) {
            $session->add_statement(new stack_secure_loader($key . ':' . $value, 'input ' . $key));
            $is .= ',[' . stack_utils::php_string_to_maxima_string($key) . ',';
            if (strpos($value, 'ev(') === 0) { // Unpack the value if we have simp...
                $is .= stack_utils::php_string_to_maxima_string(mb_substr($value, 3, -6)) . ']';
            } else {
                $is .= stack_utils::php_string_to_maxima_string($value) . ']';
            }
        }
        $is .= ']';
        $session->add_statement(new stack_secure_loader($is, 'input-strings'));
        $prtev = new prt_evaluatable($sig['sig'], 1, new castext2_static_replacer([]), $prt->get_trace());
        $session->add_statement(new stack_secure_loader('simp:false', 'prt-simplification'));
        $session->add_statement($prtev);
        $session->instantiate();

        $this->assertEquals(0, $prtev->get_score());
        $expected = 'You need to add a constant of integration, otherwise this appears to be correct. ' .
            'Well done. Boo!';
        $this->assertEquals($expected, $prtev->get_feedback());
        $this->assertEquals(array('ATInt_const.', '1-0-0'), $prtev->get_answernotes());
        $expected = array('ATInt(sans,(x+1)^3/3+c,ev(x,simp));', '/* ------------------- */',
            'prt_testprt(sans);');
        $this->assertEquals($expected, $prtev->get_trace());
    }

    public function test_multi_node_prt() {

        $newprt = new stdClass;
        $newprt->name = 'multiprt';
        $newprt->id = '0';
        $newprt->value = 5;
        $newprt->feedbackstyle = 1;
        // Opportunities for runtime errors in the PRT.
        $newprt->feedbackvariables = 'sa1:1/(2-ans1);';
        $newprt->firstnodename = '0';
        $newprt->nodes = [];
        $newprt->autosimplify = true;

        $node = $this->create_default_node();
        $node->id              = 0;
        $node->nodename        = '0';
        $node->sans            = 'sa1';
        $node->tans            = '1';
        $node->answertest      = 'AlgEquiv';
        $node->truefeedback    = 'Yeah!';
        $node->trueanswernote  = '1-0-1';
        $node->falsefeedback   = 'Wait for it...';
        $node->falseanswernote = '1-0-0';
        $node->falsenextnode   = 1;
        $newprt->nodes[] = $node;

        $node = $this->create_default_node();
        $node->id              = 1;
        $node->nodename        = '1';
        $node->sans            = '1/(1+ans1)';
        $node->tans            = '1/3';
        $node->answertest      = 'AlgEquiv';
        $node->truefeedback    = 'Yeah good!';
        $node->truescore       = 0.7;
        $node->trueanswernote  = '1-1-1';
        $node->falsefeedback   = 'Boo!';
        $node->falseanswernote = '1-1-0';
        $newprt->nodes[] = $node;

        $prt = new stack_potentialresponse_tree_lite($newprt, 5);

        $this->assertFalse($prt->is_formative());
        $this->assertEquals(array('AlgEquiv' => true), $prt->get_answertests());
        $expected = array('NULL' => 'NULL', '1-0-1' => '1-0-1', '1-0-0' => '1-0-0',
            '1-1-1' => '1-1-1', '1-1-0' => '1-1-0');
        $this->assertEquals($expected, $prt->get_all_answer_notes());

        // For $inputs we only need the names of the inputs, not the full inputs.
        $inputs = array('ans1' => true);
        $boundvars = array();
        $defaultpenalty = 0.1;
        $security = new stack_cas_security();
        $pathprefix = '/p/' . '0';
        $sig = $prt->compile($inputs, $boundvars, $defaultpenalty, $security, $pathprefix, null);

        // Test 1 - a correct answer.
        $inputs = array('ans1' => '2');

        $session = new stack_cas_session2([], new stack_options(), 123);
        // Add preamble from PRTs as well.
        if ($sig['be'] != '') {
            $session->add_statement(new stack_secure_loader($sig['be'], 'preamble PRT: ' . $prt->get_name()));
        }
        if ($sig['cv'] != '') {
            $session->add_statement(new stack_secure_loader($sig['cv'], 'contextvariables PRT: ' . $prt->get_name()));
        }
        // The prt definition itself.
        $session->add_statement(new stack_secure_loader($sig['def'], 'definition PRT: ' . $prt->get_name()));
        // Suppress simplification of raw inputs.
        $session->add_statement(new stack_secure_loader('simp:false', 'input-simplification'));
        $is = '_INPUT_STRING:["stack_map"';
        foreach ($inputs as $key => $value) {
            $session->add_statement(new stack_secure_loader($key . ':' . $value, 'input ' . $key));
            $is .= ',[' . stack_utils::php_string_to_maxima_string($key) . ',';
            if (strpos($value, 'ev(') === 0) { // Unpack the value if we have simp...
                $is .= stack_utils::php_string_to_maxima_string(mb_substr($value, 3, -6)) . ']';
            } else {
                $is .= stack_utils::php_string_to_maxima_string($value) . ']';
            }
        }
        $is .= ']';
        $session->add_statement(new stack_secure_loader($is, 'input-strings'));
        $prtev = new prt_evaluatable($sig['sig'], 1, new castext2_static_replacer([]), $prt->get_trace());
        $session->add_statement(new stack_secure_loader('simp:false', 'prt-simplification'));
        $session->add_statement($prtev);
        $session->instantiate();

        $this->assertEquals(0.7, $prtev->get_score());
        $expected = 'Wait for it... Yeah good!';
        $this->assertEquals($expected, $prtev->get_feedback());
        $this->assertEquals(array('1-0-0', '1-1-1'), $prtev->get_answernotes());
        $expected = array('sa1:1/(2-ans1);', '/* ------------------- */', 'ATAlgEquiv(sa1,1);',
            'ATAlgEquiv(1/(1+ans1),1/3);', '/* ------------------- */', 'prt_multiprt(ans1);');
        $this->assertEquals($expected, $prtev->get_trace());
    }
}
