# Question quality review

This optional checklist supports an author or reviewer using the
[STACK essentials](stack_essentials.md). Adapt it to the intended learners and
assessment. It does not prescribe notation, partial credit, penalties, languages
or a particular teaching method. Record those choices with the author.

## Purpose and mathematical contract

- State the learning objective, prerequisites, assumptions and domain. Include
  the prerequisites used in hints and solutions, not only in the prompt.
- Check what the response format provides evidence of. A correct final value
  alone may not demonstrate a method or justification. Add reasoning steps only
  when they serve the objective; avoid separately grading duplicated evidence.
- Define the accepted answers and any required form. For non-unique tasks,
  test the defining properties rather than equality with one example: a basis
  may differ in order, scaling or choice of vectors.
- Make question-specific symbols and conventions clear wherever they are first
  needed, including independently displayed feedback. Keep the task usable
  when it is reused outside its original worksheet.
- Inspect the source, authorship and licence before adapting a question.
  Preserve attribution and distinguish inherited content from adaptations.

## Variants and meaningful tests

- Choose randomisation that provides useful practice at the intended level.
  Check structural cases, accidental shortcuts and collisions between correct
  answers and intended mistakes. Retain intentional boundary cases.
- When changing a generator, update the prompt, model answers, marking,
  feedback, solution and test fixtures together. Check for stale constants on
  success branches as well as failure branches.
- Check that negative fixtures remain wrong and diagnostic fixtures reach the
  intended branch across deployed variants. For a small finite family, examine
  every case where practical; otherwise record the sampled seeds and limits.
  Passing sampled seeds does not prove correctness over an unbounded generator.
- Use independently derived expected results where possible. Include valid
  alternatives, plausible mistakes and nearby responses that should receive
  different feedback. For example, test a transpose diagnostic only when the
  transposed answer is distinct from the correct answer.
- Apply the API guide's [execution checks](stack_api.md#run-the-tests).
  Record which variants were tested and which will actually be deployed.

## Feedback and worked solutions

- Base a diagnosis on a property of the submitted response. A failed defining
  relation, contradictory coordinate or missing condition can give the learner
  a useful next check. If the cause is uncertain, describe what fails without
  asserting an unsupported misconception or intention.
- A useful pattern is: what the response establishes, the remaining issue,
  why it matters, and a productive next question or check. Use only the parts
  needed for that response; avoid repeating the worked solution as a diagnosis.
- For dependent work, identify the first relevant error or unjustified step
  and avoid cascades caused by it. Recognise valid follow-through according to
  the agreed marking policy. Independent parts may need separate feedback.
- Distinguish valid unfinished work from incorrect work. Accept alternative
  valid reasoning; do not require the model solution's sequence unless that
  sequence is part of the stated task.
- Make the worked solution explain decisive choices and all requested
  conclusions. Distinguish one valid example from a description of all answers.
  Agree when hints and the complete solution become available.

## Rendered interaction

- Inspect the prompt, input validation, every reachable feedback branch, hints
  and worked solution in each supported language. Check that they express the
  same task and marking contract and use terminology learners can understand.
- On the target site, exercise blank, partial, correct, incorrect and malformed
  submissions, followed by correction and retry where supported. Check that
  validation, revealed controls and dependencies allow the intended next step.
- Inspect the actual quiz layout as well as a standalone preview. Keep controls
  and feedback associated with their mathematical content; check long answers,
  translated labels, narrow viewports, keyboard use and required browsers.
  Essential meaning must not depend only on colour or spatial position.
- Recheck the saved/imported version and required assets. An API test does not
  verify native editor validation, site settings or browser interaction.

## Review record and later refinement

Record the source revision, intended use and marking policy; versions and
seed/test coverage; representative response-to-feedback examples; rendered
states and platforms checked; and unresolved issues with an owner. Identify
who performed automated checks, AI-assisted review and human mathematical or
pedagogical review. Never imply that passing tests or an agent's approval is
human approval. The author or responsible reviewer decides readiness for use.

After use, where access and data-handling rules permit, inspect missed response
classes and whether feedback supports a useful revision. Turn material cases
into synthetic regression tests without copying student identities or private
responses into shared examples. Improved retries alone do not establish
learning.
