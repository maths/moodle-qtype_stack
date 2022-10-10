# Writing worked solutions in STACK

There is something of an art to writing worked solutions in STACK which are robust to different random versions.  This page contains an example of a detailed, and flexible, solution to solving quadratic equations.

Creating a worked solution, in this example and more generally, uses the following basic ideas.

1. We should start with the worked solution and work backwards to the question.  Here we start with numbers \(a\), \(n_1\) and \(n_2\) and expand out \(a(x-n_1)(x-n_2)\) to keep careful control over the roots and the coefficient of \(x^2\).
2. Technically, we should solve as many problems as possible at the mathematical level in Maxima, with `simp:false`, and not try to solve problems at the LaTeX level.  This means we will be simplifying _parts_ of expressions explicitly using Maxima code `ev( ... , simp)` within larger expressions.  For example, consider the expression \( (x-3)^2 - 2^2 \).  This might be created from variables in Maxima as `(ev( sqrt(c2)*x+c1/2, simp))^2 - n5^2 = 0` with variables `c2:1`, `c1:-6` and `n5:2`.  This example simplifies the contents of the brackets, but not the constant term outside.   The advantage of working at the mathematical level is that Maxima will display negative values of `c1` with a minus sign and not as \( (x+ -3)^2 - 2^2 \).
3. Steps can be ommited in the worked solution, or conditional statements added to the worked solution, using the [question blocks](Question_blocks.md) functionality.  The castext is the right place to deal with formatting, not within the question variables.

Note, in the example below the presentation is kept very simple.  Ultimately, some better styling (CSS) would significantly improve the presentation, perhaps using a two-column layout.

The following is the question variables field.

```
/* Control the coeffient of x^2 and the roots. */
a1:1;
n1:-2;
n2:3;
/* Define the quadratic and monic quadratic from the roots. */
p0:ev(expand(a1*(x-n1)*(x-n2)), simp);
p1:ev(expand((x-n1)*(x-n2)), simp);
/* Coefficients of the polynomial.  */
c0:ev(coeff(p1,x,0),simp);
c1:ev(coeff(p1,x,1),simp);
c2:ev(coeff(p1,x,2),simp);
/* Calculations based on coefficients. */
n3:ev((c1/2)^2,simp);  /* b/2 */
n4:ev((c1/2)^2-c0,simp); /* b^2/4-c */
n5:ev(sqrt(n4),simp); 
n6:ev(sqrt(c2)*x,simp); /* We need this simplified, especially when c2=1. */

/* These are lines in the working, (p*) or other associated expressions (q*).  */
q0:ev(expand((x+c1/2)^2),simp);

p2:ev(p1-c0,simp) = ev(-1*c0,simp);
p3:ev(p1-c0,simp) + disp_select(n3) = disp_select(n3) - c0;
p4:disp_select((ev(x+c1/2,simp))^2) = n4;
p5:(ev(n6+c1/2,simp))^2 = disp_select(n5^2) ;
p6:(ev(n6+c1/2,simp))^2 - n5^2 = 0;
p7:(ev(n6+c1/2,simp)-n5)*(ev(n6+c1/2,simp)+n5) = 0;
p8:(n6+disp_select(ev(c1/2,simp)-n5))*(n6+disp_select(ev(c1/2,simp)+n5)) = 0;
p9:(ev(n6+c1/2-n5,simp))*(ev(n6+c1/2+n5,simp)) = 0;

/* The correct answer. */
ta:x=n1 nounor x=n2;
```

If your STACK version is older than 20221010 then you will need to add this function to the question variables.

```
texdisp_select(ex) := sconcat("\\color{red}{\\underline{", tex1(first(args(ex))), "}}");
texput(disp_select, texdisp_select);
```

The question text is simply `Solve \({@p0@}=0\).`  The correct answer is `ta`, and a PRT with `ATAlgEquiv(ans1,ta)` is sufficient for now.  (Better feedback could be provided, of course.)

In the Options turn the Question-level simplify to `no`.

The point of this document is the general feedback, i.e. the worked solution.

