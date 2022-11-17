# Answer tests

An _answer test_ is used to compare two expressions to establish whether they satisfy some mathematical criteria. The
prototype test is to establish if they are _algebraically equivalent_.  Answer tests are grouped as follows:

1. Equivalence, e.g. "algebraic equivalence" (many variations).
2. Syntactic form, e.g. "is in factored form".
3. [Numerical accuracy](Answer_tests_numerical.md), e.g. "is written to 3 decimal places".
4. [Scientific](../Authoring/Units.md), e.g. for dealing with dimensional numerical quantities.
5. Specific subject tests, e.g. sets, logical expressions, calculus (where tests provide feedback automatically in common situations such as a missing constant of integration).

We also provide (string match tests)[Strings.md].

## Introduction ##

Informally, the answer tests have the following syntax

    [Errors, Result, FeedBack, Note] = AnswerTest(StudentAnswer, TeacherAnswer, [Opt], [Raw])

Where,

| Variable        | Description
| --------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------
| StudentAnswer   | A CAS expression, assumed to be the student's answer.
| TeacherAnswer   | A CAS expression, assumed to be the model answer.
| Opt             | If needed, any options which the specific answer test provides. For example, a variable, the accuracy of the numerical comparison, number of significant figures.
| Raw             | If needed, the raw string of the student's input to ensure, e.g. Maxima does not remove trailing zeros when establishing the number of significant figures.

Note that since the tests can provide feedback, tests which appear to be symmetrical, e.g. Algebraic Equivalence, really need to assume which expression belongs to the student and which to the teacher.

| Variable  | Description
| --------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Errors    | Hopefully this will be empty!
| Result    | is either `true`, `false`, or `fail` (which indicates a failure of the test).  This determines which branch of the tree is traversed.
| FeedBack  | This is a text string which is displayed to the student. It is [CASText](CASText.md) which may depend on properties of the student's answer.
| Note      | This is a text string which is used for [Reporting](Reporting.md). Each answer note is concatenated with the previous notes and any contributions from the branch.

The feedback is only shown to a student if the quiet option is set to 'no'.  If feedback is shown, then examples are given in the answer-test test suite.

We expose the exact behaviour of each answer test by giving registered users access to STACK's test suite for STACK Answer tests.  This can be found on a live server at `.../moodle/question/type/stack/answertests.php`. This script compares pairs of expressions and displays the outcomes from each test. This script is mainly used to ensure STACK is working, but it is invaluable for understanding what each test really does.  In particular it enables question authors to see examples of which expressions are the same and different together with examples of the automatically generated feedback.
We provide a static page giving the outcome of all [answer test results](Answer_tests_results.md).


## In general ##

