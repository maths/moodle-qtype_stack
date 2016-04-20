# KeyVals

There are two fields which allow you to define and manipulate computer algebra system variables.
These are called the Question Variables and Answer Variables.

The field is a string which contains a list of assignments of the form

    key : value

for example

    p : (x-1)^3

Each `key` is the name of a variable local to the question, and `value` is an expression in [Maxima's](../CAS/Maxima.md) language.
When evaluated this list is passed to the CAS, and evaluated in order. The value obtained for each key will be stored and used later, for example in the question marking routines.
The keys need not be unique, although only the last value will be available for use later.

These fields are known as _KeyVal_ fields.

## Maxima's assignments `a:3` ##

Computer algebra systems each use a different syntax to denote the assignment of a value to a variable.
For example, Maple and Derive use `:=`. Mathematica uses `=` or `:=`, depending on when the assignment is to take place.
Maxima uses the form `key:value`, which is unusual and not intuitive.
Maxima reserves `:=` to denote function definition, e.g. `f(x):=x^2`.

__STACK 3 now uses Maxima's assignment rules.  This is a change from STACK 2.__  Questions will be imported and quietly changed.

## Notes ##

* Items are separated by either a newline or ;
* Adding `;` at the end of each statement is optional, but makes it easier to cut and paste into a Maxima session.
* If you type a string not in the form `key : value`, a variable name such as `dumvar3` will be
    assigned automatically to keep track of the command in the list of question variables.
* If a student uses a variable longer than one letter in length which has been assigned a value in the question variables then the attempt will be rejected as invalid.
    Hence, it is a sensible idea to use variable names which are not used as parameters.
    For example if you set an integration question then you should avoid using the variable `c`, otherwise students won't be able to write `+c` in the normal way to indicate a constant of integration.
* You can include C-style block comments for increased clarity, and these may appear on separate lines
    e.g. `dice : rand(6) + 1 /* roll it! */`
* Avoid using variable names with a single letter, otherwise a student might type this in and it will not automatically be forbidden.  You can always forbid them explicitly in the input "forbid" options.

## Question variables {#Question_variables}

The question variables are evaluated when a version of a question is created.   The displayed forms are available to all other [CASText](CASText.md) fields and the values to other parts of the question, e.g.

* Teacher's answers in [inputs](Inputs.md) are defined in terms of question variables.
* [Question note](Question_note.md).
* All fields in each of the [potential response tree](Potential_response_trees.md).
* Each input when testing the item.

If the teacher uses a variable name which is two characters or longer, then students will not be able to use this variable name in their input.  Input from students with two charater variable names which appear in the question variables will be rejected as invalid.  Students can always use single letter variable names.  Teachers are therefore advised to avoid single letter variable names.

## Feedback variables {#Feedback_variables}

The feedback variables form one field in the [potential response tree](Potential_response_trees.md).

When using the [potential response tree](Potential_response_trees.md) it is often very useful
to manipulate the student's answer _before_ applying any of the [Answer tests](Answer_tests.md).
This gives the opportunity to perform sophisticated mathematical operations.
Of course, using these makes interoperability very difficult.

Before each answer test is applied the following list of variables is assembled and evaluated

1. The values of the [question variables](KeyVals.md#Question_variables).
2. The values of each [inputs](Inputs.md).
3. The feedback variables.

The values of the evaluated feedback variables can be used as expressions in the answer tests and in the feedback.
