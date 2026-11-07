# STACK question essentials

This file is a minimal, tool-agnostic brief for anyone, or any AI assistant, writing STACK questions. It covers only what is needed to produce a working question with question tests. It deliberately contains no style advice and no default choices.

This draft accompanies issue maths/moodle-qtype_stack#1925 and was written with AI assistance (Hermes Agent), reviewed by the account holder before submission.

## 1. What a STACK question is made of

A STACK question is a Moodle question type with five parts:

1. Question text, written in CASText. It carries the student-facing statement and the input placeholders.
2. Question variables: Maxima code run once per attempt. Randomised values live here.
3. Inputs: the answer boxes. Each input has a name and an input type, and is checked against a teacher answer.
4. Potential response trees (PRTs): the decision logic that scores an input. A PRT is a small tree of nodes, each node applying one answer test.
5. Question tests: saved answer cases with expected scores. These are what prove the question still works after an edit.

A question without question tests is not finished.

## 2. CASText basics

CASText is the text format used in the question text, the feedback and the question note.

- `{@ ... @}` inserts the value of a Maxima expression, simplified.
- With the simplify flag off in the question settings, the value is inserted unsimplified.
- `[[input:ans1]]` places input `ans1`.
- `[[validation:ans1]]` places the validation feedback for `ans1`.
- `[[feedback:prt1]]` places the feedback of PRT `prt1`.

Every input needs both a `[[input:...]]` and a `[[validation:...]]` placeholder. Every PRT needs a `[[feedback:...]]` placeholder, otherwise its feedback never reaches the student.

## 3. Question variables

Maxima code, one or more statements, evaluated once when the attempt starts.

```
a:rand(6)+2;
b:rand(6)+2;
ta:a*b;
```

Use `rand()` here for randomisation, not in the question text. Do not use `rand()` inside an input's teacher answer or inside feedback: those are evaluated at different times and the values can disagree.

When a question is randomised, the question note must contain the randomised values, for example `{@a@} times {@b@}`. The note is how variants are distinguished, and it is what makes randomised question tests readable.

## 4. Inputs

An input needs at minimum:

- `name`: the same string used in `[[input:name]]` and in the PRT.
- `type`: for example `algebraic`, `numerical`, `matrix`, `units`, `string`.
- `tans`: the teacher answer, in Maxima.

The teacher answer is the expression the input is compared against. It does not have to be the only correct answer: the answer test in the PRT decides equivalence.

## 5. PRTs

A PRT holds one or more nodes. Each node has:

- `answertest`: the comparison rule, for example `AlgEquiv`, `NumRelative`, `String`, `UnitsRelative`. In Maxima code the same tests are written as `ATAlgEquiv` and so on; in the XML they are written without the `AT` prefix.
- `sans`: the student's answer, normally the input name.
- `tans`: the expression to compare against, usually a question variable.
- A true branch and a false branch, each with a score, a penalty, a next node (use `-1` to end), an answer note and optional feedback.

Answer notes are the machine-readable record of which branch was taken. Question tests assert on them, so keep them unique and stable.

## 6. Question tests

Each `qtest` block holds one case:

- `testinput`: a value for each input, normally the teacher answer for a true case.
- `expected`: for each PRT, the expected score, expected penalty and expected answer note.

Minimum useful set for a one-input, one-PRT question: one case that should score 1 with the true answer note, and one case that should score 0 with the false answer note. Add a case for every branch you expect to matter, including malformed input and a case near a tolerance boundary.

Question tests must be re-run after any change to variables, inputs, PRT logic or answer notes. A question whose tests have not been re-run since its last edit is untested.

## 7. Version numbers

Each question carries a `stackversion` string in its XML and a version recorded by Moodle. Treat both as bookkeeping: they tell you which STACK release a question was last saved with, and they let an export be traced back. They do not prove the question still works. Only the question tests do that, and only on the current version of the question.

## 8. Minimal complete example

One randomised algebraic input, one PRT, two question tests.

```xml
<quiz>
  <question type="stack">
    <name>
      <text>Product of two random integers</text>
    </name>
    <questiontext format="html">
      <text><![CDATA[<p>Type in {@ta@}.</p>
        <p>[[input:ans1]] [[validation:ans1]]</p>
        <p>[[feedback:prt1]]</p>]]></text>
    </questiontext>
    <generalfeedback format="html"><text/></generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.1</penalty>
    <hidden>0</hidden>
    <stackversion><text>2020123000</text></stackversion>
    <questionvariables>
      <text>a:rand(6)+2;
b:rand(6)+2;
ta:a*b;</text>
    </questionvariables>
    <questionnote>
      <text>{@a@} times {@b@}</text>
    </questionnote>
    <questionsimplify>1</questionsimplify>
    <assumepositive>0</assumepositive>
    <assumereal>0</assumereal>
    <input>
      <name>ans1</name>
      <type>algebraic</type>
      <tans>ta</tans>
      <boxsize>15</boxsize>
      <strictsyntax>1</strictsyntax>
      <insertstars>2</insertstars>
      <forbidwords>solve</forbidwords>
      <forbidfloat>1</forbidfloat>
      <mustverify>1</mustverify>
      <showvalidation>1</showvalidation>
    </input>
    <prt>
      <name>prt1</name>
      <value>1.0000000</value>
      <autosimplify>1</autosimplify>
      <feedbackvariables><text/></feedbackvariables>
      <node>
        <name>0</name>
        <answertest>AlgEquiv</answertest>
        <sans>ans1</sans>
        <tans>ta</tans>
        <testoptions/>
        <quiet>0</quiet>
        <truescoremode>=</truescoremode>
        <truescore>1.0000000</truescore>
        <truenextnode>-1</truenextnode>
        <trueanswernote>prt1-1-T</trueanswernote>
        <truefeedback format="html"><text/></truefeedback>
        <falsescoremode>=</falsescoremode>
        <falsescore>0.0000000</falsescore>
        <falsenextnode>-1</falsenextnode>
        <falseanswernote>prt1-1-F</falseanswernote>
        <falsefeedback format="html"><text/></falsefeedback>
      </node>
    </prt>
    <qtest>
      <testcase>1</testcase>
      <description>teacher answer scores full marks</description>
      <testinput><name>ans1</name><value>ta</value></testinput>
      <expected>
        <name>prt1</name>
        <expectedscore>1.0000000</expectedscore>
        <expectedpenalty>0.0000000</expectedpenalty>
        <expectedanswernote>prt1-1-T</expectedanswernote>
      </expected>
    </qtest>
    <qtest>
      <testcase>2</testcase>
      <description>a single factor scores zero</description>
      <testinput><name>ans1</name><value>a</value></testinput>
      <expected>
        <name>prt1</name>
        <expectedscore>0.0000000</expectedscore>
        <expectedpenalty>0.1000000</expectedpenalty>
        <expectedanswernote>prt1-1-F</expectedanswernote>
      </expected>
    </qtest>
  </question>
</quiz>
```

The structure above is taken from a working question shipped with STACK, `samplequestions/stacklibrary/Features/input-type-sample-questions/Algebraic_input.xml`, with the randomisation and the question note added.

## 9. Checklist before a question is shared

1. Every input has an input and a validation placeholder, and every PRT has a feedback placeholder.
2. The teacher answer for each input is an expression evaluated in the question variables, not literal arithmetic typed into the input.
3. Random values come from the question variables only, and the question note identifies the variant.
4. Each PRT has at least one true test case and one false test case.
5. Question tests have been run and all pass on the current version of the question.
6. The question has been previewed once as a student would see it.
