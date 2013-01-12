# Potential response trees

The potential response tree is the algorithm which establishes the mathematical properties of the student's answer and assigns outcomes.
For examples of how to use this see the entry on
[establishing properties of the student's answer via the potential response tree](Authoring_quick_start.md#Answer_props_via_prt)
in the quick start guide.

## When is the tree used? ##

Each potential response tree relies on one or more of the [inputs](Inputs.md).
STACK automatically detects which elements are needed in the [answer tests](Answer_tests.md) or
[feedback variables](KeyVals.md#Feedback_variables).
The first time a student submits an input it is validated.
The second time it is submitted it is available for assessment by a potential response tree.
Only when all inputs upon which a tree relies are valid and submitted will the tree be traversed.

## Before the tree is traversed ##

Each potential response tree can set Maxima's level of [simplification](../CAS/Simplification.md).
Before the tree is traversed the [feedback variables](KeyVals.md#Feedback_variables) are evaluated.
The feedback variables may depend on the values of the [question variables](KeyVals.md#Question_variables) and the [inputs](Inputs.md).
The values of these variables are available to the [answer tests](Answer_tests.md) and all [CASText](CASText.md) fields within the tree, for example the feedback could be built using these variables.

## Traversing the tree ##

A potential response tree (technically an acyclic directed graph) consists of an arbitrary number of linked nodes we call potential responses.

In each node two expressions are compared using a specified [answer tests](Answer_tests.md), and the result is either `true` or `false`. A corresponding branch of the tree has the opportunity to each of the following.

1. Adjust the score, (e.g. assign a value, add or subtract a value);
2. Add written feedback specifically for the student;
3. Generate an "[answer note](Potential_response_trees.md#Answer_note)", used by the teacher for evaluative assessment;
4. Nominate the next node, or end the process.

## Outcomes  ##

The outcomes are

1. The raw score
2. The penalty for this attempt
3. [Feedback](Feedback.md) to the student
4. An Answer Note

### Question Value {#Question_value}

The potential response tree itself is expected to return a numerical raw score between \(0\) and \(1\).
This number is multiplied by the question value before being returned to the student as [feedback](Feedback.md) or recorded in the database.

### Answer note {#Answer_note}

The answer note is a tag which is key for reporting purposes.  It is designed to record the outcome of each answer test and the unique path through the tree.  This is automatically generated, but can be changed to something meaningful.   When looking for identical paths through the tree we have to do so, regardless of which random numbers were selected in this version of the question given to a particular student.  Hence, this string may not depend on any of the variables.

The answer note is the concatenation of each answer note from the [answer tests](Answer_tests.md) and
then the corresponding true/false branch.  This note provides a record of the result of applying each
test and the route taken through the tree.

This field is given a default value automatically and is used for [reporting](Reporting.md) students' work.
