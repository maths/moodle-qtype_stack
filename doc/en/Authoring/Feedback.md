# Feedback

The purpose of STACK is to assess students' answers to mathematical questions,
and on the basis of the properties we establish to assign _feedback_.

This document describes the ways STACK provides feedback to students. It will not go into much depth on creating the feedback, which is documented under [potential response trees](Potential_response_trees.md).

## Types of assessment

* _Formative assessment_ is to support and inform students' learning.
  Feedback here could be _qualitative_, e.g. written comments tailored to the student's answer and designed to help them improve their performance on the task.
* _Summative assessment_ is to establish the achievement of the student.
  In mathematics, summative feedback is most often _quantitative_,  either a mark or a percentage.
* _Evaluative assessment_ is to measure the effectiveness of the teaching or the
  assessment of students.  Such assessments could have quality enhancement or quality audit functions.
  See [reporting](Reporting.md). The ability to automatically generate data about an individual student or
  across a cohort is one particular strength of CAA, potentially enabling regular, detailed evaluative assessment.

In STACK multi-part questions there is a complete separation between two important components.

1. a list of [inputs](Inputs.md)
2. a list of [potential response trees](Potential_response_trees.md)

Specific feedback is associated with each input and each potential response tree.  Typically, it is placed in the `Specific feedback` section, as that gives Moodle more control over when it is shown. However, the feedback tags can be positioned anywhere within the [question text](CASText.md#question_text).

## Validation ##

Before an input is available to a [potential response trees](Potential_response_trees.md),
it must be validated.  In particular, at each attempt, each input is assigned a status.

1. NULL, which indicates the field has not been previously given a value by the student,
   or the field is now empty since the student has deleted an answer.
2. new, indicates an answer has not been validated or assessed, but has been changed from a previous attempt.
3. invalid, which indicates that the field is not valid.
4. valid, a response which is valid but not scored.
5. score.  In this case, the answer is available to any potential response tree requiring it.

Normally a student will view a displayed form of their expression and submit it again.  This default behaviour is inappropriate for multiple choice/selection interactions, and can be changed for each input using the option "Student must verify".  Whether the student's answer is echoed back and shown is controlled by a separate option "Show the validation".  Validation errors are always shown.

Whether a string entered by the student is valid or invalid does not depend on the question. I.e. there _should_ be a consistent mechanism for what constitutes a syntactically valid expression. However, in practice things are not quite so clean!  Some [input options](Inputs.md#Input_options) do affect validity, such as _forbid floats_.   Some symbols, e.g. \(i\) and \(j\) change meaning in different contexts, e.g. \(\sqrt{-1}\) or vector components.  See details about [options](Options.md).

# Potential response trees

Each [potential response tree](Potential_response_trees.md) returns three outcomes:

1. a numerical score,
2. text for the students,
3. an [answer note](Potential_response_trees.md#Answer_note) for use by the teacher during [reporting](Reporting.md).

These correspond approximately to summative, formative and evaluative functions of assessment respectively.
The [general feedback](CASText.md#General_feedback) (known as worked solution in previous versions) is fixed and may not depend on the student's answers.
Hence it is not considered to be feedback to the student's work in the strict sense.  However, it remains a very useful outcome for students.

The amount of feedback available in each question is governed by the question behaviours.

## Numerical score  ##

Each potential response tree calculates a numerical score between 0 and 1.  This is then multiplied by the [question value](Potential_response_trees.md#Question_value) for each potential response tree.  The final score for the question is the sum over all potential response trees.

The numerical scores are assembled by traversing each potential response tree.

* Each branch of each node can add, subtract or set an absolute, score.
* The outcome at the end should be between 0 and 1.  If the score, \(s\), lies outside this range it is taken to be \( \min(\max(s,0),1) \) to bring it within range, then it is scaled by multiplying by the [question value](Potential_response_trees.md#Question_value) for that potential response tree.
* A "penalty" may also set in the potential response tree for this attempt. Normally the penalty field in each branch of the potential response tree is empty, in which case the question level penalty value is used.  However, these fields are useful to _remove_ any penalty for this outcome, by setting it to zero explicitly.
* After the whole tree has been traversed, if the score is 1 then the penalty is always assigned to 0.

STACK adjusts the score for each potential response tree, based on the number of valid, different attempts.  The penalty scheme deducts from the score a small amount (default is \(0.1=10\%\)) for each different and valid attempt which is not completely correct.   It is designed to _reward persistence and diligence_ when students initially get a question wrong, but they try again.

It works in the following way. For each attempt \(k\), we let

* \(s_k\) be the score from the potential response tree.
* \(p_k\) be the "penalty" as follows:
 * If \(s_k=1\) then \(p_k=0\), else
 * If the penalty \(p\) set in the _last branch_ traversed before exiting the potential response tree is not `NULL` then \(p_k=p\), else
 * \(p_k\) is the penalty set in the question options, (default \(0.1=10\%\) ).

The default penalty scheme takes the _maximum_ score for each attempt, so that by accruing further penalties a student may never be worse off.

To be specific

1. Let \( (s_i,p_i) \) for \(i=1,\cdots n\) be the list of scores and penalties for a particular potential response tree, for each different valid attempt.
2. The score for attempt \(k\) is defined to be

\[ \mbox{Question value} \times \max\left\{ s_i-\sum_{j=1}^i p_j,\ i=1,\cdots k \right\}.\]

Notice that this is purely a function of a list of (score, penalty) pairs.

The score for that attempt is the sum of the marks for each potential response tree once penalties have been deducted from each tree.

## Text for the student  ##

The text-based feedback for students is a concatenation of the following elements.

* *Answer test feedback.* Many of the [answer tests](Answer_tests.md) generate feedback of their own. This can be suppressed using the quiet option. While this feedback is often not needed, it would be very difficult for the teacher to re-create this.
* *Bespoke feedback.* Each branch of the [potential response trees](Potential_response_trees.md) generates some feedback.
* *Generic feedback.* Once the [potential response trees](Potential_response_trees.md) has been traversed and all feedback assigned, the score is used to generate some generic feedback. If the raw score equals \(0\) then the default feedback is _Incorrect answer_.   If the raw score equals \(1\) then the default feedback is _Correct answer, well done_. Otherwise the generic feedback is _Your answer is partially correct_.  These strings can be modified in the [options](Options.md).
