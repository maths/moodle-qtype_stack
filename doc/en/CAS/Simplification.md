# Simplification

The level of simplification performed by Maxima can be controlled by changing Maxima's global variable `simp`, e.g.

    simp:true

This variable can be set at the question level using the [options](../Authoring/Options.md) or for each [Potential response tree](../Authoring/Potential_response_trees.md).

When this is `false`, no simplification is performed and Maxima is quite happy to deal with an expression such as \(1+4\) without actually performing the addition.
This is most useful for dealing with very elementary expressions.

However, there are still some problems with the unary minus, e.g. \(4+(-3)\) which we would like to always display as \(4-3\).

If you are using `simp:false` to evaluate an expression with simplification on you can use

    ev(ex,simp)

Some example are given:

* Matrix examples in [showing working](Matrix.md#Showing_working).
* An example of a question with `simp:false` is discussed in [authoring quick start 3](../Authoring/Authoring_quick_start_3.md).

Note also that [question tests](../Authoring/Testing.md#Simplification) do not simplify test inputs.
