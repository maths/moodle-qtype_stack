# Compound inputs

Compound inputs are a "meta" type of input, combining other simple input types.  All inputs are either "simple" or "compound".  Compound inputs are not nested, or recusive.

The compound input extends the JSON input type, so it's value is a string which must be a valid JSON object.

The input upacks the JSON object, deals with various parts, and then (when valid) creates a single Maxima object, which is the value of the input.

Compound inputs can call the validation methods of other simple inputs within a question.  Those inputs must be defined in the question, even if they are unused.  This gives the full range of options available to that input.

## Repeat inputs

The repeat input deals with information collected by the `[[repeat]]` block.

For each input used in the repeat block, we create a maxima list of expressions.

For example, if the repeat block depends inputs `[[input:ans1]]` and `[[input:ans2]]`the student chose to give three inputs `x^2`, `x^3`, `x^5` for `ans1` and `2`, `3`, `5` for `ans2` then the value of this input will be

    (
     repeatedans1:[x^2,x^3,x^5],
     repeatedans2:[2,3,5]
    );

Notes, 

1. By defining variable `repeatedans1` in this way we automatically enable authors to refer to the list of answers to `ans1` in the PRTs.  We do not, however, actually use the name of `ans1` in the PRT (that answer is not used directly by students in the normal way).
2. This is a single Maxima block which executes gives the answer lists as separate variables.

## Creating the teacher's answer.

The teacher's answer must be a valid JSON string, in a particular structure expected by the repeat input.  To create the teacher's answer use the following helper function.

    repeat_encode([["ans1",[x^2,x^3]],["ans2",[0.5*x^2]]]);

1. `repeat_encode` takes an arbitrary number of arguments.
2. Each argument must be a list with two elements.
   * The first element of the list is the input name _as a string_.  (Input names in question variables cannot be used as variables).
   * The second elemnt of the list if the list of expressions.
