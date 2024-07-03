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
                    <text><![CDATA[<p>To multiply matrices \(A\) and \(B\) we need to remember that the \((i,j)\)th entry
                    is the scalar product of the \(i\)th row of \(A\) with the \(j\)th column of \(B\).</p>
                <p>\[ {@A@}.{@B@} = {@C@} = {@D@}.\]</p>]]></text>
                    </generalfeedback>
                    <defaultgrade>5.0000000</defaultgrade>
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
                S:for a:1 thru first(matrix_size(A)) do for b:1 thru second(matrix_size(A)) do
                C[ev(a,simp),ev(b,simp)]:apply("+",zip_with("*",A[ev(a,simp)],BT[ev(b,simp)]));
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
                        <text><![CDATA[<p>Remember, you do not multiply matrices by multiplying the
                        corresponding entries! A quite different process is needed.</p>]]></text>
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
                 <text><![CDATA[<p>We can either do this question by inspection (i.e. spot the answer) or in a
                 more formal manner by using the substitution \[ u = ({@v@}-{@a@}).\] Then, since \(\frac{d}{d{@v@}}u=1\)
                 we have \[ \int {@p@} d{@v@} = \int u^{@n@} du = \frac{u^{@n+1@}}{@n+1@}+c = {@ta@}+c.\]</p>]]></text>
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
                <text><![CDATA[<p>We can either do this question by inspection (i.e. spot the answer)
                or in a more formal manner by using the substitution \[ u = ({@v@}-{@a@}).\] Then,
                since \(\frac{d}{d{@v@}}u=1\) we have \[ \int {@p@} d{@v@} = \int u^{@n@}
                du = \frac{u^{@n+1@}}{@n+1@}+c = {@ta@}+c.\]</p>]]></text>
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
                <scientificnotation>*10</scientificnotation>
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
            </quiz>',
        'multipleanswers' =>
        '<quiz>
          <question type="stack">
            <name>
              <text>Equations of straight lines</text>
            </name>
            <questiontext format="html">
              <text><![CDATA[<p></p>

        <p>a) Two straight lines \(g\) and \(h\) are given by \(g:\ x+y=1\) and \(h:\ x-y=1\).
        What applies to the positional relationship of these lines?</p>
        <p>[[input:ans1]] [[validation:ans1]][[feedback:prt1]]</p>

        <hr>

        <p style="margin-top:1em;">b) Now two straight lines \(\tilde g\) and \(\tilde h\) are given by
        \(\tilde g:\ t\,x+y=1,\quad \tilde h:\ x+t\,y=1\) with a real parameter \(t\).</p>
        <p> Determine the parameter \(t\) for the following cases.</p>

        <p style="margin-top: 1.5em">The lines are identical for \(t=\) [[input:ans2]] [[validation:ans2]][[feedback:prt2]]</p>
        <p>The lines are parallel for \(t=\) [[input:ans3]] [[validation:ans3]][[feedback:prt3]]</p>
        <p>The lines are perpendicular to each other for \(t=\) [[input:ans4]] [[validation:ans4]][[feedback:prt4]]</p>
        <p></p>]]></text>
            </questiontext>
            <generalfeedback format="html">
              <text></text>
            </generalfeedback>
            <defaultgrade>10</defaultgrade>
            <penalty>0.1</penalty>
            <hidden>0</hidden>
            <idnumber></idnumber>
            <stackversion>
              <text>2020052700</text>
            </stackversion>
            <questionvariables>
              <text><![CDATA[/*Stephan Bach, OTH Amberg-Weiden*/

        ta1:[[a,false,"The lines are identical."], [b,false,"The lines are parallel (but not identical)."],
        [c,true,"The lines are perpendicular to each other."],[d,false,"The lines
        intersect but are not perpendicular to each other."]];
        ta2:1;
        ta3:-1;
        ta4:0;]]></text>
            </questionvariables>
            <specificfeedback format="html">
              <text></text>
            </specificfeedback>
            <questionnote>
              <text></text>
            </questionnote>
            <questiondescription format="moodle_auto_format">
              <text></text>
            </questiondescription>
            <questionsimplify>1</questionsimplify>
            <assumepositive>0</assumepositive>
            <assumereal>0</assumereal>
            <prtcorrect format="html">
              <text><![CDATA[<p><img alt="Richtig" title="Richtig"
              src="https://moodle.oth-aw.de/theme/image.php/clean/core/1554451383/i/grade_correct">Correct
              answer, well done!</p>]]></text>
            </prtcorrect>
            <prtpartiallycorrect format="html">
              <text><![CDATA[<p><span style="font-size:24px;color:grey;">!
              </span>Your answer is partially correct.</p>]]></text>
            </prtpartiallycorrect>
            <prtincorrect format="html">
              <text><![CDATA[<p><img alt="Falsch" title="Falsch"
              src="https://moodle.oth-aw.de/theme/image.php/clean/core/1554451383/i/grade_incorrect">
              Wrong answer.</p>]]></text>
            </prtincorrect>
            <multiplicationsign>dot</multiplicationsign>
            <sqrtsign>1</sqrtsign>
            <complexno>i</complexno>
            <inversetrig>cos-1</inversetrig>
            <logicsymbol>lang</logicsymbol>
            <matrixparens>[</matrixparens>
            <variantsselectionseed></variantsselectionseed>
            <input>
              <name>ans1</name>
              <type>dropdown</type>
              <tans>ta1</tans>
              <boxsize>15</boxsize>
              <strictsyntax>1</strictsyntax>
              <insertstars>0</insertstars>
              <syntaxhint></syntaxhint>
              <syntaxattribute>0</syntaxattribute>
              <forbidwords></forbidwords>
              <allowwords></allowwords>
              <forbidfloat>1</forbidfloat>
              <requirelowestterms>0</requirelowestterms>
              <checkanswertype>1</checkanswertype>
              <mustverify>0</mustverify>
              <showvalidation>0</showvalidation>
              <options></options>
            </input>
            <input>
              <name>ans2</name>
              <type>algebraic</type>
              <tans>ta2</tans>
              <boxsize>2</boxsize>
              <strictsyntax>1</strictsyntax>
              <insertstars>0</insertstars>
              <syntaxhint></syntaxhint>
              <syntaxattribute>0</syntaxattribute>
              <forbidwords></forbidwords>
              <allowwords></allowwords>
              <forbidfloat>1</forbidfloat>
              <requirelowestterms>0</requirelowestterms>
              <checkanswertype>1</checkanswertype>
              <mustverify>1</mustverify>
              <showvalidation>3</showvalidation>
              <options></options>
            </input>
            <input>
              <name>ans3</name>
              <type>algebraic</type>
              <tans>ta3</tans>
              <boxsize>2</boxsize>
              <strictsyntax>1</strictsyntax>
              <insertstars>0</insertstars>
              <syntaxhint></syntaxhint>
              <syntaxattribute>0</syntaxattribute>
              <forbidwords></forbidwords>
              <allowwords></allowwords>
              <forbidfloat>1</forbidfloat>
              <requirelowestterms>0</requirelowestterms>
              <checkanswertype>1</checkanswertype>
              <mustverify>1</mustverify>
              <showvalidation>3</showvalidation>
              <options></options>
            </input>
            <input>
              <name>ans4</name>
              <type>algebraic</type>
              <tans>ta4</tans>
              <boxsize>2</boxsize>
              <strictsyntax>1</strictsyntax>
              <insertstars>0</insertstars>
              <syntaxhint></syntaxhint>
              <syntaxattribute>0</syntaxattribute>
              <forbidwords></forbidwords>
              <allowwords></allowwords>
              <forbidfloat>1</forbidfloat>
              <requirelowestterms>0</requirelowestterms>
              <checkanswertype>1</checkanswertype>
              <mustverify>1</mustverify>
              <showvalidation>3</showvalidation>
              <options></options>
            </input>
            <prt>
              <name>prt1</name>
              <value>7.0000000</value>
              <autosimplify>1</autosimplify>
              <feedbackstyle>1</feedbackstyle>
              <feedbackvariables>
                <text></text>
              </feedbackvariables>
              <node>
                <name>0</name>
                <description></description>
                <answertest>AlgEquiv</answertest>
                <sans>ans1</sans>
                <tans>c</tans>
                <testoptions></testoptions>
                <quiet>0</quiet>
                <truescoremode>=</truescoremode>
                <truescore>1.0000000</truescore>
                <truepenalty></truepenalty>
                <truenextnode>-1</truenextnode>
                <trueanswernote>prt1-1-T</trueanswernote>
                <truefeedback format="html">
                  <text></text>
                </truefeedback>
                <falsescoremode>=</falsescoremode>
                <falsescore>0.0000000</falsescore>
                <falsepenalty></falsepenalty>
                <falsenextnode>-1</falsenextnode>
                <falseanswernote>prt1-1-F</falseanswernote>
                <falsefeedback format="html">
                  <text><![CDATA[<p>The correct answer is: "{@ta1[3][3]@}"<br></p>]]></text>
                </falsefeedback>
              </node>
            </prt>
            <prt>
              <name>prt2</name>
              <value>1.0000000</value>
              <autosimplify>1</autosimplify>
              <feedbackstyle>1</feedbackstyle>
              <feedbackvariables>
                <text></text>
              </feedbackvariables>
              <node>
                <name>0</name>
                <description></description>
                <answertest>AlgEquiv</answertest>
                <sans>ans2</sans>
                <tans>ta2</tans>
                <testoptions></testoptions>
                <quiet>0</quiet>
                <truescoremode>=</truescoremode>
                <truescore>1.0000000</truescore>
                <truepenalty></truepenalty>
                <truenextnode>-1</truenextnode>
                <trueanswernote>prt2-1-T</trueanswernote>
                <truefeedback format="html">
                  <text></text>
                </truefeedback>
                <falsescoremode>=</falsescoremode>
                <falsescore>0.0000000</falsescore>
                <falsepenalty></falsepenalty>
                <falsenextnode>-1</falsenextnode>
                <falseanswernote>prt2-1-F</falseanswernote>
                <falsefeedback format="html">
                  <text><![CDATA[<p>The lines are identical for \(t={@ta2@}\).</p>]]></text>
                </falsefeedback>
              </node>
            </prt>
            <prt>
              <name>prt3</name>
              <value>1.0000000</value>
              <autosimplify>1</autosimplify>
              <feedbackstyle>1</feedbackstyle>
              <feedbackvariables>
                <text></text>
              </feedbackvariables>
              <node>
                <name>0</name>
                <description></description>
                <answertest>AlgEquiv</answertest>
                <sans>ans3</sans>
                <tans>ta3</tans>
                <testoptions></testoptions>
                <quiet>0</quiet>
                <truescoremode>=</truescoremode>
                <truescore>1.0000000</truescore>
                <truepenalty></truepenalty>
                <truenextnode>-1</truenextnode>
                <trueanswernote>prt3-1-T</trueanswernote>
                <truefeedback format="html">
                  <text></text>
                </truefeedback>
                <falsescoremode>=</falsescoremode>
                <falsescore>0.0000000</falsescore>
                <falsepenalty></falsepenalty>
                <falsenextnode>-1</falsenextnode>
                <falseanswernote>prt3-1-F</falseanswernote>
                <falsefeedback format="html">
                  <text><![CDATA[<p>The lines are parallel for \(t={@ta3@}\).</p>]]></text>
                </falsefeedback>
              </node>
            </prt>
            <prt>
              <name>prt4</name>
              <value>1.0000000</value>
              <autosimplify>1</autosimplify>
              <feedbackstyle>1</feedbackstyle>
              <feedbackvariables>
                <text></text>
              </feedbackvariables>
              <node>
                <name>0</name>
                <description></description>
                <answertest>AlgEquiv</answertest>
                <sans>ans4</sans>
                <tans>ta4</tans>
                <testoptions></testoptions>
                <quiet>0</quiet>
                <truescoremode>=</truescoremode>
                <truescore>1.0000000</truescore>
                <truepenalty></truepenalty>
                <truenextnode>-1</truenextnode>
                <trueanswernote>prt4-1-T</trueanswernote>
                <truefeedback format="html">
                  <text></text>
                </truefeedback>
                <falsescoremode>=</falsescoremode>
                <falsescore>0.0000000</falsescore>
                <falsepenalty></falsepenalty>
                <falsenextnode>-1</falsenextnode>
                <falseanswernote>prt4-1-F</falseanswernote>
                <falsefeedback format="html">
                  <text><![CDATA[<p>The lines are perpendicular to each other for \(t={@ta4@}\).</p>]]></text>
                </falsefeedback>
              </node>
            </prt>
            <qtest>
              <testcase>1</testcase>
              <description></description>
              <testinput>
                <name>ans1</name>
                <value>c</value>
              </testinput>
              <testinput>
                <name>ans2</name>
                <value>ta2</value>
              </testinput>
              <testinput>
                <name>ans3</name>
                <value>ta3</value>
              </testinput>
              <testinput>
                <name>ans4</name>
                <value>ta4</value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>1.0000000</expectedscore>
                <expectedpenalty>0.0000000</expectedpenalty>
                <expectedanswernote>prt1-1-T</expectedanswernote>
              </expected>
              <expected>
                <name>prt2</name>
                <expectedscore>1.0000000</expectedscore>
                <expectedpenalty>0.0000000</expectedpenalty>
                <expectedanswernote>prt2-1-T</expectedanswernote>
              </expected>
              <expected>
                <name>prt3</name>
                <expectedscore>1.0000000</expectedscore>
                <expectedpenalty>0.0000000</expectedpenalty>
                <expectedanswernote>prt3-1-T</expectedanswernote>
              </expected>
              <expected>
                <name>prt4</name>
                <expectedscore>1.0000000</expectedscore>
                <expectedpenalty>0.0000000</expectedpenalty>
                <expectedanswernote>prt4-1-T</expectedanswernote>
              </expected>
            </qtest>
            <qtest>
              <testcase>2</testcase>
              <description></description>
              <testinput>
                <name>ans1</name>
                <value>d</value>
              </testinput>
              <testinput>
                <name>ans2</name>
                <value>0</value>
              </testinput>
              <testinput>
                <name>ans3</name>
                <value>1</value>
              </testinput>
              <testinput>
                <name>ans4</name>
                <value>-1</value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>0.0000000</expectedscore>
                <expectedpenalty>0.1000000</expectedpenalty>
                <expectedanswernote>prt1-1-F</expectedanswernote>
              </expected>
              <expected>
                <name>prt2</name>
                <expectedscore>0.0000000</expectedscore>
                <expectedpenalty>0.1000000</expectedpenalty>
                <expectedanswernote>prt2-1-F</expectedanswernote>
              </expected>
              <expected>
                <name>prt3</name>
                <expectedscore>0.0000000</expectedscore>
                <expectedpenalty>0.1000000</expectedpenalty>
                <expectedanswernote>prt3-1-F</expectedanswernote>
              </expected>
              <expected>
                <name>prt4</name>
                <expectedscore>0.0000000</expectedscore>
                <expectedpenalty>0.1000000</expectedpenalty>
                <expectedanswernote>prt4-1-F</expectedanswernote>
              </expected>
            </qtest>
          </question>
        </quiz>',
        'plots' =>
        '<quiz>
          <question type="stack">
            <name>
              <text>Graphical differentiation</text>
            </name>
            <questiontext format="html">
              <text><![CDATA[<p></p>
        <p>The graph of a function \(f\) is given below.<br></p>

        <center>
         {@p[1]@}
        </center>

        <p>Which of the following diagrams could show the graph of the derivative function of \(f\)?</p>
        <p>[[input:ans1]] [[validation:ans1]]</p>
        <p></p>]]></text>
            </questiontext>
            <generalfeedback format="html">
              <text></text>
            </generalfeedback>
            <defaultgrade>1</defaultgrade>
            <penalty>0.1</penalty>
            <hidden>0</hidden>
            <idnumber></idnumber>
            <stackversion>
              <text>2020052700</text>
            </stackversion>
            <questionvariables>
              <text><![CDATA[/*OTH Amberg-Weiden*/
        /*A polynomial of degree 3 is to be differentiated graphically*/

        x1: rand_with_step(-2,2,1);
        x2: rand_with_prohib(-2,2,[x1]);
        f1: rand([-1,1])*(x-x1)^2*(x-x2)+rand_with_step(-1,1,0.5);

        xmin: min(x1,x2,0)-0.5;
        xmax: max(x1,x2,0)+0.5;

        /*Find the division of the y-axis*/
        z_der:block([z], z:realroots(diff(f1,x)), z:map(rhs, z), return(z));
        xpos:append([xmin,xmax],z_der);
        ymin: lmin(ev(f1,x=xpos));
        ymax: lmax(ev(f1,x=xpos));
        ymax: max(abs(ymin),abs(ymax));
        ymin:-ymax;
        dx:(xmax-xmin)/40;
        dy:(ymax-ymin)/40;

        /*Define the options*/
        n:(rand(5)+1)/4;
        g:[f1,diff(f1,x),integrate(f1,x),-n*diff(f1,x),-n*f1];

        /*Plots*/
        gcol:[blue,red,red,red,red];
        p:makelist( plot(g[i], [x,xmin,xmax], [y,ymin,ymax], [axes,solid], [box,false],
        [xtics,xmax+1,0,xmax+1],[ytics,ymax+1,0,ymax+1], [label,["x",xmax-dx,-dy],
        ["y",-dx,ymax-dy]], [color,gcol[i]]),i,1,5);

        /*Model answer*/
        ta:[[a,true,p[2]],[b,false,p[3]],[c,false,p[4]],[d,false,p[5]]];
        n:random_permutation([1,2,3,4]);
        ta:makelist(ta[n[i]],i,1,4);

        /*For the answer note:*/
        gcol2:[blue,green,red,red,red];
        p2:makelist( plot(g[i], [x,xmin,xmax], [y,ymin,ymax], [axes,solid], [box,false],
        [xtics,xmax+1,0,xmax+1],[ytics,ymax+1,0,ymax+1],[color,gcol2[i]], [size,200,200]),i,1,5);
        p2:append([p2[1]], makelist(p2[n[i]+1],i,1,4) );]]></text>
            </questionvariables>
            <specificfeedback format="html">
              <text>[[feedback:prt1]]</text>
            </specificfeedback>
            <questionnote>
              <text><![CDATA[<table>
        <tr>
          <td>
            {@p2[1]@}
          </td>
          <td>
            {@p2[2]@}
          </td>
          <td>
           {@p2[3]@}
          </td>
          <td>
            {@p2[4]@}
          </td>
          <td>
          {@p2[5]@}
          </td>
        </tr>
        </table>]]></text>
            </questionnote>
            <questiondescription format="moodle_auto_format">
              <text></text>
            </questiondescription>
            <questionsimplify>1</questionsimplify>
            <assumepositive>0</assumepositive>
            <assumereal>0</assumereal>
            <prtcorrect format="html">
              <text><![CDATA[<p><img alt="Richtig" title="Richtig"
              src="https://moodle.oth-aw.de/theme/image.php/clean/core/1554451383/i/grade_correct">Correct answer,
              well done!</p>]]></text>
            </prtcorrect>
            <prtpartiallycorrect format="html">
              <text></text>
            </prtpartiallycorrect>
            <prtincorrect format="html">
              <text><![CDATA[<p><img alt="Falsch" title="Falsch"
              src="https://moodle.oth-aw.de/theme/image.php/clean/core/1554451383/i/grade_incorrect">
              Wrong answer.</p>]]></text>
            </prtincorrect>
            <multiplicationsign>dot</multiplicationsign>
            <sqrtsign>1</sqrtsign>
            <complexno>i</complexno>
            <inversetrig>cos-1</inversetrig>
            <logicsymbol>lang</logicsymbol>
            <matrixparens>[</matrixparens>
            <variantsselectionseed></variantsselectionseed>
            <input>
              <name>ans1</name>
              <type>radio</type>
              <tans>ta</tans>
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
              <mustverify>0</mustverify>
              <showvalidation>0</showvalidation>
              <options></options>
            </input>
            <prt>
              <name>prt1</name>
              <value>1.0000000</value>
              <autosimplify>1</autosimplify>
              <feedbackstyle>1</feedbackstyle>
              <feedbackvariables>
                <text></text>
              </feedbackvariables>
              <node>
                <name>0</name>
                <description></description>
                <answertest>AlgEquiv</answertest>
                <sans>ans1</sans>
                <tans>a</tans>
                <testoptions></testoptions>
                <quiet>1</quiet>
                <truescoremode>=</truescoremode>
                <truescore>1.0000000</truescore>
                <truepenalty></truepenalty>
                <truenextnode>-1</truenextnode>
                <trueanswernote>prt1-1-T</trueanswernote>
                <truefeedback format="html">
                  <text></text>
                </truefeedback>
                <falsescoremode>=</falsescoremode>
                <falsescore>0.0000000</falsescore>
                <falsepenalty></falsepenalty>
                <falsenextnode>-1</falsenextnode>
                <falseanswernote>prt1-1-F</falseanswernote>
                <falsefeedback format="html">
                  <text></text>
                </falsefeedback>
              </node>
            </prt>
            <deployedseed>1366340640</deployedseed>
            <deployedseed>154527566</deployedseed>
            <deployedseed>217439111</deployedseed>
            <deployedseed>1423545490</deployedseed>
            <deployedseed>1987028544</deployedseed>
            <qtest>
              <testcase>1</testcase>
              <description></description>
              <testinput>
                <name>ans1</name>
                <value>a</value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>1.0000000</expectedscore>
                <expectedpenalty>0.0000000</expectedpenalty>
                <expectedanswernote>prt1-1-T</expectedanswernote>
              </expected>
            </qtest>
          </question>
        </quiz>',
        'iframes' =>
        '<quiz>
        <!-- question: 126427  -->
          <question type="stack">
            <name>
              <text>Interactivity: Drag points to be increasing</text>
            </name>
            <questiontext format="html">
              <text><![CDATA[<p>Drag the points \(u_1,\ldots, u_8\) so that
              they show the first 8 terms of an increasing sequence.</p>
        <p style="display:none">[[input:da_ans1]] [[validation:da_ans1]]</p>
        [[jsxgraph width="360px" height="360px" input-ref-da_ans1="inputans1"]] JXG.Options.axis.ticks.minorTicks = 0;
        var board = JXG.JSXGraph.initBoard(divid, { boundingbox: [-1, 10, 9, -10], axis: true,
          grid: true, showNavigation: false, showCopyright: false
        }); /* State represented as a JS-object, first define default then try loading the
        stored values. */ var state = [1,1,1,1,1,1,1,1]; var stateInput =
        document.getElementById(inputans1); if (stateInput.value) {
          if(stateInput.value != \'\') {
            state = JSON.parse(stateInput.value);
          }
        } /* create a group of vertical lines x=i with a draggable point on each one */ var vline = [];
        var answer = []; for (let i of [1, 2, 3, 4, 5, 6, 7, 8]) { vline.push(board.create(\'line\', [i, -1, 0] /*
          given [c,a,b] plot ax+by+c=0
        */ , { visible: false })); /* create the draggable points, each constrained to lie on one of the vertical lines,
        and using the existing state for the y-coordinate */ answer.push(board.create(\'glider\',
        [i, state[i-1], vline[i - 1]], { color: \'#003399\',
        name: "u" + i, showInfobox: false })); } /* update the stored state when things change */
        board.on(\'update\', function() { var vals = []; for (let pts of answer) { vals.push(pts.Y()); };
        stateInput.value = "[" + vals + "]"; }); [[/jsxgraph]]]]></text>
            </questiontext>
            <generalfeedback format="html">
              <text></text>
            </generalfeedback>
            <defaultgrade>1</defaultgrade>
            <penalty>0.1</penalty>
            <hidden>0</hidden>
            <idnumber></idnumber>
            <stackversion>
              <text>2023010400</text>
            </stackversion>
            <questionvariables>
              <text>ta1:[1,2,3,4,5,6,7,8];</text>
            </questionvariables>
            <specificfeedback format="html">
              <text>[[feedback:prt1]]</text>
            </specificfeedback>
            <questionnote>
              <text></text>
            </questionnote>
            <questiondescription format="moodle_auto_format">
              <text></text>
            </questiondescription>
            <questionsimplify>1</questionsimplify>
            <assumepositive>0</assumepositive>
            <assumereal>0</assumereal>
            <prtcorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i>
              </span> Correct answer, well done.]]></text>
            </prtcorrect>
            <prtpartiallycorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i>
              </span> Your answer is partially correct.]]></text>
            </prtpartiallycorrect>
            <prtincorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i>
              </span> Incorrect answer.]]></text>
            </prtincorrect>
            <multiplicationsign>dot</multiplicationsign>
            <sqrtsign>1</sqrtsign>
            <complexno>i</complexno>
            <inversetrig>cos-1</inversetrig>
            <logicsymbol>lang</logicsymbol>
            <matrixparens>[</matrixparens>
            <variantsselectionseed></variantsselectionseed>
            <input>
              <name>da_ans1</name>
              <type>algebraic</type>
              <tans>ta1</tans>
              <boxsize>15</boxsize>
              <strictsyntax>1</strictsyntax>
              <insertstars>0</insertstars>
              <syntaxhint></syntaxhint>
              <syntaxattribute>0</syntaxattribute>
              <forbidwords></forbidwords>
              <allowwords></allowwords>
              <forbidfloat>0</forbidfloat>
              <requirelowestterms>0</requirelowestterms>
              <checkanswertype>0</checkanswertype>
              <mustverify>0</mustverify>
              <showvalidation>0</showvalidation>
              <options></options>
            </input>
            <prt>
              <name>prt1</name>
              <value>1.0000000</value>
              <autosimplify>1</autosimplify>
              <feedbackstyle>1</feedbackstyle>
              <feedbackvariables>
                <text><![CDATA[termdiffs:makelist(da_ans1[i+1]-da_ans1[i],i,1,7);
        increased:map(lambda([x],is(x>0)),termdiffs);
        feedback:delete(null,makelist(if increased[i] then null else u[i+1]<=u[i], i, 1, 7));]]></text>
              </feedbackvariables>
              <node>
                <name>0</name>
                <description></description>
                <answertest>AlgEquiv</answertest>
                <sans>setify(increased)</sans>
                <tans>{true}</tans>
                <testoptions></testoptions>
                <quiet>1</quiet>
                <truescoremode>=</truescoremode>
                <truescore>1</truescore>
                <truepenalty></truepenalty>
                <truenextnode>-1</truenextnode>
                <trueanswernote>prt1-1-T</trueanswernote>
                <truefeedback format="html">
                  <text></text>
                </truefeedback>
                <falsescoremode>=</falsescoremode>
                <falsescore>0</falsescore>
                <falsepenalty></falsepenalty>
                <falsenextnode>-1</falsenextnode>
                <falseanswernote>prt1-1-F</falseanswernote>
                <falsefeedback format="html">
                  <text>It looks like your sequence is not increasing, since {@first(feedback)@}.</text>
                </falsefeedback>
              </node>
            </prt>
            <qtest>
              <testcase>1</testcase>
              <description></description>
              <testinput>
                <name>da_ans1</name>
                <value>ta1</value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>1.0000000</expectedscore>
                <expectedpenalty>0.0000000</expectedpenalty>
                <expectedanswernote>prt1-1-T</expectedanswernote>
              </expected>
            </qtest>
            <qtest>
              <testcase>2</testcase>
              <description></description>
              <testinput>
                <name>da_ans1</name>
                <value>reverse(ta1)</value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>0.0000000</expectedscore>
                <expectedpenalty>0.1000000</expectedpenalty>
                <expectedanswernote>prt1-1-F</expectedanswernote>
              </expected>
            </qtest>
          </question>
        </quiz>',
        'download' =>
        '<quiz>
          <question type="stack">
            <name>
              <text>Serving out data: download file</text>
            </name>
            <questiontext format="html">
              <text><![CDATA[[[comment]]Use them like this in the question-text.[[/comment]]
        <p>Load the data from
        <a href="[[textdownload name="data.csv"]]{@stack_csv_formatter(data,lab)@}[[/textdownload]]">this file</a>
        and calculate the mean of data set \(A\).</p>
        <p>[[input:ans1]] [[validation:ans1]]</p>]]></text>
            </questiontext>
            <generalfeedback format="moodle_auto_format">
              <text></text>
            </generalfeedback>
            <defaultgrade>1</defaultgrade>
            <penalty>0.1</penalty>
            <hidden>0</hidden>
            <idnumber></idnumber>
            <stackversion>
              <text>2023010401</text>
            </stackversion>
            <questionvariables>
              <text><![CDATA[/* Define these in question variables: */
        lab: ["A","B","C"];
        data: makelist([rand(322)/100.0,rand(600)/100.0,rand(300)/100.0], i, 50);
        /* And make a question. */
        taA: mean(map(first,data));
        taB: mean(map(second,data));
        taC: mean(map(third,data));
        ]]></text>
            </questionvariables>
            <specificfeedback format="html">
              <text>[[feedback:prt1]]</text>
            </specificfeedback>
            <questionnote>
              <text></text>
            </questionnote>
            <questionsimplify>1</questionsimplify>
            <assumepositive>0</assumepositive>
            <assumereal>0</assumereal>
            <prtcorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:green;">
              <i class="fa fa-check"></i></span> Correct answer, well done.]]></text>
            </prtcorrect>
            <prtpartiallycorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:orange;">
              <i class="fa fa-adjust"></i></span> Your answer is partially correct.]]></text>
            </prtpartiallycorrect>
            <prtincorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span>
              Incorrect answer.]]></text>
            </prtincorrect>
            <decimals>.</decimals>
            <scientificnotation>*10</scientificnotation>
            <multiplicationsign>dot</multiplicationsign>
            <sqrtsign>1</sqrtsign>
            <complexno>i</complexno>
            <inversetrig>cos-1</inversetrig>
            <logicsymbol>lang</logicsymbol>
            <matrixparens>[</matrixparens>
            <variantsselectionseed></variantsselectionseed>
            <input>
              <name>ans1</name>
              <type>numerical</type>
              <tans>taA</tans>
              <boxsize>15</boxsize>
              <strictsyntax>1</strictsyntax>
              <insertstars>0</insertstars>
              <syntaxhint></syntaxhint>
              <syntaxattribute>0</syntaxattribute>
              <forbidwords></forbidwords>
              <allowwords></allowwords>
              <forbidfloat>0</forbidfloat>
              <requirelowestterms>0</requirelowestterms>
              <checkanswertype>0</checkanswertype>
              <mustverify>1</mustverify>
              <showvalidation>1</showvalidation>
              <options>minsf:3</options>
            </input>
            <prt>
              <name>prt1</name>
              <value>1.0000000</value>
              <autosimplify>1</autosimplify>
              <feedbackstyle>1</feedbackstyle>
              <feedbackvariables>
                <text></text>
              </feedbackvariables>
              <node>
                <name>0</name>
                <answertest>NumRelative</answertest>
                <sans>ans1</sans>
                <tans>taA</tans>
                <testoptions>0.01</testoptions>
                <quiet>0</quiet>
                <truescoremode>=</truescoremode>
                <truescore>1</truescore>
                <truepenalty></truepenalty>
                <truenextnode>-1</truenextnode>
                <trueanswernote>prt1-1-T</trueanswernote>
                <truefeedback format="html">
                  <text></text>
                </truefeedback>
                <falsescoremode>=</falsescoremode>
                <falsescore>0</falsescore>
                <falsepenalty></falsepenalty>
                <falsenextnode>-1</falsenextnode>
                <falseanswernote>prt1-1-F</falseanswernote>
                <falsefeedback format="html">
                  <text></text>
                </falsefeedback>
              </node>
            </prt>
            <deployedseed>874478059</deployedseed>
            <deployedseed>1358483538</deployedseed>
            <deployedseed>372918353</deployedseed>
            <deployedseed>563119235</deployedseed>
            <deployedseed>252265368</deployedseed>
            <qtest>
              <testcase>1</testcase>
              <testinput>
                <name>ans1</name>
                <value>taA</value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>1.0000000</expectedscore>
                <expectedpenalty>0.0000000</expectedpenalty>
                <expectedanswernote>prt1-1-T</expectedanswernote>
              </expected>
            </qtest>
          </question>
        </quiz>',
        'test' => '
        <quiz>
          <question type="stack">
            <name>
              <text>Algebraic input</text>
            </name>
            <questiontext format="html">
              <text><![CDATA[<p>Type in {@ta@}.</p><p>[[input:ans1]] [[validation:ans1]]</p>
        <p>(Note, this assumes single variable variable names)</p>]]></text>
            </questiontext>
            <generalfeedback format="html">
              <text></text>
            </generalfeedback>
            <defaultgrade>1.0000000</defaultgrade>
            <penalty>0.1000000</penalty>
            <hidden>0</hidden>
            <idnumber></idnumber>
            <stackversion>
              <text>2020123000</text>
            </stackversion>
            <questionvariables>
              <text>ta:a*b</text>
            </questionvariables>
            <specificfeedback format="html">
              <text>[[feedback:prt1]]</text>
            </specificfeedback>
            <questionnote>
              <text></text>
            </questionnote>
            <questionsimplify>1</questionsimplify>
            <assumepositive>0</assumepositive>
            <assumereal>0</assumereal>
            <prtcorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:green;">
              <i class="fa fa-check"></i></span> Correct answer, well done.]]></text>
            </prtcorrect>
            <prtpartiallycorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:orange;">
              <i class="fa fa-adjust"></i></span> Your answer is partially correct.]]></text>
            </prtpartiallycorrect>
            <prtincorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:red;">
              <i class="fa fa-times"></i></span> Incorrect answer.]]></text>
            </prtincorrect>
            <multiplicationsign>dot</multiplicationsign>
            <sqrtsign>1</sqrtsign>
            <complexno>i</complexno>
            <inversetrig>cos-1</inversetrig>
            <logicsymbol>lang</logicsymbol>
            <matrixparens>[</matrixparens>
            <variantsselectionseed></variantsselectionseed>
            <input>
              <name>ans1</name>
              <type>algebraic</type>
              <tans>ta</tans>
              <boxsize>15</boxsize>
              <strictsyntax>1</strictsyntax>
              <insertstars>2</insertstars>
              <syntaxhint></syntaxhint>
              <syntaxattribute>0</syntaxattribute>
              <forbidwords>solve</forbidwords>
              <allowwords></allowwords>
              <forbidfloat>1</forbidfloat>
              <requirelowestterms>0</requirelowestterms>
              <checkanswertype>1</checkanswertype>
              <mustverify>1</mustverify>
              <showvalidation>1</showvalidation>
              <options></options>
            </input>
            <prt>
              <name>prt1</name>
              <value>1.0000000</value>
              <autosimplify>1</autosimplify>
              <feedbackstyle>1</feedbackstyle>
              <feedbackvariables>
                <text></text>
              </feedbackvariables>
              <node>
                <name>0</name>
                <answertest>AlgEquiv</answertest>
                <sans>ans1</sans>
                <tans>ta</tans>
                <testoptions></testoptions>
                <quiet>0</quiet>
                <truescoremode>=</truescoremode>
                <truescore>1.0000000</truescore>
                <truepenalty></truepenalty>
                <truenextnode>-1</truenextnode>
                <trueanswernote>prt1-1-T</trueanswernote>
                <truefeedback format="html">
                  <text></text>
                </truefeedback>
                <falsescoremode>=</falsescoremode>
                <falsescore>0.0000000</falsescore>
                <falsepenalty></falsepenalty>
                <falsenextnode>-1</falsenextnode>
                <falseanswernote>prt1-1-F</falseanswernote>
                <falsefeedback format="html">
                  <text></text>
                </falsefeedback>
              </node>
            </prt>
            <qtest>
              <testcase>1</testcase>
              <testinput>
                <name>ans1</name>
                <value>37</value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>1.0000000</expectedscore>
                <expectedpenalty>0.0000000</expectedpenalty>
                <expectedanswernote>prt1-1-T</expectedanswernote>
              </expected>
            </qtest>
            <qtest>
              <testcase>2</testcase>
              <testinput>
                <name>ans1</name>
                <value></value>
              </testinput>
              <expected>
                <name>prt1</name>
                <expectedscore>1.0000000</expectedscore>
                <expectedpenalty>0.0000000</expectedpenalty>
                <expectedanswernote>NULL</expectedanswernote>
              </expected>
            </qtest>
          </question>
        </quiz>',
        'test2' => '
        <quiz>
          <question type="stack">
            <name>
              <text>Algebraic input</text>
            </name>
            <questiontext format="html">
              <text><![CDATA[<p><img src="test.png"></img>Type in {@ta@}.</p><p>[[input:ans1]] [[validation:ans1]]</p>
        <p>(Note, this assumes single variable variable names)</p>]]></text>
            </questiontext>
            <generalfeedback format="html">
              <text></text>
            </generalfeedback>
            <defaultgrade>1.0000000</defaultgrade>
            <penalty>0.1000000</penalty>
            <hidden>0</hidden>
            <idnumber></idnumber>
            <stackversion>
              <text>2020123000</text>
            </stackversion>
            <questionvariables>
              <text>ta:a*b</text>
            </questionvariables>
            <specificfeedback format="html">
              <text>[[feedback:prt1]]</text>
            </specificfeedback>
            <questionnote>
              <text></text>
            </questionnote>
            <questionsimplify>1</questionsimplify>
            <assumepositive>0</assumepositive>
            <assumereal>0</assumereal>
            <prtcorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:green;">
              <i class="fa fa-check"></i></span> Correct answer, well done.]]></text>
            </prtcorrect>
            <prtpartiallycorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:orange;">
              <i class="fa fa-adjust"></i></span> Your answer is partially correct.]]></text>
            </prtpartiallycorrect>
            <prtincorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:red;">
              <i class="fa fa-times"></i></span> Incorrect answer.]]></text>
            </prtincorrect>
            <multiplicationsign>dot</multiplicationsign>
            <sqrtsign>1</sqrtsign>
            <complexno>i</complexno>
            <inversetrig>cos-1</inversetrig>
            <logicsymbol>lang</logicsymbol>
            <matrixparens>[</matrixparens>
            <variantsselectionseed></variantsselectionseed>
            <input>
              <name>ans1</name>
              <type>algebraic</type>
              <tans>ta</tans>
              <boxsize>15</boxsize>
              <strictsyntax>1</strictsyntax>
              <insertstars>2</insertstars>
              <syntaxhint></syntaxhint>
              <syntaxattribute>0</syntaxattribute>
              <forbidwords>solve</forbidwords>
              <allowwords></allowwords>
              <forbidfloat>1</forbidfloat>
              <requirelowestterms>0</requirelowestterms>
              <checkanswertype>1</checkanswertype>
              <mustverify>1</mustverify>
              <showvalidation>1</showvalidation>
              <options></options>
            </input>
            <prt>
              <name>prt1</name>
              <value>1.0000000</value>
              <autosimplify>1</autosimplify>
              <feedbackstyle>1</feedbackstyle>
              <feedbackvariables>
                <text></text>
              </feedbackvariables>
              <node>
                <name>0</name>
                <answertest>AlgEquiv</answertest>
                <sans>ans1</sans>
                <tans>ta</tans>
                <testoptions></testoptions>
                <quiet>0</quiet>
                <truescoremode>=</truescoremode>
                <truescore>1.0000000</truescore>
                <truepenalty></truepenalty>
                <truenextnode>-1</truenextnode>
                <trueanswernote>prt1-1-T</trueanswernote>
                <truefeedback format="html">
                  <text></text>
                </truefeedback>
                <falsescoremode>=</falsescoremode>
                <falsescore>0.0000000</falsescore>
                <falsepenalty></falsepenalty>
                <falsenextnode>-1</falsenextnode>
                <falseanswernote>prt1-1-F</falseanswernote>
                <falsefeedback format="html">
                  <text></text>
                </falsefeedback>
              </node>
            </prt>
          </question>
        </quiz>',
        'test3' => '
        <quiz>
          <question type="stack">
            <name>
              <text>Algebraic input</text>
            </name>
            <questiontext format="html">
              <text><![CDATA[<p>Type in {@ta@}.</p><p>[[input:ans1]] [[validation:ans1]]</p>
        <p>(Note, this assumes single variable variable names)</p>]]></text>
            </questiontext>
            <generalfeedback format="html">
              <text></text>
            </generalfeedback>
            <defaultgrade>1.0000000</defaultgrade>
            <penalty>0.1000000</penalty>
            <hidden>0</hidden>
            <idnumber></idnumber>
            <stackversion>
              <text>2020123000</text>
            </stackversion>
            <questionvariables>
              <text>ta:a*b</text>
            </questionvariables>
            <specificfeedback format="html">
              <text>[[feedback:prt1]]</text>
            </specificfeedback>
            <questionnote>
              <text></text>
            </questionnote>
            <questionsimplify>1</questionsimplify>
            <assumepositive>0</assumepositive>
            <assumereal>0</assumereal>
            <prtcorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:green;">
              <i class="fa fa-check"></i></span> Correct answer, well done.]]></text>
            </prtcorrect>
            <prtpartiallycorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:orange;">
              <i class="fa fa-adjust"></i></span> Your answer is partially correct.]]></text>
            </prtpartiallycorrect>
            <prtincorrect format="html">
              <text><![CDATA[<span style="font-size: 1.5em; color:red;">
              <i class="fa fa-times"></i></span> Incorrect answer.]]></text>
            </prtincorrect>
            <multiplicationsign>dot</multiplicationsign>
            <sqrtsign>1</sqrtsign>
            <complexno>i</complexno>
            <inversetrig>cos-1</inversetrig>
            <logicsymbol>lang</logicsymbol>
            <matrixparens>[</matrixparens>
            <variantsselectionseed></variantsselectionseed>
            <input>
              <name>ans1</name>
              <type>algebraic</type>
              <tans>ta</tans>
              <boxsize>15</boxsize>
              <strictsyntax>1</strictsyntax>
              <insertstars>2</insertstars>
              <syntaxhint></syntaxhint>
              <syntaxattribute>0</syntaxattribute>
              <forbidwords>solve</forbidwords>
              <allowwords></allowwords>
              <forbidfloat>1</forbidfloat>
              <requirelowestterms>0</requirelowestterms>
              <checkanswertype>1</checkanswertype>
              <mustverify>1</mustverify>
              <showvalidation>1</showvalidation>
              <options></options>
            </input>
            <prt>
              <name>prt1</name>
              <value>1.0000000</value>
              <autosimplify>1</autosimplify>
              <feedbackstyle>1</feedbackstyle>
              <feedbackvariables>
                <text></text>
              </feedbackvariables>
              <node>
                <name>0</name>
                <answertest>AlgEquiv</answertest>
                <sans>ans1</sans>
                <tans>wrong</tans>
                <testoptions></testoptions>
                <quiet>0</quiet>
                <truescoremode>=</truescoremode>
                <truescore>1.0000000</truescore>
                <truepenalty></truepenalty>
                <truenextnode>-1</truenextnode>
                <trueanswernote>prt1-1-T</trueanswernote>
                <truefeedback format="html">
                  <text></text>
                </truefeedback>
                <falsescoremode>=</falsescoremode>
                <falsescore>0.0000000</falsescore>
                <falsepenalty></falsepenalty>
                <falsenextnode>-1</falsenextnode>
                <falseanswernote>prt1-1-F</falseanswernote>
                <falsefeedback format="html">
                  <text></text>
                </falsefeedback>
              </node>
            </prt>
          </question>
        </quiz>',
    ];

    protected static array $answers = [
        'matrices_correct' => '{"ans1_sub_0_0": "35", "ans1_sub_0_1": "30", "ans1_sub_1_0": "28", "ans1_sub_1_1": "24"}',
        'multiple_mixed' => '{"ans1": "3", "ans2": "1", "ans3": "0", "ans4": "0"}',
    ];

    public static function get_question_string(string $name): string {
        return self::$questiondata[$name];
    }

    public static function get_answer_string(string $name): string {
        return self::$answers[$name];
    }
}
