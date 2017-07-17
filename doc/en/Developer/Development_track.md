# Development track for STACK 3.6 and STACK 4.0

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

# STACK 3.6

This track is developing more inputs, including a "reasoning by equivalence" input type. This is the current TODO list for release of the equiv input type.

This branch is developing a "reasoning by equivalence" input, and associated input improvements.

* *done* Add a "scratch working" area in which students can record their thinking etc. alongside the final answer.
* *done* Modify the text area input so that each line is validated separately.
* Make the syntax hint is CAS text, to depend on the question variables.

# Reasoning by equivalence input type.

The primary puropose of this branch is the reasoning by equivalence input type.

* Inputs which enable student to input steps in the working. In particular, variable numbers of input boxes.
* *done* Fix the instant validation. Change the Javascript in yui/input/inputs.js around stack_textarea_input.prototype.get_value = function() to not return a Maxima list.  We need the raw input, line breaks and all, to get a proper validation of the student's answer.  This need us to change ajax.php as well.
* *done* Display a teacher's answer as a worked solution, just as the input type uses.

For a future version: follow a "model solution", and give feedback based on the steps used.  E.g. identify where in the students' solution a student deviates from the model solution.


## Interface features

* Add an option to display and/or using language strings not '\wedge', '\vee'.
* *done* Change the syntax hint so that the *value* of the variables is used.  This enables the first line to be seeded with random parameters.
* *done* Equational reasoning.  If the next line begins with an = sign.


## Comments.

* *done* Basic comment mechanism.  Any lines which are strings are treated as comments which break the argument.
* Improve spacing of comments, e.g. \intertext{...}?
* Ensure comments in equivalence inputs are correctly displayed by the answer tests, not by EQUIVCOMMENT tags.

## Mathematical features

Add mathematical support in the following order.

1. *done* Solving quadratic equations.
2. *done* Rearranging equations to "make \(x\) the subject".  Allow assumptions (assume positive).
3. *done* Solving inequalities.
4. Equating coefficients as a step in reasoning by equivalence. E.g. \( a x^2+b x+c=r x^2+s x+t \leftrightarrow a=r \mbox{ and } b=s \mbox{ and } c=t\). See `poly_equate_coeffs` in assessment.mac
5. Solving simple simultaneous equations.  (Interface)
6. Logarithms and simple logarithmic equations.
7. Include calculus operations.

## CAS features

* Natural domain function.
* Add a "Not equals" operator.  For example:

    infix("<>");
    p:x<>y;
    texput("<>","{\neq}", infix);
    tex(p);

* Answer test(s) which acts on the whole argument.

## Steps in working

* Expand this to be an implication reasoning engine as well.  E.g. differentiating both sides, and "taking logs".
* Auto identify what the student has done in a particular step?
* Develop a metric to measure the distance between expressions.  Use this as a measure of "step size" when working with expressions.

# STACK 4.0

This track adds major new features, and changes the interaction model for STACK substantially.  The goal is to develop STACK 3.6 and STACK 4.0 in parallel.  At a point where both are stable we will decide whether to actually release 3.6, or to merge and release all features as one new major change.

* Expand the CASText format to enable us to embed the _value_ of a variable in CASText, not just the displayed form.
* Conditionals in CASText adaptive blocks. (Aalto) See [question blocks](../Authoring/Question_blocks.md) for our plans.
* Add state to the question model.
