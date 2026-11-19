# Authoring STACK questions with an LLM

Many STACK authors are using LLMs/AI to assist with question writing.

In the autumn of 2026 we published a case study [From print to interactive: an SMP A-level textbook built in STACK with AI agents](https://stack-assessment.org/CaseStudies/2026/SMP_Textbook/) describing success in generating a complete mathematics textbook using Claude.

## Why STACK is still a useful tool

There is no doubt that AI is changing the world.  However, we still see a role for STACK, and similar tools.

* Trust and transparency: STACK is deterministic and auditable, giving educators control over how answers are assessed.
* Cost: AI can assist with the capital cost of creating questions, but is not needed for ongoing assessment.
* Good-practice designed in: STACK's design and question authoring workflows make it easier for authors to write reliable and consistent assessments.  E.g. questions are tested before students use them, and the design of STACK itself is reliable and stable for the long-term.
* Community: The STACK community have over 25 years of experience of digital assessment and how to use these effectively.

Just as MCQ is still needed, STACK will retain an important place in the educational landscape.  The key point is that STACK and AI are complements, not competitors. AI can help students explore mathematics; STACK can establish whether they have demonstrated the required skills. In an AI-rich world, trustworthy assessment is more important than ever.

## Workflows for STACK question generation

To help question authors make the best of STACK and its features, we have produced a basic "stack_essentials" file designed to help LLM agents write questions to a basic standard.  See `agents/stack_essentials.md`.

The following practices have proved effective.

1. Plan before writing any XML.  For each question, name the misconception it targets, the variables and their ranges, the structure of the potential response trees and the test cases.  A plan which says only "use an algebraic input" cannot be checked.
2. Separate writing from reviewing.  A reviewer which has just written a question is not independent.  The reviewer should run the question tests itself, rather than accept the author's report, and a different model often finds things the author's model does not.
3. Passing question tests show that a question is consistent, not that it is correct.  Ask which answers it would mark wrongly: a wrong answer accepted, and a correct answer in a legitimate form rejected.
4. Look at every deployed variant as students will see it.  No test checks whether a plot is right, whether a distractor is accidentally true, or whether the feedback teaches anything.
5. Keep the questions (Moodle XML) under version control as the master copy, and treat Moodle as the place where they are published.

See `agents/stack_question_review.md` for a checklist for reviewing questions.



