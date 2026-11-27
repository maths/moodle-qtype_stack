# Drag-and-drop (Parsons) questions in STACK

**Audience: the assistant. Read this before planning, writing or reviewing a
STACK question that uses the `[[parsons]]` block: ordering a proof, grouping
items into columns, or placing items in a grid.**

This document adds to the [STACK essentials](stack_essentials.md), which still
apply in full, and to the optional [question quality review](stack_question_review.md).
A project or authoring kit may add its own conventions on top of all three:
for example, whether these questions carry marks.

STACK reference: `doc/en/Specialist_tools/Drag_and_drop/` (the block, the
three layouts and troubleshooting), and `doc/en/Topics/Proof/` (the proof
library, assessment and an authoring workflow). Working examples are in
`samplequestions/stacklibrary/Doc-Examples/Specialist-Tools-Docs/ParsonsProof/`
and `.../Drag-and-drop/`, and templates in
`samplequestions/stacklibrary/Templates/Parsons-Proof/`. The libraries are
`stack/maxima/contrib/prooflib.mac` and `matchlib.mac`, and the input is
`stack/input/parsons/parsons.class.php`. Checked against STACK 4.13.1 and
4.15.0, which agree on everything here.

### Choose a layout

The block's header chooses the layout. Each layout has its own library and
its own functions; do not mix them.

| Layout | Header | Library | Encode | Decode (feedback variables) | Model answer | Test input |
|---|---|---|---|---|---|---|
| Proof: one ordered list, or a tree | `[[parsons input="ans1"]]` | `prooflib.mac` | `parsons_encode(proof_steps)` | `parsons_decode(ans1)` | `[ta, proof_steps]` | `apply(parsons_answer, [ta, proof_steps])` |
| Grouping: columns of any length | `columns="3"` | `matchlib.mac` | `match_encode(steps)` | `match_decode(ans1)` | `[ta, steps, headers]` or `[ta, steps, 3]` | `apply(group_answer, [ta, steps])` |
| Grid: fixed columns and rows | `columns="3" rows="2"` | `matchlib.mac` | `match_encode(steps)` | `match_decode(ans1, true)` | `[ta, steps, headers, 2]`, or with `3` or `index` in place of `headers` or `2` | `apply(match_answer, [ta, steps, headers])` |

Grouping is for sorting into categories, where the order within a column does
not matter. Grid is for matching, where each item has a position; in a grid
the student can drop an item in any cell, while in a grouping items stack from
the top of a column. `rows` without `columns` is not allowed.

### Rules for every layout

**Load the library** in the question variables, before anything uses it.
`stack_include_contrib("prooflib.mac")` fetches the file from STACK's `master`
branch on GitHub when the question is compiled, so the server needs network
access and gets the newest copy; `stack_include("contribl://prooflib.mac")`
loads the copy installed with the server's STACK, which matches its version but
which STACK's docs advise against for live questions
(`doc/en/Authoring/Inclusions.md`). Agree the choice with the author, and use it
consistently.

**The steps list.** A list of `["key", "text"]` pairs (a proof step may add a
third element, a comment some display functions show):

- The text is a Maxima string, so every backslash is doubled: `"\\(n=2m+1\\)"`.
  STACK's caschat page has "Protect slashes within Maxima string variables" to
  convert ordinary LaTeX once (`doc/en/Topics/Proof/Proof_Parsons_workflow.md`).
- The text may contain HTML, including `<img>` tags, and a STACK plot,
  `plot(x^2, [x,-1,1], [size,250,250])`, as the second element.
- Use string keys, never numbers, when the list is permuted: numerical keys are
  positions and break under `random_permutation`.
- For maths in the text, insert whole expressions with `tex1()`
  (`sconcat("\\(", tex1(x^2 + b*x + c), "\\)")`), never a number spliced in
  front of a symbol, which gives `x^2 + -2x`.

**Randomise the order in Maxima**, `steps: random_permutation(steps);`, never in
the browser, so each variant is reproducible. The question note then records
the order: `{@map(first, steps)@}`.

**The block's JSON.** Either the encoded steps alone,
`{# parsons_encode(proof_steps) #}`, or an object:

