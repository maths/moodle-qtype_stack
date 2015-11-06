# This branch holds the development track for STACK 3.5

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

# Changes in the current "equiv" branch.  This is the current TODO list for release of the equiv input type.

This branch is developing a "reasoning by equivalence" input, and associated input improvements.

* *done* Add a "scratch working" area in which students can record their thinking etc. alongside the final answer.
* *done* Modify the text area input so that each line is validated separately.
* Make sure the syntax hint is CAS text, to depend on the question variables.  This should be used to see the text area etc.

## Reasoning by equivalence input type.

* Inputs which enable student to input steps in the working. In particular, variable numbers of input boxes.
* Follow a "model solution", and give feedback based on the steps used.  E.g. identify where in the students' solution a student deviates from the model solution.
* Expand this to be an implication reasoning engine as well.  E.g. differentiating both sides.
* Auto identify what the student has done in a particular step?

* *done* Fix the instant validation. Change the Javascript in yui/input/inputs.js around stack_textarea_input.prototype.get_value = function() to not return a Maxima list.  We need the raw input, line breaks and all, to get a proper validation of the student's answer.  This need us to change ajax.php as well.

### Interface features

### CAS features

* Equating coefficients as a step in reasoning by equivalence. E.g. \( a*x^2+b*x+c=r*x^2+s*x+t \leftrightarrow a=r and b=s and c=t\). See `poly_equate_coeffs` in assessment.mac  
* Natural domain function.
* Add a "Not equals" operator.  For example:

    nfix("<>");
    p:x<>y;
    texput("<>","{\neq}", infix);
    tex(p);

