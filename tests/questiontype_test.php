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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/fixtures/test_base.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once(__DIR__ . '/../questiontype.php');

// Unit tests for the STACK question type class.
//
// @package   qtype_stack.
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class qtype_stack_test extends qtype_stack_walkthrough_test_base {

    /** @var qtype_stack */
    private $qtype;

    public function setUp() {
        parent::setUp();
        $this->qtype = new qtype_stack();
    }

    public function tearDown() {
        $this->qtype = null;
        parent::tearDown();
    }

    public function assert_same_xml($expectedxml, $xml) {
        $this->assertEquals(str_replace("\r\n", "\n", $expectedxml),
                str_replace("\r\n", "\n", $xml));
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'stack');
    }

    public function test_get_possible_responses_test0() {
        $qdata = test_question_maker::get_question_data('stack', 'test0');

        $expected = array(
            'firsttree-0' => array(
                'firsttree-1-F' => new question_possible_response('firsttree-1-F', 0),
                'firsttree-1-T' => new question_possible_response('firsttree-1-T', 1),
                null          => question_possible_response::no_response(),
            ),
        );

        $this->assertEquals($expected, $this->qtype->get_possible_responses($qdata));
    }

    public function test_get_possible_responses_test3() {
        $qdata = test_question_maker::get_question_data('stack', 'test3');

        $expected = array(
            'odd-0' => array(
                'odd-0-0' => new question_possible_response('odd-0-0', 0),
                'odd-0-1' => new question_possible_response('odd-0-1', 0.25),
                null     => question_possible_response::no_response(),
            ),
            'even-0' => array(
                'even-0-0' => new question_possible_response('even-0-0', 0),
                'even-0-1' => new question_possible_response('even-0-1', 0.25),
                null      => question_possible_response::no_response(),
            ),
            'oddeven-0' => array(
                'oddeven-0-0' => new question_possible_response('oddeven-0-0', 0),
                'oddeven-0-1' => new question_possible_response('oddeven-0-1', 0.125),
                null         => question_possible_response::no_response(),
            ),
            'oddeven-1' => array(
                'oddeven-1-0' => new question_possible_response('oddeven-1-0', 0),
                'oddeven-1-1' => new question_possible_response('oddeven-1-1', 0.125),
                null         => question_possible_response::no_response(),
            ),
            'unique-0' => array(
                'unique-0-0' => new question_possible_response('unique-0-0', 0),
                'unique-0-1' => new question_possible_response('unique-0-1', 0.25),
                null        => question_possible_response::no_response(),
            ),
        );

        $this->assertEquals($expected, $this->qtype->get_possible_responses($qdata));
    }

    public function test_initialise_question_instance() {
        $qdata = test_question_maker::get_question_data('stack', 'test3');
        $q = $this->qtype->make_question($qdata);
        $expectedq = test_question_maker::make_question('stack', 'test3');
        $expectedq->stamp = $q->stamp;
        $expectedq->version = $q->version;
        $this->assertEquals($expectedq, $q);
    }

    public function test_question_tests_test0() {
        // This unit test runs a question test, really just to verify that
        // there are no errors.
        $qdata = test_question_maker::get_question_data('stack', 'test0');
        $question = question_bank::get_qtype('stack')->make_question($qdata);

        // Create the question usage we will use.
        $quba = question_engine::make_questions_usage_by_activity('qtype_stack', context_system::instance());
        $quba->set_preferred_behaviour('adaptive');
        $question->seed = 1;
        $slot = $quba->add_question($question, $question->defaultmark);
        $quba->start_question($slot, 1);

        // Prepare the display options.
        $options = new question_display_options();
        $options->readonly = true;
        $options->flags = question_display_options::HIDDEN;
        $options->suppressruntestslink = true;

        foreach ($qdata->testcases as $testcase) {
            $result = $testcase->test_question($quba, $question, 1);
            $this->assertTrue($result->passed());
        }
    }

    public function test_xml_export() {
        $qdata = test_question_maker::get_question_data('stack', 'test0');

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 0  -->
  <question type="stack">
    <name>
      <text>test-0</text>
    </name>
    <questiontext format="html">
      <text>What is $1+1$? [[input:ans1]]
                                [[validation:ans1]]</text>
    </questiontext>
    <generalfeedback format="html">
      <text></text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <questionvariables>
      <text></text>
    </questionvariables>
    <specificfeedback format="html">
      <text>[[feedback:firsttree]]</text>
    </specificfeedback>
    <questionnote>
      <text></text>
    </questionnote>
    <questionsimplify>1</questionsimplify>
    <assumepositive>0</assumepositive>
    <assumereal>0</assumereal>
    <prtcorrect format="html">
      <text><![CDATA[<p>Correct answer, well done.</p>]]></text>
    </prtcorrect>
    <prtpartiallycorrect format="html">
      <text><![CDATA[<p>Your answer is partially correct.</p>]]></text>
    </prtpartiallycorrect>
    <prtincorrect format="html">
      <text><![CDATA[<p>Incorrect answer.</p>]]></text>
    </prtincorrect>
    <multiplicationsign>dot</multiplicationsign>
    <sqrtsign>1</sqrtsign>
    <complexno>i</complexno>
    <inversetrig>cos-1</inversetrig>
    <matrixparens>[</matrixparens>
    <variantsselectionseed></variantsselectionseed>
    <input>
      <name>ans1</name>
      <type>algebraic</type>
      <tans>2</tans>
      <boxsize>5</boxsize>
      <strictsyntax>1</strictsyntax>
      <insertstars>0</insertstars>
      <syntaxhint></syntaxhint>
      <syntaxattribute>0</syntaxattribute>
      <forbidwords></forbidwords>
      <allowwords></allowwords>
      <forbidfloat>1</forbidfloat>
      <requirelowestterms>0</requirelowestterms>
      <checkanswertype>0</checkanswertype>
      <mustverify>1</mustverify>
      <showvalidation>1</showvalidation>
      <options></options>
    </input>
    <prt>
      <name>firsttree</name>
      <value>1</value>
      <autosimplify>1</autosimplify>
      <feedbackvariables>
        <text></text>
      </feedbackvariables>
      <node>
        <name>0</name>
        <answertest>EqualComAss</answertest>
        <sans>ans1</sans>
        <tans>2</tans>
        <testoptions></testoptions>
        <quiet>0</quiet>
        <truescoremode>=</truescoremode>
        <truescore>1</truescore>
        <truepenalty>0</truepenalty>
        <truenextnode>-1</truenextnode>
        <trueanswernote>firsttree-1-T</trueanswernote>
        <truefeedback format="html">
          <text></text>
        </truefeedback>
        <falsescoremode>=</falsescoremode>
        <falsescore>0</falsescore>
        <falsepenalty>0</falsepenalty>
        <falsenextnode>-1</falsenextnode>
        <falseanswernote>firsttree-1-F</falseanswernote>
        <falsefeedback format="html">
          <text></text>
        </falsefeedback>
      </node>
    </prt>
    <deployedseed>12345</deployedseed>
    <qtest>
      <testcase>1</testcase>
      <testinput>
        <name>ans1</name>
        <value>2</value>
      </testinput>
      <expected>
        <name>firsttree</name>
        <expectedscore>1</expectedscore>
        <expectedpenalty>0</expectedpenalty>
        <expectedanswernote>firsttree-1-T</expectedanswernote>
      </expected>
    </qtest>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }

    public function test_xml_import() {
        $xml = '<!-- question: 0  -->
  <question type="stack">
    <name>
      <text>test-0</text>
    </name>
    <questiontext format="html">
      <text>What is $1+1$? [[input:ans1]] [[validation:ans1]]</text>
    </questiontext>
    <generalfeedback format="html">
      <text></text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <questionvariables>
      <text></text>
    </questionvariables>
    <specificfeedback format="html">
      <text>[[feedback:firsttree]]</text>
    </specificfeedback>
    <questionnote>
      <text></text>
    </questionnote>
    <questionsimplify>1</questionsimplify>
    <assumepositive>0</assumepositive>
    <assumereal>0</assumereal>
    <prtcorrect format="html">
      <text><![CDATA[<p>Correct answer, well done.</p>]]></text>
    </prtcorrect>
    <prtpartiallycorrect format="html">
      <text><![CDATA[<p>Your answer is partially correct.</p>]]></text>
    </prtpartiallycorrect>
    <prtincorrect format="html">
      <text><![CDATA[<p>Incorrect answer.</p>]]></text>
    </prtincorrect>
    <multiplicationsign>dot</multiplicationsign>
    <sqrtsign>1</sqrtsign>
    <complexno>i</complexno>
    <inversetrig>cos-1</inversetrig>
    <matrixparens>[</matrixparens>
    <variantsselectionseed></variantsselectionseed>
    <input>
      <name>ans1</name>
      <type>algebraic</type>
      <tans>2</tans>
      <boxsize>5</boxsize>
      <strictsyntax>1</strictsyntax>
      <insertstars>0</insertstars>
      <syntaxhint></syntaxhint>
      <syntaxattribute>0</syntaxattribute>
      <forbidwords></forbidwords>
      <allowwords></allowwords>
      <forbidfloat>1</forbidfloat>
      <requirelowestterms>0</requirelowestterms>
      <checkanswertype>0</checkanswertype>
      <mustverify>1</mustverify>
      <showvalidation>1</showvalidation>
      <options></options>
    </input>
    <prt>
      <name>firsttree</name>
      <value>1</value>
      <autosimplify>1</autosimplify>
      <feedbackvariables>
        <text></text>
      </feedbackvariables>
      <node>
        <name>0</name>
        <answertest>EqualComAss</answertest>
        <sans>ans1</sans>
        <tans>2</tans>
        <testoptions></testoptions>
        <quiet>0</quiet>
        <truescoremode>=</truescoremode>
        <truescore>1</truescore>
        <truepenalty>0</truepenalty>
        <truenextnode>-1</truenextnode>
        <trueanswernote>firsttree-1-T</trueanswernote>
        <truefeedback format="html">
          <text></text>
        </truefeedback>
        <falsescoremode>=</falsescoremode>
        <falsescore>0</falsescore>
        <falsepenalty>0</falsepenalty>
        <falsenextnode>-1</falsenextnode>
        <falseanswernote>firsttree-1-F</falseanswernote>
        <falsefeedback format="html">
          <text></text>
        </falsefeedback>
      </node>
    </prt>
    <deployedseed>12345</deployedseed>
    <qtest>
      <testcase>1</testcase>
      <testinput>
        <name>ans1</name>
        <value>2</value>
      </testinput>
      <expected>
        <name>firsttree</name>
        <expectedscore>1</expectedscore>
        <expectedpenalty>0</expectedpenalty>
        <expectedanswernote>firsttree-1-T</expectedanswernote>
      </expected>
    </qtest>
  </question>
';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->try_importing_using_qtypes(
                $xmldata['question'], null, null, 'stack');

        $expectedq = new stdClass();
        $expectedq->qtype                 = 'stack';
        $expectedq->name                  = 'test-0';
        $expectedq->questiontext          = 'What is $1+1$? [[input:ans1]] [[validation:ans1]]';
        $expectedq->questiontextformat    = FORMAT_HTML;
        $expectedq->generalfeedback       = '';
        $expectedq->generalfeedbackformat = FORMAT_HTML;
        $expectedq->defaultmark           = 1;
        $expectedq->length                = 1;
        $expectedq->penalty               = 0.3333333;

        $expectedq->questionvariables     = '';
        $expectedq->specificfeedback      = array('text' => '[[feedback:firsttree]]', 'format' => FORMAT_HTML, 'files' => array());
        $expectedq->questionnote          = '';
        $expectedq->questionsimplify      = 1;
        $expectedq->assumepositive        = 0;
        $expectedq->assumereal            = 0;
        $expectedq->prtcorrect            = array('text' => '<p>Correct answer, well done.</p>',
                                                    'format' => FORMAT_HTML, 'files' => array());;
        $expectedq->prtpartiallycorrect   = array('text' => '<p>Your answer is partially correct.</p>',
                                                    'format' => FORMAT_HTML, 'files' => array());;
        $expectedq->prtincorrect          = array('text' => '<p>Incorrect answer.</p>',
                                                    'format' => FORMAT_HTML, 'files' => array());;
        $expectedq->multiplicationsign    = 'dot';
        $expectedq->sqrtsign              = 1;
        $expectedq->complexno             = 'i';
        $expectedq->inversetrig           = 'cos-1';
        $expectedq->matrixparens          = '[';
        $expectedq->variantsselectionseed = '';

        $expectedq->ans1type               = 'algebraic';
        $expectedq->ans1modelans           = 2;
        $expectedq->ans1boxsize            = 5;
        $expectedq->ans1strictsyntax       = 1;
        $expectedq->ans1insertstars        = 0;
        $expectedq->ans1syntaxhint         = '';
        $expectedq->ans1syntaxattribute    = 0;
        $expectedq->ans1forbidwords        = '';
        $expectedq->ans1allowwords         = '';
        $expectedq->ans1forbidfloat        = 1;
        $expectedq->ans1requirelowestterms = 0;
        $expectedq->ans1checkanswertype    = 0;
        $expectedq->ans1mustverify         = 1;
        $expectedq->ans1showvalidation     = 1;
        $expectedq->ans1options            = '';

        $expectedq->firsttreevalue              = 1;
        $expectedq->firsttreeautosimplify       = 1;
        $expectedq->firsttreefeedbackvariables  = '';
        $expectedq->firsttreeanswertest[0]      = 'EqualComAss';
        $expectedq->firsttreesans[0]            = 'ans1';
        $expectedq->firsttreetans[0]            = '2';
        $expectedq->firsttreetestoptions[0]     = '';
        $expectedq->firsttreequiet[0]           = 0;
        $expectedq->firsttreetruescoremode[0]   = '=';
        $expectedq->firsttreetruescore[0]       = 1;
        $expectedq->firsttreetruepenalty[0]     = 0;
        $expectedq->firsttreetruenextnode[0]    = -1;
        $expectedq->firsttreetrueanswernote[0]  = 'firsttree-1-T';
        $expectedq->firsttreetruefeedback[0]    = array('text' => '', 'format' => FORMAT_HTML, 'files' => array());
        $expectedq->firsttreefalsescoremode[0]  = '=';
        $expectedq->firsttreefalsescore[0]      = 0;
        $expectedq->firsttreefalsepenalty[0]    = 0;
        $expectedq->firsttreefalsenextnode[0]   = -1;
        $expectedq->firsttreefalseanswernote[0] = 'firsttree-1-F';
        $expectedq->firsttreefalsefeedback[0]   = array('text' => '', 'format' => FORMAT_HTML, 'files' => array());

        $expectedq->deployedseeds = array('12345');

        $qtest = new stack_question_test(array('ans1' => '2'), 1);
        $qtest->add_expected_result('firsttree', new stack_potentialresponse_tree_state(
                        1, true, 1, 0, '', array('firsttree-1-T')));
        $expectedq->testcases[1] = $qtest;

        $this->assertEquals($expectedq->deployedseeds, $q->deployedseeds); // Redundant, but gives better fail messages.
        $this->assertEquals($expectedq->testcases, $q->testcases); // Redundant, but gives better fail messages.
        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }
}
