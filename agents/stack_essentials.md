# STACK essentials for AI-assisted question authoring

**Audience: the assistant. This is authoritative for the essential technical features of any STACK question.**

This document defines the essential technical requirements for STACK questions.
It is written for AI coding agents and LLMs assisting human question authors.
A project or authoring kit may add its own conventions on top of these.
For an optional review of mathematical purpose, feedback and learner interaction,
see [Question quality review](stack_question_review.md). Agree the intended use
with the author; technical test success alone does not establish suitability.

### What a STACK question is made of

1. **Question variables**: Maxima code, run once when a variant starts. Random
   values are chosen here.
2. **Text fields**, such as the question text and the worked solution, written
   in CASText: HTML with mathematics and the values of CAS expressions.
3. **Inputs**: the answer boxes. Each has a name, a type and a teacher's answer.
4. **Potential response trees (PRTs)**: the marking. Each is a tree of nodes,
   and each node compares two expressions with one answer test.
5. **Question tests**: stored answers, each with the expected outcome of every
   PRT.

A question without tests is not finished: they are the only evidence that it
marks as intended, and the only check after each change.

### Write the question as Moodle XML

STACK questions are imported and exported as Moodle XML. Moodle's importer
(`question/format/xml/format.php`, `import_headers()`) reads the fields every
question type shares; STACK's `import_from_xml()` in `questiontype.php` reads
the rest. Nearly every element is optional and takes a default when omitted.
This is the smallest question that is complete in practice: randomised, marked
and tested.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<quiz>
  <question type="stack">
    <name><text>Differentiate a power</text></name>
    <questiontext>
      <text><![CDATA[<p>Differentiate \(x^{{@n@}}\) with respect to \(x\).</p>
<p>[[input:ans1]] [[validation:ans1]]</p>]]></text>
    </questiontext>
    <generalfeedback>
      <text><![CDATA[<p>\(\frac{d}{dx} x^{{@n@}} = {@ta1@}\).</p>]]></text>
    </generalfeedback>
    <stackversion><text>2026080600</text></stackversion>
    <questionvariables>
      <text>n: 2 + rand(5);
ta1: n*x^(n-1);</text>
    </questionvariables>
    <specificfeedback><text>[[feedback:prt1]]</text></specificfeedback>
    <prtcorrect><text><![CDATA[[[commonstring key="symbolicprtcorrectfeedback"/]] [[commonstring key="defaultprtcorrectfeedback"/]]]]></text></prtcorrect>
    <prtpartiallycorrect><text><![CDATA[[[commonstring key="symbolicprtpartiallycorrectfeedback"/]] [[commonstring key="defaultprtpartiallycorrectfeedback"/]]]]></text></prtpartiallycorrect>
    <prtincorrect><text><![CDATA[[[commonstring key="symbolicprtincorrectfeedback"/]] [[commonstring key="defaultprtincorrectfeedback"/]]]]></text></prtincorrect>
    <questionnote><text>{@n@}</text></questionnote>
    <input>
      <name>ans1</name>
      <type>algebraic</type>
      <tans>ta1</tans>
    </input>
    <prt>
      <name>prt1</name>
      <node>
        <name>0</name>
        <answertest>AlgEquiv</answertest>
        <sans>ans1</sans>
        <tans>ta1</tans>
        <trueanswernote>prt1-1-T</trueanswernote>
        <falseanswernote>prt1-1-F</falseanswernote>
      </node>
    </prt>
    <deployedseed>1</deployedseed>
    <deployedseed>2</deployedseed>
    <deployedseed>3</deployedseed>
    <deployedseed>10</deployedseed>
    <deployedseed>17</deployedseed>
    <qtest>
      <testcase>1</testcase>
      <testinput><name>ans1</name><value>ta1</value></testinput>
      <expected><name>prt1</name><expectedscore>1</expectedscore><expectedpenalty>0</expectedpenalty><expectedanswernote>prt1-1-T</expectedanswernote></expected>
    </qtest>
    <qtest>
      <testcase>2</testcase>
      <testinput><name>ans1</name><value>x^n</value></testinput>
      <expected><name>prt1</name><expectedscore>0</expectedscore><expectedpenalty>0.1</expectedpenalty><expectedanswernote>prt1-1-F</expectedanswernote></expected>
    </qtest>
  </question>
