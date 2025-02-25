# Differential Equations

## Notation

This page provides examples of how to represent ordinary differential equations (ODEs) in [Maxima](../../CAS/Maxima_background.md) when writing STACK questions.

### Representing ODEs

In a Maxima session we can represent an ODE as

    ODE: x^2*'diff(y,x) + 3*y*x = sin(x)/x;

Notice the use of the `'` character in front of the `diff` function to prevent evaluation. Applied to a function call, such as `diff`, the single quote prevents evaluation of the function call, although the arguments of the function are still evaluated (if evaluation is not otherwise prevented). The result is the noun form of the function call.

### Entering DEs

The syntax to enter a derivative in Maxima is `diff(y,x,n)`.  Teachers need to use an apostrophe`'` character in front of the `diff` function to prevent evaluation in question variables (etc). E.g. to type in \( \frac{\mathrm{d}^2y}{\mathrm{d}x^2}\) you need to use `'diff(y,x,2)`.

Students' answers always have noun forms added. If a student types in `diff(y,x)` then this is protected by a special function `noundiff(y,x)` (etc), and ends up being sent to answer test as `'diff(y,x,1)`. If a student types in (literally) `diff(y,x)+1 = 0` this will end up being sent to answer test as `'diff(y,x,1)+1 = 0`.

The answer test `AlgEquiv` evaluates all nouns.   This has a (perhaps) unexpected side-effect that `noundiff(y,x)` will be equivalent to `0`, and `noundiff(y(x),x)` is not.  For this reason we have an alternative [answer test](../../Authoring/Answer_Tests/index.md) `AlgEquivNouns` which does not evaluate all the nouns.
The `ATEqualComAss` also evaluates its arguments but does not "simplify" them.  So, counter-intuitively perhaps, we currently do have `ATEqualComAss(diff(x^2,x), 2*x);` as true.

Students might expect to enter expressions like \( y' \), \( \dot{y} \) or \( y_x \) (especially if you are using `derivabbrev:true`, see below).   The use by Maxima of the apostrophe which affects evaluation also has a side-effect that we can't accept `y'` as valid student input.  Input `y_x` is an atom.  Individual questions could interpret this as `'diff(y,x)` but there is no systematic mechanism for interpreting subscripts as derivatives.  Input `dy/dx` is the division of one atom `dy` by another `dx` and so will commute with other multiplication and division in the expression as normal.  There is no way to protect input `dy/dx` as \( \frac{\mathrm{d}y}{\mathrm{d}x}\).  The only input which is interpreted by STACK as a derivative is Maxima's `diff` function, and students must type this as input.

The expression `diff(y(x),x)` is not the same as `diff(y,x)`.  In Maxima `diff(y(x),x)` is not evaluated further.  Getting students to type `diff(y(x),x)` and not `diff(y,x)` will be a challenge.  Hence, if you want to condone the difference, it is probably best to evaluate the student's answer in the feedback variables as follows to ensure all occurrences of `y` become `y(x)`.

    ans1:'diff(y(x),x)+1 = 0;
    ansyx:subst(y,y(x),ans1);

Trying to substitute `y(x)` for `y` will throw an error.  Don't use the following, as if the student has used `y(x)` then it will become `y(x)(x)`!

    ans1:'diff(y,x)+1 = 0;
    ansyx:ev(ans1,y=y(x));

Further work is needed to better support partial derivatives (input, display and evaluation).

### Displaying ODEs

Maxima has two notations to display ODEs.

If `derivabbrev:false` then`'diff(y,x)` is displayed in STACK as \( \frac{\mathrm{d}y}{\mathrm{d}x}\).   Note this differs from Maxima's normal notation of \( \frac{\mathrm{d}}{\mathrm{d}x}y\).

If `derivabbrev:true` then `'diff(y,x)` is displayed in STACK and Maxima as \( y_x \).

* Extra brackets are sometimes produced around the differential.
* You must have `simp:true` otherwise the display routines will not work.

## Next

- [Using and maniplulating differential equations in STACK](../../Topics/Differential_equations/Question_Variables.md)
 
## See also

[Maxima reference topics](index.md#reference) 
