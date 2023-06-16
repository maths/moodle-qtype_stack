# Bespoke validators

This extra option `validator` to a particular [input](../Authoring/Inputs.md) allows additional bespoke validation, based on a function defined by the question author.  For example, you could require that the student's answer is a _list of exactly three equations_.

Please check [existing, supported, validation options](../Authoring/Inputs.md#options) before defining your own!

For example, to check a list has at most three elements define the function named `validate_listlength` in the question variables, e.g.

    validate_listlength(ex) := block([l],
      if not(listp(ex)) then return(castext("Your answer must be a list")),
      l:length(ex),
      if l < 3 then return(castext("Your list only has {#l#} elements, which is too few.")),
      ""
    );

To use this feature put the following in the input extra options.

    validator:validate_listlength

Notes:

1. The validator must be a pure function of a single variable. There must be no reference to the input name within the validator function definition, indeed you cannot reference an input in the question variables.
2. If the function returns a non-empty string, then the student's answer will be considered invalid, and the string displayed to the student as a validation error message as part of the input validation.
3. If the function returns an empty string or `true` then the student's input is considered to be valid.  The use of an empty string here for valid is designed to encourage teachers to write meaningful error messages to students!
4. The function can reference other question variables, e.g. the teacher's answer.
5. The function is always executed with `simp:false` regardless of the question settings.
6. The function is called after the built-in validation checks, and only if the expression is already valid otherwise.  So, you cannot replace basic validation (by design).  This means you will/should have an expression which Maxima can evaluate if it gets as far as your validator function.  E.g. no missing `*` or mismatched brackets.
8. The student still cannot use any of the variable names defined in the question variables.
9. Validators only operate on a single input, and there is no mechanism to validate a combination of inputs at once.
10. The recommended style for naming validator functions is to begin the name with `validate_`.

A single validator function can be re-used on multiple inputs within a single question. If you regularly copy validator functions from question to question please consider contributing this as a function to the core of STACK (see below for details). We expect to collect and support regularly used validators in future.

## Combining validators

If you wish to test for a number of separate properties then it is probably best to create separate functions for each poperty and combine them into a single validator.

For example, imagine you would like the following:

1. the answer must be a list;
2. the list has three elements;
3. each element is an equation.

E.g. `[x^2=1, y=1, x+z=1]` is a valid answer.  `[x^2+5, y=1]` is invalid (for two reasons).

Functions which establish these properties are:

    /* Define validator fuctions separately. */
    validate_islist(ex) := if listp(ex) then "" else "Your answer must be a list.";
    validate_allequations(ex) := if all_listp(equationp, ex) then "" else "All elements of your answer should be equations.";
    validate_checklen(ex) :=  if ev(is(length(ex)=3),simp) then "" else "Your list must have 3 elements.";
    /* Combine the validator functions. */
    validate_equationlist(ex) := stack_multi_validator(ex, [validate_islist, validate_allequations, validate_checklen]);

The last line creates a single validator function using the convenience function `stack_multi_validator` supported by STACK.

STACK supports two convenience functions

1. `stack_multi_validator` executes _all_ the validator functions and concatenates the result.
2. `stack_seq_validator` executes the validator functions in list order until one fails.  This means you can make assumptions in later validators about the _form_ of the expression.

If any validator throws an error then the student's answer is invalid.  E.g. using `any_listp` on a non-list will throw a Maxima error.

## Supported validators

The Maxima code is stored in the sourcecode in `stack/maxima/validator.mac`, e.g. on [github](https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/validator.mac).

### Contributing validators to the core of STACK {#contributing}

When you regularly find yourself testing for particular properties, and copying code between questions, please consider contributing functions to the STACK core for longer term support.

You can [post your suggestion on the project's GitHub site](https://github.com/maths/moodle-qtype_stack/issues) or [submit code directly as a pull request](https://github.com/maths/moodle-qtype_stack/pulls).

## Localisation and language support

To localise your validation messages use the castext `lang` block. For example

    ta:phi^2-1;
    validate_vars(ex) := block(
        if ev(subsetp(setify(listofvars(ex)),setify(listofvars(ta))), simp) then return(""),
        castext("[[lang code='fi']]Vastauksesi sisältää vääriä muuttujia.[[/lang]][[lang code='en']]Your answer contains the wrong variables.[[/lang]]")
    );

For the supported validator function, all language strings are drawn from the STACK language pack: STACK stores all language strings in the [plugin source code](https://github.com/maths/moodle-qtype_stack/blob/master/lang/en/qtype_stack.php), and these are then translated by volunteers using the online [AMOS system](https://lang.moodle.org/).

Individual language strings can then be referred to using STACK's `[[commonstring ... /]]` block.  For example, the language pack contains the string

    $string['Illegal_strings'] = 'Your answer contains "strings" these are not allowed here.';

An example of how to use this in Maxima code is below.

    validate_listoftwo(ex):=block(
        if not(listp(ex)) then return("Your answer must be a list."),
        if not(is(length(ex)=2)) then return("Your list must have two elements."),
        if stringp(second(ex)) then return(castext("[[commonstring key='Illegal_strings' /]]")),
        true
    );

In this example

1. `["Quadratic",x^2-1]` is valid.
2. `[x^2-1,"Quadratic"]` is invalid because the second argument here is a string. In this case the error message comes from the common language pack.

Many language examples have variables which need to be injected.  In this example, the variable `m0` needs to be injected.

    $string['ValidateVarsSpurious']   = 'These variables are not needed: {$a->m0}.';

To inject variables into a language string we define the value of `m0` in the `[[commonstring ... /]]` block.

    validate_spuriousvar(ex):=block([%_tmp,simp],
        simp:false,
        %_tmp: listofvars(ex),
        simp:true,
        %_tmp: setdifference(setify(%_tmp), {x,y,z}),
        if cardinality(%_tmp) = 0 then return(""),
        castext("[[commonstring key='ValidateVarsSpurious' m0='listify(%_tmp)'/]]")
    );

Note, when injecting a value `m0='X'` the `X` must be a Maxima expression, not a displayed string.

1. to inject the Maxima expression `X` with `{@...@}` injection (without wrapping like `\(...\)`) to a named placeholder `m0` use `m0='X'`.
1. to inject the Maxima expression `X` with `{#...#}` injection, to get raw values, to a named placeholder `m0` use `raw_m0='X'`.

For other prefix options see the [documentaiton for the commonstring block](../Authoring/Question_blocks/Static_blocks.md#commonstring-block).

## Further examples

To forbid the underscore character in a student's input.

    validate_underscore(ex) := if is(sposition("_", string(ex)) = false) then ""
               else "Underscore characters are not permitted in this input.";

# Sharing validators between questions

It is common to want to share validators between questions.  It would also be very helpful to contribute commonly used validator functions back to the STACK project.  To include a validator in more than one question you could post your validator function publicly.

1. Get the validator function working reliably in your question, locally.
2. Add the maxima function to this file, [`https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/contrib/validators.mac`](https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/contrib/validators.mac) or another file, preferably contributing to the STACK project.
3. Add documentation and comprehensive test cases (please!) to let other people know what the validator is intended to do, and to help ensure behaviour remains stable.
4. Include the [optional validators within the cas logic](../Authoring/Inclusions.md#inclusions-within-cas-logic) with either of the following

    stack_include("https://raw.githubusercontent.com/maths/moodle-qtype_stack/master/stack/maxima/contrib/validators.mac");
    stack_include_contrib("validators.mac");

Note the url `https://raw.githubusercontent.com/` is used to include the raw content of this file.

Including external content always poses a minor additional security risk.  In this case (1) the content is included and then subject to the same checks as if you had typed it yourself, and (2) the developers will take the same care in accepting contributions to the master branch as they do with the existing code base.

### Example: forbid underscores in an input

Create a new question.

1. Add the following to the question variables, which loads contributed validators.

    stack_include("https://raw.githubusercontent.com/maths/moodle-qtype_stack/master/stack/maxima/contrib/validators.mac");

  or add the following to the question variables

    stack_include_contrib("validators.mac");

2. Use the extra option `validator:validate_underscore` in the input.

