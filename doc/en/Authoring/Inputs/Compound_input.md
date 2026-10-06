# Compound inputs

Compound inputs are a "meta" type of input, combining other simple input types.  All inputs are either "simple" or "compound".  Compound inputs are not nested, or recusive.

The compound input extends the JSON input type, so it's value is a string which must be a valid JSON object.

The input upacks the JSON object, deals with various parts, and then (when valid) creates a single Maxima object, which is the value of the input.

Compound inputs can call the validation methods of other simple inputs within a question.  Those inputs must be defined in the question, even if they are unused.  This gives the full range of options available to that input.

## Repeat inputs

The repeat input deals with information collected by the [`[[repeat]]` block](../Question_blocks/Dynamic_blocks.md#interactive-repeat).  The name of the repeat input is given as the `save_state` parameter of the corresponding `[[repeatbutton]]`, and the input must be included in the question text together with its validation tag, e.g. `[[input:state1]] [[validation:state1]]`.  The input is hidden from the students: its value is managed by the repeat button.

For each input used in the repeat block, we create a maxima list of expressions.

For example, if the repeat block contains inputs `[[input:ans1]]` and `[[input:ans2]]` and the student chose to give three rows with `x^2`, `x^3`, `x^5` for `ans1` and `2`, `3`, `5` for `ans2` then the value of this input will be

    (
     repeatedans1:[x^2,x^3,x^5],
     repeatedans2:[2,3,5]
    );

Notes,

1. By defining variable `repeatedans1` in this way we automatically enable authors to refer to the list of answers to `ans1` in the PRTs.  We do not, however, actually use the name of `ans1` in the PRT (that answer is not used directly by students in the normal way).
2. This is a single Maxima block which executes gives the answer lists as separate variables.
3. Each value is validated by its own input (`ans1`, `ans2`), with all the options of that input and the security settings of the question.  For example, if `ans1` forbids floats, so does every entry of `repeatedans1`.
4. Rows which are completely empty are ignored.  If no non-empty rows remain, the input is blank (not answered).  A row in which only some inputs are filled in is invalid, unless the empty input has the `allowempty` option.
5. Since the inputs inside the repeat block are not used directly, give them a single valid value as teacher's answer and use their extra option `hideanswer`.

### Creating the teacher's answer.

The value of the repeat input is a JSON string, in a particular structure expected by the repeat input, e.g.

    {"data":{"ans1":["x^2","x^3"],"ans2":["0.5*x^2"]}}

To create the teacher's answer use the following helper function.

    repeat_encode([["ans1",[x^2,x^3]],["ans2",[0.5*x^2]]]);

1. `repeat_encode` takes a single list as its argument, with one entry for each input in the repeat block.
2. Each entry must be a list with two elements.
   * The first element of the list is the input name _as a string_.  (Input names in question variables cannot be used as variables).
   * The second element of the list is the list of expressions.

The same function can be used for the values of the repeat input in question tests.