</quiz>
```

**The file.** UTF-8, with root element `<quiz>` and one
`<question type="stack">` for each question. The order of elements inside a
question does not matter.

**Elements the importer requires.** The question's `<name><text>`, and the
`<name>` of every `<input>`, `<prt>` and `<node>`, and the `<testcase>` number
of every `<qtest>`. Without them the import fails.

**Text fields and plain fields.** These fields wrap their content in `<text>`:
`name`, `questiontext`, `generalfeedback`, `stackversion`, `questionvariables`,
`specificfeedback`, `questionnote`, `questiondescription`, `prtcorrect`,
`prtpartiallycorrect`, `prtincorrect`, `feedbackvariables`, `truefeedback` and
`falsefeedback`. Every other field holds its value directly, as in
`<tans>ta1</tans>`. A text field may carry a `format` attribute; it defaults to
`html`.

**Escaping.** Wrap any `<text>` that contains HTML in `<![CDATA[ ... ]]>`.
Without CDATA the XML parser reads the HTML tags as XML elements, the field is
not a string, and the question does not import correctly. CDATA cannot contain
`]]>`. Outside CDATA, write `<` as `&lt;` and `&` as `&amp;`; this matters in
Maxima code, such as `x<1` in `<tans>`, `<sans>` or a test `<value>`. XML
defines only `&lt; &gt; &amp; &apos; &quot;`: an HTML entity such as `&nbsp;`
outside CDATA is a parse error, so use the character itself or a numeric
reference such as `&#160;`.

**Inputs.** Each `[[input:ans1]]` in the question text needs an `<input>` named
`ans1` and a `[[validation:ans1]]` tag, each exactly once, in the question
text. `<tans>` is the teacher's answer, an expression in the question
variables. How to choose and configure inputs is in "Choose and configure
inputs" below.

**PRTs.** A potential response tree is a graph of `<node>`s named `0`, `1`, ….
`<truenextnode>` and `<falsenextnode>` name the next node; `-1`, the default,
ends the tree. The first node is the one no other node points to: there must be
exactly one, and no cycles. Give every branch an answer note, distinct within
its PRT. STACK's convention is `prt1-1-T`: the PRT's name, the node's name plus
one, and the branch. Do not omit a note: Moodle's import stores an omitted note
as empty, while the API fills in `prt1-1-T` and `prt1-1-F` whichever node or
PRT it is, so tests cannot tell the branches apart.

**The feedback tag.** Each PRT's `[[feedback:prt1]]` appears exactly once, in
the question text or in `<specificfeedback>`; a tag that appears twice marks the
question broken. If `<specificfeedback>` is omitted and the question text
contains `[[input:ans1]]`, the importer fills it with `[[feedback:prt1]]`. So
when the feedback tags are in the question text, write an empty
`<specificfeedback><text></text></specificfeedback>`.

Where to put the tags is a choice. Both places obey the quiz's "Specific
feedback" review option, so students see the feedback at the same times either
way (`formulation_and_controls()` and `feedback()` in `renderer.php`). A tag in
the question text puts the feedback next to the inputs it is about. A tag in
`<specificfeedback>` puts it in the feedback area below the question, and with
more than one PRT that area also shows the overall standard feedback (below).

**Standard PRT feedback.** `<prtcorrect>`, `<prtpartiallycorrect>` and
`<prtincorrect>` hold the generic feedback, such as "Correct answer, well
done.", that a PRT shows before its own feedback. Write all three exactly as in
the example above, with STACK's `[[commonstring]]` defaults. Never hardcode
text, icons or colours in them.

- `[[commonstring]]` takes the string from STACK's language pack in the
  student's language, so the feedback is translated and matches STACK's
  styling. A hardcoded copy is English-only and drifts from STACK's own. If a
  question needs other wording, write it with a `[[lang]]` block per language.
- Write the fields even though they are optional. An omitted field takes the
  site's setting at import (`qtype_stack | prtcorrect` and so on, defaults in
  `settings.php`). Sites upgraded from before STACK 4.12.0 may still have the
  old setting, hardcoded English HTML, and the API uses its own default.
- Which field is shown depends on the PRT's raw score, before any penalty:
  correct above 0.999999, incorrect below 0.000001, otherwise partially
  correct. Nothing is shown when the PRT has a runtime error
  (`standard_prt_feedback()` in `renderer.php`).
- Only the PRT feedback style Standard (`<feedbackstyle>` 1, the default)
  shows these fields. Compact (2) and Symbol only (3) show the symbol from the
  language pack instead, and Formative (0) shows neither.
