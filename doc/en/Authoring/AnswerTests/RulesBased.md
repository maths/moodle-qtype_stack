# Rules-based answer tests

Rules-based answer tests are a special kind of mathematical [answer test](index.md).

### Equality up to associativity and commutativity ### {#EqualComAss}

The `EqualComAss` test establishes that two expressions are equal up to commutativity and associativity of addition and multiplication, together with their inverses minus and division.

For example \[a+b=b+a\mbox{,}\] but \[x+x\neq 2x\mbox{.}\] This is very useful in elementary algebra, where we want the form of the answer exactly. This test seeks to establish whether two expressions are the same when the basic operations of arithmetic addition/multiplication and Boolean and/or are assumed to be nouns but are commutative and associative.  Hence, \(2x+y=y+2x\) but \(x+x+y\neq 2x+y\).  The unary minus commutes with multiplication in a way natural to establishing the required form of equivalence.

This is a particularly useful test for checking that an answer is written in a particular form, e.g. "simplified".

Notes 

1. This test does not include laws of indices, so \(x\times x \neq x^2\). Since we are dealing only with nouns \(-\times -\) does not simplify to \(1\). E.g. \(-x\times -x \neq x\times x \neq x^2\).  This also means that \(\sqrt{x}\) is not considered to be equivalent to \(x^{\frac{1}{2}}\) under this test.  In many situations this notation is taken to mean the same thing, but internally in Maxima they are represented by different functions and are not converted to a canonical form by the test.
2. By design, addition commutes with subtraction, so \( -1+2\equiv 2-1\) and multiplication commutes with division, so \( (ab)/c\equiv a(b/c) \).
3. By design \(-1/4x \neq -x/4\) since we do not have the rule \( 1\times x \rightarrow x\).  To establish this equivalence we would need a different answer test.
4. This test can also be used to establish \(\{4,4\} \neq \{4\}\), but \(\{1,2\} = \{2,1\}\) since the arguments of the set constructor function are commutative.  Sets are not associative, so \(\{1,2\} \neq \{\{1\},2\}\).  (See Maxima's `flatten` command.)
5. Simplification is automatically switched off when this test is applied, otherwise it makes no sense. 


### Unary minus and division ###

In order to understand how the tests work it is essential to understand how we represent unary minus and division internally.

Without simplification, Maxima has a unary minus function.  Litterally `minus(x)`.  This is transformed into `UNARY_MINUS nounmul ex`  The use of multiplication here allows `-` to commute with other multiplication, so we can spot things like \(-x \times -y = --xy\) using associativity and commutativity.

Similarly, we replace division \(a/b\) with `a nounmul UNARY_RECIP(b)`.  This means that `UNARY_RECIP(b)` is not automatically the same as `1 nounmul UNARY_RECIP(b)`, without an additional rule.

### EqualComAssRules ###

This is an advanced test.

This test allows question authors to create equivalence classes based on equality up to associativity and commutativity with the addition of optional rules. For example, the teacher can include the identity operations of addition and multiplication: \(0+ x\rightarrow x\) and \(1\times x\rightarrow x\).  This makes it much easier to establish things like \(0-1\times i\) is equivalent to \(-i\).  However, more general integer arithmatic is still not automatically included so \(2\times 3 \neq 6\).

This test always assumes associativity and commutativity of addition and multiplication.  Essentially this test extends the `EqualComAss` test by adding in additional rules. Without assumptions of commutativity and associativity we would need all sorts of additional rules, such as \(x+0 \rightarrow x\), since without commutativity this would not be captured by the rule `zeroAdd`, i.e. \(0+x \rightarrow x\).  Furthermore, the way `EqualComAss` deals with unary minus and division make associativity and commutativity difficult to add in their pure form.

Each rule is a named function in Maxima, and each rule has an associated predicate function to decide if the rule is applicable at the top level of an expression.   E.g. `zeroAddp(0+x)` would return `true` and `zeroAdd(0+x)` would return `x`.

The teacher must supply an option consisting of a list of the following rule names.

| Name              | Rule                                                                                   |
|-------------------|----------------------------------------------------------------------------------------|
| (`ALG_TRANS`)     | _Always included_                                                                      |
| `assAdd`          | Associativity of addition                                                              |
| `assMul`          | Associativity of multiplication                                                        |
| `comAdd`          | Commutativity of addition                                                              |
| `comMul`          | Commutativity of multiplication                                                        |
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
| Other             |                                                                                        |
| `intFac`          | Factor integers (incompatible with `intMul`)                                           |
| `negDist`         | Distribute only `UNARY_MINUS` over a sum (incompatible with `negOrd`)                  |
| `sqrtRem`         | Remove the `sqrt` function and replace with `^(1/2)`                                   |

The rule `negOrd` deserves comment.  Ultimately we only compare parse trees exactly, and so we need to order terms in sums and products (commutativity).
However \(y-x\) is never ordered as \(-x+y\).  Furthermore, \(-(x-y) \neq -x+y\).  We need to factor out the unary minus and ensure that the coefficient of the leading term is not negative.
Factoring out is better than distributing here, since in a produce such as \(-(x-y)(x-z)\) it is not clear which term in the product the inital minus sign will end up in.
Since `negOrd` is a factor command, it is incompatible with `negDist`.

For convenience sets of rules can be specificed.  E.g. you can use the name `ID_TRANS` in place of the list `[zeroAdd,zeroMul,oneMul,oneDiv,onePow,idPow,zeroPow,zPow]` to include all of the basic identity operators.

If you want to remove tests from a list you can use code such as `delete(zeroAdd, ID_TRANS)`.

The test takes the student's answer and teacher's answer and repeatedly applies the rules in turn until the expressions remain the same.  The rules are designed to always shorten the expression, so the process is guranteed to terminate.  Once the expression is written in final form, the test compares the two expression trees.

Note that we do not gurantee the simplification is mathematically correct!  E.g. if you are unlucky enough to try the rule `zeroPow` on the expression `0^(1-1)` then since `1-1` is not equal to zero (taken literally) then the rule applies and you have failed to spot a potential `0^0` error.

If you add the rule `testdebug` then you will see both expressions in the answer note.  This is useful for debugging, but would clutter up things in a production setting.

## Examples of use ##

### Unique prime factorisation ###

Imagine we have asked students to find the prime decomposition of \(1617 = 3^1\cdot 7^2\cdot 11^1\).  This is the answer we are aiming at, but we also want to condone the answer
\(3\cdot 7^2\cdot 11\).   We can do this with the rule `[idPow]`.  We might also (being generous perhaps) want to also accept \( 2^0\cdot 3^1\cdot 5^0\cdot 7^2\cdot 11^1 \).  We can do this with the three rules `[oneMul,idPow,zPow]`.  You can try this code in the sandbox.

    ATEqualComAssRules(2^0*3^1*5^0*7^2*11^1, 3^1*7^2*11^1, [oneMul,idPow,zPow]);

Note, this test always assumes commutativity so you can't (currently) enforce the order of writing the prime factors.

## Developer notes ##

This functionality was introduced in April 2021.  It is essential that the rules, and any combination of the rules, can only proceed in a single direction and that no infinite loops are created.  So, `intAdd` is fine because adding together two integers will make an expression _simpler_ which in this case is shorter.  For this reason we do not have expanding out (i.e. distribution) rules in the above set, and no rules of indices (which readily lead to mathemtical errors).  Use Maxima's simplifier if you want to include such rules.

The rules names are Maxima functions, but they assume `simp:false` and that the expression has noun forms e.g. `nounadd` instead of `+`.  You can use `equals_commute_prepare(ex)` to change an expression into this noun form.  The goal of this code is to create reliable equivalence classes of expressions, not perform algebraic manipulation as we traditionally know it. In particular the way unary minus is transformed into multiplication with a special tag `UNARY_MINUS` is likely to cause confusion to students if an expression is manipulated using these rules and then shown to a student.  The transoformation is designed to go in one direction only, and we do not support displaying the resulting manipulated expressions in traditional form.

__As of May 2023, these rules are not intended as an end-user simplifier and we do not currently support user-defined rules (sorry!).__

# See also

* [Answer tests](index.md)
