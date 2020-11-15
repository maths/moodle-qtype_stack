# Numerical answer tests

An _answer test_ is used to compare two expressions to establish whether they satisfy some mathematical criteria. 
This page is dedicated to answer tests which establish numerical precision.
Other tests are documented in a page on [answer tests](../Authoring/Answer_tests.md).  

Please also see the separate notes on [numbers](../CAS/Numbers.md) and [scientific units](Units.md).  

With other answer tests a question author can manipulate the student's answer with the CAS before applying an answer test.  With these tests it is _essential_ that the SAns field is exactly the name of the input.  For example, if your input is `ans1` then the SAns field of the answer test must only be `ans1`. The reason for this is that Maxima changes the display of floating-point numbers to remove or add trailing zeros.  These answer tests must take the value typed at the input, not the value passed through the CAS, otherwise digits will be changed.  Further information and examples are given in the page on [numbers](../CAS/Numbers.md).   This problem does not apply to the teacher's answer because we do not test whether the teacher has supplied the correct number of trailing zeros.  We only test if the student's answer is sufficiently close in numerical precision.  So, it is safe to calculate the teacher's answer field of the answer test from question variables using the CAS.  Care will be needed to display the correct number of significant figures with trailing zeros elsewhere in the question though.

# Introduction to numerical testing #

There are two issues of which a question author should be aware.

* Limits on numerical accuracy.
* Dealing with trailing zeros.

All software have limitations on the extent to which they can robustly deal with numerical quantities.  Maxima, PHP (and hence STACK) are no exceptions.  Integers are essentially unproblematic, and CAS will support (almost) arbitrary precision integers.  Floating-point representations of real numbers are more difficult, and a classic discussion of how to represent continuous quantities in finite state machine is given by D. Goldberg. _What every computer scientist should know about floating-point arithmetic._ Computing Surveys, 23(1):5-48, March 1991.

# Answer tests in STACK#

### NumRelative & NumAbsolute ###

The option to these tests is a tolerance.  The default tolerance is 0.05.

* Relative: Tests whether `abs(sa-ta) <= opt * abs(ta)` 
* Absolute: Tests whether `abs(sa-ta) < opt`  

NumRelative  and NumAbsolute can also accept lists and sets.  Elements are automatically converted to floats and simplified (i.e. `ev(float(ex),simp)`) and are compared to the teacher's answer using the appropriate numerical test and accuracy.  A uniform accuracy must be used.  With lists the order is important, but with sets it is not.  Checking whether two sets are approximately equal is an interesting mathematical problem....

### GT & GTE ###

"Greater than" or "Greater than or equal to".  Both arguments are assumed to be numbers. The Answer test fully simplifies the SAns and converts this to a float if possible. This is needed to cope with expressions involving sums of surds, \(\pi\) etc.  Therefore, do expect some numerical rounding which may cause the test to fail in very sharp comparisons.

# Significant figure testing #

The significant figures of a number are digits that carry meaning. This includes all digits except

* leading zeros;
* trailing zeros when they are only placeholders to indicate the scale of the number.

To establish the number of significant figures which arise from a calculation it is necessary to know the number of significant figures involved in the floating-point numbers used in the calculation.  This causes a problem in assessment when we have a numerical expression, such as \(100\), and seek to infer the number of significant figures.  Does this have one significant figure or three?

The following cases illustrate the difficulties in inferring the number of significant digits from only the written form of a number.

* \(0.0010\) has exactly \(2\) significant digits.
* \(100\) has at least \(1\) and maybe even \(3\) significant digits.
* \(1.00e3\) has exactly \(3\) significant digits.
* \(10.0\) has exactly \(3\) significant digits.
* \(0\) has \(1\) significant digit.
* \(0.00\) has at least \(1\) and maybe even \(3\) significant digits.
* \(0.01\) has exactly \(1\) significant digit.

