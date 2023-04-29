# Bespoke validators

This extra option `validator` to a particular [input](../Authoring/Inputs.md) allows additional bespoke validation, based on a function defined by the question author.  For example, you could require that the student's answer is a _list of exactly three equations_.

Please check existing, supported, valiation options before defining your own!

To use this feature put the following in the input extra options.

    validator:myvalidityidea

Define the function named `myvalidityidea` in the question variables, e.g.

    myvalidityidea(ex) := block([l],
      l:length(ex),
      if l < 3 then return(castext("Your list only has {#l#} elements, which is too few.")),
      ""
    );

Notes:

1. There must be no reference to the input name within the validator function definition, indeed you cannot reference an input in the question variables.  The validator must be a pure function of a single variable.
2. If the function returns a non-empty string, then the student's answer will be considered invalid, and the string displayed to the student as a validation error message as part of the input validation.
3. If the function returns an empty string or `true` then the student's input is considered to be valid.  The use of an empty string here for valid is designed to encourage teachers to write meaningful error messages to students!
4. The function can reference other question variables, e.g. the teacher's answer.
5. The function is always executed with `simp:false` regardless of the question settings.
6. The function is only called last, and only if the expression is already valid otherwise.  So, you cannot replace basic validation (by design).  This means you will/should have an expression which Maxima can evaluate if it gets as far as your validator function.  E.g. no missing `*` or mismatched brackets.
8. The student still cannot use any of the variable names defined in the question variables.
9. Validators only operate on a single input, and there is no mechanism to validate a combination of inputs at once.

A single validator function can be re-used on multiple inputs within a single question. If you regularly copy validator functions from question to question please consider contributing this as a function to the core of STACK.  We expect to collect and support regularly used validators in future.

## Combining validators

If you wish to test for a number of separate properties then it is probably best to create separate functions for each poperty and combine them into a single validator.

For example, imaging you would like the following

1. the answer must be a list;
2. the list has three elements;
3. each element is an equation.

E.g. `[x^2=1, y=1, x+z=1]` is a valid answer.  `[x^2+5, y=1]` is invalid (for two reasons).

Functions which establish these properties are

    /* Define validator fuctions separately. */
    islist(ex) := if listp(ex) then "" else "Your answer must be a list.";
    allequations(ex) := if all_listp(equationp, ex) then "" else "All elements of your answer should be equations.";
    checklen(ex) :=  if ev(is(length(ex)=3),simp) then "" else "Your list must have 3 elements.";
    /* Combine the validator functions. */
    myvalidator(ex) := stack_multi_validator(ex, [islist, allequations, checklen]);

The last line creates a single validator function using the convenience function `stack_multi_validator` supported by STACK.

STACK supports two convenience functions

1. `stack_multi_validator` executes _all_ the validator functions and concatinates the result. 
2. `stack_seq_validator` executes the validator functions in list order until one fails.  This means you can make assumptions in later validators about the _form_ of the expression.

If any validator throws an error then the student's answer is invalid.  E.g. using `any_listp` on a non-list will throw a Maxima error.

## Supported validators

The Maxima code is stored in the sourcecode in `stack/maxima/validator.mac`, e.g. on [github](https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/validator.mac).  When you regularly find yourself testing for particular properties, and copying code between questions, please consider contributing functions to the Maxima core for longer term support.

## Localisation and language support

To localise your validation messages use the castext `lang` block. For example

    ta:phi^2-1;
    myvalidityidea(ex) := block(
        if ev(subsetp(setify(listofvars(ex)),setify(listofvars(ta))), simp) then return(""),
        castext("[[lang code='fi']]Vastauksesi sisältää vääriä muuttujia.[[/lang]][[lang code='en']]Your answer contains the wrong variables.[[/lang]]")
    );

Note, any language strings in supported validators _must_ appear in the STACK language pack!  STACK stores all language strings in the [plugin source code](https://github.com/maths/moodle-qtype_stack/blob/master/lang/en/qtype_stack.php), and these are then translated by volunteers using the online [AMOS system](https://lang.moodle.org/).

Individual language strings can then be referred to using STACK's `[[commonstring ... /]]` block.  For example, the language pack contains the string

    $string['Illegal_strings'] = 'Your answer contains "strings" these are not allowed here.';

An example of how to use this in Maima code is below.

    myvalidator(ex):=block(
        if not(listp(ex)) then return("Your answer must be a list."),
        if not(is(length(ex)=2)) then return("Your list must have two elements."),
        if stringp(second(ex)) then return(castext("[[commonstring key='Illegal_strings' /]]")),
        true
    );

In this example

1. `["Quadratic",x^2-1]` is valid.
2. `[x^2-1,"Quadratic"]` is invalid because the second argument here is a string. In this case the error message comes from the common language pack.