You can apply functions before applying the tests using the feedback variables.  For example, to ignore case sensitivity you can apply the [Maxima commands defined by STACK](../CAS/Maxima.md#Maxima_commands_defined_by_STACK) `exdowncase(ex)` to the arguments, before you apply one of the other answer tests.
However, some tests really require the raw student's input.  E.g. the numerical decimal place test really requires the name of an input as the `SAns` field.  If you manipulate an input, you may end up dropping trailing zeros and ruining the number of decimal places in the expression.  STACK will warn you if you need to use the name of an input.

## Equivalence ##

A crucial component in the assessment process the ability to decide if two expressions are equivalent.  It turns out there are many useful senses when trying to assess students' answers.

Let us assume a teacher has asked a student to expand out \((x+1)^2\) and the response they have from one student is \(x^2+x+x+1\).
This is "correct" in the sense that it is algebraically equivalent to \((x+1)^2\) and is in expanded form
(actually two separate mathematical properties) but "incorrect" in the sense that the student has not _gathered like terms_ by performing an addition \(x+x\).
What about a response \(2x+x^2+1\)?  This is, arguably, better in the sense that the terms are gathered,
but the student here did not _order_ the terms to write their expression in canonical form.
Hence, we need quite a number of different answer tests to establish equality in various senses of the word.

This list is in approximate order of the size of the equivalence classes from most to least restrictive.

| Test                                              | Description (see below for more details)
| ------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------
| CasEqual                                          | Are the parse trees of the two expressions equal?  
| [EqualComAss](Answer_tests_rules_based.md)        | Are they equal up to commutativity and associativity of addition and multiplication, together with their inverses minus and division? 
| [EqualComAssRules](Answer_tests_rules_based.md)   | Are they equal up to commutativity, associativity and with optional rules? 
| AlgEquivNouns                                     | Are they _algebraically equivalent_, preserving noun forms of operators, e.g. `diff`?
| [AlgEquiv](Answer_tests.md#AlgEquiv)              | Are they _algebraically equivalent_?
| SubstEquiv                                        | Can we find a substitution of the variables of \(ex_2\) into \(ex_1\) which renders \(ex_1\) algebraically equivalent to \(ex_2\)?
| SameType                                          | Are the two expressions of the same [types of object](../CAS/Maxima.md#Types_of_object)?  Note that this test works recursively over the entire expression.

### AlgEquiv {#AlgEquiv}

This is the most commonly used test.  The pseudo code

    If
      simplify(ex1-ex2) = 0
    then
      true
    else
      false.

This test will work with a variety of [types of object](../CAS/Maxima.md#Types_of_object) of mathematical objects, including lists, sets, equations, inequalities and matrices.

* This test disregards whether [simplification](../CAS/Simplification.md) is switched on, it always fully simplifies all its arguments.
* Use `AlgEquiv(predicate(ex),true)` with [predicate functions](../CAS/Predicate_functions.md).

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

We recommend you do _not_ use algebraic equivalence testing for floating point numbers.  Instead use one of the [numerical tests](Answer_tests_numerical.md).  Examples of why algebraic equivalence fails when you might expect it to pass, e.g. `ATAlgEquiv(452,4.52*10^2)` (Maxima 5.44.0, November 2022), are given in the documentation on [numbers](../CAS/Numbers.md).

### EqualComAss ###

A particularly useful test is to establish that two expressions are equal up to commutativity and associativity of addition and multiplication, together with their inverses minus and division.  For example, under this test
\( x+y = y+x \mbox{ but } x+x \neq 2x\).
Please see the [separate documentation](Answer_tests_rules_based.md).

### AlgEquivNouns ###

Algebraic equivalence evaluates as much as possible, to try to establish equivalence.  This means, e.g. that `diff(y,x)` is always evaluated to \(0\).  If you use AlgEquivNouns then noun forms of operators are not evaluated, so `diff(y,x)` will be evaluated but `'diff(y,x)` and `noundiff(y,x)` will not.

Even with this answer test `noundiff(y,x)` and `noundiff(y(x),x)` are different!

Note, that logic nouns such as `nounand` are still evaluated by this test!  Sorry, but logical noun functions are dealt with internally in a very different way than Maxima noun functions such as `'diff(y,x)` and the parallel `noundiff`.  Use a different test, such as `EqualComAss`.

It was the need to selectively evaluate some nouns but not others that led to the development of the [rule-based answer tests](Answer_tests_rules_based.md) to deal with the need for these options in a coherent way.

### CasEqual ###

The CAS returns the result of the simple Maxima command

    if StudentAnswer=TeacherAnswer then true else false.

There is no explicit simplification here (unlike AlgEquiv).
This test always assumes [simplification](../CAS/Simplification.md) is off, i.e. `simp:false`, regardless of any question settings.  If this is too strict, use `ev(ex,simp)` in the arguments to simplify them explicitly first.
When simplification is off this test effectively tests whether the parse trees are identical. 

Please note, the behaviour of this test relies on the internal representation of expressions by Maxima, rather than an explicit mathematical property such as "equivalence".  Explicit properties should be tested in preference to using this test!

### SubstEquiv ###

Can we find a substitution of the variables of \(ex_2\) into \(ex_1\) which renders \(ex_1\) algebraically equivalent to \(ex_2\)?

* Because we have to test every possibility, the algorithm is factorial in the number of variables.  For this reason, the test only works for 4 or fewer variables.
* This test makes a substitution then uses AlgEquiv.
* If you add an answer test option (not required) in the form of a list of variables, these variables will be "fixed" during the comparison.  E.g.
  * `ATSubstEquiv(x=A+B, x=a+b)` will match with `[A = a,B = b,x = x]`.
  * `ATSubstEquiv(x=A+B, x=a+b, [x])` will match with `[A = a,B = b]`.
  * `ATSubstEquiv(y=A+B, x=a+b, [x])` will not match since `x` in the teacher's answer is fixed here.

The optional argument, which must be a list of variables, is useful if you want to establish that a student has used arbitrary constants in \(A\sin(x)+B\cos(x)\) but make sure \(x\) really stays as \(x\).


### SysEquiv ###

The SysEquiv (system equivalence) test takes in two lists of polynomial equations in any number of variables and determines whether the two systems have the same set of solutions.
This is done using the theory of Grobner bases to determine whether the ideals generated by the two systems are equal.
As the test allows for polynomials in several variables, it can cope with the intersections of the conic sections, as well as a large number of geometrically interesting curves.

* This test does not check if the student actually "fully solved" the equations!  E.g. \[ [x^2=1] \equiv [(x-1)\cdot (x+1)=0] \] under this test.
* This test disregards whether [simplification](../CAS/Simplification.md) is switched on, it only simplifies its arguments where required.
This allows the test to list equations in feedback that the student has erroneously included in their system.
* You can allow the student to include "redundant assignments".  For example, if you have `[90=v*t,90=(v+5)*(t-1/4)]` but the student has `[d=90,d=v*t,d=(v+5)*(t-1/4)])` then the systems are not equivalent, becuase the student has an extra variable.  Use `stack_eval_assignments` to eliminate explicit assignments of the form `var=num` and evaluate the other expression in this context.

### Sets ###

This test deals with equality of sets.  The algebraic equivalence functions give very minimal feedback.  This test is designed to give much more detailed feedback on what is and _is not_ included in the student's answer.  Hence, this essentially tells the student what is missing.  This is kind of feedback is tedious to generate without this test.

The test simplifies both sets, and does a comparison based on the simplified versions.  The comparison relies on `ev(..., simp, nouns)` to undertake the simplification.  If you need stronger simplification (e.g. trig) then you will need to add this to the arguments of the function first.

### Equiv and EquivFirstLast ###

These answer tests are used with [equivalence reasoning](../CAS/Equivalence_reasoning.md).  See the separate documentation.

# Form {#Form}

Often, we wish to establish if the student's expression has the correct _form_.
For example, consider the following various written forms of \(x^2-4x+4\).

\[(x-2)(x-2),\quad (x-2)^2,\quad  (2-x)^2,\quad  4\left(1-\frac{x}{2}\right)^2.\]

Each of these might be considered to be factored.  **Establishing `ex` is factored is not the same as comparing it with** `factor(ex)`.

Related tests establish that an expression is _expanded_ or in _partial_ fraction form.

| Expression        | Description
| ----------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| LowestTerms       | This test checks that all numbers written in the first expression are in lowest terms and that the denominator is clear of surds and complex numbers.  Notes
|                   |     * if you want to check whether a rational polynomial is written in lowest terms, this is not the test to use.  Instead, apply the [predicate functions](../CAS/Predicate_functions.md)  `lowesttermsp` to the expression.
|                   |     * the second argument to this function is ignored, i.e. this test does not confirm algebraic equivalence.  You might as well use 0 here.
| SingleFrac        | This test checks that SAns written as a "single fraction". See below.
| Expanded          | Confirms SAns is equal to `expand(SAns)`.  Note, the second argument to this answer test is not used (but must be non-empty).  Note with this test that an expression such as \(x^2-(a+b)x+ab\) is not considered to be expanded, and this test will return false.
| FacForm           | This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is "factored" over the rational numbers. See below for more details.  The answer test expects the option to be the variable, which is needed to generate feedback. If the answer is incorrect, quite detailed feedback is provided.
| PartFrac          | This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is in "partial fraction form". The option must be the variable.
| CompletedSquare   | This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is in "completed square form". The option must be the variable.

### Single fractions ### {#SingleFrac}

This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is written as a single fraction. Notes

* This test works at the top level, making sure the expression as a whole is a single fraction.
* if you also want this expression written in lowest terms, then this is quite a separate test.  You need to first confirm you have a single fraction then add a new potential response. One way is to use the [../CAS/Predicate functions](../CAS/Predicate_functions.md) `lowesttermsp(ex)` and compare the result with `true` with the AlgEquiv test.
* The algebraic equivalence check is for convenience.  If you only want to check an expression is a single fraction make \(SAns=TAns\), i.e. ATSingleFrac(ex,ex) will do.

### Factorisation of polynomials ### {#FacPoly} 

An expression is said to be factored if it is written as a product of powers of distinct irreducible factors.
Strictly speaking, in establishing that an expression is in factored form, we might not even care whether the terms in the product are fully simplified, as long as they are irreducible.

Irreducibility on the other hand means we can't find further factors, but here we need some care.

Consider \(x^8+16x^4+48\).

1. Any non-trivial factorization, e.g. \((x^4+4)(x^4+12)\).
2. A factorization into irreducible factors over the integers/rational numbers, i.e. \((x^2+2x+x)(x^2-2x+2)(x^4+12)\).
3. A factorization into terms irreducible over the reals, i.e. \((x^2+2x+x)(x^2-2x+2)(x^2+2\sqrt[4]{3}x+2\sqrt[4]{3})(x^2-2\sqrt[4]{3}x+2\sqrt[4]{3})\).
4. A factorization into irreducible polynomials over the Gaussian integers, with \(i\) allowed, i.e. \((x+1+i)(x+1-i)(x-1+i)(x-1-i)(x^4+12)\).
5. A factorization over the complex numbers, where the factor \((x^4+12)\) would also be split into the four terms \(x\pm\sqrt[4]{3}(1\pm i)\).

In elementary teaching, meaning 4. is unlikely to occur. Indeed, we might take this example to represent factoring over any extension field of the rational numbers.  We normally seek to establish that the factors are irreducible over the integers (which is equivalent to irreducibility over the rational numbers) or the reals.  But, unlike a canonical form, we are not particularly interested in the order of the terms in this product, or the order of summands inside these terms.

The FacForm test establishes that the expression is factored over the rational numbers.  If the coefficients of the polynomial are all real, at worst you will have quadratic irreducible terms.  There are some delicate cases such as: \((2-x)(3-x)\) vs  \((x-2)(x-3)\)  and \((1-x)^2\) vs \((x-1)^2\), which this test will cope with.

It is also possible a student will do something which is just plain odd, e.g. \(x^2-4x+4\) can be rewritten as \(x(x-4+4/x)\) which is a "product of powers of distinct irreducible factors" but not acceptable to most teachers.  The student's answer must also be a polynomial in the variable (using `polynomialp` as the test predicate).

### Factorisation of integers ###

If you would like to ask a student to factor a polynomial, then do not use the FacForm answer test.  The FacForm answer test is designed to use with polynomials.

Instead, switch off simplification and define

    ta:factor(12);

and use EqualComAss as the answer test.

Note however that EqualComAss does not think that `2^2*3` and `2*2*3` are the same!

### Partial fractions ###

To help provide feedback to students on how to calculate the partial fraction form we have special function `poly_about_a(ex, v, a)` which writes the polynomial `ex` in variable `v` about the point `v=a`.  E.g. \(x^2=1-2(x-1)+(x-1)^2\) when written about \(x=1\). This is basically the complete finite Taylor series for the polynomial about \(x=1\).  The form "about \(x=a\)" can readily be calculated by "shift-expand-shift" and without derivatives.  It is, in my view, somewhat neglected. See [doi:10.1017/S0025557200003569](https://www.cambridge.org/core/journals/mathematical-gazette/article/abs/limitfree-derivatives/3410B7A9E318FAAD27C2948EED073DCF) for applications to a limit-free derivative for polynomials.

Here, e.g. if we have the question variables

    n1:4;
    p1:(9*y-8)/(y-n1)^2
    p2:poly_about_a(num(p1), y, n1);
    p3:map(lambda([ex], ex/denom(p1)), p2);

then feedback of the following form

    You need to do more work on the term {@p1@}.
    In particular, write the numerator {@num(p1)@} about the point {@y=n1@}.
    This gives {@num(p1)=p2@}, which allows us to complete the partial fraction form as follows:
    \[ {@p1@} = \frac{ {@p2@} }{ {@denom(p1)@} } = {@p3@} = {@ev(p3,simp)@}. \]

is rendered as:

You need to do more work on the term \({\frac{9 y-8}{{\left(y-4\right)}^2}}\).
In particular, write the numerator \({9 y-8}\) about the point \({y=4}\).
This gives \({9 y-8=28+9 \left(y-4\right)}\), which allows us to complete the partial fraction form as follows:
\[ {\frac{9 y-8}{{\left(y-4\right)}^2}} = \frac{ {28+9 \left(y-4\right)} }{ {{\left(y-4\right)}^2} } = {\frac{28}{{\left(y-4\right)}^2}+\frac{9 \left(y-4\right)}{{\left(y-4\right)}^2}} = {\frac{9}{y-4}+\frac{28}{{\left(y-4\right)}^2}}. \]

## Numerical Precision {#Precision}

These tests deal with the precision of numbers.  See dedicated page on [numerical answer tests](Answer_tests_numerical.md).

## Scientific units

A dedicated answer test for scientific units is described on the [units](../Authoring/Units.md) page.

## Tests for specific subject areas

### PropLogic ###

An answer test designed to deal with [propositional logic](../CAS/Propositional_Logic.md).  See the separate documentation.

### Calculus: Diff ###

This test is a general differentiation test: it is passed if the arguments are algebraically equivalent, but gives feedback if it looks like the student has integrated instead of differentiated. The first argument is the student's answer. The second argument is the model answer. The answer test option must be the variable with respect to which differentiation is assumed to take place.

There are edge cases, particularly with \(e^x\) where differentiation is indistinguishable from integration.  You may need to use the "quiet" option in these cases.

### Calculus: Int ### {#Int}

This test is designed for a general indefinite integration question: it is passed if both the arguments are indefinite integrals of the same expression. The first argument is the student's answer.
The second argument is the model answer. The answer test option needs to be the variable with respect to which integration is assumed to take place, or a list (see below).

Getting this test to work in a general setting is a very difficult challenge.
In particular, the test assumes that the constant of integration is expressed in a form similar to +c, although which variable used is not important.
This test, in particular, has a lot of test cases which really document what the test does in detail.  E.g. what mark would you give a student who integrated \( \int \frac{1}{x} dx = \log(k\times abs(x))\)?  The test cases document what design decisions we have made.

The issue of \( \int \frac{1}{x} dx = \log(x)+c\) vs  \( \int \frac{1}{x} dx 
= \log(|x|)+c\) is a particular challenge.  The test is currently defined in 
such a way that if the teacher uses \( \log(|x|)+c \) in their answer, then 
they would expect the student to do so.  If they don't use the absolute value 
function, then they don't expect students to but will accept this in an 
answer.   It is, after all, not "wrong".  However, in the case of partial 
fractions where there are more than one term of the form \(\log(x-a)\) then 
we insist the student is at least consistent.  If the teacher has *any* 
\(\log(|x-a|)\) then the student must use \(|...|\) in *all* of them.  If the 
teacher has no \(\log(|x-a|)\) (i.e. just things like \(\log(x-a)\)) then the 
student must have all or none. 

The Int test has various additional options.

The question author must supply these options in the form of a list `[var, opt1, ...]`.  The first argument of this list must be the variable with respect to which integration is taking place.  

If one of the `opt?` is exactly the token `NOCONST` then the test will condone a lack of constant of integration.  That is, if a student has missed off a constant of integration, or the answers differ by a numerical constant, then full marks will be awarded.  Weird constants (e.g. \(+c^2\)) will still be flagged up.

The answer test architecture only passes in the *answer* to the test.  The question is not available at that point; however, the answer test has to infer exactly which expression, including the algebraic form, the teacher has set in the question. This includes stripping off constants of integration and constants of integration may occur in a number of ways, e.g. in logarithms.
In many cases simply differentiating the teacher's answer is fine, in which case the question author need not worry.  Where this does not work, the question author will need to supply the expression from the question in the right form as an option to the answer test.  This is done simply by adding it to the list of options.

    [x, x*exp(5*x+7)]

The test cannot cope with some situations.  Please contact the developers when you find some of these.  This test is already rather overloaded, so please don't expect every request to be accommodated! 

## See also

* [Maxima](../CAS/Maxima.md)
