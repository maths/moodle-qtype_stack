# STACK essentials for AI-assisted question authoring

**Audience: the assistant. This is authoritative for the essential technical features of any STACK question.**

This document defines the essential technical requirements for STACK questions.
It is written for AI coding agents and LLMs assisting human question authors.
A project or authoring kit may add its own conventions on top of these.
For an optional review of mathematical purpose, feedback and learner interaction,
see [Question quality review](stack_question_review.md). Agree the intended use
with the author; technical test success alone does not establish suitability.

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
`ans1` and a `[[validation:ans1]]` tag. `<tans>` is the teacher's answer, an
expression in the question variables.

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

**The question note.** If the question variables call a random function such
as `rand()`, the note must not be empty (`questionnotempty`). It names the
variant, so it must show every random value: two deployed variants with the
same note are reported as duplicates. An omitted note becomes `{@ta1@}`.

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
- Notes are compared as exact strings, so each must be distinct within its PRT.
- Write the route the PRT is designed to take, and let the test confirm it.
  Never copy the actual notes into the expectation: that records what the PRT
  does, right or wrong, and tests nothing.
- Re-check every route whenever a node is added, removed or re-wired.

Before STACK 4.13.0 only the last note was compared. A single note is still
read that way, so older tests pass; the full route is the form to write now.

### Deploy variants of a randomised question

Deploying variants is strongly recommended for every question whose question
variables use randomisation. STACK detects this by the use of `rand`,
`rand_with_prohib` and the like, `multiselqn` or `stack_seed`
(`random_variants_check()` in `question.php`). Each `<deployedseed>` fixes one
variant. Students get one of the deployed variants, and every test is run on
each of them.

Without deployed seeds students may get any variant, including one nobody has
tested; the bulk tester reports the question, and the API's test route runs no
tests at all.

How many depends on the question: enough for students to see genuinely
different variants, but each one must be checked. For each seed:

- its question note must differ from every other deployed seed's, since STACK
  treats variants with the same note as the same variant;
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

**It is not the final gate.** The API fills omitted elements from its own
defaults, not a site's settings, and a site may have plugins or settings the
API does not. Before a question is used, run its tests on the target site too:
on the question dashboard, or with the bulk tester.

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