- A question with more than one PRT also shows one of the three at the top of
  the specific feedback, chosen by the PRTs' weighted total score. It appears
  only when `<specificfeedback>` is not empty (`specific_feedback()` in
  `renderer.php`).
- The fields are CASText evaluated once per variant with the question
  variables, so they cannot depend on the student's answer. Feedback about the
  answer belongs in the PRT nodes.

**The question note.** The note is CASText that names the variant. STACK
treats two variants as the same if and only if their notes are equal: its
reports group attempts by note, and each attempt stores its note when it
starts, so editing the note later does not change past attempts. In a
randomised question:

- the note must not be empty. The editing form refuses an empty note when the
  question variables use a random function (`questionnotempty`), but neither
  an XML import nor the API checks this;
- it must show every value that distinguishes one variant from another, so
  that different variants get different notes. Show the answer too, so the
  note alone answers a student's query about their variant, for example
  `\(p = {@p@}\), answer \({@ta1@}\)`;
- it holds text and mathematics only: images, plots and files are not shown
  in most places the note appears.

A question without randomisation may leave the note empty. An omitted
`<questionnote>` becomes `{@ta1@}` on import.

**Tests and deployed seeds.** Each `<qtest>` gives a `<testinput>` for every
input and an `<expected>` result for every PRT; each `<deployedseed>` is one
deployed variant. What to put in them is in the next two sections.

**Defaults.** An omitted element takes a default from one of two places. Some
are fixed in the code: `<defaultgrade>` 1 (0 for a question with no inputs),
`<penalty>` 0.1, input `<type>` algebraic, node `<answertest>` AlgEquiv,
`<truescore>` 1, `<falsescore>` 0, PRT `<value>` 1. The rest come from the
site's STACK settings (`get_config('qtype_stack', ...)`, defaults in
`settings.php`), for example `<boxsize>` 15, `<forbidfloat>` 1, `<mustverify>`
1, `<showvalidation>` 1 and `<multiplicationsign>` dot. A site administrator
can change these, so the same file may behave differently on two sites. Write
an element whenever the question depends on its value. A question exported
from Moodle lists every element.

### Write the text fields in CASText

The question text, specific feedback, general feedback (the worked solution),
question note, question description, PRT node feedback and the standard PRT
feedback are CASText: HTML with mathematics and the values of CAS expressions,
evaluated for each variant.

- **Mathematics.** Use `\(...\)` for inline and `\[...\]` for displayed
  mathematics. STACK does not support `$...$` or `$$...$$`. Outside
  mathematics, format with HTML, not LaTeX.
- **`{@ex@}`** evaluates `ex` and inserts its LaTeX, already as inline
  mathematics: with `ta1: 2*x`, `{@ta1@}` gives `\({2\cdot x}\)`. Do not
  wrap it in `\(...\)`; for displayed mathematics write `\[{@ex@}\]`. A string
  is inserted as plain text. The multiplication sign follows
  `<multiplicationsign>`.
- **Inside mathematics**, `{@ex@}` keeps its outer braces, so
  `\frac{@a@}{@b@}` is correct. On STACK 4.13.1 and earlier, text of 10
  characters or fewer between two `{@...@}` loses a backslash at its end, so
  `{@a@} \\{@b@}` gives `\{3}`, not a line break (issue #1910, fixed in
  4.14.0). There, write `\\ {@b@}`, or build the whole object in the CAS, for
  example `{@matrix([a], [b])@}`.
- **`{#ex#}`** inserts the Maxima form, `2*x`: use it to show a student how to
  type an answer. A string is inserted with its quotes.
- **Simplification.** Values are simplified according to `<questionsimplify>`,
  or the PRT's `<autosimplify>` in PRT feedback. To show an expression
  unsimplified in a simplified question, write `{@ev(1+1, simp=false)@}`, which
  gives `1+1`.
- **Variables.** Every CASText field can use the question variables. PRT node
  feedback can also use the PRT's feedback variables, and so the student's
  answer. The specific feedback, general feedback and question note cannot use
  the inputs.
- **Placeholders.** `[[input:ans1]]` and `[[validation:ans1]]` go in the
  question text only; `[[feedback:prt1]]` in the question text or the specific
  feedback (see "The feedback tag"). Never put a placeholder inside mathematics.
  The general feedback, question note and question description hold none, and
  the specific feedback no input or validation tag: the editing form refuses
  them, and an import marks the question broken. Before STACK 4.15.0 only
  input tags were caught.
