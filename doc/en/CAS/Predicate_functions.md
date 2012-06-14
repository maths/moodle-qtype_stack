# Predicate functions

A predicate function takes an expression and returns Boolean values `true` or `false`.

The convention in [Maxima](Maxima.md) is to end predicate
functions with the letter "p". Many predicate functions exist
already within Maxima.  Some of the more useful to us are
listed below.   STACK defines an additional range of predicate
functions.  Some are described here, others are in the relevant specific sections of the documentation, such as [numbers](Numbers.md).

Since establishing mathematical properties are all about predicates they are particularly important for STACK.

You can use predicate functions directly in the [potential response tree](../Authoring/Potential_response_trees.md) by comparing the result with `true` using the
[answer test](../Authoring/Answer_tests.md) AlgEquiv.

# Maxima type predicate functions #

The following are a core part of Maxima, but there are many others.  Notice, predicate functions end in the letter "p".

| Function                | Predicate
| ----------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `floatnump(ex)`         | Determines if \(ex\) is a float.
| `numberp(ex)`           | Determines if \(ex\) is a number.  _NOTE_ `numberp` returns `false` if its argument is a symbol, even if the argument is a symbolic number such as \(\sqrt{2}\), \(\pi\) or \(i\), or declared to be even, odd, integer, rational, irrational, real, imaginary, or complex.   This function also does not work when `simp:false`, so see the dedicated page on [numbers](Numbers.md).
| `setp(ex)`              | Determines if \(ex\) is a set.
| `listp(ex)`             | Determines if \(ex\) is a list.
| `matrixp(ex)`           | Determines if \(ex\) is a matrix.
| `polynomialp(ex,[v])`   | Determines if \(ex\) is a polynomial in the list of variables v.

# STACK type predicate functions

The following type predicates are defined by STACK.

| Function                  | Predicate
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `equationp(ex)`           | Determines if \(ex\) is an equation.
| `inequalityp(ex)`         | Determines if \(ex\) is an inequality.
| `expressionp(ex)`         | Determines if \(ex\) is _not_ a matrix, list, set, equation or inequality.
| `polynomialsimpp(ex)`     | Determines if \(ex\) is a polynomial in its own variables.


# STACK general predicates #

The following are defined by STACK.

| Function              | Predicate
| --------------------- | ------------------------------------------------------------------------------------------------
| `element_listp(ex,l)` | `true` if `ex` is an element of the _list_ \(l\).  (Sets have `elementp`, but lists don't)
| `all_listp(p,l)`      | `true` if all elements of \(l\) satisfy the predicate \(p\).
| `any_listp(p,l)`      | `true` if any elements of \(l\) satisfy the predicate \(p\).
| `sublist(l,p)`        | Return a list containing only those elements of the list \(l\) for which the predicate p is true

(The last of these is core Maxima and is not, strictly speaking, a predicate function)

# STACK other predicate functions #

| Function                  | Predicate
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `expandp(ex)`             | true if \(ex\) equals its expanded form.
| `factorp(ex) `            | true if \(ex\) equals its factored form.  Note, if you would like to know if an expression is factored you need to use the [FacForm](../Authoring/Answer_tests.md#Form) answer test.  See the notes on this for more details.
| `continuousp(ex,v,xp) `   | true if \(ex\) is continuous with respect to \(v\) at \(xp\) (unreliable).
| `diffp(ex,v,xp,[n]) `     | true if \(ex\) is (optionally \(n\) times) differentiable with respect to \(v\) at \(xp\) (unreliable).

The last two functions rely on Maxima's `limit` command and hence are not robust.

## See also

[Maxima reference topics](index.md#reference.md).
