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
require_once(__DIR__ . '../../api/controller/DownloadController.php');
require_once(__DIR__ . '../../api/controller/GradingController.php');
require_once(__DIR__ . '../../api/controller/RenderController.php');
require_once(__DIR__ . '../../api/controller/ValidationController.php');
require_once(__DIR__ . '../../api/controller/TestController.php');


use api\controller\DownloadController;
use api\controller\GradingController;
use api\controller\RenderController;
use api\controller\ValidationController;
use api\controller\TestController;
use api\util\StackIframeHolder;
use stack_api_test_data;
use Psr\Http\Message\ResponseInterface as ResponseInt;
use Psr\Http\Message\StreamInterface as StreamInt;
use Psr\Http\Message\ServerRequestInterface as RequestInt;
use qtype_stack_testcase;

/**
 * @group qtype_stack
 * @covers \qtype_stack
 */
class api_controller_test extends qtype_stack_testcase {
    /** @var object used to store output */
    public object $output;
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
        set_config('stackapi', true, 'qtype_stack');
        StackIframeHolder::$iframes = [];
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
        foreach ($reflection->getMethods() as $method) {
            $methods[] = $method->name;
        }
        $this->request = $this->getMockBuilder(RequestInt::class)
            ->setMockClassName('RequestTest')
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
        foreach ($reflection->getMethods() as $method) {
            $methods[] = $method->name;
        }

        $this->response = $this->getMockBuilder(ResponseInt::class)
            ->setMockClassName('ResponseTest')
            ->setMethods($methods)
            ->getMock();

        $reflection = new \ReflectionClass(StreamInt::class);
        $methods = [];
        foreach ($reflection->getMethods() as $method) {
            $methods[] = $method->name;
        }

        $this->result = $this->getMockBuilder(StreamInt::class)
            ->setMockClassName('StreamInterfaceTest')
            ->setMethods($methods)
            ->getMock();

        $this->result->expects($this->any())->method('write')->will($this->returnCallback(
            function() {
                $this->output = json_decode(func_get_args()[0]);
                return 1;
            })
        );

        // The controllers call getBody() on the response object but then call write() on the result
        // so we have to mock both. We override the write method to write to a propery of the testsuite
        // so we have something easily accessible to perform some asserts on.
        $this->response->expects($this->any())->method('getBody')->will($this->returnCallback(
            function() {
                return $this->result;
            })
        );