- **Blocks.** CASText also has blocks, such as `[[if test='...']]...[[/if]]`,
  `[[define x='...'/]]`, `[[comment]]...[[/comment]]` and
  `[[lang code='en']]...[[/lang]]` (one file per block in
  `stack/cas/castext2/blocks/`).

### Choose and configure inputs

STACK separates validity from correctness. Validation decides whether the
student's entry is a well-formed answer of the expected kind; only a valid
answer reaches the PRT, and an invalid one costs no penalty. So put every check
of form on the input (its type, options or a validator), and keep the PRT for
the mathematics. A PRT that tests for floats, the wrong variables or the wrong
type of object marks a student wrong, with a penalty, for a slip of entry.

**Placeholders.** Each `[[input:ans1]]` and `[[validation:ans1]]` appears
exactly once, in the question text. The editing form refuses a repeat, and an
XML import marks the question broken (`validate_fromform()` in
`questiontype.php`), but the API does not check: its tests pass. To refer to an
input twice, refer to it in words.

**Layout.** `[[validation:ans1]]` becomes a `<div>`, or a `<span>` for compact
validation (`<showvalidation>` 3) and equivalence-reasoning inputs
(`replace_validation_tags()` in `stack/input/inputbase.class.php`). HTML closes
an open `<p>` at a `<div>`, so a line holding an input and its validation goes
in a `<div>`, not a `<p>`.

**The input type.** Pick the type that rejects what the answer cannot be:

- A single number uses `numerical`, not `algebraic`. It always rejects
  variables ("This input expects a number, and so may not contain variables"),
  so it needs no validator. With `<forbidfloat>` 1 and
  `<requirelowestterms>` 0 it accepts `1/2`, `2/4`, `-3` and `sqrt(2)` as
  exact values, and rejects `0.5`.
- The no-variables check is numerical-only. A `matrix` input accepts symbolic
  entries, so a matrix of numbers needs a validator, for example
  `validate_numeric(ex) := if listofvars(ex) = [] then "" else "Enter numbers only.";`.

**Variables.** The extra option `checkvars:1` (in `<options>`) makes an answer
invalid if it contains a variable that the teacher's answer does not;
`checkvars:2` flags a missing variable, `checkvars:3` both. Where the allowed
set differs from the teacher's answer's variables, write a validator.

**Validators.** The extra option `validator:validate_vars` calls a function
the question variables define. It returns `""` (or `true`) for valid and a
message for invalid.

- It runs only after STACK's own validation has passed, and always with
  `simp:false`. A set literal is then not put into canonical order, so
  `subsetp(setify(listofvars(ex)), {x1,x2,x3})` rejects the correct `x2+x1`.
  Wrap such tests in `ev(..., simp)`, or test membership with
  `every(lambda([v], member(v, [x1,x2,x3])), listofvars(ex))`.
- It takes one argument, the student's answer, and must not name an input. It
  can use the other question variables, such as the teacher's answer.
- Students cannot type the name of a question variable unless the input lists
  it in `<allowwords>`, so an allowed variable name should not also be a
  question variable.
- An error inside the validator makes the answer invalid.
- STACK supplies `stack_multi_validator`, `stack_seq_validator` and
  `validate_nofunctions` (`stack/maxima/validator.mac`).

**Subscripts.** A student may type `x_1` for `x1`. With the extra option
`consolidatesubscripts`, STACK changes any atom of the form letters, `_`,
digits to the same name without the `_` before validation and marking
(`420_consolidate_subscripts`). Without it `x_1` is a different variable, and
is marked wrong.

**Forbidden words.** Students may call Maxima functions such as `diff`,
`integrate`, `factor`, `expand` and `solve`. When a question asks the student
to do by hand what one of these does, list it in `<forbidwords>`. Forbid only
the operation being assessed.

**Syntax hints.** `<syntaxhint>` pre-fills the box, for example `sqrt(?)` or
`matrix([?,?],[?,?])`. With `<syntaxattribute>` 1 it is a greyed placeholder
that disappears when the student types; with 0 it is text the student edits.
Keep a hint generic: never give the shape or values of the answer.

**Test the validation.** For each input that relies on validation, add a test
entering an invalid answer and expecting `NULL` (see "Test every question").
Run it: a validator that never rejects anything looks the same as one that
works. On STACK 4.14.0 and earlier a test of a `matrix` input expecting `NULL`
fails as "Empty question tests are not permitted!" when `<mustverify>` is 0
(`process_results()` in `stack/questiontest.php`, fixed in 4.15.0): there, also
fill in a non-matrix input, or set `<mustverify>` 1.

