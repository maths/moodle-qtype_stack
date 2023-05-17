# Calculus answer tests

There are two answer tests for dealing with calculus problems.

### Diff ###

This test is a general differentiation test: it is passed if the arguments are algebraically equivalent, but gives feedback if it looks like the student has integrated instead of differentiated. The first argument is the student's answer. The second argument is the model answer. The answer test option must be the variable with respect to which differentiation is assumed to take place.

There are edge cases, particularly with \(e^x\) where differentiation is indistinguishable from integration.  You may need to use the "quiet" option in these cases.

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

The test cannot cope with some situations.  Please contact the developers when you find some of these.  This test is already rather overloaded, so please don't expect every request to be accommodated!

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