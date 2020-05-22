# Semi-automatic Marking

The primary goal of STACK is automatic marking of students' answers.  However, there are many situations where we want students to justify their answer, or provide a response which cannot be marked automatically.

It is certainly possible to have students answer one of the other question types.  E.g. Moodle provides an "essay" question type in which students can type in their answer (essay?), or the teacher can permit students to upload a file, e.g. a picture of their written response.  The teacher can then mark this by hand.

Another option is to automatically mark students' short answers using a question type such as the [pattern match](https://moodle.org/plugins/qtype_pmatch) question type in Moodle.

STACK provides the "notes" [input type](Inputs.md).  There are some advantages to using the notes input, rather than an essay.

1. It is part of a STACK question, so students' answers can be between other parts of a multi-part question.
2. When students "validate" their answer, any maths types in using LaTeX is displayed.  If the teacher shows validation then students get a preview of their answer, and LaTeX will be displayed.

## Manual grading.

The notes input has a special extra option `manualgraded`, and the default option value is `manualgraded:false`.  If you specify `manualgraded:true` then the _whole STACK quesion_ will require manual grading!

There really is no way to mix automatic and manually graded parts of a question. Therefore, if you want automatic and manual marking you must have separate questions.