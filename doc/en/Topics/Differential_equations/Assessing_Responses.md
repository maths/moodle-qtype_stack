# Differential Equations

This page provides examples of how to assess the solutions to ordinary differential equations (ODEs) using the [potential response tree](../../Authoring/Potential_response_trees.md) when writing STACK questions.

## Assessing answers ##

ODEs provide a good example of the principle that we should articulate the properties we are looking for in ordinary differential equations.  These properties are

1. The answer satisfies the differential equation.
2. The answer satisfies any initial/boundary conditions.
3. The answer is general.
4. The answer is in the required form.

Hence, for ODE questions we need a potential response tree which establishes a number of separate properties.
On the basis of the properties satisfied, we then need to generate outcomes.

### Satisfying the differential equation ###

When marking this kind of question, it is probably best to take the student's answer and substitute this into the ODE.
The student's answer should satisfy the equation. Just "looking like the model answer" isn't as robust.
How else does the teacher avoid the problem of knowing which letter the student used to represent an arbitrary constant?

E.g. in Maxima code

    ode:x^2*'diff(y,x) + 3*y*x = sin(x)/x;
    ans: (c - cos(x))/x^3; /* The student's (correct) answer */
    sa1:subst(y=ans,ode);
    sa2:ev(sa1,nouns);
    sa3:fullratsimp(expand(sa2));

`sa1`, `sa2` and `sa2` can be used as part of the feedback when a student doesn't get the right answer.

### Satisfying any initial/boundary conditions ###

If the student's answer is `ans` then we can check initial/boundary conditions at a point `x=x0` simply by using

    ev(ans,x=x0);
    block([ds],ds:diff(ans,x),ev(ds,x=x0));

Notice in the second example the need to calculate the derivative of the student's answer before it is evaluated at the point `x=x0`.
These values can be compared with answer tests in the usual way.

### Arbitrary constants ###

Further tests are needed to ensure the student's solution is non-trivial, satisfies any initial conditions, or is suitably general.

To find which constants are present in an expression use Maxima's `listofvars` command.
In particular, to find if `c` appears in an expression `ans` we can use the predicate `member`

    member(c,listofvars(ans))

However, it is unusual to want to specify the name of a constant.  A student may choose another name.  The example below may be helpful here.

Sometimes students use the \(\pm\) operator, e.g. instead of typing in \( Ae^{\lambda t} \) they type in \( \pm Ae^{\lambda_1 t} \) as `+-A*e^(lambda*t)`.  The \(\pm\) has a somewhat ambiguous status in mathematics, but it is likely that many people will want to condone its use here.

