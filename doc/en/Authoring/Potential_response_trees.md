# Potential response trees

The potential response tree is the algorithm which establishes the mathematical properties of the student's answer and assigns outcomes. For examples of how to use this, see the entry on [improving feedback](../AbInitio/Authoring_quick_start_3.md) in the quick start guide.

## When is the tree used? ##

Each potential response tree relies on one or more of the [inputs](../Authoring/Inputs/index.md). STACK automatically detects which elements are needed in the [answer tests](Answer_Tests/index.md) or [feedback variables](Variables.md#Feedback_variables). The first time a student submits an input it is validated. The second time it is submitted it is available for assessment by a potential response tree. Only when all inputs upon which a tree relies are valid and submitted will the tree be traversed.

## Before the tree is traversed ##

Each potential response tree can set Maxima's level of [simplification](../CAS/Simplification.md). Before the tree is traversed the [feedback variables](Variables.md#Feedback_variables) are evaluated. The feedback variables may depend on the values of the [question variables](Variables.md#Question_variable.s) and the [inputs](../Authoring/Inputs/index.md). The values of these variables are available to the [answer tests](Answer_Tests/index.md) and all [castext](CASText.md) fields within the tree, for example the feedback could be built using these variables.

Notes:

1. You cannot define a feedback variable with the same name as an input.  For example, if your input is `ans1` then it is tempting to define a feedback variable `ans1:exdowncase(ans1)` to ensure it is in lower case.  Do not do this!  Please use a different variable name.  This is because in some situations the answer test will choose to take the raw value of `ans1` exactly as the student typed it.  Any redefinition will interfere with this process.

2. If one of the feedback variables throws an error then this will not stop the PRT executing.  If there is an error, this will be flagged in the response summary as `[RUNTIME_FV_ERROR]` (fv here means feedback variables).  See notes on [error trapping](Error_trapping.md) for advice on how to use this.

3. It is possible for the feedback variables to halt the execution of the potential response tree (just as if one of the inputs were blank/invalid).  However, this is an advanced use-case.  See below for details.

## Traversing the tree ##

A potential response tree (technically an acyclic directed graph) consists of an arbitrary number of linked nodes we call potential responses.

In each node two expressions are compared using a specified [answer tests](Answer_Tests/index.md), and the result is either `true` or `false`. A corresponding branch of the tree has the opportunity to each of the following.

1. Adjust the score, (e.g. assign a value, add or subtract a value).  Scores can be floating point numbers or variables defined elsewhere (e.g. question variables/feedback variables).
2. Add written feedback specifically for the student
3. Generate an "[answer note](Potential_response_trees.md#Answer_note)", used by the teacher for evaluative assessment
4. Nominate the next node, or end the process.
5. Any runtime error during traversing the tree will cause an error.  This error will stop further execution of the tree, and students will see a runtime error message.  This will be flagged in the response summary as `[RUNTIME_ERROR]`.  If you have statements likely to throw an error you should evaluate them in the feedback variables first. See notes on [error trapping](Error_trapping.md) for advice on how to use this.

## Outcomes  ##

The outcomes are

1. The raw score
2. The penalty for this attempt
3. [Feedback](Feedback.md) to the student (see below for full details)
4. An answer note

### Question Value {#Question_value}

The potential response tree itself is expected to return a numerical raw score between \(0\) and \(1\). This number is multiplied by the question value before being returned to the student as [feedback](Feedback.md) or recorded in the database.

### Answer note {#Answer_note}

The answer note is a tag which is key for reporting purposes. It is designed to record the outcome of each answer test and the unique path through the tree. This is automatically generated, but can be changed to something meaningful. When looking for identical paths through the tree we have to do so, regardless of which random numbers were selected in this variant of the question given to a particular student.  Hence, this string may not depend on any of the variables.

The answer note is the concatenation of each answer note from the [answer tests](Answer_Tests/index.md) and then the corresponding true/false branch.  This note provides a record of the result of applying each test and the route taken through the tree.

This field is given a default value automatically and is used for [reporting](../STACK_question_admin/Reporting.md) students' work.

This field may not be empty and for each node in a tree the string must be unique.

Do not use `;`, `|` characters in your answer note.  These characters are used to split the response summary in the reporting scripts.

## Scores and penalties ##

A score is generated by each potential response tree.  Because the tree is only traversed when all inputs are valid, the score is only generated for a valid attempt.

If a score is generated it is based only on the current values of the inputs.  This means that it is not based on either (1) previous values of the inputs, or (2) the number of previous attempts.  (Requests have been made to enable attempt number to be available, but this has not been implemented
yet.)

If a score is generated then a penalty is also generated. The penalty system is designed to encourage students to make repeated attempts in a formative setting.  For example, a student is asked to find  \( \int x^2, \mathrm{d}x\).

Attempt 1:  \( x^3/3\).  Score \(=0\), Penalty \(=0.1\), Feedback: "You have missed a constant of integration."

Attempt 2:  \( x^3/3+c\).  Score \(=1\), Penalty \(=0\), Feedback: "Well done."

Overall, the potential response tree returns the current score minus total penalties so far, in this example \(0.9\).  This is multiplied by the "Question value" set in the potential response tree.  These are summed across all potential response trees.

In this example, some colleagues would prefer to give partial credit for missing a constant of integration rather than zero marks and a penalty.  In a formative setting, where students have an opportunity to have another attempt, the penalty system has been found to be an effective way to encourage students to have another attempt and to read the feedback.  In an examination, where no feedback is available and so further attempts are not made, different choices need to be made and partial credit would be more appropriate than a zero mark.

* The penalty is given a default value in the question.  This is a mandatory field; the default for STACK is 0.1.
* Penalties are cumulative, but the student will be given the maximum possible mark.  I.e. while they accumulate penalties they are never worse off by repeatedly attempting the question.  In particular, if the student in the above example makes another attempt and scores \(0\) they will retain their mark of \(0.9\).  This is to encourage students to have another go in a formative setting.  STACK generates a list of penalty adjusted scores for each attempt, and takes the maximum.
* The penalty can be assigned a different value in the nodes of the potential response tree. This means, e.g., the teacher can assign a cumulative penalty
for a particular answer.
* The penalties are also controlled at a quiz level by the "question behaviours" mechanism for the quiz.  Hence, if you set the behaviour as "Adaptive mode (no penalties)" the penalty assigned will be ignored when the question is used by students in the quiz.

## Formative potential response trees ##

The outcomes of score, penalty, feedback and note are always produced.  Normally, whether this information should be shown to a student is a function of the quiz, and in Moodle the question behaviour.  It should not be set at the level of an individual question.

There are rare, but important, situations where we need a purely formative PRT.

E.g. Imagine a questions with inputs A, and B.

1. Input A has a dedicated PRT to establish if it is correct.
2. Input B has a dedicated PRT to establish if it is correct.
3. There is an additional PRT which depends on both A & B.  This gives formative feedback to the student, e.g. "try a more interesting combination of answers next time!" but is of no consequence to the correctness of A & B.

With a formative potential response tree, there is no general feedback such as "Correct answer well done".  There is never a mark (and marks for this PRT do not contribute to the question, or completeness of an answer).

## Response tree feedback: feedbackstyle ##

The feedback created by PRTs has the following parts concatenated together.

    [Generic feedback] [Runtime errors] [PRT generated feedback] [Score ?]

The `[Generic feedback]` is a question level option, e.g. "Standard feedback for correct", to provide consistency across a question. By default the `[Generic feedback]` contains both an initial symbol, and a language sentence.  The current "correct" default is

<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.

How PRT feedback is displayed is controlled by the PRT option `feedbackstyle` as follows.  Note the Generic feedback might include the symbol, if you retain the default.

Value | Options      | Symbol | Generic feedback | Errors | PRT feedback | Score ?
------|--------------|--------|------------------|--------|--------------|------------------------------------------
  0   | Formative    |  No    |  No              |  Yes   |  Yes         | No (PRT does not contribute to score)
  1   | Standard     |  No    |  Yes             |  Yes   |  Yes         | Respects quiz setting
  2   | Compact      |  Yes   |  No              |  Yes   |  Yes         | No
  3   | Symbol only  |  Yes   |  No              |  Yes   |  No          | No

Note that the "Compact" PRT feedback uses `<span>` tags and not `<div>`.  This allows inclusion inline, without new paragraphs settings.  However, `<span>` tags cannot contain a block level element, such as a `<div>` or `<p>`. So, if you include a block level element in your PRT feedback then the browser may "spit this out" and misplace the feedback. Also, MathJax may not display mathematics correctly on the page when there is an HTML error such as this.  If you use the "Compact" feedback, please author only minimal PRT feedback with no block level HTML elements.

## Halting the response tree within the feedback variables ##

STACK implements a "model" of how assessment works.  Students interact with "inputs" and the system establishes properties of these inputs with response trees. The response trees _automatically_ execute when each non-empty input is "valid".  The introduction of (i) formative potential response trees, and particularly (ii) the `allowempty` option for inputs has expanded this model.

For example, the `allowempty` option is a property of an _input_ and not a response tree.  It is not possible to specify in the _input settings_ that one response tree will allow an input to be empty, but another one will not.  There are some advanced use-cases with multiple response trees (perhaps some formative) which need more control over when the PRT is executed.

For this reason it is sometimes helpful to allow pre-processing in the feedback variables to halt the execution of a particular potential response tree.  Halting and "bailing out" is similar to deciding an input is blank/invalid.  Scores are not updated, feedback is not generated, etc.  This "attempt" does not attract a penalty within the penalty system.

STACK provides a special constant `%stack_prt_stop_p`.  By default this is set to `false` at the start of the feedback variables for each PRT.  If this evaluates to the boolean `true` at the end of the feedback variables then the response tree will not execute and the process will "bail out".

For example, you could add the following to the feedback variables check if `ans1` is empty.

    %stack_prt_stop_p:if is(ans1=EMPTYANSWER) then true else false;

(Note, different inputs indicate empty answers in different ways.  Some use `[EMPTYANSWER]` and matrices reflect the size of the matrix.  This is to make sure the _type_ of an empty answer matches the type of the answer in a regular input.)

Once the PRT has started there is no way to "bail out", or disregard the results of the tree. The decision must be made within the feedback variables.  (If you have a compelling use-case to add this option please contact the developers.)

The PRT will return a note `prt1-bail` to indicate the tree attempted to execute but then stopped and "bailed out".  This is in contrast to the note `!` used to indicate the PRT did not execute at all.

This feature can be useful with multiple inputs and _formative PRTs_.  Consider the following question asking a student to give up to three example expressions (E.g. "Give me an example of....").

1. We have three inputs `ans1`, `ans2` and `ans3`.  There is one PRT for each to establish the relevant properties.
2. We have another PRT, checking they are all different.
3. We have a formative PRT e.g. saying something like "All your examples are polynomials, please try something else".

A student just types in two examples, leaving `ans3` empty.

Set up all options with the `allowempty` option, and filter out `EMPTYANSWER` in the last two PRTs.  The last two PRTs can now work if a student only has two examples.  In the first three PRTs we can bail when they are empty.
