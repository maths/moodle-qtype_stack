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
require_once(__DIR__ . '../../api/controller/GradingController.php');
require_once(__DIR__ . '../../api/controller/RenderController.php');
require_once(__DIR__ . '../../api/util/StackQuestionLoader.php');
require_once(__DIR__ . '../../api/controller/ValidationController.php');


use api\controller\GradingController;
use api\controller\RenderController;
use api\controller\ValidationController;
use api\util\StackQuestionLoader;
use stack_api_test_data;
use Psr\Http\Message\ResponseInterface as ResponseInt;
use Psr\Http\Message\ServerRequestInterface as RequestInt;
use qtype_stack_testcase;

/**
 * Class to fake the response output object and store the actual JSON
 */
class MockBody {
    public object $output;
    public function write() {
        $this->output = json_decode(func_get_args()[0]);
        return $this->output;
    }
}

/**
 * @group qtype_stack
 * @covers \qtype_stack
 */
class api_test extends qtype_stack_testcase {
    /** @var object used to store output */
    public object $result;
    /** @var array the api call data */
    public array $requestdata;
    /** @var object request object mock */
    public object $request;
    /** @var object response object mock */
    public object $response;

    /**
     * Setup tests by mocking response and request.
     *
     * @return void
     */
    public function setUp(): void {
        parent::setUp();
        $this->requestdata = [];
        $this->requestdata['seed'] = '';
        $this->requestdata['readOnly'] = false;
        $this->requestdata['renderInputs'] = true;

        // Need to mock request and response for the controllers but Moodle only
        // has the interfaces, not the classes themselves. We have to get an array
        // of method names to mock them all or the interface will complain that
        // it hasn't been implemented.
        $reflection = new \ReflectionClass(RequestInt::class);
        $methods = [];
        foreach($reflection->getMethods() as $method) {
            $methods[] = $method->name;
        }
        $this->request = $this->getMockBuilder(RequestInt::class)
            ->setMockClassName('Request')
            ->setMethods($methods)
            ->getMock();
        // Need to use callback so data can be altered in each test.
        $this->request->method("getParsedBody")->will($this->returnCallback(
            function() {
                return $this->requestdata;
            })
        );


        $reflection = new \ReflectionClass(ResponseInt::class);
        $methods = [];
        foreach($reflection->getMethods() as $method) {
            $methods[] = $method->name;
        }

        $this->response = $this->getMockBuilder(ResponseInt::class)
            ->setMockClassName('Response')
            ->setMethods($methods)
            ->getMock();

        $this->result = new MockBody();

        // The controllers call getBody() on the response object but then call write() on the result.
        // We return a MockBody object with a write method which updates the test's result property
        // so we can actually perform some asserts.
        $this->response->expects($this->any())->method('getBody')->will($this->returnCallback(
            function() {
                return $this->result;
            })
        );

        $this->response->expects($this->any())->method('withHeader')->willReturn($this->response);
    }

    public function test_render() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('matrices');
        $rc = new RenderController();
        $rc->__invoke($this->request, $this->response, []);
        $this->assertMatchesRegularExpression('/^<p>Calculate/', $this->result->output->questionrender);
        $this->assertEquals(86, $this->result->output->questionseed);
        $this->assertEquals('matrix([35,30],[28,24])', $this->result->output->questioninputs->ans1->samplesolution->_val);
        $this->assertMatchesRegularExpression('/^<div class="matrixsquarebrackets"><table class="matrixtable"/', $this->result->output->questioninputs->ans1->render);
        $this->assertMatchesRegularExpression('/^<p>To multiply matrices/', $this->result->output->questionsamplesolutiontext);
        $this->assertEquals(0, count((array)$this->result->output->questionassets));
        $this->assertContains(86, $this->result->output->questionvariants);
        $this->assertContains(219862533, $this->result->output->questionvariants);
        $this->assertContains(1167893775, $this->result->output->questionvariants);
        $this->assertEquals(3, count($this->result->output->questionvariants));
        $this->assertEquals(0, count($this->result->output->iframes));
    }

    public function xtest_render_specified_seed() {
        $this->requestdata['seed'] = 219862533;
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('matrices');
        $rc = new RenderController();
        $rc->__invoke($this->request, $this->response, []);
        $this->assertMatchesRegularExpression('/^<p>Calculate/', $this->result->output->questionrender);
        $this->assertEquals(219862533, $this->result->output->questionseed);
    }

    public function xtest_validation() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('matrices');
        $this->requestdata['answers'] = (array) json_decode(stack_api_test_data::get_answer_string('matrices'));
        $this->requestdata['inputName'] = 'ans1';
        $vc = new ValidationController();
        $vc->__invoke($this->request, $this->response, []);
        $this->assertMatchesRegularExpression('/\\\[ \\\left\[\\begin\{array\}\{cc\} 1 & 2 \\\\ 3 & 4 \\end{array}\\right\] \\\]/s', $this->result->output->validation);
    }

    public function xtest_grade() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('matrices');
        $this->requestdata['answers'] = (array) json_decode(stack_api_test_data::get_answer_string('matrices'));
        $this->requestdata['inputName'] = 'ans1';
        $gc = new GradingController();
        $gc->__invoke($this->request, $this->response, []);
        $this->assertEquals(1, $this->result->output->score);
    }

    public function xtest_question_loader() {
        $xml = stack_api_test_data::get_question_string('matrices');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml);

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

    public function xtest_question_loader_use_defaults() {
        global $CFG;
        $xml = stack_api_test_data::get_question_string('usedefaults');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml);
        $this->assertEquals($question->options->get_option('decimals'), get_config('qtype_stack', 'decimals'));
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
        $this->assertEquals($question->inputs['ans1']->get_parameter('showValidation'), get_config('qtype_stack', 'inputshowvalidation'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('insertStars'), get_config('qtype_stack', 'inputinsertstars'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidFloats'), get_config('qtype_stack', 'inputforbidfloat'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('lowestTerms'), get_config('qtype_stack', 'inputrequirelowestterms'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('sameType'), get_config('qtype_stack', 'inputcheckanswertype'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidWords'), get_config('qtype_stack', 'inputforbidwords'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('boxWidth'), get_config('qtype_stack', 'inputboxsize'));
    }

    public function xtest_question_loader_do_not_use_defaults() {
        global $CFG;
        $xml = stack_api_test_data::get_question_string('optionset');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml);
        $this->assertEquals($question->options->get_option('decimals'), ',');
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