Therefore, with trailing zeros there are a number of cases in which it is not possible to tell from the written form of an expression precisely how many significant digits are present in a student's answer.  This creates a problem for automatic assessment.

To avoid this ambiguity some scientists adopt a convention where \(100\) has _exactly one significant digit_.  To express one hundred to three significant digits it is _necessary_ to use \(1.00e2\).  This conforms to the specifications in international standards and it certainly avoids ambiguity but in many assessment situations teachers do not want to enforce such strict rules.  STACK seeks to provide tools for both situations:  very strict enforcement of the significant figures rules (needed when teaching significant figures of course!) and a more liberal situation in which STACK will accept an input of \(100\) when the teacher wanted 1, 2 or 3 significant figures.

In addition to the number of significant figures used to express the number, a teacher will also want to establish that the student actually has the right number!  In more liberal situations a teacher will condone an error in the last place.  E.g. they will accept an answer written to four significant figures, but where only three are actually correct.

### StrictSigFigs ####

This test enforces the strict rules of significant figures.  It does not check for numerical precision and looks only at surface features of the number representation.

The option is the required number of significant figures.  This must be an integer only.

### NumSigFigs ####

This is a more liberal test.  Primarily it checks numerical accuracy, but it also checks the number of significant figures in a liberal way.  That is to say, it makes sure that the number of significant figures specified by the teacher lies within the range inferred from the student's answer.  For example, this test will accept \(100\) as being correctly written to 1, 2 or 3 significant figures.  If you want to enforce the precise rules use the `StrictSigFigs` test.

Tests 

1. whether the student's answer contains `opt` significant figures, and
2. whether the answer is accurate to `opt` significant figures.   

If the option is a list `[n,m]` then we check the answer has been written to `n` significant figures, with an accuracy of up to `m` places.  If the answer is too far out then rounding feedback will not be given.   A common test would be to ask for \([n,n-1]\) to permit the student to enter the last digit incorrectly.

If the options are of the form `[n,0]` then only the number of significant figures in `sa` will be checked.  This ignores any numerical accuracy and completely ignores the second argument to the function.  Note, that this test is liberal in establishing the number of significant figures.  For strict enforcement of the rules, use `StrictSigFigs` instead.

If the options are of the form `[n,-1]` then the test checks the student has _at least_ `n` significant figures in the answer, and that numerical accuracy is correct.

This test only supports numbers where \(|sa|<10^{22}\).  Please see the [notes about numerical rounding](../CAS/Numbers.md) for the differences between rounding. In `NumSigFigs` the teacher's answer will be rounded to the specified number of significant figures before a comparison is made.

### NumDecPlaces ###

Tests (i) whether the student's answer is equivalent to the teacher's and (ii) is written to `opt` decimal places.  The option, which must be a positive integer, dictates the number of digits following the decimal separator `.`.  Note that trailing zeros are ''required'', i.e. to two decimal places you must write `12.30` not just `12.3`.  The test rounds the numbers to the specified number of decimal places before trying to establish equivalence.


### NumDecPlacesWrong ###

Tests if the student appears to have the decimal point in the wrong place.  For example, `3.141` and `31.41` will be considered the same under this test.  `opt` is the number of places to consider, from the most significant.

This test condones any lack of trailing zeros.  Use other tests to establish if a student has sufficient places.

## Other issues ##

A teacher may wish to see if a student's answer is incorrect only by orders of magnitude.  E.g. a student answers \(110\) instead of \(1100\).  To do this, a teacher can check whether \(\log_{10}\) of the ratio of two expressions is an integer, e.g.

     integerp(rat(float(log(sa/ta)/log(10))))
     
There is currently no functionality within the numerical answer tests to automatically check this, and there is no dedicated answer test to establish this property.  A separate PRT node is needed.



# See also

* [Answer tests](Answer_tests.md)
* [CAS and numbers](../CAS/Numbers.md)
* [Scientific units](Units.md)
* [Maxima](../CAS/Maxima.md)
