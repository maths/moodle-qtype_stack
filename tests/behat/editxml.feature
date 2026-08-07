@qtype @qtype_stack
Feature: Test editing XML of a question.
  In order easilt edit PRTs
  As an teacher
  I need to be able to edit their XML.

  Background:
    Given I set up STACK using the PHPUnit configuration
    And the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name                  | template |
      | Test questions   | stack | Simple STACK question | test1    |
      | Test questions   | truefalse | Simple true false question | true |

  @javascript
  Scenario: Update XML with bad XML
    When I am on the "Course 1" "core_question > course question bank" page logged in as "teacher"
    And I choose "STACK question dashboard" action for "Simple STACK question" in the question bank
    And I follow "Edit question XML"
    And I set the following fields to these values:
      | Question XML        | Broken question                                                       |
    And I press "id_submitbutton"
    Then I should see "QUESTION WAS NOT SAVED"
    And I should see "Version 1"
    And the following fields match these values:
      | Question XML        | Broken question                                                       |
    And I set the field "Question XML" to multiline:
    """
<quiz>
<!-- question: 446000  -->
  <question type="stack">
    <name>
      <text>Simple STACK question</text>
    </name>
    <questiontext format="html">
      <text>Find
                       \[ \int {@p@} d{@v@}\]
                       [[input:ans1]]
                       [[validation:ans1]]</text>
    </questiontext>
    <generalfeedback format="html">
      <text>We can either do this question by inspection (i.e. spot the answer)
                               or in a more formal manner by using the substitution
                               \[ u = ({@v@}-{@a@}).\]
                               Then, since $\frac{d}{d{@v@}}u=1$ we have
                               \[ \int {@p@} d{@v@} = \int u^{@n@} du = \frac{u^{@n+1@}}{@n+1@}+c = {@ta@}+c.\]</text>
    </generalfeedback>
    <defaultgrade>4</defaultgrade>
    <penalty>-1</penalty>
    <hidden>0</hidden>
    <idnumber></idnumber>
    <stackversion>
      <text>2025092900</text>
    </stackversion>
    <questionvariables>
      <text>n : rand(5)+3; a : rand(5)+3; v : x; p : (v-a)^n; ta : (x-7)^4/4; ta1 : ta</text>
    </questionvariables>
    <specificfeedback format="html">
      <text>[[feedback:PotResTree_1]]</text>
    </specificfeedback>
    <questionnote format="html">
      <text>{@p@}, {@ta@}, updated.</text>
    </questionnote>
    <questiondescription format="html">
      <text>This is a revised test question.</text>
    </questiondescription>
    <questionsimplify>1</questionsimplify>
    <assumepositive>0</assumepositive>
    <assumereal>0</assumereal>
    <prtcorrect format="html">
      <text>Correct answer, well done!</text>
    </prtcorrect>
    <prtpartiallycorrect format="html">
      <text>Your answer is partially correct!</text>
    </prtpartiallycorrect>
    <prtincorrect format="html">
      <text>Incorrect answer :-(</text>
    </prtincorrect>
    <decimals>.</decimals>
    <scientificnotation>*10</scientificnotation>
    <multiplicationsign>dot</multiplicationsign>
    <sqrtsign>1</sqrtsign>
    <complexno>i</complexno>
    <inversetrig>cos-1</inversetrig>
    <logicsymbol>lang</logicsymbol>
    <matrixparens>[</matrixparens>
    <isbroken>0</isbroken>
    <variantsselectionseed></variantsselectionseed>
    <input>
      <name>ans1</name>
      <type>algebraic</type>
      <tans>ta+c</tans>
      <boxsize>20</boxsize>
      <strictsyntax>1</strictsyntax>
      <insertstars>0</insertstars>
      <syntaxhint></syntaxhint>
      <syntaxattribute>0</syntaxattribute>
      <forbidwords>int, [[BASIC-ALGEBRA]]</forbidwords>
      <allowwords>popup, boo, Sin</allowwords>
      <forbidfloat>1</forbidfloat>
      <requirelowestterms>0</requirelowestterms>
      <checkanswertype>0</checkanswertype>
      <mustverify>1</mustverify>
      <showvalidation>1</showvalidation>
      <options></options>
    </input>
    <prt>
      <name>PotResTree_1</name>
      <value>1.0000000</value>
      <autosimplify>1</autosimplify>
      <feedbackstyle>1</feedbackstyle>
      <feedbackvariables>
        <text>sa:subst(x=-x,ans1)+ans1</text>
      </feedbackvariables>
      <node>
        <name>0</name>
        <description>Anti-derivative test</description>
        <answertest>Int</answertest>
        <sans>ans1+0</sans>
        <tans>ta</tans>
        <testoptions>x</testoptions>
        <quiet>0</quiet>
        <truescoremode>=</truescoremode>
        <truescore>1</truescore>
        <truepenalty></truepenalty>
        <truenextnode>-1</truenextnode>
        <trueanswernote>PotResTree_1-1-T</trueanswernote>
        <truefeedback format="html">
          <text></text>
        </truefeedback>
        <falsescoremode>=</falsescoremode>
        <falsescore>0</falsescore>
        <falsepenalty></falsepenalty>
        <falsenextnode>-1</falsenextnode>
        <falseanswernote>PotResTree_1-1-F</falseanswernote>
        <falsefeedback format="html">
          <text></text>
        </falsefeedback>
      </node>
    </prt>
    <hint format="html">
      <text><![CDATA[Hint 1<br>]]></text>
    </hint>
    <hint format="html">
      <text><![CDATA[<p>Hint 2<br></p>]]></text>
    </hint>
  </question>

</quiz>
"""
    And I press "id_submitbutton"
    And I should see "Version 2"
    And I should see "The penalty must be a numeric value between 0 and 1"
    And I should see "The question has been marked as broken"
    And I follow "STACK question dashboard"
    And I follow "Compare question XML"
    Then I should see "Compare question XML"
    And the field "Version" matches value "2"
    And the field "Compared with version" matches value "1"
    And I should see "Latest"
    And I should see "Question"
    And I set the field "Version" to "1"
    And the field "Version" matches value "1"
    And I follow "Latest"
    And the field "Version" matches value "2"
    And I should see "Compared with version"
    And I should see "</quiz>"
    And ".stack-xml-compare-diff" "css_element" should exist
    And ".stack-xml-compare-row-added" "css_element" should exist in the "-1" "table_row"
    And I should see "<penalty>" in the "-1" "table_row"
    And ".stack-xml-compare-inline-added" "css_element" should exist in the "-1" "table_row"
    And ".stack-xml-compare-row-deleted" "css_element" should exist in the "0.4" "table_row"
    And I should see "<penalty>" in the "0.4" "table_row"
    And ".stack-xml-compare-inline-deleted" "css_element" should exist in the "0.4" "table_row"
    And I set the field "Question" to "Simple true false question"
    And I wait until "Simple true false question" "text" exists
    And I should see "<question type=\"truefalse\">"
    And I should not see "STACK question dashboard"
    And I should not see "Edit question XML"
    And I set the field "Question" to "Simple STACK question"
    And I wait until "Simple STACK question" "text" exists
    And I should see "</quiz>"
    And I follow "Show differences only"
    And I should see "Show all lines"
    And I should not see "</quiz>"
    And ".stack-xml-compare-row-same" "css_element" should exist
    And ".stack-xml-compare-row-added" "css_element" should exist in the "-1" "table_row"
    And ".stack-xml-compare-row-deleted" "css_element" should exist in the "0.4" "table_row"
    And I follow "Show all lines"
    And I should see "Show differences only"
    And ".stack-xml-compare-row-same" "css_element" should exist
    And I follow "Show split display"
    And I should see "Show unified display"
    And I should see "-1" in the "<penalty>" "table_row"
    And I should see "0.4" in the "<penalty>" "table_row"
    And ".stack-xml-compare-inline-added" "css_element" should exist in the "<penalty>" "table_row"
    And ".stack-xml-compare-inline-deleted" "css_element" should exist in the "<penalty>" "table_row"
    And ".stack-xml-compare-code-compared" "css_element" should exist in the "<penalty>" "table_row"
    And I should see "This is a basic test question." in the "This is a revised test question." "table_row"
    And ".stack-xml-compare-inline-added" "css_element" should exist in the "This is a revised test question." "table_row"
    And ".stack-xml-compare-inline-deleted" "css_element" should exist in the "This is a revised test question." "table_row"
    And I should see "{@p@}, {@ta@}." in the "{@p@}, {@ta@}, updated." "table_row"
    And ".stack-xml-compare-inline-added" "css_element" should exist in the "{@p@}, {@ta@}, updated." "table_row"
    And I follow "Show unified display"
    And I should see "Show split display"
    And I set the field "Compared with version" to "2"
    And the field "Compared with version" matches value "2"
    And I set the field "Compared with version" to "1"
    And the field "Compared with version" matches value "1"
    And I wait until ".stack-xml-compare-row-added" "css_element" exists
    And ".stack-xml-compare-row-added" "css_element" should exist in the "-1" "table_row"
    And I should see "<penalty>" in the "-1" "table_row"
    And ".stack-xml-compare-inline-added" "css_element" should exist in the "-1" "table_row"
    And ".stack-xml-compare-row-deleted" "css_element" should exist in the "0.4" "table_row"
    And I should see "<penalty>" in the "0.4" "table_row"
    And ".stack-xml-compare-inline-deleted" "css_element" should exist in the "0.4" "table_row"
