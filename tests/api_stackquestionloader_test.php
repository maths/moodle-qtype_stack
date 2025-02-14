<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// Unit tests for the Stack question type API.
//
// @copyright 2023 University of Edinburgh.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/apifixtures.class.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '../../api/util/StackQuestionLoader.php');

use api\util\StackQuestionLoader;
use stack_api_test_data;
use qtype_stack_testcase;

/**
 * @group qtype_stack
 * @covers \qtype_stack
 */
class api_stackquestionloader_test extends qtype_stack_testcase {

    public function test_question_loader() {
        $xml = stack_api_test_data::get_question_string('matrices');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml)['question'];

        // Testing a representative selection of fields.
        $this->assertEquals('test_3_matrix', $question->name);
        $this->assertEquals('<p><span class="correct">Correct answer, well done.</span></p>', $question->prtcorrect);
        $this->assertEquals('html', $question->prtcorrectformat);
        $this->assertEquals('-1', $question->prts['prt1']->get_nodes_summary()[0]->truenextnode);
        $this->assertEquals('1-0-T ', $question->prts['prt1']->get_nodes_summary()[0]->trueanswernote);
        $this->assertEquals(10, $question->prts['prt1']->get_nodes_summary()[0]->truescore);
        $this->assertEquals('=', $question->prts['prt1']->get_nodes_summary()[0]->truescoremode);
        $this->assertEquals('1', $question->prts['prt1']->get_nodes_summary()[0]->falsenextnode);
        $this->assertEquals('1-0-F', $question->prts['prt1']->get_nodes_summary()[0]->falseanswernote);
        $this->assertEquals(0, $question->prts['prt1']->get_nodes_summary()[0]->falsescore);
        $this->assertEquals('=', $question->prts['prt1']->get_nodes_summary()[0]->falsescoremode);
        $this->assertEquals(true, $question->prts['prt1']->get_nodes_summary()[0]->quiet);
        $this->assertEquals('ATAlgEquiv(ans1,TA)', $question->prts['prt1']->get_nodes_summary()[0]->answertest);
        $this->assertContains(86, $question->deployedseeds);
        $this->assertContains(219862533, $question->deployedseeds);
        $this->assertContains(1167893775, $question->deployedseeds);
        $this->assertEquals(3, count($question->deployedseeds));
    }

    public function test_question_loader_use_defaults() {
        global $CFG;
        $xml = stack_api_test_data::get_question_string('usedefaults');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml)['question'];
        $this->assertEquals($question->options->get_option('decimals'), get_config('qtype_stack', 'decimals'));
        $this->assertEquals($question->options->get_option('scientificnotation'),
                get_config('qtype_stack', 'scientificnotation'));
        $this->assertEquals($question->options->get_option('assumepos'), get_config('qtype_stack', 'assumepositive'));
        $this->assertEquals($question->options->get_option('assumereal'), get_config('qtype_stack', 'assumereal'));
        $this->assertEquals($question->options->get_option('multiplicationsign'), get_config('qtype_stack', 'multiplicationsign'));
        $this->assertEquals($question->options->get_option('sqrtsign'), get_config('qtype_stack', 'sqrtsign'));
        $this->assertEquals($question->options->get_option('complexno'), get_config('qtype_stack', 'complexno'));
        $this->assertEquals($question->options->get_option('logicsymbol'), get_config('qtype_stack', 'logicsymbol'));
        $this->assertEquals($question->options->get_option('inversetrig'), get_config('qtype_stack', 'inversetrig'));
        $this->assertEquals($question->options->get_option('matrixparens'), get_config('qtype_stack', 'matrixparens'));
        $this->assertEquals($question->options->get_option('simplify'), get_config('qtype_stack', 'questionsimplify'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('mustVerify'), get_config('qtype_stack', 'inputmustverify'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('showValidation'),
                get_config('qtype_stack', 'inputshowvalidation'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('insertStars'), get_config('qtype_stack', 'inputinsertstars'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidFloats'),
                get_config('qtype_stack', 'inputforbidfloat'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('lowestTerms'),
                get_config('qtype_stack', 'inputrequirelowestterms'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('sameType'),
                get_config('qtype_stack', 'inputcheckanswertype'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidWords'), get_config('qtype_stack', 'inputforbidwords'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('boxWidth'), get_config('qtype_stack', 'inputboxsize'));
    }

    public function test_question_loader_do_not_use_defaults() {
        global $CFG;
        $xml = stack_api_test_data::get_question_string('optionset');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml)['question'];
        $this->assertEquals($question->options->get_option('decimals'), ',');
        $this->assertEquals($question->options->get_option('scientificnotation'), '*10');
        $this->assertEquals($question->options->get_option('assumepos'), true);
        $this->assertEquals($question->options->get_option('assumereal'), true);
        $this->assertEquals($question->options->get_option('multiplicationsign'), 'cross');
        $this->assertEquals($question->options->get_option('sqrtsign'), false);
        $this->assertEquals($question->options->get_option('complexno'), 'j');
        $this->assertEquals($question->options->get_option('logicsymbol'), 'symbol');
        $this->assertEquals($question->options->get_option('inversetrig'), 'acos');
        $this->assertEquals($question->options->get_option('matrixparens'), '(');
        $this->assertEquals($question->options->get_option('simplify'), false);
        $this->assertEquals($question->inputs['ans1']->get_parameter('mustVerify'), false);
        $this->assertEquals($question->inputs['ans1']->get_parameter('showValidation'), false);
        $this->assertEquals($question->inputs['ans1']->get_parameter('insertStars'), true);
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidFloats'), false);
        $this->assertEquals($question->inputs['ans1']->get_parameter('lowestTerms'), true);
        $this->assertEquals($question->inputs['ans1']->get_parameter('sameType'), true);
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidWords'), 'test');
        $this->assertEquals($question->inputs['ans1']->get_parameter('boxWidth'), 30);
    }
}