```
[[parsons input="ans1" columns="3"]]
{ "steps": {# match_encode(steps) #},
  "headers": {# headers #},
  "available_header": "Drag from here:" }
[[/parsons]]
<div>[[input:ans1]] [[validation:ans1]]</div>
```

- The keys must be among `steps` (required), `options`, `headers`,
  `available_header` and `index`. A misspelt key is not caught by validation:
  the page shows every item in one yellow block. Check this first when the
  layout looks wrong.
- `headers` is a list with one string per column (one for a proof);
  `available_header` is a string in grouping and grid layouts.
- `index` (grid only) labels the rows: a list of `rows + 1` strings, the first
  being the corner cell (`""` for none).
- `options` passes Sortable.js options. `ghostClass`, `group` and `onSort` are
  ignored with a warning, as is any unknown option.
- The encode functions hash the keys, so the page source does not reveal them.

**Block parameters** worth knowing: `clone="true"` makes every item reusable
(needed when one item belongs in several places, more often in grouping and
grid); `height`, `width` and `aspect-ratio` size the area (by default it fits
its content); `transpose="true"` starts columns horizontal; `log="true"`
records every move, with timestamps, in the submitted answer. See
`Question_block.md` for the rest.

**The input.** Type `parsons`, with `<mustverify>` 0 and `<showvalidation>` 0:
otherwise the student sees the raw JSON state. The model answer (`<tans>`) is
the list in the table, built from **question variable names**, for example
`[ta, steps, headers]`. STACK chooses the test function by splitting the
*unevaluated* model answer at commas (`answer_function_testcase()` in
`parsons.class.php`), so a literal list written into `<tans>` has the wrong
number of parts, and a single variable holding the list (`ta1` with
`ta1: [ta, proof_steps]`) is not a list at all: either way STACK cannot build
the teacher's-answer test, and reports an incorrect model answer. Write the
list out in `<tans>`, element by element. The teacher's
answer shown to the student is drawn from the model answer; the extra option
`hideanswer` hides it.

**The PRT.** Decode in the feedback variables, compare there, and make every
node that compares decoded structures **quiet**: otherwise a wrong answer shows
the student the hashed or decoded internals.

**Tests.** A test input mocks what the block would submit, with the function
in the table, applied to question variables: correct with `ta`, wrong with a
wrong arrangement defined in the question variables (for example `wa`, two
steps swapped or two items exchanged between columns). This is also the form
STACK itself generates for the teacher's answer test
(`get_teacher_answer_testcase()`). Add a test for each further correct
arrangement the PRT accepts, and one reaching each diagnostic node. Tests never
run the block, so check it in a preview (see "Check in a preview").

### Proof layout

**The teacher's answer** is a tree of proof construction functions whose
arguments are keys or sub-proofs (`doc/en/Topics/Proof/Proof_CAS_library.md`):

| Function | Meaning |
|---|---|
| `proof("a", "b", ...)` | these steps in this order |
| `proof_c(...)` | the arguments in any order |
| `proof_opt("a")` | an optional step; wrap each one separately |
| `proof_iff(P, Q)` | exactly two sub-proofs, in either order |
| `proof_cases(j, P, Q, ...)` | the first argument (the case split) fixed, the cases in any order |
| `proof_goal(P, Q, ..., g)` | the last argument (the goal) fixed, the rest in any order |
| `proof_ind(s, b, i, c)` | statement and conclusion fixed, base case and step in either order |

`proof_alternatives(ta)` lists every tree equivalent to `ta`, recursing into
sub-proofs; it is expensive for deep trees with many commuting parts, so
compute it once. `proof_flatten` turns a tree into a flat list of keys.

**Choose the assessment** (`doc/en/Topics/Proof/Proof_assessment.md`):

- One order only: `sa: parsons_decode(ans1);` and `ATAlgEquiv(sa, ta)`.
- Any equivalent order, with automatic feedback (the usual choice):

  ```
  sa: parsons_decode(ans1);
  [pd, saa]: proof_assessment(sa, proof_alternatives(ta));
  ```

  `pd` is the edit distance to the closest acceptable proof: test
  `ATAlgEquiv(pd, 0)`. In the false branch,
  `{@proof_assessment_display(saa, proof_steps)@}` shows the student which
  steps to move, insert or delete. When `pd` is at least the length of the
  proof, everything needs editing and the display helps little.
