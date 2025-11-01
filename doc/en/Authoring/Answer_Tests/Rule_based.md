# Rule-based answer tests

Rule-based answer tests are a special kind of mathematical [answer test](index.md).

### Equality up to associativity and commutativity ### {#EqualComAss}

The `EqualComAss` test establishes that two expressions are equal up to commutativity and associativity of addition and multiplication, together with their inverses minus and division.

For example \[a+b=b+a\text{,}\] but \[x+x\neq 2x\text{.}\] This is very useful in elementary algebra, where we want the form of the answer exactly. This test seeks to establish whether two expressions are the same when the basic operations of arithmetic addition/multiplication and Boolean and/or are assumed to be nouns but are commutative and associative.  Hence, \(2x+y=y+2x\) but \(x+x+y\neq 2x+y\).  The unary minus commutes with multiplication in a way natural to establishing the required form of equivalence.

This is a particularly useful test for checking that an answer is written in a particular form, e.g. "simplified".

Notes

1. Simplification is automatically switched off when this test is applied, otherwise it makes no sense.
2. This test does not include laws of indices, so \(x\times x \neq x^2\). Since we are dealing only with nouns \(-\times -\) does not simplify to \(1\). E.g. \(-x\times -x \neq x\times x \neq x^2\).  This also means that \(\sqrt{x}\) is not considered to be equivalent to \(x^{\frac{1}{2}}\) under this test.  In many situations this notation is taken to mean the same thing, but internally in Maxima they are represented by different functions and are not converted to a canonical form by the test.
3. By design, addition commutes with subtraction, so \( -1+2\equiv 2-1\) and multiplication commutes with division, so \( (ab)/c\equiv a(b/c) \).
4. By design \(-1/4x \neq -x/4\) since we do not have the rule \( 1\times x \rightarrow x\).  To establish this equivalence we would need a different answer test.
5. By design \(0.75 \neq 1/4\).  If you would like to rationalise numbers within an expression `ex` you can pre-process it with `num_ensure_rational(ex)`.  This traverses the expression tree and makes sure all floating point numbers are converted.  This is different from applying Maxima's `rat` function at the top level which changes the structure of the expression.  Beware of converting binary floats into rational expressions though!
6. This test can also be used to establish \(\{4,4\} \neq \{4\}\), but \(\{1,2\} = \{2,1\}\) since the arguments of the set constructor function are commutative.  Sets are not associative, so \(\{1,2\} \neq \{\{1\},2\}\).  (See Maxima's `flatten` command.)


### Unary minus and division ###

In order to understand how the tests work it is essential to understand how we represent unary minus and division internally.

Without simplification, Maxima has a unary minus function:  literally `minus(x)`.  This is transformed into `UNARY_MINUS nounmul ex`  The use of multiplication here allows `-` to commute with other multiplication, so we can spot things like \(-x \times -y = --xy\) using associativity and commutativity.

Similarly, we replace division \(a/b\) with `a nounmul UNARY_RECIP(b)`.  This means that `UNARY_RECIP(b)` is not automatically the same as `1 nounmul UNARY_RECIP(b)`, without an additional rule.

### EqualComAssRules ###

This is an advanced test.

This test allows question authors to create equivalence classes based on equality up to associativity and commutativity with the addition of optional rules. For example, the teacher can include the identity operations of addition and multiplication: \(0+ x\rightarrow x\) and \(1\times x\rightarrow x\).  This makes it much easier to establish things like \(0-1\times i\) is equivalent to \(-i\).  However, more general integer arithmetic is still not automatically included so \(2\times 3 \neq 6\).

This test always assumes associativity of addition and multiplication.   By default the test assumes commutativity of addition and multiplication, but this can be dropped.  Essentially this test extends the `EqualComAss` test by adding in additional rules. Without assumptions of commutativity and associativity we would need all sorts of additional rules, such as \(x+0 \rightarrow x\), since without commutativity this would not be captured by the rule `zeroAdd`, i.e. \(0+x \rightarrow x\).  Furthermore, the way `EqualComAss` deals with unary minus and division make associativity and commutativity difficult to add in their pure form.

Each rule is a named function in Maxima, and each rule has an associated predicate function to decide if the rule is applicable at the top level of an expression.   E.g. `zeroAddp(0+x)` would return `true` and `zeroAdd(0+x)` would return `x`.

The teacher must supply an option consisting of a list of the following rule names.

| Name              | Rule                                                                                   |
|-------------------|----------------------------------------------------------------------------------------|
| (`ALG_TRANS`)     | _Included by default_                                                                 |
| `assAdd`          | Associativity of addition                                                              |
| `assMul`          | Associativity of multiplication                                                        |
| `comAdd`          | Commutativity of addition                                                              |
| `comMul`          | Commutativity of multiplication                                                        |
|  -                 | _Options to switch off the defaults_                                                  |
| `noncomAdd`       | Indicate addition is non-commutative                                                   |
| `noncomMul`       | Indicate multiplication is non-commutative                                             |
| `comMulNum`       | Commutativity of numbers (inc unary minus) only within multiplication                  |
| `comNeg`          | Commutativity of only unary minus within multiplication                                |
| (`ID_TRANS`)      |                                                                                        |
| `zeroAdd`         | \(0+x \rightarrow x\)                                                                  |
| `zeroMul`         | \(0\times x \rightarrow 0\)                                                            |
| `oneMul`          | \(1\times x \rightarrow x\)                                                            |
| `oneDiv`          | \(\frac{x}{1} \rightarrow x\)                                                          |
| `onePow`          | \(1^x \rightarrow 1\)                                                                  |
| `idPow`           | \(x^1 \rightarrow x\)                                                                  |
| `zeroPow`         | \(0^x \rightarrow 0\) if \(x \neq 0\)                                                  |
| `zPow`            | \(x^0 \rightarrow 1\)  if \(x \neq 0\)                                                 |
| (`NEG_TRANS`)     |                                                                                        |
| `negNeg`          | \(-(-x) \rightarrow x\)                                                                |
| `negDiv`          | \( y/(-x) \rightarrow -y/x \)  (Note, this assumes `UNARY_RECIP` and `UNARY_MINUS`)    |
| `negOrd`          | Order summands so that the leading coefficient is not negative (see notes below).      |
| (`DIV_TRANS`)     |                                                                                        |
| `recipMul`        | \( x/a\times y/b \rightarrow (x\,y)/(a\,b) \)                                          |
| `divDiv`          | \( a/(b/c) \rightarrow a\,c/b \)                                                       |
|                   | Note \( a/b/c \) is interpreted as  \( (a/b)/c=a/(b\,c) \) in Maxima.                  |
| `divCancel`       | Cancel common factors in numerator and denominator.                                    |
| (`INT_ARITH`)     |                                                                                        |
| `intAdd`          | Perform addition on integers                                                           |
| `intMul`          | Perform multiplication on integers                                                     |
| `intPow`          | Perform exponentiation when both arguments are integers                                |
|                    |                                                                                        |
| `ratAdd`          | Add any integer fractions in a sum                                                     | 
| `ratLow`          | Write a fraction \(a/b\) in lowest terms. \(a\) and \(b\) must be integers             | 
| Other             |                                                                                        |
| `intFac`          | Factor integers (incompatible with `intMul`)                                           |
| `negDist`         | Distribute only `UNARY_MINUS` over a sum (incompatible with `negOrd`)                  |
| `sqrtRem`         | Remove the `sqrt` function and replace with `^(1/2)`                                   |

Notes: 

* We do not guarantee the simplification is mathematically correct!  E.g. if you are unlucky enough to try the rule `zeroPow` on the expression `0^(1-1)` then since `1-1` is not equal to zero (taken literally) then the rule applies and you have failed to spot a potential `0^0` error.
* The rule `negOrd` deserves comment.  Ultimately we only compare parse trees exactly, and so we need to order terms in sums and products (commutativity). However \(y-x\) is never ordered as \(-x+y\).  Furthermore, \(-(x-y) \neq -x+y\).  We need to factor out the unary minus and ensure that the coefficient of the leading term is not negative. Factoring out is better than distributing here, since in a produce such as \(-(x-y)(x-z)\) it is not clear which term in the product the initial minus sign will end up in. Since `negOrd` is a factor command, it is incompatible with `negDist`.
* Note that `oneDiv` only operates on the very special case of a single \(1\) in the denominator of a fraction.  E.g. in \(\frac{x}{1\times a}\) we have a product \(1\times a\) in the denominator.  To further simplify this you need `oneMul` rather than `oneDiv`.

By default the test assumes commutativity of addition and multiplication.  If you choose the `nonmulCom` rule then you can switch off commutativity of multiplication.  However, rules such as `zeroMul` include both \(0\times x \rightarrow 0\) and \(x\times 0 \rightarrow 0\).  The rules `intMul` (etc) would appear to be non-compatible with `nonmulCom`, however they are very useful in that by performing integer arithmetic we bring integers to the front of the expression.

For convenience sets of rules can be specified.  E.g. you can use the name `ID_TRANS` in place of the list `[zeroAdd,zeroMul,oneMul,oneDiv,onePow,idPow,zeroPow,zPow]` to include all of the basic identity operators.

If you want to remove tests from a list you can use code such as `delete(zeroAdd, ID_TRANS)`.

The test takes the student's answer and teacher's answer and repeatedly applies the rules in turn until the expressions remain the same.  The rules are designed to always shorten the expression, so the process is guaranteed to terminate.  Once the expression is written in final form, the test compares the two expression trees.

If you add the rule `testdebug` then you will see both expressions in the answer note.  This is useful for debugging, but would clutter up things in a production setting.

## Examples of use ##

### Unique prime factorisation ###

Imagine we have asked students to find the prime decomposition of \(1617 = 3^1\cdot 7^2\cdot 11^1\).  This is the answer we are aiming at, but we also want to condone the answer
\(3\cdot 7^2\cdot 11\).   We can do this with the rule `[idPow]`.  We might also (being generous perhaps) want to also accept \( 2^0\cdot 3^1\cdot 5^0\cdot 7^2\cdot 11^1 \).  We can do this with the three rules `[oneMul,idPow,zPow]`.  You can try this code in the sandbox.

    ATEqualComAssRules(2^0*3^1*5^0*7^2*11^1, 3^1*7^2*11^1, [oneMul,idPow,zPow]);

Note, this test always assumes commutativity so you can't (currently) enforce the order of writing the prime factors.

### Fractions with one in the numerator ###

Imagine the teacher's answer is \(\frac{\sin(3x)}{2}\) but a student types in \(\frac{1}{2}\sin(3x)\).  In this case `ATEqualComAss` won't establish equivalence because the rule \(1\times\sin(3x)\rightarrow \sin(3x)\) is needed.  This can be done with the following.

    ATEqualComAssRules(1/2*sin(3*x), sin(3*x)/2, [oneMul]);


## Developer notes ##

This functionality was introduced in April 2021.  It is essential that the rules, and any combination of the rules, can only proceed in a single direction and that no infinite loops are created.  So, `intAdd` is fine because adding together two integers will make an expression _simpler_ which in this case is shorter.  For this reason we do not have expanding out (i.e. distribution) rules in the above set, and no rules of indices (which readily lead to mathematical errors).  Use Maxima's simplifier if you want to include such rules.

STACK creates parallel operators for `*`, `+` etc. so that we have full control over the simplifier and the rules in play.  E.g. `nounadd` instead of `+`.

You can use `equals_commute_prepare(ex)` to change an expression into this noun form.  An optional second argument `equals_commute_prepare(ex, sc)` is the set of operators considered commutative, e.g. typically a subset of `{"nouneq", "nounand", "nounor", "nounset", "nounadd", "nounmul"}`.

The simplifier rules assume `simp:false` and that the expression has noun forms.
The simplifier rule names are Maxima functions with exactly the names shown above.
Each rule has a predicate function, which decides if the rule can be applied _at the top level of the expression_.  E.g. `oneMulp(1*x)` is true, but `oneMulp(2+(1*x))` is false because the `1*x` is not at the top level of the expression `2+(1*x)`, it's deeper within the expression.

To deal with unary minus we transform it into multiplication with a special tag `UNARY_MINUS`. For example `-x` becomes `UNARY_MINUS * x`.  This approach looks odd at first, but does not confuse `UNARY_MINUS` with the integer \(-1\) or the unary function "minus".  In this way multiple unary minus operations commute to the front of an expression.  E.g. `(-x)*(-y) = UNARY_MINUS * UNARY_MINUS * x * y` (when `*` is assumed to be commutative and associative, of course!)

Similarly, division is also conveted to `UNARY_RECIP`.  E.g. `(-x)/(-y) = UNARY_MINUS nounmul UNARY_RECIP(UNARY_MINUS nounmul y) nounmul x`.

We the use the rule `negDiv` to pull out the unary minus outside the devision (pulls `UNARY_MINUS` outside `UNARY_RECIP`), but we also need the rules `assMul` (associativity) and `comMul` (commutativity).  E.g. try the following in the STACK-maxima sandbox.

    ex:(-x)/(-y);
    ex:equals_commute_prepare(ex);
    transl(ex,[assMul, comMul, negDiv]);

This results in `UNARY_MINUS nounmul UNARY_MINUS nounmul x nounmul UNARY_RECIP(y)`, literally `- * - * x * 1/y`.   We could also include the rule `negNeg` to remove the double minus.

    transl(ex,[assMul, comMul, negDiv, negNeg]);

gives `x nounmul UNARY_RECIP(y)`.

The goal of this code is to create reliable equivalence classes of expressions.  We are gradually expanding the use to allow full control over elementary expressions. Note in particular the use of `UNARY_MINUS` and `UNARY_RECIP` are likely to cause confusion to students if an expression is manipulated using these rules and then shown to a student.  The function `verb_arith` removes all the noun forms used by this simplifier, translating the expression back to core Maxima functions. Note however that `UNARY_MINUS` and `UNARY_RECIP(ex)` are normally replaced by `(-1)*` and `ex^(-1)` respectively.

The simplifier is designed to go in one direction only to establish membership of an equivalence class. We do not (as of Dec 2024) support displaying the resulting manipulated expressions in traditional form.

The code is all in `stack/maxima/noun_simp.mac`.

__We do not currently support user-defined rules (sorry!).__

# See also

* [Answer tests](index.md)
