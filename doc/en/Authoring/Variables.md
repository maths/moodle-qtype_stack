# Defining variables

There are two fields which allow you to define and manipulate computer algebra system variables.
These are called the Question Variables and Answer Variables.

The field is a string which contains a list of assignments of the form

    key : value;

for example

    p : (x-1)^3;

Each `key` is the name of a variable local to the question, and `value` is an expression in [Maxima's](../CAS/Maxima.md) language.
When evaluated, this list is passed to the CAS, and evaluated in order. The value obtained for each key will be stored and used later, for example in the question marking routines.
The keys need not be unique, although only the last value will be available for use later.

These fields are known as _KeyVal_ fields.

## Maxima's assignments `a:3` ##

Computer algebra systems each use a different syntax to denote the assignment of a value to a variable.
For example, Maple and Derive use `:=`. Mathematica uses `=` or `:=`, depending on when the assignment is to take place.
Maxima uses the form `key:value`, which is unusual and not intuitive.
Maxima reserves `:=` to denote function definition, e.g. `f(x):=x^2`.
STACK uses Maxima's assignment rules.

## Notes ##

* Items are separated by either a newline or `;`.
* Adding `;` at the end of each statement is optional, but makes it easier to cut and paste into a Maxima session.  Please add these.
* If you type a string not in the form `key : value`, a variable name such as `dumvar3` will be assigned automatically to keep track of the command in the list of question variables.
* The `key` must be a simple variable name.  It must start with letters, and can contain numbers and underscore characters.
* If a student uses a variable which has been assigned a value in the question variables then the attempt will be rejected as invalid.  You can use the "allowed words" to enable students' expressions with question variables to be considered valid.
    Hence, it is a sensible idea to use variable names which are not used as parameters in the question, or likely to occur in the student's answer. For example if you set an integration question then you should avoid using the variable `c`, otherwise students won't be able to write `+c` in the normal way to indicate a constant of integration.
* You can include C-style block comments for increased clarity, and these may appear on separate lines
    e.g. `dice : rand(6) + 1; /* roll it! */`
* Do not define a feedback variable with the same name as an input.  For example, if your input is `ans1` then you cannot define a feedback variable `ans1:exdowncase(ans1)`.  Choose something different, e.g. `ansmod1:exdowncase(ans1)`

## Question variables {#Question_variables}

The question variables are evaluated when a variant of a question is created.   The displayed forms are available to all other [CASText](CASText.md) fields and the values to other parts of the question, e.g.

* Teacher's answers in [inputs](Inputs.md) are defined in terms of question variables.
* [Question note](Question_note.md).
* All fields in each of the [potential response tree](Potential_response_trees.md).
* Each input when testing the item.

## Feedback variables {#Feedback_variables}

The feedback variables form one field in the [potential response tree](Potential_response_trees.md).

When using the [potential response tree](Potential_response_trees.md) it is often very useful
to manipulate the student's answer _before_ applying any of the [Answer tests](Answer_tests.md).
This gives the opportunity to perform sophisticated mathematical operations.

Before each answer test is applied the following list of variables is assembled and evaluated

1. The values of the question variables.
2. The values of each [inputs](Inputs.md).
3. The feedback variables.

The values of the evaluated feedback variables can be used as expressions in the answer tests and in the feedback.

Note, you cannot redefine the value of an input as a key in the feedback variables.  e.g. you cannot have something like `ans1:ans1+1`.
You must use a new variable name.  
When an answer test is evaluated, if the SA or TA field is exactly the name of an input then the raw student's value is used, and not the value from the feedback variables. 
This is because some of the answer tests require exactly what is typed (e.g. trailing zeros) and not the value through the CAS.  
To avoid this problem authors must use new variable names to distinguish between the actual input typed by the student and any calculated value.

## Creating variable names ##

Teachers may not use the Maxima 'concat' command, so you cannot create variable names of your own using code of the following form.

    vars:makelist(concat(x,k),k,1,5);

Instead use

    vars0:stack_var_makelist(k, 5);

If you want to start numbering at 1 instead of 0, use

    vars1:rest(stack_var_makelist(k, 6));

