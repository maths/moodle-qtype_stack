# Free-text inputs in STACK questions

**Audience: the assistant. Read this before planning, writing or reviewing a
STACK question in which the student types working or a proof into a
free-text input.**

This document adds to the [STACK essentials](stack_essentials.md), which still
apply in full, and to the optional [question quality review](stack_question_review.md).
A project or authoring kit may add its own conventions on top of all three.

Free text is never the default. A student's typed argument cannot be marked
automatically, so use it only where seeing the working or the argument serves
the objective, and prefer a structured format (separate inputs, a Parsons
block, a scaffolded sequence of parts) where that tests the point better.
Agree with the author which pattern below each question uses, and whether
anyone will mark the questions by hand.

STACK reference: `doc/en/Specialist_tools/Free_text_input/`,
`doc/en/Authoring/Question_blocks/ASCII.md` and `ASCII_extractors.md`, and
"Free-text input" in `doc/en/Authoring/Inputs/Text_input.md`. Working examples
are in
`samplequestions/stacklibrary/Doc-Examples/Specialist-Tools-Docs/Free-text-input/`.
The rules here are checked against STACK 4.15.0. Earlier versions differ in
places, noted below.

### Choose a pattern

| Pattern | What is graded | Use it for |
|---|---|---|
| A. Working, extracted answers | values read from the text, automatically | a calculation whose working matters; a "show that" ending in values |
| B. Working beside answer inputs | ordinary inputs, automatically | a multi-part question whose answers are typed, chosen or entered in a matrix, with the working written beside them |
| C. Write, then compare | nothing: no marks | a proof the student should write in full and compare with a model |
| D. Manually graded | by a person | only where someone will actually mark the responses |

Choose the first that fits: the automatically graded patterns come first.

A and B both grade answers automatically; they differ in where the answers
come from. In A they are read out of the student's text, so the student writes
one piece of working and nothing else. In B the student enters them in
ordinary inputs, so any input type works (dropdown, matrix, checkbox), nothing
depends on the student labelling a line correctly, and the working is
free-form. Prefer B when an answer is not a short expression on a line of its
own, or when there are more than about three answers. B can also extract one
value from the working and ask for others in boxes
(`Free-text_with_extractors.xml` extracts a characteristic polynomial and asks
for the eigenvalues in boxes); the extracted values then follow pattern A.

### Rules for every pattern

**The input.** Type `freetext`, with `<mustverify>` 0 and `<showvalidation>` 0,
and a wide box (`<boxsize>` 60 to 100). Always set the extra option
`manualgraded` explicitly: the freetext input defaults to `manualgraded:true`
(`stack/input/freetext/freetext.class.php`), and one input with it set turns
the *whole question* into a manually graded one, including its automatically
graded parts. For patterns A and B the options are
`monospace:true, manualgraded:false`, unless the author agrees otherwise. For
patterns C and D they are `monospace:true, manualgraded:true`.

**Layout.** Put the input, its validation and the `[[ascii]]` preview in one
`<div class="free-text-container">`, which sets them side by side
(`free-text-container-vertical` stacks them):

```
<div class="free-text-container">
[[input:ans1]] [[validation:ans1]]
[[ascii input="ans1"]][[/ascii]]
</div>
<div>[[hint title="How to type your working"]][[commonstring key="free_text_fact"/]][[/hint]]</div>
```

The hint gives STACK's standard help on typing markdown and AsciiMath;
`[[facts:free_text]]` is the alternative.

**Say what is wanted.** The question text tells the student what is graded and
where it goes, or that nothing is. For example, in A: "Finish with two lines
on their own, `x = ` your value and `y = ` your value"; in B: "Your working is
not marked. Enter your answers in the boxes below it"; in C: "This is not
marked. Compare your proof with the model one when you submit the quiz."

**The teacher's answer is a string**, written as a student would type it:
markdown, backticks round inline mathematics, and a backtick alone on a line
to open and close a block of lines (which the default filter aligns on the
first `=`). With random values, build it with `castext("...")` and insert
each value with `{#expr#}`; `[[if test='...']]` blocks work inside it. Do not
use `{@...@}` inside `castext()` here: it inserts LaTeX, not text a student
would type.

**What `{#expr#}` prints.** It prints Maxima syntax, so `3*x` appears as
`3*x`, which the preview renders as \(3\cdot x\). That is acceptable, since
students may type `*` too. Watch for:

- **A line whose terms are all negative.** Its first term prints as `-(2*x)`:
  `-(2*x)-2 = 0`. Maxima puts a positive term first where there is one, so
  arrange the randomisation or the working (collect terms on the side where
  the coefficient is positive) so that every line has a positive term.
