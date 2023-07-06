# Predicate functions

A predicate function takes an expression and returns Boolean values `true` or `false`.

The convention in [Maxima](Maxima.md) is to end predicate
functions with the letter "p". Many predicate functions exist
already within Maxima.  Some of the more useful to us are
listed below.   STACK defines an additional range of predicate
functions.  Some are described here, others are in the relevant specific sections of the documentation, such as [numbers](Numbers.md).

Since establishing mathematical properties are all about predicates they are particularly important for STACK.

You can use predicate functions directly in the [potential response tree](../Authoring/Potential_response_trees.md) by comparing the result with `true` using the
[answer test](../Authoring/Answer_Tests/index.md) AlgEquiv.

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

| Function                   | Predicate
| -------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `variablep(ex)`            | Determines if \(ex\) is avariable, that is an atom but not a real numberm, \(i\) or a string.
| `equationp(ex)`            | Determines if \(ex\) is an equation.
| `functionp(ex)`            | Determines if \(ex\) is a function definition, using the operator `:=`.
| `inequalityp(ex)`          | Determines if \(ex\) is an inequality.
| `expressionp(ex)`          | Determines if \(ex\) is _not_ a matrix, list, set, equation, function or inequality.
| `polynomialpsimp(ex)`      | Determines if \(ex\) is a polynomial in its own variables.
| `simp_numberp(ex)`         | Determines if \(ex\) is a number when `simp:false`.
| `simp_integerp(ex)`        | Determines if \(ex\) is an integer when `simp:false`.
| `real_numberp(ex)`         | Determines if \(ex\) is a real number.
| `rational_numberp(ex)`     | Determines if \(ex\) is written as a fraction.  For a true mathematical rational number use `rational_numberp(ex) or simp_integerp(ex)`
| `lowesttermsp(ex)`         | Determines if a fraction \(ex\) is in lowest terms.  
| `complex_exponentialp(ex)` | Determines if \(ex\) is written in complex exponential form, \(r e^{i\theta} \).  Needs `simp:false`.
| `imag_numberp(ex)`         | Determines if \(ex\) is a purely imaginary number.

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
| `factorp(ex) `            | true if \(ex\) equals its factored form.  Note, if you would like to know if an expression is factored you need to use the [FacForm](../Authoring/Answer_Tests/index.md#Form) answer test.  Prime integers equal their factored form, composite integers do not.
| `continuousp(ex,v,xp) `   | true if \(ex\) is continuous with respect to \(v\) at \(xp\) (unreliable).
| `diffp(ex,v,xp,[n]) `     | true if \(ex\) is (optionally \(n\) times) differentiable with respect to \(v\) at \(xp\) (unreliable).

The last two functions rely on Maxima's `limit` command and hence are not robust.

# Establishing form #

A lot of what teachers do is try to establish if a student's answer "looks right" that is, in an appropriate form.

`linear_term_p(ex, p)` establishes that the expression `ex` is a simple product of one expression for which the predicate `p` is true and zero or more real numbers.

`linear_combination_p(ex, p)` establishes that the expression `ex` is a linear combination of terms for which `p` is true.

The teacher can then use this function to build more complex predicates such as the following

    fouriertermp(ex) := if ((safe_op(ex)="cos" or safe_op(ex)="sin") and linear_term_p(first(args(ex)), variablep)) then true else false$

This predicate function decides if we have a term of the form \(\sin(n\, v)\) or \(\cos(n\, v)\) where \(n\) is any product of real numbers (e.g. \(3\pi/2\)) and \(v\) is any variable.  A teacher might prefer to specify a particular variable.

    fouriertermp(ex) := if ((safe_op(ex)="cos" or safe_op(ex)="sin") and linear_term_p(first(args(ex)), lambda([ex2], ex2=t))) then true else false$

So, if you want to decide if the student's answer looks like \( \sum_{k=1}{n} a_k\cos(k\pi t) + a_k\cos(k\pi t) \) the combined predicate `linear_combination_p(ex, fouriertermp)` can be used.

Testing for form in this way is probably more reliable that the `substequiv` answer test which fails to match up expressions like \(A\sin(t)+B\cos(t)\) with \(A\sin(t)-B\cos(t)\).  As every, the minus sign is a problem.  However, the following predicate will work.

    simpletrigp(ex) := if (ex=cos(t) or ex=sin(t)) then true else false$

and the test `linear_combination_p(ex, simpletrigp)` will be able to do this.


# Related functions #

This is not, strictly speaking, a predicate function.  It is common to want to ensure that a student's expression is free of things like \(\sqrt{2}\), \(a^{1/2}\) or \(1+\sqrt[3]{2}\) in the denominator.  This include any complex numbers.

`rationalized(ex)` searches across the whole expression `ex` and looks in the denominators of any fractions.  If the denominators are free of such things the function returns `true` otherwise the function returns the list of offending expressions.  This design allows efficient feedback of the form ``the denominator in your expression should be free of the following: ...".

## See also

[Maxima reference topics](index.md#reference.md)
