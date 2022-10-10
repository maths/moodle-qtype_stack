# Writing worked solutions in STACK

There is something of an art to writing worked solutions in STACK which are robust to different random versions.  This page contains an example of a detailed, and flexible, solution to solving a quadratic equation.

The method of creating a worked solution used here uses two basic ideas.

1. We should solve as many problems as possible at the mathematical level, with `simp:false`, and not try to solve problems at the LaTeX level.  This means we will be simplifying _parts_ of expressions explicitly using `ev( ... , simp)` within larger expressions.  For example consider an expression like \( (x-3)^2 - 2^2 \).  This might be created in Maxima as `(ev(sqrt(c2)*x+c1/2,simp))^2 - n5^2 = 0` for appropriate values of the variables, i.e. `c2:1`, `c1:-6` and `n5:2`.  The example simplifies the contents of the brackets, e.g. if `c2` is a perfect square, or \(1\), this will be simplified, as will an even value of `c1`.   The advantage of working at the mathematical level is that Maxima will display negative values of `c1` as for example.
2. Steps can be ommited in the worked solution, or conditional statements added to the worked solution, using the [question blocks](Question_blocks.md) functionality.  The castext is the right place to deal with formatting, not within the question variables.

Note, in the example below the presentation is kept very simple.  Ultimately, some better styling (CSS) would significantly improve the presentation, perhaps using a two-column layout.

If your STACK version is older than 20221010 then you will need to add this function to the question variables.

```
texdisp_select(ex) := sconcat("\\color{red}{\\underline{", tex1(first(args(ex))), "}}");
texput(disp_select, texdisp_select);
```

The following is the question variables field.

```
a1:1;
n1:-2+3*%i;
n2:-2-3*%i;
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

The question is simply `Solve \({@p0@}=0\).`  The correct answer is `ta`, and a PRT with `ATAlgEquiv(ans1,ta)` is sufficient for now.  (Better feedback could be provided, of course.)

In the Options turn the Question-level simplify to `no`.

The point of this document is the general feedback, i.e. the worked solution.

```
Solve \({@p0@}=0\).
[[ if test='is(a1=1)' ]]
Since the coefficient of the highest power, \(x^2\), equals one, we have what is known as a "monic" polynomial and can start to solve this.
[[ else ]]
The first step is to divide through by the coefficient of the highest power, \(x^2\), so we have what is known as a "monic" polynomial.  Now we have to solve \({@p1@}=0\).
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

This particular worked solution will create a reasonable step by step solution in all the following cases

1. Roots integer and distinct.
2. One root is zero.  Requires one "if" statement as a question block to suppress "add constant term to both sides".
3. \(a \neq 1\). Requires one "if" statement as a question block, to divide through by \(a\) at the start.
4. Roots contain a surd.  Requires one "if" statement as a question block, to suppress simplification of numbers which can't be added.
5. Roots are Gaussian integers.
6. Roots are complex conjugate.

There are many ways to solve quadratics, but this method has been selected for the following reasons.

* This method makes use of the completed square and difference of two squares.
* This method it always "works", and therefore generalises if introduced early, e.g. just with integer roots.
* This method involves "appreciation of form", in particular "can we make this a perfect square?", which is an important theme in algebraic manipulation.

The fact that this method does _not_ work well with integer roots integer which are identical (i.e. repeated roots) suggests that case is probably, arguably, best assessed with dedicated questions assessing the single issue.  Invariance of the steps in the worked should is arguably a good test of when questions are the same or different, for a particular student group.

Within this basic idea of invariance, some special cases, e.g. \(a=1\), \(c=0\) merely omit one or more of the steps in the worked solution.  If \(c=0\) then it makes no sense to have a step "Subtract the constant term from both sides."  This special cases does not really lead to a genurinly new cases, we just need to omit a particular step.

but this is exactly the kind of didactic decision making which is interesting to us!