- **Negative constants** print the same way on their own: `-(1/2)`.
- **Term order.** Maxima orders `x + 4*y` as `4*y+x` unless the question
  variables call `ordergreat(x, y)`.
- **Joined signs.** Insert whole expressions (`{#expand(eq1)#}`), never a
  number before a symbol, which gives `x+-3` or `1*x`.
- **Lists, matrices and constants** print in Maxima syntax (`[1,2]`, `%pi`,
  `%e`, `%i`), which is not what a student types. Build such a line with
  `sconcat`, formatting each value with a helper defined in the question
  variables, for example:

  ```
  ftam(ex) := if constantp(ex) and freeof(%i, ex) and is(ex < 0)
      then sconcat("-", ftam(-ex))
      else ssubst("i", "%i", ssubst("e", "%e", ssubst("pi", "%pi", string(ex))));
  fttup(l) := sconcat("(", simplode(map(ftam, l), ", "), ")");
  ftlist(l) := sconcat("[", simplode(map(ftam, l), ", "), "]");
  ftco(c, s) := if c = 1 then s else if c = -1 then sconcat("-", s)
      else sconcat(ftam(c), " ", s);
  ```

  `fttup([1, -1/2, 0])` gives the tuple `(1, -1/2, 0)` and `ftlist` the list
  `[1, -1/2, 0]`; `ftco(c, "v")` gives `3 v`, `v` or `-v`, never `1 v`.

These faults appear on some random variants only. Read the model working,
rendered as the student sees it, on every deployed variant: the question
tests do not see the preview (see "Check in a preview").

**Worked solution.** The general feedback gives a sentence of method, then the
model working rendered the same way as the student's preview:
`[[ascii]]{@ta1@}[[/ascii]]`. It is what the student compares their own
working with, so it shows every step the answers depend on.

**Filters.** Use the default (no `[[filter]]` block: `markdown-math` with
`aligneq`). The `calculation` filter evaluates `{@...@}` numerically in the
student's browser, so the student has a calculator: agree it with the author
first. The `cas` filter is not for students.