Internally, the \(\pm\) operator is represented with an infix (or prefix) operation `#pm#`, which is part of STACK but not core Maxima.  Instead of `a+-b` teachers must type `a#pm#b`.  Students' answers get translated into this format.
Mostly when dealing with expressions you need to remove the \(\pm\) operator.  To remove the \(\pm\) operator STACK provides the function `pm_replace(ex)` which performs the re-write rules
\[ a\pm b \rightarrow (a+b) \vee (a-b) \]
\[ \pm a \rightarrow a \vee -a \]
(actually using STACK's `nounor` operator to prevent evaluation).

If you simply want to implement the re-write rule \( a\pm b \rightarrow a+b, \) i.e. ignore the \(\pm\) operator, then you can use `subst( "+","#pm#", ex)`.  For example, this substitution can be done in the feedback variables on a student's answer.  If you would like to test code offline with `#pm#` then you will need to make use of the [Maxima sandbox](../../CAS/STACK-Maxima_sandbox.md).

## Second order linear differential equations with constant coefficients ##

One important class of ODEs are the second order linear differential equations with constant coefficients.

Generating these kinds of problems is relatively simple: we just need to create a quadratic with the correct sort of roots.

Let us assume we have two real roots.  We might expect an answer \( Ae^{\lambda_1 t}+Be^{\lambda_2 t} \).
We might have an unusual, but correct, answer such as  \( Ae^{\lambda_1 t}\left(1+Be^{\lambda_2 t}\right) \).  Hence, we can't just "look at the answer".

Take question variables.

    sa1 : subst(y(t)=ans1,ode);
    sa2 : ev(sa1,nouns);
    sa3 : fullratsimp(expand(sa2));
    l   : delete(t,listofvars(ans1));
    lv  : length(l);

    b1  : ev(ans1,t=0,fullratsimp);
    b2  : ev(ans1,t=1,fullratsimp);
    m   : float(if b2#0 then fullratsimp(b1/b2) else 0);

1. Here `sa1`, `sa2` and `sa3` are used to ensure the answer satisfies the ODE and if not to provide feedback.
2. To ensure we have two constants we count the number of variables using `listofvars`, not including `t`. We are looking for two constants.
3. To ensure the solution is suitably general, we confirm \(y(1)\neq 0\) and calculate \(y(0)/y(1)\).
    If this simplifies to a number then the constants have cancelled out and we don't have a general solution consisting of two linearly independent parts.

These are the properties a correct answer should have.  If the teacher has a preference for the form, then a separate test is required to enforce it.
For example, you might like the top operation to be a \(+\), i.e. sum.   This can be confirmed by

    aop : is(equal(op(ans1),"+"));

Then test `aop` is `true` with another answer test.  Note that the arguments to answer tests cannot contain double quotes, so a question variable is needed here.

Next, let us assume we have complex root, e.g. in the equation

\[ \ddot{y}+2\dot{y}+5=0 \]

we have \(\lambda = -1 \pm 2i\).

We potentially have quite a variety of solutions.

\[ y=e^{-t}(A\sin(2t)+B\cos(2t))\]

\[ y=Ae^{-t}\sin(2t+B)\]

\[ y=Ae^{(-1+2i)t}+Be^{(-1-2i)t}\]

The advantage is that the same code correctly assesses all these forms of the answer.

### Separating the general from particular solution.

Consider the differential equation \[ \ddot{y}+4\dot{y}=8\tan(t) \] with corresponding general solution

    ode:'diff(y,t,2)+4*y-8*tan(t);
    ans1:-2*sin(2*t)-4*t*cos(2*t)+4*log(cos(t))*sin(2*t)+c_1*cos(2*t)+c_2*sin(2*t);

The solution of such an equation consists of the sum \(y(t) = c_1\ y_1(t)+c_2\ y_2(t)+y_p(t)\).   The _general solution_ is the term \(c_1\ y_1(t)+c_2\ y_2(t)\) and the particular solution is the part \(y_p(t)\).  It is useful to separate these.  Run the above code, which should work.  Then we execute the following, which checks the general solution part is made up of two linearly independent parts.

    /* Calculate the "Particular integral", (by setting both constants to zero) and then separate out the "general solution".*/
    ansPI:ev(ans1,maplist(lambda([ex],ex=0), l));
    ansGS:ans1-ansPI;
    g1  : ev(ansGS,t=0,fullratsimp);
    g2  : ev(ansGS,t=1,fullratsimp);
    m   : float(if g2#0 then fullratsimp(g1/g2) else 0);

Notice to calculate \(y_p(t)\) we set the constants \(c_1=c_2=0\), but using the variables in the list `l` which is defined above as the list of constants without \(t\).

## First order exact differential equations ##

An important class of differential equations are the so-called first order exact differential equations of the form

\[ p(x,y)\cdot \dot{y}(x) + q(x,y) = 0.\]

Assume that \(h(x,y)=c\) gives an implicit function, which satisfies this equation.  Then

\[ \frac{\mathrm{d}h}{\mathrm{d}x}=\frac{\partial h}{\partial y}\cdot \frac{\mathrm{d}y}{\mathrm{d}x}+\frac{\partial h}{\partial x}=0\]

and so

\[ \frac{\partial h}{\partial y} = p(x,y), \quad \frac{\partial h}{\partial x}=q(x,y).\]

Differentiating once further (and assuming sufficient regularity of \(h\)) we have

\[ \frac{\partial p}{\partial x} = \frac{\partial^2 h}{\partial x\partial y}=\frac{\partial q}{\partial y}.\]

Note that this condition on \(p\) and \(q\) is necessary and sufficient for the ODE to be exact.
In search of such a function \(h(x,y)\) we may define

\[ h_1 = \int q(x,y)\mathrm{d}x + c_1(y),\]

\[ h_2 = \int p(x,y)\mathrm{d}y + c_2(x).\]

Notice here that \(c_1\) and \(c_2\) are arbitrary functions of integration.  To evaluate these we differentiate again, for example taking the first of these we find

\[ \frac{\mathrm{d}h_1}{\mathrm{d}y}=\frac{\mathrm{d}}{\mathrm{d}y}\left(\int q(x,y)\mathrm{d}x \right) + \frac{\mathrm{d}c_1}{\mathrm{d}y} = p(x,y)\]

where this last equality arises from the differential equation.  Rearranging this and solving we have

\[ c_1(y) = \int\left( p(x,y)- \frac{\mathrm{d}}{\mathrm{d}y}\left(\int q(x,y)\mathrm{d}x \right)\right) \mathrm{d}y.\]

Similarly we may solve for
\[ c_2(x) = \int\left( q(x,y)- \frac{\mathrm{d}}{\mathrm{d}x}\left(\int p(x,y)\mathrm{d}y \right)\right) \mathrm{d}x.\]
If \(h_1=h_2\) then we have an exact differential equation, and \(h=h_1=h_2\) given the integral of our ODE.

### Example \(x\dot{y}+y+4=0\) ###

As an example consider

\[ x\dot{y}+y+4=0.\]

Then \(p=x\) and \(q=y+4\).

\[ c_1(y) = \int\left( p(x,y)- \frac{\mathrm{d}}{\mathrm{d}y}\left(\int q(x,y)\mathrm{d}x \right)\right) \mathrm{d}y = \int\left( x- \frac{\mathrm{d}}{\mathrm{d}y}\left(\int y+4\mathrm{d}x \right)\right) \mathrm{d}y\]

\[ = \int\left( x- \frac{\mathrm{d}}{\mathrm{d}y}\left(xy+4x\right)\right) \mathrm{d}y=0\]

And so

\[ h_1 = \int q(x,y)\mathrm{d}x + c_1(y) = \int y+4 \mathrm{d}x = x(y+4)+c.\]

Now,

\[ c_2(x) = \int\left( q(x,y)- \frac{\mathrm{d}}{\mathrm{d}x}\left(\int p(x,y)\mathrm{d}y \right)\right) \mathrm{d}x = \int (y+4) - \frac{\mathrm{d}}{\mathrm{d}x}\left(\int x\mathrm{d}y \right) \mathrm{d}x
\]

\[ = \int (y+4) - y \mathrm{d}x = 4x.\]

And so,

\[ h_2 = \int p(x,y)\mathrm{d}y + c_2(x) = \int x \mathrm{d}y +4x = xy+4x+c\]

In both cases we obtain the same answer for \(h(x,y)=xy+4x\).

### Maxima code ###

The following Maxima code implements this method, and provides further examples of how to manipulate ODEs.

    /* Solving exact differential equations in Maxima */
    (kill(all),load("format"))$

    ODE:x*'diff(y,x)+y+4$

    /* Ensure we have an expression, not an equation */
    if op(ODE)="=" then ODE:lhs(ODE)-rhs(ODE);

    /* This should write the ODE in the form
       p*'diff(y,x)+q
       which we can then sort out to get the coefficients*/
    ODE:format(ODE,%poly('diff(y,x)))$
    ODEc:coeffs(ODE,'diff(y,x));
    q:ODEc[2][1];
    p:ODEc[3][1];

    /* Check our condition for an exact ODE */
    if fullratsimp(diff(p,x)-diff(q,y))=0 then print("EXACT") else print("NOT EXACT")$

    /* Next we need to solve
       [diff(h,x)=q,diff(h,y)=p]
       to find the integral of our ODE */
    h1:integrate(q,x);
    h2:integrate(p,y);

    H1:h1+integrate(p-diff(h1,y),y);
    H2:h2+integrate(q-diff(h2,x),x);
    /* Note, H1 and H2 should be the same! */

    /* Hence the solution is, in terms of y=...+c*/
    solve(H1=c,y);

Further examples are
    /* Non-exact equations */
    ODE:y=x*'diff(y,x);

    /* Exact equations */
    ODE:2*y*x*'diff(y,x)+y^2-2*x=0$
    ODE:sin(x)*cosh(y)-'diff(y,x)*cos(x)*sinh(y)=0$
    ODE:(3*x^2*cos(3*y)+2*y)*'diff(y,x)=-2*x*sin(3*y)$
    ODE:x*'diff(y,x)+y+4$

