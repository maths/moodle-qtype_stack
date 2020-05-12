#  Propositional Logic

STACK loads the "logic" package from Maxima.

## Boolean functions

Maxima has Boolean operators `and`, `or`, and `not`.  These rely on the underlying LISP implementation and as a result the `simp:false` is ignored.  
To illustrate the problem, try the following.

    simp:false$
    true and true;
    x=1 or x=2;

The results respectively (of the second two) are

    true;
    false;

Note, there is no mechanism in Maxima to represent a list of assignments such as `x=1 or x=2`,
which would be a natural way to express the solution to a quadratic equation.

To solve this problem STACK has introduced `nounand` and `nounor` which are commutative and associative operators.

Students do *not* need to use `nounand` and `nounor` in answers.  
Any `and` and `or` operators occurring in their answers are always automatically converted into these noun forms.

Teachers *always* need to use `nounand` and `nounor` in CAS expressions when they want to write non-simplifying expressions.  
For example, when defining the "teacher's answer" they should use the noun forms as appropriate.  
Teachers often need to use Boolean logic, and so need to consciously separate the difference between these operators and concepts.

Note, the answer tests do *not* convert noun forms to the Maxima forms.  
Otherwise both `x=1 or x=2` and `x=1 or x=3` would be evaluated to `false` and a teacher could not tell that they are different!  
To replace all `nounand` (etc) operators and replace them with the Maxima equivalent, use `noun_logic_remove(ex)`.

## Operators and notes

1. `and` This is a lisp function.  Teachers should use `nounand` to prevent evaluation of `x=1 and x=0` to `false` even without simplification.  Students type `and` and this is always converted internally to `nounand`.
2. `or` This is a lisp function.  Teachers should use `nounor` to prevent evaluation of `x=1 or x=0` to `false` even without simplification.  Students type `or` and this is always converted internally to `nounor`.
3. `not` This is a lisp function.  Teachers should use `nounnot` to prevent evaluation.  Students type `not` and this is always converted internally to `nounnot`.
4. `nand` is provided by the logic package, which respects the value of `simp`.
5. `nor` is provided by the logic package, which respects the value of `simp`.
6. `xor` is provided by the logic package, which respects the value of `simp`.
7. `eq` is provided by the logic package, but this input syntax is not supported in STACK.  Instead we provide an `xnor` function.
8. `implies` is provided by the logic package, which respects the value of `simp`.

Notes

* There is no support for symbolic logic symbol input currently and students cannot type `&`, `*` for `and`, and similarly `+`  students cannot type for `or`.
* There is no existential operator (not that this is propositional logic, but for the record) or an interpretation of '?' as there exits, and there is no universal operator (which some people type in as `!`).

The function `verb_logic(ex)` will remove the noun forms such as `nounand` and subsitute in the lisp versions, this will enable evaluation of expressions.  The function `noun_logic(ex)` will replace any remaining lisp but beware that any evaluation (even with `simp:false`) will evaluate lisp logical expressions.  It is best to use noun forms at the outset, e.g. in the question variables, and only use the lisp forms when calculating, e.g. to evaluate in the PRT.

## Answer tests

The answer tests protect the logical operators.  This behaviour is to prevent evaluation of expressions such as `x=1` as a boolean predicate fuction.  I.e. the default behaviour is to give priority to the assumption an arbitary expression is an algebraic expression, or a set of equations, inequalities etc.  The other answer tests (e.g. algebraic equivalence) will do there best.

The answer test `PropLogic` replaces all noun logical expressions with the Maxima versions, and then tests two expressions using the function `logic_equiv` from Maxima's logic package.  This answer test does not support sets, lists, etc.

The value of the student's answer will always have `nounand` etc. inserted.  Before you manipulate the student's answer, e.g. with the logic package functions, you will need to apply `noun_logic_remove(ex)`.

## Truth tables

STACK provides various functions for creating and dealing with [tables](../Authoring/Tables.md).

`truth_table(ex)` returns the true table of the expression `ex`.  The function will throw an error if the number of variables exceeds 5.  The first row of the table is the headings, consisting of a list of variables, and the expression itself.  See the documentation on [tables](../Authoring/Tables.md) for more functionality.
