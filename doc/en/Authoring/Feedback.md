# Feedback

The purpose of STACK is to assess student's answers to mathematical questions,
and on the basis of the properties we establish to assign _feedback_. 

* **Formative assessment** is to support and inform students' learning.
  Feedback here could be _qualitative_, e.g. written comments tailored to the student's answer.
* **Summative assessment** is to establish the achievement of the student.
  In mathematics, summative feedback is most often _quantitative_,  either a mark or a percentage.
* **Evaluative assessment** is to measure the effectiveness of the teaching or the
  assessment of students.  Such assessments could have quality enhancement or quality audit functions.
  See [reviewing](Reviewing). The ability to automatically generate data about an individual student or
  across a cohort is one particular strength of CAA, allowing regular, detailed and accurate evaluative assessment.

In STACK there is a complete separation between two important components.

1. a list of [interaction elements](Interaction_elements)
2. a list of [potential response trees](Potential_response_trees)

Feedback is associated with each of these and it can be positioned anywhere within the [question stem](CASText#Question_stem).

# Validation #

Before an interaction element is available to a [potential response trees](Potential_response_trees)
it must be validated.  In particular, at each attempt, each interaction element is assigned a status.

1. NULL, which indicates the field has not been previously given a value by the student,
   or the field is now empty since the student has deleted an answer.
2. new, indicates an answer has not been validated or assessed, but has been changed from a previous attempt.
3. invalid, which indicates that the field is not valid.
4. valid, a response which is valid but not scored.
5. score.  In this case, the answer is available to any potential response tree requiring it.

Whether a string entered by the student is valid or invalid does not depend on the question.
However, some [interaction element options](Interaction_elements#Interaction_element_options)
do affect validity, such as _forbid floats_.

# Properties #

Each [potential response tree](Potential_response_trees) returns three outcomes

1. a numerical score
2. text for the student
3. an [answer note](Potential_response_trees#Answer_note)
   for use by the teacher during [reviewing](Reviewing)

These correspond approximately to formative, summative and evaluative functions of assessment.
The [worked solution](CASText#Worked_solution) is fixed, and hence is not considered to be feedback to the student's work.
However, it remains a very useful worked solution.

The amount of feedback available in each question is governed by an [options](Options), [feedback used](Options#Feedback_used). 

## Numerical score  ##

A numerical score may be shown, between \(0\) and the [question value](Potential_response_trees#Question_value).

## Text for the student  ##

The text-based feedback for students is a concatenation of the following elements.

### Answer test feedback  ###

Many of the [answer tests](Answer_tests) generate feedback of their own. This can be suppressed using the quiet option.
While this feedback is often not needed, it would be very difficult for the teacher to re-create this.  

### Bespoke feedback  ###

Each branch of the [potential response trees](Potential_response_trees) generates some feedback.

### Generic feedback  ###

Once the [potential response trees](Potential_response_trees) has been traversed and all
feedback assigned, the score is used to generate some generic feedback.
If the raw score equals \(0\) then the default feedback is

	<span class='incorrect'>Incorrect answer.</span>

If the raw score equals \(1\) then the default feedback is

	<span class='correct'>Correct answer, well done.</span>

Otherwise the generic feedback is

	<span class='partially'>Your answer is partially correct.</span>

These strings can be modified in the [options](Options).