```
Solve \({@p0@}=0\).
[[ if test='is(a1=1)' ]]
Since the coefficient of the highest power, \(x^2\), equals one, we have what is known as a "monic" polynomial which we can start to solve.
[[ else ]]
The first step is to divide through by the coefficient of the highest power, \(x^2\), so we have what is known as a "monic" polynomial where the coefficient of the highest power, \(x^2\), equals one.  Doing this, we now have to solve \({@p1@}=0\).
[[/ if ]]
Assume \(b\) is the coefficient of \(x\), which in this case is {@c1@}. Divide this by \(2\), and consider \( (x+b/2)^2 = {@ (ev(sqrt(c2)*x,simp)+c1/2)^2 = q0@} \).  We use this as follows.
\[ {@p1=0@} \] 
[[ if test='is(c0#0)' ]]
Subtract the constant term from both sides.
\[ {@p2@} \] 
[[/ if ]]
Add  \(b^2/4\) to both sides
\[ {@p3@} \]
and add the numerical terms on the right hand side.  Now is the time to use \(  {@ (ev(sqrt(c2)*x,simp)+c1/2)^2 = q0@} \) and notice the calculation so far makes the left side a perfect square, so we may now factor the left hand side
\[ {@p4@} \]
Write the right hand side as a square
\[ {@p5@} \]
and subtract this from both sides.
\[ {@p6@} \]
Now we have the difference of two squares.
\[ {@p7@} \]
Select the numbers in each factor
\[ {@p8@} \]
[[ if test='integerp(ev(c1/2-n5,simp))' ]]
and perform arithmetic
\[ {@p9@} \]
[[/ if ]]
Hence we have the solutions {@x=n1@} and {@x=n2@}.
```

This particular worked solution will create a reasonable step-by-step solution in all the following cases:

1. Roots integer and distinct.
2. One root is zero.  Requires one "if" statement as a question block to suppress "add constant term to both sides".
3. \(a \neq 1\). Requires one "if" statement as a question block, to divide through by \(a\) at the start.
4. Roots contain a surd.  Requires one "if" statement as a question block, to suppress simplification of numbers which can't be added and simplified.
5. Roots are Gaussian integers.
6. Roots are complex conjugate.

There are many ways to solve quadratics, but this method has been selected for the following reasons.

* This "works" for all quadracits. Therefore if introduced early the method generalises beyond the special case of integer roots.
* This method makes use of the completed square and difference of two squares, themselves both important topics.
* This method involves "appreciation of form", in particular "can we make this a perfect square?", which is an important theme in algebraic manipulation.  This is a general concept in elementary algebra.

However, this method does _not_ work well with repeated integer roots. Hence, repeated roots is arguably better assessed with dedicated questions assessing the single issue explicitly.  Similarly, this worked solution does not work well with the dfference of two squares, i.e. \( x^2-c^2=(x-c)(x+c) \). Both perfect squares, and the differece of two squares, _could_ be accommodated with more question blocks.  However, invariance of the steps in the worked should is arguably a good test of when questions are the same or different, for a particular student group.  The orginal goal was to write a single STACK question with a worked solution which is robust in a variety of situations.  The attempt to write a general worked solution, and the above analysis of the general case,  has suggested the following didactic sequence.

1. Perfect squares: \( (x+c)^2 = x^2+2cx+c^2 \).
2. Difference of two squares: \( (x+c)(x-c) = x^2-c^2 \).
3. The general case, solved by using both of the above.

Within this basic idea of invariance, some special cases of the general quadratic \( ax^2+bx+c=0\), e.g. \(a=1\) or \(c=0\) merely omit one or more of the steps in the general worked solution.  For example, if \(c=0\) then it makes no sense to have a step "Subtract the constant term from both sides."  This special cases does not really lead to genurinly new cases in the worked solution, we just need to omit a particular step which is trivial in this example.  These sub-cases could be conciously used to create progressivly more complex cases, even within the relm of quadratics with integer roots.

This worked solution does work even in the case \(a \neq 1\).  For example \({3\,x^2-x-2}=0\) has roots \(-2/3\) and \(1\), and the worked solution above gives a reasonable solution with the following values.

```
a1:3;
n1:-2/3;
n2:1;
```

Notice this method has conciously avoided taking the square roots of both sides of an equation and hence entirely side-stepped the confusing issue of how to deal with \( \pm \).  Avoiding taking the square roots of both sides of an equation does not lead to the shortest worked solution in all cases.

This method completely side-steps factoring with a "guess and check" method, even though this is widley taught and quicker when mastered.