        $this->response->expects($this->any())->method('withHeader')->willReturn($this->response);
    }

    public function tearDown(): void {
        \stack_cas_castext2_iframe::register_counter('///IFRAME_COUNT///');
    }

    public static function tearDownAfterClass(): void {
        // Should not really be necessary.
        set_config('stackapi', false, 'qtype_stack');
    }

    public function test_render() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('matrices');
        $rc = new RenderController();
        $rc->__invoke($this->request, $this->response, []);
        $this->assertMatchesRegularExpression('/^<p>Calculate/', $this->output->questionrender);
        $this->assertEquals(86, $this->output->questionseed);
        $this->assertEquals('matrix([35,30],[28,24])', $this->output->questioninputs->ans1->samplesolution->_val);
        $this->assertMatchesRegularExpression('/^<div class="matrixsquarebrackets"><table class="matrixtable"/',
                $this->output->questioninputs->ans1->render);
        $this->assertMatchesRegularExpression('/^<p>To multiply matrices/', $this->output->questionsamplesolutiontext);
        $this->assertEquals(0, count((array)$this->output->questionassets));
        $this->assertContains(86, $this->output->questionvariants);
        $this->assertContains(219862533, $this->output->questionvariants);
        $this->assertContains(1167893775, $this->output->questionvariants);
        $this->assertEquals(3, count($this->output->questionvariants));
        $this->assertEquals(0, count($this->output->iframes));
    }

    public function test_render_specified_seed() {
        $this->requestdata['seed'] = 219862533;
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('matrices');
        $rc = new RenderController();
        $rc->__invoke($this->request, $this->response, []);
        $this->assertMatchesRegularExpression('/^<p>Calculate/', $this->output->questionrender);
        $this->assertEquals(219862533, $this->output->questionseed);
    }

    public function test_render_plots() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('plots');
        $rc = new RenderController();
        $rc->__invoke($this->request, $this->response, []);
        $this->assertEquals(4, count((array)$this->output->questionassets));
        $this->assertEquals(true, isset($this->output->questionassets->{'input-ans1-1-0.svg'}));
        $this->assertEquals(true, isset($this->output->questionassets->{'input-ans1-2-0.svg'}));
        $this->assertEquals(true, isset($this->output->questionassets->{'input-ans1-3-0.svg'}));
        $this->assertEquals(true, isset($this->output->questionassets->{'input-ans1-4-0.svg'}));
    }

    public function test_render_iframes() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('iframes');
        $rc = new RenderController();
        $rc->__invoke($this->request, $this->response, []);
        $this->assertEquals(1, count($this->output->iframes));
    }

    public function test_render_download() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('download');
        $rc = new RenderController();
        $rc->__invoke($this->request, $this->response, []);
        $this->assertMatchesRegularExpression('/javascript\:download\(\'data.csv\'\, 1\)/s', $this->output->questionrender);
    }

    public function test_validation() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('matrices');
        $this->requestdata['answers'] = (array) json_decode(stack_api_test_data::get_answer_string('matrices_correct'));
        $this->requestdata['inputName'] = 'ans1';
        $vc = new ValidationController();
        $vc->__invoke($this->request, $this->response, []);
        $this->assertMatchesRegularExpression('/\\\[ \\\left\[\\begin\{array\}\{cc\} 1 & 2 \\\\ 3 & 4 \\end{array}\\right\] \\\]/s',
                $this->output->validation);
        $this->assertEquals(0, count($this->output->iframes));
    }

    public function test_grade() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('matrices');
        $this->requestdata['answers'] = (array) json_decode(stack_api_test_data::get_answer_string('matrices_correct'));
        $this->requestdata['inputName'] = 'ans1';
        $gc = new GradingController();
        $gc->__invoke($this->request, $this->response, []);
        $this->assertEquals(true, $this->output->isgradable);
        $this->assertEquals(1, $this->output->score);
        $this->assertEquals(1, $this->output->scores->prt1);
        $this->assertEquals(1, $this->output->scores->total);
        $this->assertEquals(1, $this->output->scoreweights->prt1);
        $this->assertEquals(5, $this->output->scoreweights->total);
        $this->assertEquals('<p>[[feedback:prt1]]</p>', $this->output->specificfeedback);
        $this->assertStringContainsString('correct', $this->output->prts->prt1);
        $this->assertEquals(0, count((array)$this->output->gradingassets));
        $this->assertEquals('Seed: 86; ans1: matrix([35,30],[28,24]) [valid]; prt1: !', $this->output->responsesummary);
        $this->assertEquals(0, count($this->output->iframes));
    }

    public function test_grade_scores() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('multipleanswers');
        $this->requestdata['answers'] = (array) json_decode(stack_api_test_data::get_answer_string('multiple_mixed'));
        $this->requestdata['inputName'] = 'ans1';
        $gc = new GradingController();
        $gc->__invoke($this->request, $this->response, []);
        $this->assertEquals(true, $this->output->isgradable);
        $this->assertEqualsWithDelta(0.9, $this->output->score, 0.0001);
        $this->assertEquals(1, $this->output->scores->prt1);
        $this->assertEquals(1, $this->output->scores->prt2);
        $this->assertEquals(0, $this->output->scores->prt3);
        $this->assertEquals(1, $this->output->scores->prt4);
        $this->assertEqualsWithDelta(0.9, $this->output->scores->total, 0.0001);
        $this->assertEqualsWithDelta(0.7, $this->output->scoreweights->prt1, 0.0001);
        $this->assertEqualsWithDelta(0.1, $this->output->scoreweights->prt2, 0.0001);
        $this->assertEqualsWithDelta(0.1, $this->output->scoreweights->prt3, 0.0001);
        $this->assertEqualsWithDelta(0.1, $this->output->scoreweights->prt4, 0.0001);
        $this->assertEquals(10, $this->output->scoreweights->total);
    }

    public function test_download() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('download');
        $this->requestdata['filename'] = 'data.csv';
        $this->requestdata['fileid'] = 1;

        $dc = $this->getMockBuilder(DownloadController::class)
            ->setMockClassName('DownloadControllerTest')
            ->setMethods(['set_headers'])
            ->getMock();

        $dc->expects($this->any())->method('set_headers')->willReturn(true);
        $dc->__invoke($this->request, $this->response, []);
        $this->expectOutputRegex('/^A,B,C\n0.37,5.04,2.72/s');
    }

    public function test_test_controller() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('matrices');
        $this->requestdata['filepath'] = 'testpath/test.xml';
        $tc = new TestController();
        $tc->__invoke($this->request, $this->response, []);
        $results = $this->output;
        $this->assertEquals('test_3_matrix', $results->name);
        $this->assertEquals(false, $results->isupgradeerror);
        $this->assertEquals(true, $results->isgeneralfeedback );
        $this->assertEquals(true, $results->isdeployedseeds);
        $this->assertEquals(true, $results->israndomvariants);
        $this->assertEquals(true, $results->istests);
        // Three seeds.
        $this->assertEquals(3, count(get_object_vars($results->results)));
        $this->assertEquals(4, $results->results->{'86'}->passes);
        $this->assertEquals(0, $results->results->{'86'}->fails);
        $this->assertEquals('', $results->results->{'86'}->messages);
        $this->assertEquals(4, count(get_object_vars($results->results->{'86'}->outcomes)));
    }

    public function test_test_controller_fail() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('test');
        $this->requestdata['filepath'] = 'testpath/test.xml';
        $tc = new TestController();
        $tc->__invoke($this->request, $this->response, []);
        $results = $this->output;
        $this->assertEquals('Algebraic input', $results->name);
        $this->assertEquals(false, $results->isupgradeerror);
        $this->assertEquals(false, $results->isgeneralfeedback );
        $this->assertEquals(false, $results->isdeployedseeds);
        $this->assertEquals(false, $results->israndomvariants);
        $this->assertEquals(true, $results->istests);
        // No seeds.
        $this->assertEquals(1, count(get_object_vars($results->results)));
        $this->assertEquals(0, $results->results->noseed->passes);
        $this->assertEquals(2, $results->results->noseed->fails);
        $this->assertEquals(stack_string('questiontestempty'), $results->results->noseed->messages);
        $this->assertEquals('', $results->messages);
        $this->assertEquals(2, count(get_object_vars($results->results->noseed->outcomes)));
    }

    public function test_test_controller_upgrade() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('test2');
        $this->requestdata['filepath'] = 'testpath/test.xml';
        $tc = new TestController();
        $tc->__invoke($this->request, $this->response, []);
        $results = $this->output;
        $this->assertEquals('Algebraic input', $results->name);
        $this->assertEquals(true, $results->isupgradeerror);
        $this->assertEquals(false, $results->isgeneralfeedback );
        $this->assertEquals(false, $results->isdeployedseeds);
        $this->assertEquals(false, $results->israndomvariants);
        $this->assertEquals(false, $results->istests);
        $this->assertStringContainsString('missing or empty', $results->messages);
        $this->assertEquals([], $results->results);
    }

    public function test_test_controller_default_test_fail() {
        $this->requestdata['questionDefinition'] = stack_api_test_data::get_question_string('test3');
        $this->requestdata['filepath'] = 'testpath/test.xml';
        $tc = new TestController();
        $tc->__invoke($this->request, $this->response, []);
        $results = $this->output;
        $this->assertEquals('Algebraic input', $results->name);
        $this->assertEquals(false, $results->isupgradeerror);
        $this->assertEquals(false, $results->isgeneralfeedback );
        $this->assertEquals(false, $results->isdeployedseeds);
        $this->assertEquals(false, $results->israndomvariants);
        $this->assertEquals(false, $results->istests);
        $this->assertEquals(stack_string('defaulttestfail'), $results->results->noseed->messages);
        $this->assertEquals(1, $results->results->noseed->fails);
        $this->assertEquals(0, $results->results->noseed->passes);
    }

    public function test_test_controller_default_test_pass() {
        $this->requestdata['questionDefinition'] =
            str_replace('<tans>wrong</tans>', '<tans>ta</tans>', stack_api_test_data::get_question_string('test3'));
        $this->requestdata['filepath'] = 'testpath/test.xml';
        $tc = new TestController();
        $tc->__invoke($this->request, $this->response, []);
        $results = $this->output;
        $this->assertEquals('Algebraic input', $results->name);
        $this->assertEquals(false, $results->isupgradeerror);
        $this->assertEquals(false, $results->isgeneralfeedback );
        $this->assertEquals(false, $results->isdeployedseeds);
        $this->assertEquals(false, $results->israndomvariants);
        $this->assertEquals(false, $results->istests);
        $this->assertEquals('', $results->results->noseed->messages);
        $this->assertEquals(0, $results->results->noseed->fails);
        $this->assertEquals(1, $results->results->noseed->passes);
    }
}
