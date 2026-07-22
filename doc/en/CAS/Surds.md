# Surds in Maxima and STACK

## Surds and mathematics

The expression \(\sqrt{x}\) and \(x^{1/2}\) are often (but not always) considered to be equivalent, especially in elementary mathematics.

We note that from a mathematical perspective that (i) there is an implied domain convention of \(x>0\) in \(\sqrt{x}\), and (ii) there are two possible values for \(\sqrt{9}\).

Do you really want to continue using \(\sqrt{}\) in your teaching?  In his *Elements of Algebra*, L. Euler wrote the following.

> \(\S 200\) We may therefore entirely reject the radical signs at present made use of, and employ in their stead
> the fractional exponents which we have just explained: but as we have been long accustomed to
> those signs, and meet with them in most books of Algebra, it might be wrong to banish them entirely from 
> calculations; there is, however, sufficient reason also to employ, as is now frequently done, the other method of 
> notation, because it manifestly corresponds with the nature of the thing. In fact we see immediately
> that \(a^\frac12\) is the square root of \(a\), because we know that the square of \(a^\frac12\), that is to say 
> \(a^\frac12\) multiplied by \(a^\frac12\) is equal to \(a^1\), or \(a\).

A lot of elementary mathematics involves converting from one form to another and back again.  Sometimes these forms have important differences of use, e.g. factored form or completed square form for a quadratic.  However, sometimes these equivalent forms are more customary than because it *"manifestly corresponds with the nature of the thing"* in question.  I digress...

## Surds and Maxima

By default Maxima does not like to use the \(\sqrt{}\) symbol. The internal representation favours fractional powers, for very good reasons. In  Maxima command line we get:

    (%i1) 4*sqrt(2);
    (%o1) 2^(5/2)
    (%i2) 6*sqrt(2);
    (%o2) 3*2^(3/2)

Furthermore, if you execute this in a Maxima session

    simp:true;
    p:1+sqrt(x);
    ?print(p);

Then the displayed value of `p` is \(\sqrt{x}+1\) whereas the internal representation of `p` is

    ((MPLUS SIMP) 1 ((MEXPT SIMP) $X ((RAT SIMP) 1 2))) 

This means that internally Maxima has converted `sqrt(x)` to `x^(1/2)`, even though it is by default displayed as `sqrt`.  This is an example where the displayed form (text and LaTeX) does not match Maxima's internal representation.

## Surds and STACK

1. Maxima provides a boolean /flag, `sqrtdispflag`.  When `sqrtdispflag` is `false`, Maxima will display `sqrt` to with exponent 1/2.  Note, this is a function at Maxima's _display_ level only and the expression tree itself is unchanged.  STACK provides a [question level option](../Authoring/Question_options.md) to set this flag.
2. STACK defines an \(n\)th root function `root(x,n)` which is a noun form for \(\sqrt[n]{x}\).  This can be used by students, but if teachers wish to use this in question variables etc. then they must prefix this with the apostrophe to create the nounform `'root(x,n)` to prevent immediate evaluation to `x^(1/n)`.

STACK provides a function `reroot` which traverses the expression tree and turns positive integer fractional powers back into noun forms of the `root` function.  This is useful for display in feedback, etc.

## Surds and assessment

Imagine you would like the student to expand out \( (\sqrt{5}-2)(\sqrt{5}+4)=2\sqrt{5}-3 \).
There are two tests you probably want to apply to the student's answer.

1. Algebraic equivalence with the correct answer: use `ATAlgEquiv`.
2. That the expression is "expanded": use `ATExpanded`.

You probably then want to make sure a student has "gathered" like terms.  In particular you'd like to make sure a student has either
\[ 2\sqrt{5}-3 \text{ or } \sqrt{20}-3\]
but not \[ 5+4\sqrt{2}-2\sqrt{2}+6.\]
This causes a problem because `ATComAss` thinks that \[ 2\sqrt{5}-3 \neq \sqrt{20}-3.\]
So you can't use `ATComAss` here, and guarantee that all random variants will work by testing that we really have \(5+4\sqrt{2}\) for example.

What we really want is for the functions `sqrt` and `+` to appear precisely once in the student's answer, or that the answer is a sum of two things.

When surds appear in equations and sets we might need to force some kinds of simplification.  For example, when we try to establish that this set (the student's answer)
\[ {\left \{x=-\frac{\sqrt{19}}{2\cdot \sqrt{3}}-\frac{1}{2} , x=\frac{\sqrt{19}}{2\cdot \sqrt{3}}-\frac{1}{2} \right \}} \]
is equivalent to
\[ {\left \{x=\frac{-\sqrt{57}-3}{6} , x=\frac{\sqrt{57}-3}{6} \right \}} \]

If we were dealing with two *numbers*, then Maxima has no problem in establishing that 
\[ \frac{-\sqrt{57}-3}{6}-\frac{\sqrt{19}}{2\cdot \sqrt{3}}-\frac{1}{2} = 0\]
On the maxima command line try `p:(-3 + sqrt(9 + 48))/6+1/2 - sqrt(1/4 + 4/3);` then `radcan(p)`.  Within the AlgEquiv test `radcan` is applied automatically to _numbers_ within an expression, and this returns zero.

The problem with _sets_ is that we don't have the difference between two numbers.  We're trying to write all numbers in an unambiguous form, and then comepare the representation.  This (subtle) difference is the problem.  Instead of looking at equivalence with zero, we need to contol the form of surds explicitly.

### Control of surds in Maxima

See also the Maxima documentation on `radexpand`.  For example

    radexpand:false$
    sqrt((2*x+10)/10);
    radexpand:true$
    sqrt((2*x+10)/10);

The first of these does not pull out a numerical denominator.  The second does.

Similarly, consider the output from these two examples.

    p1:(-3 + sqrt(9 + 48))/6;
    radcan(p1);
    trigrat(p1);
    radcan(trigrat(p1));

    p2:-1/2 + sqrt(1/4 + 4/3);
    radcan(p2);
    trigrat(p2);
    radcan(trigrat(p2));

Why don't we always apply `trigrat` to expressions?  Without knowing something about the expression, we might "expand" out the terms which causes a practical failure of the test due to timeout.  E.g. `expand((x+y)^(2^100))` is never going to execute.  Similarly, `trigrat` causes some (trig) expressions to expand, see below.
