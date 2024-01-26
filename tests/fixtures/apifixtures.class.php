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

/**

 *
 * @copyright  2024 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class stack_api_test_data {
    protected static array $questiondata = [
        'matrices' =>
           '<quiz>
                <question type="stack">
                    <name>
                    <text>test_3_matrix</text>
                    </name>
                    <questiontext format="html">
                    <text><![CDATA[<p>Calculate \[ {@A@}.{@B@}\]</p>
                <p> [[input:ans1]] [[validation:ans1]]</p>]]></text>
                    </questiontext>
                    <generalfeedback format="html">
                    <text><![CDATA[<p>To multiply matrices \(A\) and \(B\) we need to remember that the \((i,j)\)th entry is the scalar product of the \(i\)th row of \(A\) with the \(j\)th column of \(B\).</p>
                <p>\[ {@A@}.{@B@} = {@C@} = {@D@}.\]</p>]]></text>
                    </generalfeedback>
                    <defaultgrade>1.0000000</defaultgrade>
                    <penalty>0.1000000</penalty>
                    <hidden>0</hidden>
                    <stackversion>
                    <text/>
                    </stackversion>
                    <questionvariables>
                    <text><![CDATA[A:ev(rand(matrix([5,5],[5,5]))+matrix([2,2],[2,2]),simp);
                B:ev(rand(matrix([5,5],[5,5]))+matrix([2,2],[2,2]),simp);
                TA:ev(A.B,simp);
                TB:ev(A*B,simp);
                BT:transpose(B);
                C:zeromatrix (first(matrix_size(A)), second(matrix_size(A)));
                S:for a:1 thru first(matrix_size(A)) do for b:1 thru second(matrix_size(A)) do C[ev(a,simp),ev(b,simp)]:apply("+",zip_with("*",A[ev(a,simp)],BT[ev(b,simp)]));
                D:ev(C,simp);
                C:C;]]></text>
                    </questionvariables>
                    <specificfeedback format="html">
                    <text><![CDATA[<p>[[feedback:prt1]]</p>]]></text>
                    </specificfeedback>
                    <questionnote>
                    <text>\({@A@}.{@B@}={@TA@}\)</text>
                    </questionnote>
                    <questionsimplify>0</questionsimplify>
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
                    <type>matrix</type>
                    <tans>TA</tans>
                    <boxsize>3</boxsize>
                    <strictsyntax>1</strictsyntax>
                    <insertstars>0</insertstars>
                    <syntaxhint/>
                    <syntaxattribute>0</syntaxattribute>
                    <forbidwords/>
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
                    <feedbackstyle>1</feedbackstyle>
                    <feedbackvariables>
                        <text/>
                    </feedbackvariables>
                    <node>
                        <name>0</name>
                        <answertest>AlgEquiv</answertest>
                        <sans>ans1</sans>
                        <tans>TA</tans>
                        <testoptions/>
                        <quiet>1</quiet>
                        <truescoremode>=</truescoremode>
                        <truescore>10.0000000</truescore>
                        <truepenalty/>
                        <truenextnode>-1</truenextnode>
                        <trueanswernote>1-0-T </trueanswernote>
                        <truefeedback format="html">
                        <text/>
                        </truefeedback>
                        <falsescoremode>=</falsescoremode>
                        <falsescore>0.0000000</falsescore>
                        <falsepenalty/>
                        <falsenextnode>1</falsenextnode>
                        <falseanswernote>1-0-F</falseanswernote>
                        <falsefeedback format="html">
                        <text/>
                        </falsefeedback>
                    </node>
                    <node>
                        <name>1</name>
                        <answertest>AlgEquiv</answertest>
                        <sans>ans1</sans>
                        <tans>TB</tans>
                        <testoptions/>
                        <quiet>1</quiet>
                        <truescoremode>=</truescoremode>
                        <truescore>0.0000000</truescore>
                        <truepenalty/>
                        <truenextnode>-1</truenextnode>
                        <trueanswernote>1-1-T </trueanswernote>
                        <truefeedback format="html">
                        <text><![CDATA[<p>Remember, you do not multiply matrices by multiplying the corresponding entries! A quite different process is needed.</p>]]></text>
                        </truefeedback>
                        <falsescoremode>=</falsescoremode>
                        <falsescore>0.0000000</falsescore>
                        <falsepenalty/>
                        <falsenextnode>2</falsenextnode>
                        <falseanswernote>1-1-F </falseanswernote>
                        <falsefeedback format="html">
                        <text/>
                        </falsefeedback>
                    </node>
                    <node>
                        <name>2</name>
                        <answertest>AlgEquiv</answertest>
                        <sans>ans1</sans>
                        <tans>A+B</tans>
                        <testoptions/>
                        <quiet>1</quiet>
                        <truescoremode>=</truescoremode>
                        <truescore>0.0000000</truescore>
                        <truepenalty/>
                        <truenextnode>-1</truenextnode>
                        <trueanswernote>1-3-T</trueanswernote>
                        <truefeedback format="html">
                        <text><![CDATA[<p>Please multiply the matrices. It looks like you have added them instead!</p>]]></text>
                        </truefeedback>
                        <falsescoremode>=</falsescoremode>
                        <falsescore>0.0000000</falsescore>
                        <falsepenalty/>
                        <falsenextnode>-1</falsenextnode>
                        <falseanswernote>1-3-F</falseanswernote>
                        <falsefeedback format="html">
                        <text/>
                        </falsefeedback>
                    </node>
                    </prt>
                    <deployedseed>86</deployedseed>
                    <deployedseed>219862533</deployedseed>
                    <deployedseed>1167893775</deployedseed>
                    <qtest>
                    <testcase>1</testcase>
                    <testinput>
                        <name>ans1</name>
                        <value>TA</value>
                    </testinput>
                    <expected>
                        <name>prt1</name>
                        <expectedscore>1.0000000</expectedscore>
                        <expectedpenalty>0.0000000</expectedpenalty>
                        <expectedanswernote>1-0-T </expectedanswernote>
                    </expected>
                    </qtest>
                    <qtest>
                    <testcase>2</testcase>
                    <testinput>
                        <name>ans1</name>
                        <value>TB</value>
                    </testinput>
                    <expected>
                        <name>prt1</name>
                        <expectedscore>0.0000000</expectedscore>
                        <expectedpenalty>0.1000000</expectedpenalty>
                        <expectedanswernote>1-1-T</expectedanswernote>
                    </expected>
                    </qtest>
                    <qtest>
                    <testcase>3</testcase>
                    <testinput>
                        <name>ans1</name>
                        <value>1</value>
                    </testinput>
                    <expected>
                        <name>prt1</name>
                        <expectedscore/>
                        <expectedpenalty/>
                        <expectedanswernote>NULL</expectedanswernote>
                    </expected>
                    </qtest>
                    <qtest>
                    <testcase>4</testcase>
                    <testinput>
                        <name>ans1</name>
                        <value>A</value>
                    </testinput>
                    <expected>
                        <name>prt1</name>
                        <expectedscore>0.0000000</expectedscore>
                        <expectedpenalty>0.1000000</expectedpenalty>
                        <expectedanswernote>1-3-F</expectedanswernote>
                    </expected>
                    </qtest>
                </question>
            </quiz>',
        'usedefaults' =>
           '<quiz>
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
               <prtcorrect format="html">
                 <text><![CDATA[<p><span class="correct">Correct answer, well done.</span></p>]]></text>
               </prtcorrect>
               <prtpartiallycorrect format="html">
                 <text><![CDATA[<p><span class="partially">Your answer is partially correct.</span></p>]]></text>
               </prtpartiallycorrect>
               <prtincorrect format="html">
                 <text><![CDATA[<p><span class="incorrect">Incorrect answer.</span></p>]]></text>
               </prtincorrect>
               <variantsselectionseed/>
               <input>
                 <name>ans1</name>
                 <type>algebraic</type>
                 <tans>ta+c</tans>
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
           </quiz>',
        'optionset' =>
            '<quiz>
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
                <prtcorrect format="html">
                <text><![CDATA[<p><span class="correct">Correct answer, well done.</span></p>]]></text>
                </prtcorrect>
                <prtpartiallycorrect format="html">
                <text><![CDATA[<p><span class="partially">Your answer is partially correct.</span></p>]]></text>
                </prtpartiallycorrect>
                <prtincorrect format="html">
                <text><![CDATA[<p><span class="incorrect">Incorrect answer.</span></p>]]></text>
                </prtincorrect>
                <questionsimplify>0</questionsimplify>
                <assumepositive>1</assumepositive>
                <assumereal>1</assumereal>
                <decimals>,</decimals>
                <multiplicationsign>cross</multiplicationsign>
                <sqrtsign>0</sqrtsign>
                <complexno>j</complexno>
                <inversetrig>acos</inversetrig>
                <logicsymbol>symbol</logicsymbol>
                <matrixparens>(</matrixparens>
                <variantsselectionseed/>
                <input>
                <name>ans1</name>
                <type>algebraic</type>
                <tans>ta+c</tans>
                <boxsize>30</boxsize>
                <strictsyntax>1</strictsyntax>
                <insertstars>1</insertstars>
                <forbidwords>test</forbidwords>
                <forbidfloat>0</forbidfloat>
                <requirelowestterms>1</requirelowestterms>
                <checkanswertype>1</checkanswertype>
                <mustverify>0</mustverify>
                <showvalidation>0</showvalidation>
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
            </quiz>'
    ];

    protected static array $correctanswers = [
        'matrices' => '{"ans1_sub_0_0": "35", "ans1_sub_0_1": "30", "ans1_sub_1_0": "28", "ans1_sub_1_1": "24"}'
    ];

    public static function get_question_string(string $name): string {
        return self::$questiondata[$name];
    }

    public static function get_answer_string(string $name): string {
        return self::$correctanswers[$name];
    }
}
