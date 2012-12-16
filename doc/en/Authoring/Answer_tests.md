# Answer tests

An _answer test_ is used to compare two expressions to
establish whether they satisfy some mathematical criteria. The
prototype test is to establish if they are the _same_.  That is
to say, _algebraically equivalent_.

The exact behaviour of each answer test can be seen from
STACK's _test suite for STACK Answer tests_ which is available through the
Moodle admin menu.

This compares pairs of expressions and displays the outcomes
from each test. Mainly used to ensure STACK is working, it is
invaluable for understanding what each test really does.  In
particular it enables authors to see examples of which
expressions are the same and different together with examples
of the automatically generated feedback.  This feedback can be
suppressed using the `quiet` tickbox in the potential response
tree node.


# Introduction #

Informally, the answer tests have the following syntax

    [Errors, Result, FeedBack, Note] = AnswerTest(StudentAnswer, TeacherAnswer, Opt)

Where,

| Variable        | Description
| --------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------
| StudentAnswer   | A CAS expression, assumed to be the student's answer.
| TeacherAnswer   | A CAS expression, assumed to be the model answer.
| Opt             | Any options which the specific answer test provides. For example, a variable, the accuracy of the numerical comparison, number of significant figures.

Note that since the tests can provide feedback, tests which appear to be symmetrical,
e.g. Algebraic Equivalence, really need to assume which expression belongs to the student and which to the teacher.

| Variable  | Description
| --------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Errors    | Hopefully this will be empty!
| Result    | is either `true`, `false`, or `fail` (which indicates a failure of the test).  This determines which branch of the tree is traversed.
| FeedBack  | This is a text string which is displayed to the student. It is [CASText](CASText.md) which may depend on properties of the student's answer.
| Note      | This is a text string which is used for [Reviewing](Reviewing.md). Each answer note is concatenated with the previous notes and any contributions from the branch.


# Equality #

A crucial component in the assessment process the ability to decide if two expressions are equal.

Let us assume a teacher has asked a student to expand out \((x+1)^2\) and the response they have from one student is \(x^2+x+x+1\).
This is "correct" in the sense that it is algebraically equivalent to \((x+1)^2\) and is in expanded form
(actually two separate mathematical properties) but "incorrect" in the sense that the student has not _gathered like terms_ by performing an addition \(x+x\).
What about a response \(2x+x^2+1\)?  This is, arguably, better in the sense that the terms are gathered,
but the student here an not _ordered_ terms to write their expression in canonical form.
Hence, we need quite a number of different answer tests to establish equality in various senses of the word.

