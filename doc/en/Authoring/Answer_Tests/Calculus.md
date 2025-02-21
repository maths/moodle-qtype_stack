# Calculus answer tests

There are four answer tests for dealing with calculus problems. The first is used with differentiation, the other three handle integration questions.

## Differentiation ##

### Diff ###

This test is a general differentiation test: it is passed if the arguments are algebraically equivalent, but gives feedback if it looks like the student has integrated instead of differentiated. The first argument is the student's answer. The second argument is the model answer. The answer test option must be the variable with respect to which differentiation is assumed to take place.

There are edge cases, particularly with \(e^x\) where differentiation is indistinguishable from integration.  You may need to use the "quiet" option in these cases.

## Integration tests ##

For integration, there are three answer tests.
Int has been part of STACK for a long time and tries to deal with various edge cases all in one answer test, but this complexity can sometimes lead to unexpected or unwanted behaviour.
With the aim to reduce these disadvantages at the cost of using several PRT nodes, STACK 4.9.0 introduced the Antidiff and AddConst answer tests.

### Int ###

This test is designed for a general indefinite integration question: it is passed if both the arguments are indefinite integrals of the same expression. The first argument is the student's answer.
The second argument is the model answer. The answer test option needs to be the variable with respect to which integration is assumed to take place, or a list (see below).

Getting this test to work in a general setting is a very difficult challenge.
In particular, the test assumes that the constant of integration is expressed in a form similar to \(+c\), although which variable used is not important.

The Int test has various additional options.

The question author must supply these options in the form of a list `[var, opt1, ...]`.  The first argument of this list must be the variable with respect to which integration is taking place.

If one of the `opt?` is exactly the token `NOCONST` then the test will condone a lack of constant of integration.  That is, if a student has missed off a constant of integration, or the answers differ by a numerical constant, then full marks will be awarded.  Weird constants (e.g. \(+c^2\)) will still be flagged up.

If one of the `opt?` is exactly the token `FORMAL` then the test will condone the formal derivative of the student's answer matching that of the teacher.  This is useful in examples such as \(\log(|x+3|)/2\) vs \(\log(|2x+6|)/2\) where effectively the constant of integration differs by a numerical constant.  Note, if you use the `FORMAL` option then by definition you will accept a missing constant of integration!

The answer test architecture only passes in the *answer* to the test.  The question is not available at that point; however, the answer test has to infer exactly which expression, including the algebraic form, the teacher has set in the question. This includes stripping off constants of integration and constants of integration may occur in a number of ways, e.g. in logarithms.
In many cases simply differentiating the teacher's answer is fine, in which case the question author need not worry.  Where this does not work, the question author will need to supply the expression from the question in the right form as an option to the answer test.  This is done simply by adding it to the list of options.

    [x, x*exp(5*x+7)]

The test cannot cope with some situations.  Please contact the developers when you find some of these.  This test is already rather overloaded, so please don't expect every request to be accommodated! If this test does not behave the way you want, consider using Antidiff and/or AddConst, described further below.

This test, in particular, has a lot of test cases which really document what the test does in detail.

The issue of \( \int \frac{1}{x} dx = \log(x)+c\) vs  \( \int \frac{1}{x} dx = \log(|x|)+c\) is a particular challenge. What mark would you give a student who integrated
\[ \int \frac{1}{x} dx = \log(k\times abs(x))?\]
If the teacher uses \(|..|\) in their answer then the student is also expected to use the absolute value.  The test is currently defined in such a way that if the teacher uses \( \log(|x|)+c \) in their answer, then they would expect the student to do so.  If they don't use the absolute value function, then they don't expect students to but will accept this in an  answer.

For example, if the teacher's answer is \( \log(x)+c \) (i.e. no absolute value) then all the following are considered to be correct.
\[ \log(x)+c,\ \log(|x|)+c,\ \log(k\,x),\ \log(k|x|),\ \log(|k, x|) \]

If the teacher's answer is \( \log(|x|)+c \) (i.e. with absolute value) then all the following are considered to be correct.
\[ \log(|x|)+c,\ \log(k|x|),\ \log(|k, x|)\ \]
Now, the following are rejected as incorrect, as the studnet should have used \(|..|\)
\[\log(x)+c,\ \log(k\,x)\]

Note that STACK sets the value of Maxima's `logabs:true`, which is not the default in Maxima.  This has the effect of adding the absolute value funtion when `integrate` is used.

In the case of partial  fractions where there are more than one term of the form \(\log(x-a)\) then
we insist the student is at least consistent.  If the teacher has *any*  \(\log(|x-a|)\) then the student must use \(|...|\) in *all* of them.  If the teacher has no \(\log(|x-a|)\) (i.e. just things like \(\log(x-a)\)) then the
student must have all or none. 

The phrase _"Please ask your teacher about this."_ occurs in some of the automatically generated feedback.  For example, when formal derivative of the student's answer does equal the expression they were asked to integrate but the answer differs from the correct answer in a significant way, not just, e.g., a constant of integration.  See examples with the note `ATInt_EqFormalDiff`.  If you don't want to use this phrase, alter the string tagged `seekhelp` in the language pack using, e.g., the moodle [language customisation](https://docs.moodle.org/405/en/Language_customisation)

### Antidiff ###

This test works similarly to Int, but it only checks if the student answer and the model answer have algebraically equivalent derivative in respect to the (mandatory) variable given in the options.
This test does not check for absolute values in logarithms or for the algebraic form of the student answer, but really only for algebraic equivalence of derivatives.
If you want to also check the algebraic form, consider using Int or other answer tests.
Like Int, this tests also checks if the student answer was derived using differentiation instead of integration and provides feedback.

### AddConst ###

This test can be used to detect if the student answer contains an additive constant, which is often used in calculus questions about antiderivatives and indefinite integrals.
The intended usage is to first check the student answer for being an antiderivative using Antidiff, followed by this answer test to check for an additive constant.
In combination you can then establish that the student gave a one parameter family of antiderivatives.
In calculus literature, this family is often defined as *the indefinite integral*.

This answer test requires the author to fill the options field with a list of variables which are to be ignored, i.e. the integration variable and any further variables.
You can thus check for the additive constant for the indefinite integral of \(x^n\) by passing the options `[x, n]`.
The answer test will complain if the student answer does not contain *exactly one additional variable* besides the given list in the options.

In its default mode, this test will only accept additive constants of the form `+c`, even though constant multiples of the constant (`+c/3`) and any surjective function on the reals (`+c^3`, `+ln(c)`) result in a mathematically correct parametrization of the family of antiderivatives.
The constant does not have to be added explicitely:
Testing `ln(x*exp(c))+k` with the given variables `[x,k]` will identify `c` as an additive constant, passing the answer test since `+c` can be extracted from the answer.

If the word `NONSTRICT` is a list element of the options field, then the answer test will accept any additive term in the different variable.
For example, the student answers `log(k*x)` or `x + C^3` with the options `[x, NONSTRICT]` (or `[NONSTRICT, x]`; the order does not matter) pass this answer test, whereas they will fail with the options `[x]`.
The test will still not accept added expressions in mixed variables, however.
