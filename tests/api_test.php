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

namespace qtype_stack;
use qtype_stack_walkthrough_test_base;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '../../api/controller/RenderController.php');

use api\controller\RenderController;

// Unit tests for the Stack question type API.
//
// @copyright 2023 University of Edinburgh.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

use Psr\Http\Message\ResponseInterface as ResponseInt;
use Psr\Http\Message\ServerRequestInterface as RequestInt;

class MockBody {
    public string $output = '';
    public function write() {
        $this->output = func_get_args()[0];
        return $this->output;
    }
}

/**
 * @group qtype_stack
 * @covers \qtype_stack
 */
class api_test extends qtype_stack_walkthrough_test_base {
    /** @var object used to store output */
    public object $result;
    public function test_creation() {
        define('STACK_API', true);
        $requestdata = [];
        $requestdata['questionDefinition'] = '<quiz>
        <question type="stack">
            <name>
              <text>test_1_integration</text>
            </name>
            <questiontext format="html">
              <text><![CDATA[<p>Find \[ \int {@p@} d{@v@}\] [[input:ans1]] [[validation:ans1]]</p>]]></text>
            </questiontext>
            <generalfeedback format="html">
              <text><![CDATA[<p>We can either do this question by inspection (i.e. spot the answer) or in a more formal manner by using the substitution \[ u = ({@v@}-{@a@}).\] Then, since \(\frac{d}{d{@v@}}u=1\) we have \[ \int {@p@} d{@v@} = \int u^{@n@} du = \frac{u^{@n+1@}}{@n+1@}+c = {@ta@}+c.\]</p>]]></text>
            </generalfeedback>
            <defaultgrade>1.0000000</defaultgrade>
            <penalty>0.1000000</penalty>
            <hidden>0</hidden>
            <stackversion>
              <text/>
            </stackversion>
            <questionvariables>
              <text>n:rand(5)+3;
        a:rand(5)+3;
        v:rand([x,t]);
        p:(v-a)^n;
        ta:(v-a)^(n+1)/(n+1);</text>
            </questionvariables>
            <specificfeedback format="html">
              <text><![CDATA[<p>[[feedback:prt1]]</p>]]></text>
            </specificfeedback>
            <questionnote>
              <text>\(\int {@p@} d{@v@} = {@ta@}\)</text>
            </questionnote>
            <questionsimplify>1</questionsimplify>
            <assumepositive>0</assumepositive>
            <assumereal>0</assumereal>
            <prtcorrect format="html">
              <text><![CDATA[<p><span class="correct">Correct answer, well done.</span></p>]]></text>
            </prtcorrect>
            <prtpartiallycorrect format="html">
              <text><![CDATA[<p><span class="partially">Your answer is partially correct.</span></p>]]></text>
            </prtpartiallycorrect>
            <prtincorrect format="html">
              <text><![CDATA[<p><span class="incorrect">Incorrect answer.</span></p>]]></text>
            </prtincorrect>
            <multiplicationsign>dot</multiplicationsign>
            <sqrtsign>1</sqrtsign>
            <complexno>i</complexno>
            <inversetrig>cos-1</inversetrig>
            <matrixparens>[</matrixparens>
            <variantsselectionseed/>
            <input>
              <name>ans1</name>
              <type>algebraic</type>
              <tans>ta+c</tans>
              <boxsize>20</boxsize>
              <strictsyntax>1</strictsyntax>
              <insertstars>0</insertstars>
              <syntaxhint/>
              <syntaxattribute>0</syntaxattribute>
              <forbidwords>int</forbidwords>
              <allowwords/>
              <forbidfloat>1</forbidfloat>
              <requirelowestterms>1</requirelowestterms>
              <checkanswertype>1</checkanswertype>
              <mustverify>1</mustverify>
              <showvalidation>1</showvalidation>
              <options/>
            </input>
            <prt>
              <name>prt1</name>
              <value>1.0000000</value>
              <autosimplify>1</autosimplify>
              <feedbackvariables>
                <text/>
              </feedbackvariables>
              <node>
                <name>0</name>
                <answertest>Int</answertest>
                <sans>ans1</sans>
                <tans>ta</tans>
                <testoptions>v</testoptions>
                <quiet>0</quiet>
                <truescoremode>=</truescoremode>
                <truescore>1.0000000</truescore>
                <truepenalty/>
                <truenextnode>-1</truenextnode>
                <trueanswernote>1-0-T </trueanswernote>
                <truefeedback format="html">
                  <text/>
                </truefeedback>
                <falsescoremode>=</falsescoremode>
                <falsescore>0.0000000</falsescore>
                <falsepenalty/>
                <falsenextnode>-1</falsenextnode>
                <falseanswernote>1-0-F </falseanswernote>
                <falsefeedback format="html">
                  <text/>
                </falsefeedback>
              </node>
            </prt>
            <deployedseed>1</deployedseed>
            <deployedseed>1001758021</deployedseed>
            <qtest>
              <testcase>1</testcase>
              <testinput>
                <name>ans1</name>
                <value>ta+c</value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>1.0000000</expectedscore>
                <expectedpenalty>0.0000000</expectedpenalty>
                <expectedanswernote>1-0-T</expectedanswernote>
              </expected>
            </qtest>
            <qtest>
              <testcase>2</testcase>
              <testinput>
                <name>ans1</name>
                <value>ta</value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>0.0000000</expectedscore>
                <expectedpenalty>0.1000000</expectedpenalty>
                <expectedanswernote>1-0-F</expectedanswernote>
              </expected>
            </qtest>
            <qtest>
              <testcase>3</testcase>
              <testinput>
                <name>ans1</name>
                <value>n*(v-a)^(n-1)</value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>0.0000000</expectedscore>
                <expectedpenalty>0.1000000</expectedpenalty>
                <expectedanswernote>1-0-F</expectedanswernote>
              </expected>
            </qtest>
            <qtest>
              <testcase>4</testcase>
              <testinput>
                <name>ans1</name>
                <value>(v-a)^(n+1)</value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>0.0000000</expectedscore>
                <expectedpenalty>0.1000000</expectedpenalty>
                <expectedanswernote>1-0-F</expectedanswernote>
              </expected>
            </qtest>
          </question>
        </quiz>';
        $requestdata['seed'] = '';
        $requestdata['readOnly'] = false;
        $requestdata['renderInputs'] = true;

        $reflection = new \ReflectionClass(RequestInt::class);
        $methods = [];
        foreach($reflection->getMethods() as $method) {
            $methods[] = $method->name;
        }
        $mock1 = $this->getMockBuilder(RequestInt::class)
            ->setMockClassName('Request')
            ->setMethods($methods)
            ->getMock();
        $mock1->method("getParsedBody")
            ->willReturn($requestdata);

        $reflection = new \ReflectionClass(ResponseInt::class);
        $methods = [];
        foreach($reflection->getMethods() as $method) {
            $methods[] = $method->name;
        }

        $mock2 = $this->getMockBuilder(ResponseInt::class)
            ->setMockClassName('Response')
            ->setMethods($methods)
            ->getMock();

        $this->result = new MockBody();

        $mock2->expects($this->exactly(1))->method('getBody')->will($this->returnCallback(
            function() {
                return $this->result;
            })
        );

        $mock2->expects($this->exactly(1))->method('withHeader')->willReturn($mock2);

        $x = new RenderController();

        $x->__invoke($mock1, $mock2, []);
        echo var_dump($this->result);
    }
}