### Choose the answer test

Each PRT node compares two expressions with an answer test: `<sans>`, usually
the student's answer, and `<tans>`, usually the teacher's, with options in
`<testoptions>`. Pick the test for what the question assesses.

**Value and form.** `AlgEquiv` simplifies both arguments fully, so it tests
value and ignores form: it accepts `(1+2*x)^3` when the question says
"expand". When the question asks for a form, follow the `AlgEquiv` node with a
node testing the form, such as `EqualComAss` (equal up to commutativity and
associativity), `FacForm` (factored) or `CompSquare` (completed square), and
give each failure its own feedback.

**What the form test sees.** An input passes the student's answer to the PRT
as typed: `<questionsimplify>` does not simplify it. Most answer tests,
including `EqualComAss` and `FacForm`, are called with `simp:false`
(`compile_node()` in `stack/prt.class.php`, the `$pops` table in
`stack/answertest/controller.class.php`), so they see that form. Two things are
simplified:

- the teacher's answer, and anything else computed in the question variables,
  when `<questionsimplify>` is 1. So `ta1: 5*x*x^2` is stored as `5*x^3`;
- anything computed in the feedback variables, when the PRT's `<autosimplify>`
  is 1. So `sa: ans1*1` turns the student's `5*x*x^2` into `5*x^3`, and a
  form test on `sa` passes whatever the student typed.

Give a form test the input itself, or compute from it with `<autosimplify>` 0.

**Quiet.** Many answer tests add feedback of their own, for example naming the
entries of a matrix that are wrong. `<quiet>1</quiet>` on a node hides it; the
test's own answer note is still recorded. Make a node quiet when that feedback
would mislead, typically when `<sans>` is not what the student typed but
something computed from it.

**Integration.** `Int` accepts any antiderivative of the right function, with a
constant of integration of any name. `<tans>` is the antiderivative, with or
without a constant, never the integrand: `Int` differentiates both. The option
is the variable, `x`, or a list such as `[x, NOCONST]` to accept a missing
constant. `Int` requires a constant by default, so it rejects the correct
answer to a question with one particular antiderivative, such as a definite
integral or "find \(F\) with \(F(0)=3\)": use `AlgEquiv` there.

**Numbers.**

- `AlgEquiv` treats `98.9` and `989/10` as equal, but `Sets` compares
  simplified elements exactly, so they differ there. Compare floats with
  floats.
- `NumSigFigs`, `NumDecPlaces` and the other tests that count digits read the
  student's raw input. A test input is evaluated first, and Maxima drops
  trailing zeros from a float, so the test value `28.8000` arrives as `28.8`.
  Write `dispdp(28.8, 4)` instead.

**Sets.** There is no `set` input type: `<type>set</type>` fails with "unknown
input type set". Use `algebraic`; the student types `{1, 2}`. The `Sets` test
gives feedback on which elements are wrong or missing.

### Test every question

Every question with an input must have question tests. STACK runs every test
on every deployed variant: on the question dashboard, when variants are
deployed, in the bulk tester after an upgrade, and through the API. Without
tests the marking is unchecked, and a broken question looks the same as a
working one. A question with no inputs has no PRTs and needs no tests.

**Which tests.** At least:

- the teacher's answer, scoring full marks on every PRT;
- a wrong answer, scoring 0, which proves the PRT does not accept everything;
- with several inputs, a mixed case: some parts right and some wrong;
- one test for each route through each PRT, not just each leaf: every
  diagnostic node needs a test that reaches it;
- an invalid input, wherever one is likely or the question relies on its own
  validation;
- alternative correct answers when the task permits them, such as a different
  basis or an equivalent expression;
- a near-miss for each targeted diagnostic: a response that should receive a
  different explanation. A diagnostic must not reject a valid alternative or
  infer a misconception from an answer that has several possible causes.

A wrong-answer test must reach the grader: invalid syntax alone does not test
whether the PRT rejects a mathematically incorrect answer. For dependent parts,
write the intended follow-through expectations explicitly. Use learner previews
to check partial submissions and retries; qtests alone do not exercise the UI.

**Test inputs.** A `<value>` is a Maxima expression evaluated in the context of
the question variables, so it follows the random values. It is evaluated with
`simp:false`, whatever the question's setting: wrap it in `ev(..., simp)` to
simplify it. A variable named in a test must be defined in the question
variables; feedback variables are not in scope.

