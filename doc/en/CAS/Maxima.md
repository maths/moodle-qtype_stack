# Introduction to Maxima for STACK users

A computer algebra system (CAS) is software that allows the manipulation of mathematical expressions in symbolic form. Most commonly, this is to allow the user to perform some computation.  For the purposes of assessment our calculation _establishes some relevant properties_ of the students' answers. These properties include

  * using a [predicate function](Predicate_functions.md) to find if a single expression has a property.  For example, are any of the numbers floating points?
  * comparing two expressions using an answer test to compare two expressions.  For example, is the student's expression equivalent to the teacher's?

 [randomly generating](Random.md) structured mathematical objects which become parts of a question and [plotting graphs](Plots.md) of functions.

To write more than very simple STACK questions you will need to use some Maxima commands. This documentation does not provide a detailed tutorial on Maxima. A very good introduction is given in [Minimal Maxima](http://maxima.sourceforge.net/docs/tutorial/en/minimal-maxima.pdf), which this document assumes you have read.

STACK modifies Maxima in a number of ways.

## Types of object {#Types_of_object}

Everything in Maxima is either an _atom_ or an _expression_. Atoms are either an integer number, float, string or a name.  You can use the predicate `atom()` to decide if its argument is an atom.  Expressions have an _operator_ and a list of _arguments_. Note that the underscore symbol is _not_ an operator.  Thus `a_1` is an atom in maxima. Display of subscripts and fine tuning the display is explained in the [atoms, subscripts and fine tuning the LaTeX display](Subscripts.md) page.

Maxima is a very weakly typed language.  However, in STACK we need the following "types" of expression:

  1. equations, i.e. an expression in which the top operation is an equality sign;
  2. inequalities, for example \( x<1\mbox{, or }x\leq 1\);
  3. sets, for example, \(\{1,2,3\}\);
  4. lists, for example, \([1,2,3]\).   In Maxima ordered lists are entered using square brackets, for example as `p:[1,1,2,x^2]`.
    An element is accessed using the syntax `p[1]`.
  5. [matrices](Matrix.md).  The basic syntax for a matrix is `p:matrix([1,2],[3,4])`.  Each row is a list. Elements are accessed as `p[1,2]`, etc.
  6. logical expression.  This is a tree of other expressions connected by the logical `and` and `or`.  This is useful for expressing solutions to equations, such as `x=1 or x=2`.  Note, the support for these expressions is unique to STACK.
  7. expressions.

Expressions come last, since they are just counted as being _not_ the others! STACK defines [predicate functions](Predicate_functions.md) to test for each of these types.

## Numbers {#Numbers}

Numbers are important in assessment, and there is more specific and detailed documentation on how numbers are treated: [Numbers in STACK](Numbers.md).

## Alias ##

STACK defines the following function alias names

    simplify := fullratsimp
    int := integrate

The absolute value function in Maxima is entered as `abs()`.  STACK also permits you to enter using `|` symbols, i.e.`|x|`.  This is an alias for `abs`.  Note that `abs(x)` will be displayed by STACK as \(|x|\).

STACK also redefined a small number of functions

* The plot command `plot2d` is not used in STACK questions.  Use `plot` instead, which is documented [here](Plots.md).  This ensures your image files are available on the server.
* The random number command `random` is not used in STACK questions.  Use the command `rand` instead, which is documented [here](Random.md).  This ensures pseudorandom numbers are generated and a student gets the same version each time they login.

## Parts of Maxima expressions {#Parts_of_Maxima_expressions}

### `op(x)` - the top operator

It is often very useful to take apart a Maxima expression. To help with this Maxima has a number of commands, including `op(ex)`, `args(ex)` and `part(ex,n)`. Maxima has specific documentation on this.

In particular,  `op(ex)` returns the main operator of the expression `ex`.  This command has some problems for STACK.

 1. calling `op(ex)` on an atom (see Maxima's documentation on the predicate `atom(ex)`) such as numbers or variable names, cause  `op(ex)` to throw an error.
 2. `op(ex)` sometimes returns a string, sometimes not.
 3. the unary minus causes problems.  E.g. in `-1/(1+x)`
    the operation is not "/", as you might expect, but it is "-" instead!

To overcome these problems STACK has a command

    safe_op(ex)

This always returns a string.  For an atom this is empty, i.e. `""`.  It also sorts out some unary minus problems.

### `get_ops(ex)` - all operators

This function returns a set of all operators in an expression.  Useful if you want to find if multiplication is used anywhere in an expression.

## Maxima commands defined by STACK {#Maxima_commands_defined_by_STACK}

It is very useful when authoring questions to be able to test out Maxima code in the same environment which STACK uses Maxima. That is to say, with the settings and STACK specific functions loaded. To do this see [STACK-Maxima sandbox](STACK-Maxima_sandbox.md).

STACK creates a range of additional functions and restricts those available, many of which are described within this documentation.  See also [Predicate functions](Predicate_functions.md).

| Command                         | Description
| ------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `factorlist(ex)`                | Returns a list of factors of ex without multiplicities.
| `zip_with(f,a,b)`               | This function applies the binary function \(f\) to two lists \(a\) and \(b\) returning a list.  An example is given in adding matrices to [show working](Matrix.md#Showing_working).
| `coeff_list(ex,v)`              | This function takes an expression `ex` and returns a list of coefficients of `v`.
| `coeff_list_nz(ex,v)`           | This function takes an expression `ex` and returns a list of nonzero coefficients of `v`.
| `divthru(ex)`                   | Takes an algebraic fraction, e.g. \((x^4-1)/(x+2)\) and divides through by the denominator, to leave a polynomial and a proper fraction. Useful in feedback, or steps of a calculation.
| `stack_strip_percent(ex,var)`   | Removes any variable beginning with the `%` character from `ex` and replace them with variables from `var`.  Useful for use with solve, ode2 etc.  [Solve and ode2](../Topics/Differential_equations.md#Solve_and_ode2).
| `exdowncase(ex)`                | Takes the expression `ex` and substitutes all variables for their lower case version (cf `sdowncase(ex)` in Maxima).  This is very useful if you don't care if a student uses the wrong case, just apply this function to their answer before using an [answer test](../Authoring/Answer_Tests/index.md).  Note, of course, that `exdowncase(X)-x=0.`
| `stack_reset_vars`              | Resets constants, e.g. \(i\), as abstract symbols, see [Numbers](Numbers.md).
| `safe_op(ex)`                   | Returns the operation of the expression as a string.  Atoms return an empty string (rather than throwing an error as does `op`).
| `comp_square(ex,v)`             | Returns a quadratic `ex` in the variable `v` in completed square form.
| `degree(ex,v)`                  | Returns the degree of the expanded form of `ex` in the variable `v`. See also Maxima's `hipow` command.
| `unary_minus_sort(ex)`          | Tidies up the way unary minus is represented within expressions when `simp:false`.  See also [simplification](Simplification.md).
| `texboldatoms(ex)`              | Displays all non-numeric atoms in bold.  Useful for vector questions.

## Assignment ## {#assignment}

In Maxima the assignment of a value to a variable is _very unusual_.

Input                  | Result
---------------------- | --------------------------------------
`a:1`                  | Assignment of the value \(1\) to \(a\).
`a=1`                  | An equation, yet to be solved.
`f(x):=x^2`            | Definition of a function.

In STACK simple assignments are of the more conventional form `key : value`, for example,

    n : rand(3)+2;
    p : (x-1)^n;

Of course, these assignments can make use of Maxima's functions to manipulate expressions.

    p : expand( (x-3)*(x-4) );

Another common task is that of _substitution_. This can be performed with Maxima's `subst` command. This is quite useful, for example if we define \(p\)  as follows, in the then we can use this in response processing to determine if the student's answer is odd.

    p : ans1 + subst(-x,x,ans1);

All sorts of properties can be checked for in this way. For example, interpolates. Another example is a stationary point of \(f(x)\) at \(x=a\), which can be checked for using

    p : subst(a,x,diff(ans1,x));

Here we have assumed `a` is some point given to the student, `ans1` is the answer and that \(p\) will be used in the response processing tree.

You can use Maxima's looping structures within Question variables. For example

    n : 1;
    for a:-3 thru 26 step 7 do n:n+a;

The result will be \(n=56\). It is also possible to define functions within the question variables for use within a question. 

    f(x) := x^2;
    n : f(4);

## Logarithms ##

STACK loads the contributed Maxima package `log10`.  This defines logarithms to base \(10\) automatically. STACK also creates two aliases

1. `ln` is an alias for \(\log\), which are natural logarithms
2. `lg` is an alias for \(\log_{10}\), which are logarithms to base \(10\).
    It is not possible to redefine the command `log` to be to the base \(10\).

## Sets, lists, sequences, n-tuples ##

It is very useful to be able to display expressions such as comma separated lists, and n-tuples
\[ 1,2,3,4,\cdots \]
\[ (1,2,3,4) \]
Maxima has in-built functions for lists, which are displayed with square brackets \([1,2,3,4]\), and sets with curly braces \( \{1,2,3,4\} \).
Maxima has no default functions for n-tuples or for sequences.

STACK provides an inert function `sequence`.  All this does is display its arguments without brackets. For example `sequence(1,2,3,4)` is displayed \(1,2,3,4\). STACK provides convenience functions.

* `sequenceify`, creates a sequence from the arguments of the expression.  This turns lists, sets etc. into a sequence.
* `sequencep` is a predicate to decide if the expression is a sequence.
* The atom `dotdotdot` is displayed using the tex `\ldots` which looks like \(\ldots\).  This atom cannot be entered by students.

STACK provides an inert function `ntuple`.  All this does is display its arguments with round brackets. For example `ntuple(1,2,3,4)` is displayed \((1,2,3,4)\).  `ntupleify` and `ntuplep` construct and test for ntuples. In strict Maxima syntax `(a,b,c)` is equivalent to `block(a,b,c)`.  If students type in `(a,b,c)` using a STACK input it is filtered to `ntuple(a,b,c)`. Teachers must use the `ntuple` function explicitly to construct question variables, teacher's answers, test cases and so on. The `ntuple` is useful for students to type in coordinates.

If you want to use these functions, then you can create question variables as follows

    L1:[a,b,c,d];
    D1:apply(ntuple, L1);
    L2:args(D1);
    D2:sequenceify(L2);

Then `L1` is a list and is displayed with square brackets as normal. `D1` has operator `ntuple` and so is displayed with round brackets. `L2` has operator `list` and is displayed with square brackets.  Lastly, D2 is an `sequence` and is displayed without brackets.

You can, of course, apply these functions directly.

    T1:ntuple(a,b,c);
    S1:sequence(a,b,c,dotdotdot);

If you want to use `sequence` or `ntuple` in a PRT comparison, you probably want to turn them back into lists. E.g. `ntuple(1,2,3)` is not algebraically equivalent to `[1,2,3]`.  To do this use the `args` function.   We may, in the future, give more active meaning to the data types of `sequence` and `ntuple`.

Currently, students can enter expressions with "implied ntuples" E.g

* Student input of `(1,2,3)` is interpreted as `ntuple(1,2,3)`.
* Student input of `{(1,2,3),(4,5,6)}` is interpreted as `{ntuple(1,2,3),ntuple(4,5,6)}`.
* Since no operations are defined on ntuples, students cannot currenlty enter things like `(1,2,3)+s*(1,0,0)`.  There is nothing to stop a teacher defining the expression tree `ntuple(1,2,3)+s*ntuple(1,0,0)`, but the operations `+` and `*` are not defined for ntuples and so nothing will happen!  If you want a student to enter the equation of a line/plane they should probably use the matrix syntax for vectors.  (This may change in the future).

Matrices have options to control the display of the braces.  Matrices are displayed without commas.

If you are interacting with javascript do not use `sequenceify`.  If you are interacting with javascript, such ss [JSXGraph](../Authoring/JSXGraph.md), then you may want to output a list of _values_ without all the LaTeX and without Maxima's normal bracket symbols. You can use

    stack_disp_comma_separate([a,b,sin(pi)]);

This function turns a list into a string representation of its arguments, without braces.
Internally, it applies `string` to the list of values (not TeX!).  However, you might still get things like `%pi` in the output.

You can use this with mathematical input: `{@stack_disp_comma_separate([a,b,sin(pi)])@}` and you will get the result `a, b, sin(%pi/7)` (without the string quotes) because when a Maxima variable is a string we strip off the outside quotes and don't typeset this in maths mode.


## Functions ##

It is sometimes useful for the teacher to define *functions* as part of a STACK question.  This can be done in the normal way in Maxima using the notation.

     f(x):=x^2;

Using Maxima's `define()` command is forbidden. An alternative is to define `f` as an "unnamed function" using the `lambda` command.

     f:lambda([x],x^2);

Here we are giving a name to an "unnamed function" which seems perverse.  Unnamed functions are extremely useful in many situations.

For example, a piecewise function can be defined by either of these two commands

     f(x):=if (x<0) then 6*x-2 else -2*exp(-3*x);
     f:lambda([x],if (x<0) then 6*x-2 else -2*exp(-3*x));

You can then plot this using

    {@plot(f(x),[x,-1,1])@}

# Maxima "gotcha"s! #

  * See the section above on [assignment](Maxima.md#assignment).
  * Maxima does not have a `degree` command for polynomials.  We define one via the `hipow` command.
  * Matrix multiplication is the dot, e.g. `A.B`. The star `A*B` gives element-wise multiplication.
  * The atoms `a1` and `a_1` are not considered to be algebraically equivalent.

## Further information and links  ##

* [Minimal Maxima](http://maxima.sourceforge.net/docs/tutorial/en/minimal-maxima.pdf)
* [Maxima on SourceForge](http://maxima.sourceforge.net)

## See also

[Maxima reference topics](index.md#reference)
