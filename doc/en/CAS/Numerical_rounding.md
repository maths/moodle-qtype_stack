# Numerical rounding

Internally Maxima represents floats in binary, and so even simple calculations which would be exact in base ten (e.g. adding 0.16 to 0.12) might end up in a recurring decimal float which is not exactly equal to the result you would type in directly.

Try `452-4.52*10^2` in desktop Maxima, which is not zero, therefore `ATAlgEquiv(452,4.52*10^2)` fails. (Maxima 5.44.0, November 2022).  \(4.52\times 10^2\) ends up with recurring 9s when represented as a binary float, so it is not algebraically equivalent to the integer \(452\).

Rounding like this can also occur in calculations, for example

    p1:0.29;
    p2:0.18;
    p3:0.35;
    v0:1-(p1+p2+p3);
    v1:0.18;

Then Maxima returns `0.18` for `v0`, (as expected) but `v0-v1` equals \(5.551115123125783\times 10^{-17}\) and so `ATAlgEquiv(v0,v1)` will give false.  Please always use a [numerical test](../Authoring/Answer_Tests/Numerical.md) when testing floats.

As another example, try `100.4-80.0;` in a desktop Maxima session.

## Notes about numerical rounding ##

There are two ways to round numbers ending in a digit \(5\).

* Always round up, so that \(0.5\rightarrow 1\), \(1.5 \rightarrow 2\), \(2.5 \rightarrow 3\) etc.
* Another common system is to use ``Bankers' Rounding". Bankers Rounding is an algorithm for rounding quantities to integers, in which numbers which are equidistant from the two nearest integers are rounded to the nearest even integer. \(0.5\rightarrow 0\), \(1.5 \rightarrow 2\), \(2.5 \rightarrow 2\) etc.  The supposed advantage to bankers rounding is that in the limit it is unbiased, and so produces better results with some statistical processes that involve rounding.
* In experimental work, the number of significant figures requires sometimes depends on the first digits of the number.  For example, if the first digit is a \(1\) or \(2\) then we need to take an extra significant figure to ensure the relative error is suitably small.  The maxima string functions can be used to check the first digit of a number until we have bespoke internal functions to make this check.

Maxima's `round(ex)` command rounds multiples of 1/2 to the nearest even integer, i.e. Maxima implements Bankers' Rounding.  We do not currently have an option to always round up.

STACK has defined the function `significantfigures(x,n)` to conform to convention of rounding up.

## ATAlgEquiv and floating point numbers ##

We recommend you do _not_ use algebraic equivalence testing for floating point numbers.  Instead use one of the [numerical tests](../Authoring/Answer_Tests/Numerical.md).

Lists of numbers present issues with numerical rounding as well.  The `ATAlgEquiv` answer test does work with lists, matrices etc.  However, the numerical tests expect single floating point numbers and do not accept lists etc.

If you have lists of numbers one approach is the following in the feedback variables.

````
/* ta is the teacher's answer.
   ans1 is the student's answer.
   Create a matrix.
   */
S:matrix(LSG1-ans1);
/* Calculate the matrix norm. */
N:ev(S.transpose(S),simp);
/* Now test this is less that 1E-10 with the answer test ATGT(1E-10,N). */
````

Other options include finding `ev(max(map(abs, S), simp` to find the maximum error.