- Build wrong answers from the teacher's answer, such as `ta1 + 1`, not from
  independent values, which on some seed may equal the right answer.
- Check that every wrong answer is wrong, and every diagnostic is reachable, on
  every deployed variant. On some seeds a misconception can give the right
  answer.
- Where the teacher's answer is derived from other question variables, build
  the correct test from that derivation too, not only from `ta1`, so that a
  mistake in `ta1` is caught.
- For a dropdown or radio input the value must be one of the options, for
  example `first(mcq_correct(ta1))`. For checkboxes use `mcq_correct(ta1)`.

**Expected score and penalty.** `<expectedscore>` is the raw score the PRT
returns, between 0 and 1, not its contribution to the question's mark.
`<expectedpenalty>` is 0 for a correct answer and normally the question's
`<penalty>` for a wrong one. Write both. A blank penalty means the question's
penalty, and -1 for either field skips the check, which tests nothing.

**Expected answer note: the full route.** `<expectedanswernote>` lists the
answer note of every node the response visits, in order, separated by ` | `:

    <expectedanswernote>prt1-1-F | prt1-2-T</expectedanswernote>

A note naming only the last node passes for every route that ends there, and
misses a wrongly wired `<truenextnode>` or `<falsenextnode>`. STACK compares
the expectation with the PRT's actual notes (`test_answer_note()` in
`stack/questiontestresult.php`):

- the expected notes must appear among the actual notes, in this order, and
  extra actual notes are ignored;
- the last expected note must equal the last actual note.

So:

- Write the bare list, which is read as `( ... ]`: start free, end fixed. Do
  not add `[` at the start. The actual notes include the answer test's own
  notes whenever it gives one, which depends on the answer and the random
  values, so a fixed start can pass on one seed and fail on another. The
  question dashboard's automatic test case writes `[ ... ]` when the response
  it was built from gave no answer-test note; remove the `[`.
- Leave out the answer test's own notes (`ATAlgEquiv_...` and so on). They
  depend on the student's answer, not on the route.
- `NULL` expects the PRT not to run, for example because an input is invalid.
  Leave the expected score and penalty blank with it.
- `prt1-bail` expects the PRT to stop in its feedback variables.
- Never use `()`, which accepts anything.
- Never omit `<expectedanswernote>`. From STACK 4.15.0 an omitted note is empty
  and the test fails with "Expected result is empty"; earlier versions use
  `1-0-T`, which no PRT produces, so the test fails with a confusing mismatch.
- Notes are compared as exact strings, so each must be distinct within its PRT.
- Write the route the PRT is designed to take, and let the test confirm it.
  Never copy the actual notes into the expectation: that records what the PRT
  does, right or wrong, and tests nothing.
- Re-check every route whenever a node is added, removed or re-wired.

Before STACK 4.13.0 only the last note was compared. A single note is still
read that way, so older tests pass; the full route is the form to write now.

### Write the question variables

`<questionvariables>` is Maxima code, run once when a variant starts. Its
values are available everywhere else in the question: the text fields, inputs,
PRTs and tests. Write one statement per line, each ending with `;`.

- `name: expression` assigns; `f(x) := x^2 + 1` defines a function. An `=`
  makes an equation and assigns nothing, without any error: after `a = 3;`,
  `ta1: a + 1;` leaves `ta1` as `a + 1`, and a student typing `4` is marked
  wrong.
- The inputs do not exist yet. `ans1` in the question variables is only a
  symbol: compute anything that depends on the student's answer in the PRT's
  feedback variables.
- STACK forbids about 300 names of Maxima system and option variables,
  including short words such as `tab`, `title`, `values`, `key`, `grid`,
  `error`, `delay` and `font` (`stack/cas/security-map.json`). Assigning one
  stops the whole block with "The expression tab is forbidden.", so no
  variant runs. Choose descriptive names that are not Maxima's.
- A name assigned here cannot be typed by a student, unless the input lists
  it in `<allowwords>` (`is_allowed_to_read()` in
  `stack/cas/cassecurity.class.php`). So do not use a name the student needs,
  such as `c` in an integration question.
- With `<questionsimplify>` 0 the question variables are not simplified (see
  "Simplification" below).
