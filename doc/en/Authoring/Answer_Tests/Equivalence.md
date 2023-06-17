## Equivalence answer tests ##

The prototype test is to establish if two expressions are _algebraically equivalent_.  This page documents this, and similar, equivalence tests.

A crucial component in the assessment process the ability to decide if two expressions are equivalent.  It turns out there are many useful senses when trying to assess students' answers.

Let us assume a teacher has asked a student to expand out \((x+1)^2\) and the response they have from one student is \(x^2+x+x+1\). This is "correct" in the sense that it is algebraically equivalent to \((x+1)^2\) and is in expanded form (actually two separate mathematical properties) but "incorrect" in the sense that the student has not _gathered like terms_ by performing an addition \(x+x\). What about a response \(2x+x^2+1\)?  This is, arguably, better in the sense that the terms are gathered, but the student here did not _order_ the terms to write their expression in canonical form. Hence, we need quite a number of different answer tests to establish equality in various senses of the word.

This list is in approximate order of the size of the equivalence classes from most to least restrictive.

| Test                                              | Description (see below for more details)
| ------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------
| CasEqual                                          | Are the [parse trees](../Trees.md) of the two expressions equal?  
| [EqualComAss](Rule_based.md)                      | Are they equal up to commutativity and associativity of addition and multiplication, together with their inverses minus and division? 
| [EqualComAssRules](Rule_based.md)                 | Are they equal up to commutativity, associativity and with optional rules such as \(0\times x \rightarrow 0\)? 
| AlgEquivNouns                                     | Are they _algebraically equivalent_, preserving noun forms of operators, e.g. `diff`?
| AlgEquiv                                          | Are they _algebraically equivalent_?
| SubstEquiv                                        | Can we find a substitution of the variables of \(ex_2\) into \(ex_1\) which renders \(ex_1\) algebraically equivalent to \(ex_2\)?
| SameType                                          | Are the two expressions of the same [types of object](../../CAS/Maxima.md#Types_of_object)?  Note that this test works recursively over the entire expression.

### AlgEquiv {#AlgEquiv}

This is the most commonly used test.  The pseudo code

    If
      simplify(ex1-ex2) = 0
    then
      true
    else
      false.

This test will work with a variety of [types of object](../../CAS/Maxima.md#Types_of_object) of mathematical objects, including lists, sets, equations, inequalities and matrices.

* This test disregards whether [simplification](../../CAS/Simplification.md) is switched on, it always fully simplifies all its arguments.
* Use `AlgEquiv(predicate(ex),true)` with [predicate functions](../../CAS/Predicate_functions.md).

Note: exactly what it does depends on what objects are given to it.  In particular the pseudo code above only applies to expressions.  We cannot subtract one list or set from another, so we have to use other tests.

For sets, the CAS tries to write the expression in a canonical form.  It then compares the string representations these forms to remove duplicate elements and compare sets.  This is subtly different from trying to simplify the difference of two expressions to zero.  For example, imagine we have \(\{(x-a)^{6000}\}\) and \(\{(a-x)^{6000}\}\).  One canonical form is to expand out both sides.  While this work in principal, in practice this is much too slow for assessment. 

Currently we do check multiplicity of roots, so that \( (x-2)^2=0\) and \( x=2\) are not considered to be equivalent.  Similarly \(a^3b^3=0\) is not \(a=0 \mbox{ or } b=0\).  This is a long-standing issue and we would need a separate test to ignore multiplicity of roots.

Currently, \(\{-\frac{\sqrt{2}}{\sqrt{3}}\}\) and \(\{-\frac{2}{\sqrt{6}}\}\) are considered to be different.  If you want these to be considered the same, you need to write them in a canonical form.   Instead of passing in just the sets, use the answer test to compare the following.

    ev(radcan({-sqrt(2)/sqrt(3)}),simp);
    ev(radcan({-2/sqrt(6)}),simp);

Why doesn't the test automatically apply `radcan`?  If we always did this, then \(\{(x-a)^{6000}\}\) and \(\{(a-x)^{6000}\}\) would be expanded out, which would break the system.  Since, in a given situation, we know a lot about what a student is likely to answer we can apply an appropriate form.   There isn't one rule which will work here, unfortunately.

There are also some cases which Maxima can't establish as being equivalent.  For example \[ \sqrt[3]{\sqrt{108}+10}-\sqrt[3]{\sqrt{108}-10} = 2.\]  As Maxima code

    (sqrt(108)+10)^(1/3)-(sqrt(108)-10)^(1/3)

This is Cardano's example from Ars Magna, but currently the AlgEquiv test cannot establish these are equivalent.  There are some other examples in the test suite which fail for mathematical reasons.  In cases like this, where you know you have a number, you may need to supplement the AlgEquiv test with another numerical test.

We recommend you do _not_ use algebraic equivalence testing for floating point numbers.  Instead use one of the [numerical tests](Numerical.md).  Examples of why algebraic equivalence fails when you might expect it to pass, e.g. `ATAlgEquiv(452,4.52*10^2)` (Maxima 5.44.0, November 2022), are given in the documentation on [numbers](../../CAS/Numbers.md).

### EqualComAss ###

A particularly useful test is to establish that two expressions are equal up to commutativity and associativity of addition and multiplication, together with their inverses minus and division.  For example, under this test
\( x+y = y+x \mbox{ but } x+x \neq 2x\).
Please see the [separate documentation](Rule_based.md).

### AlgEquivNouns ###

Algebraic equivalence evaluates as much as possible, to try to establish equivalence.  This means, e.g. that `diff(y,x)` is always evaluated to \(0\).  If you use AlgEquivNouns then noun forms of operators are not evaluated, so `diff(y,x)` will be evaluated but `'diff(y,x)` and `noundiff(y,x)` will not.

Even with this answer test `noundiff(y,x)` and `noundiff(y(x),x)` are different!

Note, that logic nouns such as `nounand` are still evaluated by this test!  Sorry, but logical noun functions are dealt with internally in a very different way than Maxima noun functions such as `'diff(y,x)` and the parallel `noundiff`.  Use a different test, such as `EqualComAss`.

It was the need to selectively evaluate some nouns but not others that led to the development of the [rule-based answer tests](Rule_based.md) to deal with the need for these options in a coherent way.

### CasEqual ###

The CAS returns the result of the simple Maxima command

    if StudentAnswer=TeacherAnswer then true else false.

There is no explicit simplification here (unlike AlgEquiv).
This test always assumes [simplification](../../CAS/Simplification.md) is off, i.e. `simp:false`, regardless of any question settings.  If this is too strict, use `ev(ex,simp)` in the arguments to simplify them explicitly first.
When simplification is off this test effectively tests whether the parse trees are identical. 

Please note, the behaviour of this test relies on the internal representation of expressions by Maxima, rather than an explicit mathematical property such as "equivalence".  Explicit properties should be tested in preference to using this test!

### SubstEquiv ###

Can we find a substitution of the variables of \(ex_2\) into \(ex_1\) which renders \(ex_1\) algebraically equivalent to \(ex_2\)?

* Because we have to test every possibility, the algorithm is factorial in the number of variables.  For this reason, the test only works for 4 or fewer variables.
* This test makes a substitution then uses AlgEquiv.
* If you add an answer test option (not required) in the form of a list of variables, these variables will be "fixed" during the comparison.  The list of variable is removed from both lists of the teacher's and student's variable lists before any comparison.
  * `ATSubstEquiv(x=A+B, x=a+b)` will match with `[A = a,B = b,x = x]`.
  * `ATSubstEquiv(x=A+B, x=a+b, [x])` will match with `[A = a,B = b]`.
  * `ATSubstEquiv(y=A+B, x=a+b, [x])` will not match since `x` in the teacher's answer is fixed here, but does not occur in the student's answer.

The optional argument, which must be a list of variables, is useful if you want to establish that a student has used arbitrary constants in \(A\sin(x)+B\cos(x)\) but make sure \(x\) really stays as \(x\).


### SysEquiv ###

The SysEquiv (system equivalence) test takes in two lists of polynomial equations in any number of variables and determines whether the two systems have the same set of solutions.
This is done using the theory of Grobner bases to determine whether the ideals generated by the two systems are equal.
As the test allows for polynomials in several variables, it can cope with the intersections of the conic sections, as well as a large number of geometrically interesting curves.

* This test does not check if the student actually "fully solved" the equations!  E.g. \[ [x^2=1] \equiv [(x-1)\cdot (x+1)=0] \] under this test.
* This test disregards whether [simplification](../../CAS/Simplification.md) is switched on, it only simplifies its arguments where required.
This allows the test to list equations in feedback that the student has erroneously included in their system.
* You can allow the student to include "redundant assignments".  For example, if you have `[90=v*t,90=(v+5)*(t-1/4)]` but the student has `[d=90,d=v*t,d=(v+5)*(t-1/4)])` then the systems are not equivalent, becuase the student has an extra variable.  Use `stack_eval_assignments` to eliminate explicit assignments of the form `var=num` and evaluate the other expression in this context.
