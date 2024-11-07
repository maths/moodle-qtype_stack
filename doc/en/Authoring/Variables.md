# Defining variables

There are two fields which allow you to define and manipulate computer algebra system variables.
These are called the Question Variables and Feedback variables.

The field is a string which contains a list of assignments of the form

    key : value;

for example

    p : (x-1)^3;

Each `key` is the name of a variable local to the question, and `value` is an expression in [Maxima's](../CAS/Maxima_background.md) language.
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

* Teacher's answers in [inputs](../Authoring/Inputs/index.md) are defined in terms of question variables.
* [Question note](../Authoring/Question_note.md).
* General feedback (also known as the worked solution).
* All fields in each of the [potential response tree](Potential_response_trees.md).
* Each input when testing the item.
* Question variables are not available to inputs when a student is validating their answer, unless special ''context variables'' are defined in a preamble.

### Question variables preamble and context variables

If the following commands appear within the question variables they will be available in every part of the question, in particular these commands will affect how students' input is validated.  This enables teachers to affect the display of the student's answer validation, and add assumptions to PRTs.

* `orderless` and `ordergreat`.  These commands may only appear once each in any question.
* `assume` and `declare`.
* `texput`, see notes directly below.

This collection of special variables are called "context variables".

STACK has a special constant `%_stack_preamble_end`.  Any variables _before_ this constant will be included within the context variables.  This enables you to define functions, e.g. to use with `textput`.  

Note, that students are not permitted to use any variable name defined by the teacher in the question variables.  This includes both the context variables, and the regular remaining question variables.  It is not possible to define variables which a student can then use.  Students _can_ use function names defined in the preamble. e.g. you can put `vec(ex):=stackvector(ex);` into the preamble.

For example, `texput(blob, "\\diamond")` is simple.  You can also define a function and use this function in texput.

```
tuptex(z):= block([a,b], [a,b]:args(z), sconcat("\\left[",tex1(a),",",tex1(b),"\\right)"));
texput(tup, tuptex); 
%_stack_preamble_end;
```

It is also possible to use an unnamed `lambda` function.  E.g. if you have a function `tup` then

    texput(tup,  lambda([z], block([a,b], [a,b]:args(z), sconcat("\\left[",tex1(a),",",tex1(b),"\\right)")))); 

will display `tup(a,b)` as \( \left[a,b\right) \).

To create a function `hat` so that input `hat(x)` is displayed as \(\hat{x}\) you can use:

    /* In question variables. */
    texput(hat, lambda([ex], sconcat("\\hat{", tex1(first(ex)), "}")));

As a more complicated example, to typeset `u(A_k,k,1,inf)` as \({\bigcup_{k = 1}^{\infty } {A}_{k}}\) you can use the following:

    texput(u,lambda([ex],if length(ex)<4 then return("\\bigcup_{?=?}^{?} ? ") else
        sconcat("\\bigcup_{" ,tex1(second(ex)), " = ", tex1(third(ex)), "}^{", tex1(fourth(ex)), "} ", tex1(first(ex)))));

Notice in this example how we check the length of the arguments supplied to the (inert) function `u`.  If there are fewer than the required number of arguments then this texput function returns something sensible.  Without this clause you get errors, which would be unhelpful to a student trying to type this in.

Another example is to have the function `foo` displayed as traditional fractions.

    texput(foo,lambda([e],[a,b]:args(e), sconcat("\\frac{", tex1(a), "}{", tex1(b), "}")));

Note the way the lambda expression for the tex function has _one_ argument which is split later within the function.

## Feedback variables {#Feedback_variables}

The feedback variables form one field in the [potential response tree](Potential_response_trees.md).

When using the [potential response tree](Potential_response_trees.md) it is often very useful
to manipulate the student's answer _before_ applying any of the [Answer tests](Answer_Tests/index.md).
This gives the opportunity to perform sophisticated mathematical operations.

Before each answer test is applied the following list of variables is assembled and evaluated

1. The values of the question variables.
2. The values of each [inputs](../Authoring/Inputs/index.md).
3. The feedback variables.

The values of the evaluated feedback variables can be used as expressions in the answer tests and in the feedback.

Note, you cannot redefine the value of an input as a key in the feedback variables.  e.g. you cannot have something like `ans1:ans1+1`. You must use a new variable name.  When an answer test is evaluated, if the SA or TA field is exactly the name of an input then the raw student's value is used, and not the value from the feedback variables. This is because some of the answer tests require exactly what is typed (e.g. trailing zeros) and not the value through the CAS.  To avoid this problem authors must use new variable names to distinguish between the actual input typed by the student and any calculated value.

## Creating variable names ##

Teachers may not use the Maxima 'concat' command, so you cannot create variable names of your own using code of the following form.

    vars:makelist(concat(x,k),k,1,5);

Instead use

    vars0:stack_var_makelist(k, 5);

If you want to start numbering at 1 instead of 0, use

    vars1:rest(stack_var_makelist(k, 6));