- Maxima's `addrow(M, r)` appends a row to a matrix; STACK's row operation
  "add `k` times row `j` to row `i`" is `rowadd(M, i, j, k)`. On STACK 4.9.0 to
  4.13.1 the text `addrow`, even inside a longer name, in the question
  variables, a text field or the feedback variables is reported as an upgrade
  error, and the question's tests do not run (`validate_against_stackversion()`
  in `question.php`, fixed in 4.14.0). There, build the matrix from lists
  instead, for example `matrix([1, 2], [3, 4])`.

### Randomise with STACK's functions

Generate random values in the question variables with STACK's functions.
Maxima's own `random()` is forbidden. The functions draw in turn from one
generator, started from the variant's seed (`stack_seed` holds it), so a seed
fixes a variant only for the question variables as they are now. Adding,
removing or reordering a random call changes the variant of every seed:
afterwards, check every deployed seed again.

- `rand(n)`: an integer from 0 to n-1. `n` must be positive: `rand(0)` is a
  runtime error. `rand(n.0)` is a float from 0 to n.
- `rand(L)`: one element of the list or set `L`; `rand(M)` applies `rand` to
  each entry of the matrix `M`.
- `rand_with_step(lower, upper, step)`: one of `lower`, `lower+step`, ... up to
  `upper`.
- `rand_with_prohib(lower, upper, L)`: an integer from `lower` to `upper` that
  is not in the list `L`, for example `rand_with_prohib(-5, 5, [0])`. If `L`
  rules out every value it returns `[]`, with no error.
- `rand_selection(L, n)`: `n` different elements of `L`;
  `rand_selection_with_replacement(L, n)`, `random_subset(S)`,
  `random_subset_n(S, n)`, `random_ne_subset(S)` and `random_permutation(L)`
  likewise.

These are defined in `stack/maxima/stackmaxima.mac` in the STACK source.

**Build backwards.** Choose the random values so that every variant has a good
answer and a good worked solution: every variant must take the same steps, by
the same method, with no step that disappears or needs a different method.
Pick the answer first and build the question from it, or pick from a list of
good cases, `p: rand([2, 3, 5, 7])`. Where good cases are hard to construct,
generate and check them in advance, outside the question, and pick from that
list with `rand`. Do not loop until a random value meets a condition: the loop
runs whenever the variant is generated, and can time out.

**Assert what the variant needs.** `s_assert(ex, v)` is a runtime error unless
`is(ex = v)`. Put it at the end of the question variables to state what every
variant must satisfy, for example `s_assert(is(b^2 - 4*a*c > 0), true)`. A
variant that breaks the assertion fails all its tests, and STACK will not
deploy it.

**Simplification.** With `<questionsimplify>0</questionsimplify>` the question
variables are not simplified either: `rand(6)+1` stays `1+1` and displays so,
and `L[rand(3)+1]` is a runtime error because the index is not an integer.
Use `rand(L)`, or simplify the arithmetic explicitly, `ev(rand(6)+1, simp)`.

### Deploy variants of a randomised question

Deploying variants is essential for every question whose question
variables use randomisation. STACK decides this from the question variables
(`has_random_variants()` in `question.php`). A word that begins with `rand`,
`multiselqn` or `stack_seed` makes the question random, so do not begin a
variable's name with `rand`. STACK also follows functions defined in included
libraries, and counts a call to one that uses a random function. STACK 4.14.0
and earlier do not: there a random function from an included library whose
name begins otherwise is missed, the question is treated as fixed, and every
student gets the same variant, whatever is deployed. For those releases, call
at least one random function in the question variables themselves. The API's
test route reports the result as `israndomvariants`.

Each `<deployedseed>` fixes one variant. Students get one of the deployed
variants, and every test is run on each of them.

Without deployed seeds students may get any variant, including one nobody has
tested; the bulk tester reports the question, and the API's test route runs no
tests at all. A broken variant in a live quiz is often impossible to repair:
each attempt keeps the seed it started with, and changing the random
generation to fix it changes the question under students who have already
answered it. Random generation, deployed variants and question tests work as
one system, and deploying and testing variants prevents this in most cases.
If a user asks for a randomised question without deployed variants, explain
this and recommend deploying them; if they still decline, say in your report
that the variants are untested.

How many depends on the question: enough for students to see genuinely
different variants, but each one must be checked. For each seed:

- its question note must differ from every other deployed seed's, since STACK
  treats variants with the same note as the same variant. STACK skips a
  duplicate only when it chooses seeds itself, and its question dashboard
  warns about duplicates, but neither an XML import nor the API checks: render
  each seed and compare the notes;
