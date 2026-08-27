# Semi-automatic Marking

The primary goal of STACK is automatic marking of students' answers.  However, there are many situations where we want students to justify their answer, or provide a response which cannot be marked automatically.

It is certainly possible to have students answer one of the other question types.  E.g. Moodle provides an "essay" question type in which students can type in their answer (essay?), or the teacher can permit students to upload a file, e.g. a picture of their written response.  The teacher can then mark this by hand.

Another option is to automatically mark students' short answers using a question type such as the [pattern match](https://moodle.org/plugins/qtype_pmatch) question type in Moodle.

STACK provides the "free-text" and "notes" [input type](../Authoring/Inputs/Text_input.md).  There are some advantages to using these inputs, rather than an essay.

1. It is part of a STACK question, so students' answers can be between other parts of a multi-part question.
2. When students "validate" their answer, any maths types in using LaTeX is displayed.  If the teacher shows validation then students get a preview of their answer, and LaTeX will be displayed.

## Manual grading.

Most input types support the extra option extra option `manualgraded`, and the default is `manualgraded:false`.  If one input in a STACK question has `manualgraded:true` then the _whole STACK question_ will require manual grading!

The free-text input default extra option sets `manualgraded:true`.

There really is no way to mix automatic and manually grading within a question. Therefore, if you want automatic and manual marking you have two options.

1. Use separate questions.  If you need to separate out parts to automatic and manually graded parts then you can consider [variant matching](../STACK_question_admin/Deploying_matched_variants.md) to ensure random parts have the same seed.
2. Manual grade, taking into account some automatic marks when you manually grade.

Once a student has completed the quiz, navigate to 

    Quiz -> Results

This page will show you those questions which "Requires grading".

When you are manually grading you can see both the student's attempt, and the "Response history" which contains the STACK response summary.  For manually graded questions, this summary will start with something like this

    Saved: Raw Score: 0.667/2; Seed: 8766544;  [...]

The `Raw Score` reports the total weighted mark of those PRTs which actually evaluated (i.e. with valid inputs).  The total weighted mark is given in terms of the question default mark, set in the question itself.  In this example, the question is out of two.  The student got partical credit.  It is then up to the teacher to record a final mark taking account any automatic partial grade, and their academic judgement of the manually graded input(s).

Note, questions which "Requires grading" really do need human input (manually graded).  Even though in this example some partial credit is awarded automatically, this is not automatically recorded in the gradebook.

An example manual graded question is given in

    samplequestions/stacklibrary/Doc-Examples/Specialist-Tools-Docs/Free-text-input/Free-text_manually_graded_mathematical_proof.xml 