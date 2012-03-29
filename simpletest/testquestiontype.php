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

/**
 * Unit tests for the STACK question type class.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/engine/simpletest/helpers.php');
require_once($CFG->dirroot . '/question/type/stack/questiontype.php');


/**
 * Unit tests for the STACK question type class.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_test extends UnitTestCase {
    /**
     * @var qtype_oumultiresponse
     */
    private $qtype;

    public function setUp() {
        $this->qtype = new qtype_stack();
    }

    public function tearDown() {
        $this->qtype = null;
    }

    public function assert_same_xml($expectedxml, $xml) {
        $this->assertEqual(str_replace("\r\n", "\n", $expectedxml),
                str_replace("\r\n", "\n", $xml));
    }

    public function test_name() {
        $this->assertEqual($this->qtype->name(), 'stack');
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
      <text>Generalfeedback: {={a} + {b}} is the right answer.</text>
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
    <markmode>penalty</markmode>
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
    <input>
      <name>ans1</name>
      <type>algebraic</type>
      <tans>2</tans>
      <boxsize>5</boxsize>
      <strictsyntax>1</strictsyntax>
      <insertstars>0</insertstars>
      <syntaxhint></syntaxhint>
      <forbidwords></forbidwords>
      <forbidfloat>1</forbidfloat>
      <requirelowestterms>0</requirelowestterms>
      <checkanswertype>0</checkanswertype>
      <mustverify>1</mustverify>
      <showvalidation>1</showvalidation>
    </input>
    <prt>
      <name>prt1</name>
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
        <trueanswernote>prt1-1-T</trueanswernote>
        <truefeedback format="html">
          <text></text>
        </truefeedback>
        <falsescoremode>=</falsescoremode>
        <falsescore>0</falsescore>
        <falsepenalty>0</falsepenalty>
        <falsenextnode>-1</falsenextnode>
        <falseanswernote></falseanswernote>
        <falsefeedback format="html">
          <text></text>
        </falsefeedback>
      </node>
    </prt>
    <deployedseed>{deployedseed}</deployedseed>
    <qtest>
      <testcase>1</testcase>
      <testinput>
        <name>ans1</name>
        <value>2</value>
      </testinput>
      <expected>
        <name>prt1</name>
        <expectedscore>1</expectedscore>
        <expectedpenalty>0</expectedpenalty>
        <expectedanswernote></expectedanswernote>
      </expected>
    </qtest>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }
}