- it has no runtime errors and passes every test;
- it meets the intended learning objective: exclude accidental simplifications
  or coincidences that invalidate a diagnostic test. Zero coefficients and
  answers of 0 or 1 can be intentional boundary cases; retain and test them
  when they are part of the question's purpose.

Investigate failed seeds before replacing them. Fix generator or marking
defects and rerun the tests; restrict variants deliberately when a case falls
outside the intended scope. A question without randomisation needs no
deployed seed.

### Run the tests through the STACK API

Strongly recommended: run every question's tests through the STACK API while
writing it, before it goes near a live site. The API is STACK's own code as a
stateless web service, published as Docker images (`stackmaths/stackapi`, with
`mathinstitut/goemaxima` for Maxima). It needs no Moodle, no database and no
import, and answers in seconds, so it fits the loop of edit, test, fix.

**Match the version.** The API must run the target site's STACK version, the
same number as `<stackversion>`: the `goemaxima` image with that tag, for
example `mathinstitut/goemaxima:2026080600-latest`, and a `stackapi` image of
the same release. A different version tests different code. If every question
fails with "The version of the STACK-Maxima libraries being used ... does not
match what is expected", the images and the API's configuration disagree.
Setting up the API, including building a `stackapi` image for a release that
has none published, is in `agents/stack_api.md`.

**Call the test route.** `POST /test` with a JSON body holding one question:

    {"questionDefinition": "<quiz><question type=\"stack\">...</question></quiz>"}

The API takes exactly one question per request, so split a file of several.
Then check:

- `messages` and `isupgradeerror`: a question-level error, such as a missing
  or invalid `<stackversion>`, or a question marked broken. No tests run.
- `results`: one entry per deployed seed, each with `passes`, `fails`,
  `messages` and an `outcomes` entry per test. A failing test's `reason` says
  which of score, penalty and answer note did not match.
- An empty `results` is not a pass. A randomised question with no deployed
  seeds is not tested at all.

**One seed per request on STACK 4.14.0 and earlier.** In these releases the
test route generates the variant of the first deployed seed only, and runs
every seed's tests against it. Send each seed in its own request, a copy of
the question with that one `<deployedseed>`, until the API runs a release
with this fixed.

**It is not the final gate.** The API fills omitted elements from its own
defaults, not a site's settings, and a site may have plugins or settings the
API does not. Before a question is used, run its tests on the target site too:
on the question dashboard, or with the bulk tester.

### Update a question already in use

Moodle's question import always creates new questions, even when a question
with the same name already exists: it never matches by name. To change a
question that is already in a quiz, import the corrected XML as a new version
of it, with "Import a new version" in that question's menu in the question
bank (the `qbank_importasversion` plugin, which STACK requires). The file must
hold exactly one question (`classes/importer.php` in that plugin). The plugin
does not check that the question type matches, so keep it a STACK question. Quizzes that use the latest version of the question then use the new
one, and attempts already started keep the version they began with.

So when you hand back a corrected question, say whether it replaces an existing
one, and supply it as a file with that question alone. Keep its `<name>`
unless the author asks for a new one: the name is how people recognise the
question in the bank and in reports.

### Stamp the target STACK version

Every STACK question's `<stackversion>` is the version number of STACK on the
system the question is written for. It is the plugin's version number, a date of
the form `YYYYMMDDXX` (for example `2026080600`), not the release name
(`4.13.1`). The number on a running site is `$plugin->version` in
`question/type/stack/version.php`.

    <stackversion><text>2026080600</text></stackversion>

Take the number from the project's own instructions, or ask the human. Never
guess it, never default to the latest release, and never copy it from an
example, a template or an older question.

Why: STACK reads this field as the version the question was last edited with.
Saving the question in Moodle's edit form stamps the site's current version, but
an XML import keeps whatever the file says, and a missing field is stored empty.
A question with no stamp, or one that is not a number, has an upgrade error: the
question tests are not run by the bulk tester or the API's `/test` route. The
question dashboard shows the stamp beside the site's version, and
`validate_against_stackversion()` uses it to decide which upgrade checks apply to
older questions. A stamp newer than the site's STACK gives a warning, not an
error.

The stamp does not change how the question runs: STACK always uses the code
installed on the site. Stamping an older version does not restore old
behaviour, and stamping a newer one does not make newer features available.
The stamp is a record for the people who maintain the question later.
