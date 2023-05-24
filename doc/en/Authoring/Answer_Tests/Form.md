# Algebraic Form

Often, we wish to establish if the student's expression has the correct _form_. For example, consider the following written forms of \(x^2-4x+4\).

\[(x-2)(x-2),\quad (x-2)^2,\quad  (2-x)^2,\quad  4\left(1-\frac{x}{2}\right)^2.\]

Each of these might be considered to be factored.  **Establishing `ex` is factored is not the same as comparing it with** `factor(ex)`.


### FacForm: factorisation of polynomials ###

This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is "factored" over the rational numbers. The answer test expects the option to be the variable, which is needed to generate feedback. If the answer is incorrect, quite detailed feedback is provided.

An expression is said to be factored if it is written as a product of powers of distinct irreducible factors. Strictly speaking, in establishing that an expression is in factored form, we might not even care whether the terms in the product are fully simplified, as long as they are irreducible. Irreducibility on the other hand means we can't find further factors, but here we need some care. Consider \(x^8+16x^4+48\).

1. Any non-trivial factorization, e.g. \((x^4+4)(x^4+12)\).
2. A factorization into irreducible factors over the integers/rational numbers, i.e. \((x^2+2x+x)(x^2-2x+2)(x^4+12)\).
3. A factorization into terms irreducible over the reals, i.e. \((x^2+2x+x)(x^2-2x+2)(x^2+2\sqrt[4]{3}x+2\sqrt[4]{3})(x^2-2\sqrt[4]{3}x+2\sqrt[4]{3})\).
4. A factorization into irreducible polynomials over the Gaussian integers, with \(i\) allowed, i.e. \((x+1+i)(x+1-i)(x-1+i)(x-1-i)(x^4+12)\).
5. A factorization over the complex numbers, where the factor \((x^4+12)\) would also be split into the four terms \(x\pm\sqrt[4]{3}(1\pm i)\).

In elementary teaching, meaning 4. is unlikely to occur. Indeed, we might take this example to represent factoring over any extension field of the rational numbers.  We normally seek to establish that the factors are irreducible over the integers (which is equivalent to irreducibility over the rational numbers) or the reals.  But, unlike a canonical form, we are not particularly interested in the order of the terms in this product, or the order of summands inside these terms.

The FacForm test establishes that the expression is factored over the rational numbers.  If the coefficients of the polynomial are all real, at worst you will have quadratic irreducible terms.  There are some delicate cases such as: \((2-x)(3-x)\) vs  \((x-2)(x-3)\)  and \((1-x)^2\) vs \((x-1)^2\), which this test will cope with.

It is also possible a student will do something which is just plain odd, e.g. \(x^2-4x+4\) can be rewritten as \(x(x-4+4/x)\) which is a "product of powers of distinct irreducible factors" but not acceptable to most teachers.  The student's answer must also be a polynomial in the variable (using `polynomialp` as the test predicate).

### Factorisation of integers ###

If you would like to ask a student to factor a polynomial, then do not use the `FacForm` answer test.  The `FacForm` answer test is designed to use with polynomials.

Instead, switch off simplification and define

    ta:factor(12);

and use `EqualComAss` as the answer test.

Note however that EqualComAss does not think that `2^2*3` and `2*2*3` are the same!

### PartFrac: partial fractions ###

This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is in "partial fraction form". The option must be the variable.

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

### SingleFrac: Single fractions ###

This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is written as a single fraction. Notes

* This test works at the top level, making sure the expression as a whole is a single fraction.
* if you also want this expression written in lowest terms, then this is quite a separate test.  You need to first confirm you have a single fraction then add a new potential response. One way is to use the [../CAS/Predicate functions](../../CAS/Predicate_functions.md) `lowesttermsp(ex)` and compare the result with `true` with the AlgEquiv test.
* The algebraic equivalence check is for convenience.  If you only want to check an expression is a single fraction make \(SAns=TAns\), i.e. ATSingleFrac(ex,ex) will do.

### LowestTerms ###

This test checks that all numbers written in the first expression are in lowest terms and that the denominator is clear of surds and complex numbers.

* if you want to check whether a rational polynomial is written in lowest terms, this is not the test to use.  Instead, apply the [predicate functions](../../CAS/Predicate_functions.md)  `lowesttermsp` to the expression.
* the second argument to this function is ignored, i.e. this test does not confirm algebraic equivalence.  You might as well use 0 here.

### Expanded ### 

Confirms SAns is equal to `expand(SAns)`.  Note, the second argument to this answer test is not used (but must be non-empty).  Note with this test that an expression such as \(x^2-(a+b)x+ab\) is not considered to be expanded, and this test will return false.

### CompletedSquare ###

This test checks (i) that SAns is algebraically equivalent to TAns , and (ii) that SAns is in "completed square form". The option must be the variable.