| Test                                              | Description
| ------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| CasEqual                                          | Are the parse trees of the two expressions equal?
| [EqualComAss](Answer_tests.md#EqualComAss)        | Are they equal up to commutativity and associativity of addition and multiplication, together with their inverses minus and division? For example \[a+b=b+a\mbox{,}\] but \[x+x\neq 2x\mbox{.}\] This is very useful in elementary algebra, where we want the form of the answer exactly. Simplification is automatically switched off when this test is applied, otherwise it makes no sense.
| [AlgEquiv](Answer_tests.md#AlgEquiv)              | Are they _algebraically equivalent_, i.e. does the difference simplify to zero?
| SubstEquiv                                        | Can we find a substitution of the variables of \(ex_2\) into \(ex_1\) which renders \(ex_1\) algebraically equivalent to \(ex_2\)?  If you are only interested in ignoring case sensitivity, you can apply the [Maxima commands defined by STACK](../CAS/Maxima.md#Maxima_commands_defined_by_STACK) `exdowncase(ex)` to the arguments, before you apply one of the other answer tests.
| SameType                                          | Are the two expressions of the [types_of_object](../CAS/Maxima.md#Types_of_object)?  Note that this test works recursively over the entire expression.
| SysEquiv                                          | Do two systems of polynomial equations have the same solutions? This test determines whether two systems of multivariate polynomials, i.e. polynomials with a number of variables, generate the same ideal, equivalent to checking they have the same solutions.


### AlgEquiv {#AlgEquiv}

This is the most commonly used test.  The pseudo code

    If
      simplify(ex1-ex2) = 0
    then
      true
    else
      false.

This test will work with a variety of [types of object](../CAS/Maxima.md#Types_of_object)
of mathematical objects, including lists, sets, equations, inequalities and matrices.
Exactly what it does depends on what objects are given to it.

* This test disregards whether [simplification](../CAS/Simplification.md) is switched on, it always fully simplifies all its arguments.
* Use `AlgEquiv(predicate(ex),true)` with [predicate functions](../CAS/Predicate_functions.md).

### EqualComAss: Equality up to Associativity and Commutativity ### {#EqualComAss}

This test seeks to establish whether two expressions are the same when the basic arithmetic operations of addition and multiplication are assumed to be nouns but are commutative and associative.  Hence, \(2x+y=y+2x\) but \(x+x+y\neq 2x+y\).  The unary minus commutes with multiplication in a way natural to establishing the required form of equivalence.

Notice that this test does not include laws of indices, so \(x\times x \neq x^2\). Since we are dealing only with nouns \(-\times -\) does not simplify to \(1\). E.g. \(-x\times -x \neq x\times x \neq x^2\).  An extra re-write rule could be added to achieve this, which would change the equivalence classes.

This is a particularly useful test for checking that an answer is the same and simplified.

### CasEqual ###

The CAS returns the result of the simple Maxima command

    if StudentAnswer=TeacherAnswer then true else false.

There is no explicit simplification here (unlike AlgEquiv).
This test works in different ways depending on whether [simplification](../CAS/Simplification.md) is on.
When simplification is off this test effectively tests whether the parse trees are identical.

### SysEquiv ###

The SysEquiv (system equivalence) test takes in two lists of polynomial equations in any number of variables and determines whether the two systems have the same set of solutions.
This is done using the theory of Gröbner bases to determine whether the ideals generated by the two systems are equal.
As the test allows for polynomials in several variables, it can cope with the intersections of the conic sections, as well as a large number of geometrically interesting curves.

* This test disregards whether [simplification](../CAS/Simplification.md) is switched on, it only simplifies its arguments where required.
This allows the test to list equations in feedback that the student has erroneously included in their system.

# Form {#Form}

Often we wish to establish if the student's expression has the correct _form_.
For example, consider the following various written forms of \(x^2-4x+4\).

\[(x-2)(x-2),\quad (x-2)^2,\quad  (2-x)^2,\quad  4\left(1-\frac{x}{2}\right)^2.\]

Each of these might be considered to be factored.  **Establishing `ex` is factored is not the same as comparing it with**

    factor(ex)

Related tests establish that an expression is _expanded_ or in _partial_

| Expression        | Description
| ----------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| LowestTerms       | This test checks that all numbers written in the first expression are in lowest terms.  Notes
|                   |     * if you want to check whether a rational polynomial is written in lowest terms, this is not the test to use.  Instead, apply the [predicate functions](../CAS/Predicate_functions.md)  `lowesttermsp` to the expression.
|                   |     * the second argument to this function is ignored, i.e. this test does not confirm algebraic equivalence.  You might as well use 0 here.
| Expanded          | Confirms SAns is equal to `expand(SAns)`.  Note, the second argument to this answer test is not used (but must be non-empty).  Note with this test that an expression such as \(x^2-(a+b)x+ab\) is not considered to be expanded, and this test will return false.
| FacForm           | This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is "factored" over the rational numbers. See below for more details.  The answer test expects the option to be the variable, which is needed to generate feedback. If the answer is incorrect, quite detailed feedback is provided.
| SingleFrac        | This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is written as a single fraction. Notes
|                   |     * if you also want this expression written in lowest terms, then this is quite a separate test.  You need to first confirm you have a single fraction then add a new potential response. One way is to use the [../CAS/Predicate functions](../CAS/Predicate_functions.md) `lowesttermsp(ex)` and compare the result with `true` with the AlgEquiv test.
|                   |     * The algebraic equivalence check is for convenience.  If you only want to check an expression is a single fraction make \(SAns=TAns\), i.e. ATSingleFrac(ex,ex) will do.
| PartFrac          | This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is in "partial fraction form". The option must be the variable.
| CompletedSquare   | This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is in "completed square form". The option must be the variable.

# Factorisation of polynomials {#FacPoly}

An expression is said to be factored if it is written as a
product of powers of distinct irreducible factors.   Strictly
speaking, in establishing that an expression is in factored
form, we might not even care whether the terms in the product
are fully simplified, as long as they are irreducible.

Irreducibility on the other hand means we can't find further factors, but here we need some care.

Consider \(x^8+16x^4+48\).

1. Any non-trivial factorization, e.g. \((x^4+4)(x^4+12)\).
2. A factorization into irreducible factors over the integers/rational numbers, i.e. \((x^2+2x+x)(x^2-2x+2)(x^4+12)\).
3. A factorization into terms irreducible over the reals, i.e. \((x^2+2x+x)(x^2-2x+2)(x^2+2\sqrt[4]{3}x+2\sqrt[4]{3})(x^2-2\sqrt[4]{3}x+2\sqrt[4]{3})\).
4. A factorization into irreducible polynomials over the Gaussian integers, with \(i\) allowed, i.e. \((x+1+i)(x+1-i)(x-1+i)(x-1-i)(x^4+12)\).
5. A factorization over the complex numbers, where the factor \((x^4+12)\) would also be split into the four terms \(x\pm\sqrt[4]{3}(1\pm i)\).

In elementary teaching, meaning 4. is unlikely to occur.
Indeed, we might take this example to represent factoring over
any extension field of the rationals.  We normally seek to
establish that the factors are irreducible over the integers
(which is equivalent to irreducibility over the rational
numbers) or the reals.  But, unlike a canonical form, we are
not particularly interested in the order of the terms in this
product, or the order of summands inside these terms.

The FacForm test establishes that the expression is factored
over the rational numbers.  If the coefficients of the
polynomial are all real, at worst you will have quadratic
irredicible terms.  There are some delicate cases such as:
\((2-x)(3-x)\) vs  \((x-2)(x-3)\)  and \((1-x)^2\) vs \((x-1)^2\),
which this test will cope with.

# Precision {#Precision}

These tests deal with the precision of numbers.

| Expression    | Description
| ------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| NumRelative   | Tests whether `|sa-ta| <= opt * |ta|` Hence the opt is a tolerance.
| NumAbsolute   | Tests whether `|sa-ta| < opt`  Hence the opt is a tolerance. The default tolerance is 0.05.
| NumSigFigs    | Tests (i) whether the student's answer contains `opt` significant figures, and (ii) whether the answer is accurate to `opt` significant figures.   If the option is a list \([n,m]\) then we check the answer has been written to \(n\) significant figures, with an accuracy of \(m\) places.  A common test would be to ask for \([n,n-1]\) to permit the student to enter the last digit incorrectly.
| GT            | Both are assumed to be numbers. The Answer test fully simplifies the SAns and converts this to a float if possible. This is needed to cope with expressions involving sums of surds, \(\pi\) etc.
| GTE           | See above.

# Calculus #

### Diff ###

This test is a general differentiation test. The first argument is the student's answer. The answer test options needs to be the variable.

### Int ###

This test is designed for a general indefinite integration question. The first argument is the student's answer.
The second argument is the model answer. The answer test option needs to be the variable with respect to which integration is assumed to take place.

Getting this test to work in a general setting is a very difficult challenge.
In particular, the test assumes that the constant of integration is expressed in a form similar to +c, although which variable used is not important.

The test cannot cope with the situation in which a teacher uses a constant in a form such as \(\ln(c|x|)\).
In this case the teacher should develop a test for the question from scratch.

# Other #

The following tests do not use Maxima, but instead rely on PHP.

| Expression     | Description
| -------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| String         | This is a string match, ignoring leading and trailing white space which are stripped from all answers, using PHP's trim() function.
| StringSloppy   | This function first converts both inputs to lower case, then removes all white space from the string and finally performs a strict string comparison.
| RegExp         | A regular expression match, with the expression passed via the option. This regular expression match is performed with PHP's `preg_match()` function. For example, if you want to test if a string looks like a floating point number then use the regular expression `{[0-9]*\.[0-9]*}`
|                | **NOTE:** this rest used to use PHP's `ereg()` function which has now been depricated.


# Developer #

Adding answer tests is possible, but is a developer task.  Tests currently lacking include the following.

* A numerical test which includes _units_, for science.

# See also

* [Maxima](../CAS/Maxima.md)
