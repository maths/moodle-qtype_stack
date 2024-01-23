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
        global $CFG;
        parent::setUp();
        // $CFG gets reset on every test.
        $CFG->questionsimplify = 1;
        $CFG->assumepositive = 0;
        $CFG->assumereal = 0;
        $CFG->prtcorrect = '';
        $CFG->prtpartiallycorrect = '';
        $CFG->prtincorrect = '';
        $CFG->multiplicationsign = 'dot';
        $CFG->sqrtsign = 1;
        $CFG->complexno = 'i';
        $CFG->logicsymbol = 'lang';
        $CFG->inversetrig = 'cos-1';
        $CFG->matrixparens = "[";
        $CFG->decimals = ".";
        $CFG->inputtype = 'algebraic';
        $CFG->inputboxsize = 30;
        $CFG->inputstrictsyntax = 1;
        $CFG->inputinsertstars = 0;
        $CFG->inputforbidwords = '';
        $CFG->inputforbidfloat = 0;
        $CFG->inputrequirelowestterms = 1;
        $CFG->inputcheckanswertype = 1;
        $CFG->inputmustverify = 1;
        $CFG->inputshowvalidation = 1;
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
    }

    public function test_validation() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('matrices');
        $this->requestdata['answers'] = (array) json_decode(stack_api_test_data::get_answer_string('matrices'));
        $this->requestdata['inputName'] = 'ans1';
        $vc = new ValidationController();
        $vc->__invoke($this->request, $this->response, []);
        $this->assertMatchesRegularExpression('/\\\[ \\\left\[\\begin\{array\}\{cc\} 1 & 2 \\\\ 3 & 4 \\end{array}\\right\] \\\]/s', $this->result->output->validation);
    }

    public function test_grade() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('matrices');
        $this->requestdata['answers'] = (array) json_decode(stack_api_test_data::get_answer_string('matrices'));
        $this->requestdata['inputName'] = 'ans1';
        $gc = new GradingController();
        $gc->__invoke($this->request, $this->response, []);
        $this->assertEquals(1, $this->result->output->score);
    }

    public function test_question_loader() {
        $xml = stack_api_test_data::get_question_string('matrices');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml);
        $this->assertEquals('test_3_matrix', $question->name);
    }
}