- Steps that may interleave between blocks, which no tree expresses: a
  dependency graph, `saprob: proof_assessment_dag(sa, tdag)`, which returns the
  missing, extra and misordered steps (an empty list when the proof satisfies
  the graph). It can be combined with `proof_assessment` for the feedback.

**Worked solution.** `{@proof_display(ta, proof_steps)@}` shows the structure
as nested blocks; `{@proof_display_para(ta, proof_steps)@}` as paragraphs.
`proof_steps_prune(proof_steps)` in place of `proof_steps` drops the comments.
Where several orders are correct, say so, and show one or two of
`proof_alternatives(ta)`.

**Distractors** (steps that belong in no correct proof) need no special code:
`proof_assessment` reports them as deletions. Only use them if the author
wants them.

### Grouping layout

- `ta` is grouped by column: a list with one list of keys per column, in column
  order, `[["sq", "sin"], ["abs", "sqrt"], ["rec", "sgn"]]`.
- `sans: match_decode(ans1);` returns the student's columns in the same shape.
- Where the order within a column does not matter (the usual case), compare
  `match_column_set(sans)` with `match_column_set(ta)` by `AlgEquiv`. Where
  the order of the columns does not matter either, use `match_set_column`.
- The model answer's third element is `headers` when the block sets them, or
  the number of columns when it does not; `headers` must have one entry per
  column.

### Grid layout

- `ta` is still grouped by column, of shape (columns, rows); `match_transpose`
  converts between column and row grouping.
- `sans: match_decode(ans1, true);`: the `true` says rows are set. An empty
  cell decodes as `[]`.
- Choose the comparison by what may move: `match_row_set` when the order
  within a row does not matter, `match_set_row` when the order of the rows
  does not matter (a matching task whose rows may come in any order), or
  compare `sans` with `ta` directly when every cell is fixed.
- The model answer has four elements: `[ta, steps, x, y]`, with `x` the
  headers or the number of columns, and `y` the index or the number of rows.
- In the test input, the third argument of `match_answer` only signals that
  rows are set: `apply(match_answer, [ta, steps, headers])` and
  `apply(match_answer, [wa, steps, 3])` both work.

### Older questions

Before STACK 2024072500 the functions were `stackjson_stringify`,
`proof_parsons_key_json` and `proof_parsons_interpret`, with a `string` input
(and `match_interpret`, `match_correct` in `matchlib.mac`). Such questions
still work, but the keys are unhashed and numerical keys break under
randomisation (issue #1237). When editing one, upgrade it: `parsons_encode`
in the block, input type `parsons` with model answer `[ta, proof_steps]`,
`parsons_decode` in the feedback variables, `parsons_answer` in the tests.

### Check in a preview

The question tests mock the block's output, so they never show whether the
block renders, whether the items are draggable, or whether what a student
drags decodes as the tests assume. Before a question is used, preview it on
several variants and:

1. check every item appears, in a random order, with its maths and images
   rendered, and the layout is the intended one (not one yellow block);
2. build the correct answer by dragging, submit, and check full marks;
3. build a wrong one (for a proof, swap two steps), submit, and check the
   feedback, including `proof_assessment_display` where it is used;
4. check the teacher's answer shown after submission is the intended one.

Record what was checked, and on which variants.

### Review checklist

In addition to the essentials and the question quality review:

- The layout fits the task, and its library, encode, decode and test functions
  all belong to that layout.
- Every key in the block's JSON is one of the five allowed.
- The input is type `parsons`, with `mustverify` and `showvalidation` 0, and
  its model answer is built from variable names.
- Steps are permuted in Maxima with string keys, and the question note records
  the order.
- Every PRT node comparing decoded structures is quiet.
- For a proof, every order the mathematics allows is accepted (the right proof
  construction functions, or a dependency graph), and a test covers at least
  one alternative.
- Tests cover a correct and a wrong arrangement, built with the layout's
  function from question variables.
- The preview check above was done, and its result recorded.