**Adding free text to an existing question.** Keep its inputs, PRTs and tests
unchanged. Add the freetext input under a new name (for example `work`, with
teacher's answer `tw`), and set it in every test: to the model working in
most, and to `""` in at least one.

### Pattern A: working with extracted answers

Each extracted value has its own `[[extractor]]`, its own hidden target input
and its own PRT. Extractors run in the student's browser.

**Choose the extractor.** Use only a type listed in `ASCII_extractors.md`
(STACK rejects any other at save, from the list in
`stack/cas/castext2/blocks/extractor.block.php`).

- One value, "the last line is the answer": `lastexpr`, the last line of
  mathematics. It needs a markdown filter, which the default provides.
- Several values: ask for one labelled line each, and use
  `laststringremainderwhitespace` per label:

  ```
  [[ascii input="ans1"]]
    [[extractor type="laststringremainderwhitespace" targetinput="ans2" search="x =" /]]
    [[extractor type="laststringremainderwhitespace" targetinput="ans3" search="y =" /]]
  [[/ascii]]
  ```

  It scans the lines from the end. After trimming spaces, one final full stop
  and a pair of outer backticks, a line matches only if it *starts* with the
  label (the pattern is anchored with `^` in
  `corsscripts/ascii/extractors/laststringremainderwhitespace.js`), with any
  space in the label matching any amount of space. So `x = 3`, `` `x=3` `` and
  `x = 3.` match, while `2x = 6` and `So x = 3` do not. A label therefore
  needs a line of its own, and the question text says so. The last matching
  line wins, so earlier working such as `x = (5+2y)/3` does no harm provided
  the final answer comes after it. Choose labels that cannot begin another
  line of the working.
- A regex extractor only when neither fits. Escape each backslash twice in the
  `regex` attribute.

An extractor that finds nothing clears its target input, so no stale value is
marked. From STACK 4.15.0 the reason is shown in the preview panel unless the
extractor has `errors="false"`; keep the default, so the student learns that a
label was not found. Before 4.15.0 (checked on 4.13.1) the freetext input, the
`[[ascii]]` block and every extractor and filter type exist, and an extractor
that finds nothing already clears its target input; but there is no `errors`
option on filters or extractors, and the preview shows no failure. Leave
`errors` out there.

**Hidden inputs, visible validation.** Put the target inputs together in one
hidden `<div>`, and show each one's validation on a labelled line of its own,
followed by its PRT's feedback:

```
<div style="display:none">[[input:ans2]] [[input:ans3]]</div>
<div>Your value of \(x\): [[validation:ans2]]</div>
<div>[[feedback:prt2]]</div>
<div>Your value of \(y\): [[validation:ans3]]</div>
<div>[[feedback:prt3]]</div>
```

The student then sees what was extracted, with its feedback beside it. Never
put a validation or feedback tag inside the hidden `<div>`.

**Target inputs** follow the essentials' input rules: `numerical` for a single
number, `algebraic` for an expression or an equation, with `<mustverify>` 0
and `<showvalidation>` 3 (compact).

**One PRT per extracted value**, named after its input (`prt2` for `ans2`),
with its feedback tag inline as above. Where a single value may be written
several ways (`x = 3`, `3 = x` or a bare `3`), normalise it in the feedback
variables before comparing. A mix-up between labels (the values of `x` and `y`
swapped) is a natural diagnostic; make it distinct on every variant (for
example, forbid the two values from being equal).

**Tests** set every input: the freetext input to the model working, and each
target input to the value under test. With several values, include mixed cases
(one right, one wrong). The tests check the PRTs only: the extractors never
run in a test, so they are checked in a preview.

### Pattern B: working beside answer inputs

- **Order on the page.** The question, then the free-text container and its
  help, then the answer inputs, each on its own `<div>` line with its feedback
  tag after it. The working comes first because the student does it first.
- **No PRT reads the free text** (unless a value is extracted from it, as in
  A). Say so in the question text, and that the model working appears on
  submission.
- **The answer inputs, PRTs and tests** are those of any multi-part question,
  including follow-through from an earlier part where the task allows it.
- **Tests** as for any multi-part question: correct, wrong through each
  diagnostic, and mixed. Set the freetext input to the model working in most,
  and to `""` in at least one, which shows that empty working does not stop
  the answers being graded.

### Pattern C: write, then compare

- `<defaultgrade>` 0, `<penalty>` 0, no PRT, empty `<specificfeedback>`.
- A question with no PRT always uses Moodle's manual-grading behaviour,
  whatever the input options say (`make_behaviour()` in `question.php`). There
  is no Check button, so the student sees the model proof when the quiz's
  review options show general feedback, usually once the attempt is
  submitted. Say "when you submit the quiz", not "when you check", and confirm
  the timing in a preview.
- The general feedback gives a model proof, then a short self-check list
  naming the usual errors in this proof ("Did you use a different letter for
  each odd integer?"). That list is the teaching: name the specific
  misconceptions, not generic advice.
- **One test**, entering the model proof with no PRT expectation. It shows a
  typical answer is a valid response. There is no marking logic to test, so
  the essentials' correct-and-wrong pair does not apply.
- Randomise only where the proof is about specific values. A general proof
  needs no random variants.

### Pattern D: manually graded

Use only where a person will mark the responses: confirm this with the author.

- `manualgraded:true`. The whole question then needs manual grading, even its
  automatically graded parts, and the student sees no mark until someone
  grades it. Under immediate-feedback behaviours the student gets no feedback
  on the free text either.
- Put the marking scheme in the general feedback or the question note, where
  the marker sees it beside the response.
- Any PRTs in the question still run. The marker sees their result as
  `Raw Score` in the response history, but it does not reach the gradebook
  (`doc/en/Moodle/Semi-automatic_Marking.md`). To mix automatic and manual
  marks for real, use separate questions.
- Tests as in A or B for any PRT, or as in C where there is none.
  `Free-text_manually_graded_mathematical_proof.xml` is an example.

### Check in a preview

Neither the question tests nor the API run the student's browser, so neither
sees the `[[ascii]]` preview or an extractor. Before a question is used,
preview it (or ask the author to) on several variants and:

1. paste the model working into the free text, and check the preview renders
   it as intended, with no `-(...)`, reversed term order or `+-`;
2. pattern A: check that each labelled validation line shows the expected
   value; then delete a labelled line and check that its validation clears
   and (from 4.15.0) the preview reports the failure;
3. submit, and check the marks, the feedback, and that the general feedback
   shows the model working.

Record what was checked, and on which variants.

### Review checklist

In addition to the essentials and the question quality review:

- The pattern fits the objective, and pattern D is used only where someone
  will mark by hand.
- `manualgraded` is set explicitly on every freetext input: `false` in
  patterns A and B (unless the author agreed otherwise), `true` in C and D.
- The question text says what is graded and where it goes, or that nothing is.
- Every extractor type is a listed name, its `targetinput` exists, and each
  extracted value has its own hidden input, PRT and labelled validation line.
- No validation or feedback tag is inside a hidden `<div>`.
- The model working is a string a student could have typed, and reads
  correctly on every deployed variant.
- Pattern B: at least one test has empty working.
- Pattern C: the self-check list names this proof's specific misconceptions.
- The preview check above was done, and its result recorded.
