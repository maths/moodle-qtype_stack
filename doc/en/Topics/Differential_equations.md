# Differential Equations

This page provides examples of how to represent and manipulate ordinary differential equations (ODEs) in [Maxima](../CAS/Maxima_background.md) when writing STACK questions.

## Representing ODEs

In a Maxima session we can represent an ODE as

    ODE: x^2*'diff(y,x) + 3*y*x = sin(x)/x;

Notice the use of the `'` character in front of the `diff` function to prevent evaluation. Applied to a function call, such as `diff`, the single quote prevents evaluation of the function call, although the arguments of the function are still evaluated (if evaluation is not otherwise prevented). The result is the noun form of the function call.

## Entering DEs

The syntax to enter a derivative in Maxima is `diff(y,x,n)`.  Teachers need to use an apostrophe`'` character in front of the `diff` function to prevent evaluation in question variables (etc). E.g. to type in \( \frac{\mathrm{d}^2y}{\mathrm{d}x^2}\) you need to use `'diff(y,x,2)`.

Students' answers always have noun forms added. If a student types in `diff(y,x)` then this is protected by a special function `noundiff(y,x)` (etc), and ends up being sent to answer test as `'diff(y,x,1)`. If a student types in (literally) `diff(y,x)+1 = 0` this will end up being sent to answer test as `'diff(y,x,1)+1 = 0`.

The answer test `AlgEquiv` evaluates all nouns.   This has a (perhaps) unexpected side-effect that `noundiff(y,x)` will be equivalent to `0`, and `noundiff(y(x),x)` is not.  For this reason we have an alternative [answer test](../Authoring/Answer_Tests/index.md) `AlgEquivNouns` which does not evaluate all the nouns.
The `ATEqualComAss` also evaluates its arguments but does not "simplify" them.  So, counter-intuitively perhaps, we currently do have `ATEqualComAss(diff(x^2,x), 2*x);` as true.

Students might expect to enter expressions like \( y' \), \( \dot{y} \) or \( y_x \) (especially if you are using `derivabbrev:true`, see below).   The use by Maxima of the apostrophe which affects evaluation also has a side-effect that we can't accept `y'` as valid student input.  Input `y_x` is an atom.  Individual questions could interpret this as `'diff(y,x)` but there is no systematic mechanism for interpreting subscripts as derivatives.  Input `dy/dx` is the division of one atom `dy` by another `dx` and so will commute with other multiplication and division in the expression as normal.  There is no way to protect input `dy/dx` as \( \frac{\mathrm{d}y}{\mathrm{d}x}\).  The only input which is interpreted by STACK as a derivative is Maxima's `diff` function, and students must type this as input.

The expression `diff(y(x),x)` is not the same as `diff(y,x)`.  In Maxima `diff(y(x),x)` is not evaluated further.  Getting students to type `diff(y(x),x)` and not `diff(y,x)` will be a challenge.  Hence, if you want to condone the difference, it is probably best to evaluate the student's answer in the feedback variables as follows to ensure all occurrences of `y` become `y(x)`.

    ans1:'diff(y(x),x)+1 = 0;
    ansyx:subst(y,y(x),ans1);

Trying to substitute `y(x)` for `y` will throw an error.  Don't use the following, as if the student has used `y(x)` then it will become `y(x)(x)`!

    ans1:'diff(y,x)+1 = 0;
    ansyx:ev(ans1,y=y(x));

Further work is needed to better support partial derivatives (input, display and evaluation).

## Displaying ODEs

Maxima has two notations to display ODEs.

If `derivabbrev:false` then`'diff(y,x)` is displayed in STACK as \( \frac{\mathrm{d}y}{\mathrm{d}x}\).   Note this differs from Maxima's normal notation of \( \frac{\mathrm{d}}{\mathrm{d}x}y\).

If `derivabbrev:true` then `'diff(y,x)` is displayed in STACK and Maxima as \( y_x \).

* Extra brackets are sometimes produced around the differential.
* You must have `simp:true` otherwise the display routines will not work.

## Manipulating ODEs in Maxima

This can be solved with Maxima's `ode2` command and initial conditions specified.  Below is an example of Maxima's output.

    (%i1) ODE: x^2*'diff(y,x) + 3*y*x = sin(x)/x;
                          2 dy           sin(x)
    (%o1)                x  -- + 3 x y = ------
                            dx             x
    (%i2) ode2(ODE,y,x);
                                 %c - cos(x)
    (%o2)                    y = -----------
                                      3
                                     x
    (%i3) ic1(%o2,x=%pi,y=0);
                                  cos(x) + 1
    (%o3)                   y = - ----------
                                       3
                                      x

Further examples and documentation are given in the [Maxima manual](http://maxima.sourceforge.net/docs/manual/en/maxima_22.html#SEC81)

Note that by default STACK changes the value of Maxima's `logabs` variable.  This changes the way \(1/x\) is integrated.  If you want the default behaviour of Maxima you will need to restore `logabs:false` in the question variables.

### Laplace Transforms ###

Constant coefficient ODEs can also be manipulated in STACK using Laplace Transforms. An example of a second-order constant coefficient differential equation is given below with initial conditions set and the result of the Laplace Transform is stored.

    ode: 5*'diff(x(t),t,2)-4*'diff(x(t),t)+7*x(t)=0;
    sol: solve(laplace(ode,t,s), 'laplace(x(t), t, s));
    sol: rhs(sol[1]);
    sol: subst([x(0)=-1,diff(x(t), t)=0],sol);

The `laplace` command will Laplace Transform the ode (more information in maxima docs [here](https://maxima.sourceforge.io/docs/manual/maxima_104.html#index-laplace)), but it will still be in terms of the Laplace Transform of `x(t)`, which is symbolic. The `solve` command then solves the algebraic equation for this symbolic Laplace Transformed function, and on the right-hand side of the equals sign, the desired answer is obtained using the `rhs` command. Lastly, the initial conditions need to be specified for `x(t)`. The Laplace Transform symbolically specifies values for `x(0)` and `x'(0)` and these can be replaced with the `subst` command as shown above.

## Randomly generating ODE problems ##

When randomly generating questions we could easily generate an ODE which cannot be solved in closed form, so that in particular using ode2 may be problematic.
It is much better when setting any kind of STACK question to start with the method and work backwards to generate the question.
This ensures the question remains valid over a whole range of parameters.
It also provides many intermediate steps which are useful for a worked solution.

### % characters from solve and ode2 {#Solve_and_ode2}

Maxima functions such as `solve` and `ode2` add arbitrary constants, such as constants of integration.  In Maxima these are indicated adding constants which begin with percentage characters.  For example,

    assume(x>0);
    eq1:x^2*'diff(y,x) + 3*y*x = sin(x)/x;
    sol:ode2(eq1,y,x);

results in

    y = (%c-cos(x))/x^3;

Notice the `%c` in this example. We need a function to strip out the variables starting with `%`, especially as these are sometimes numbered and we want to use a definite letter, or sequence for the constants.

The function `stack_strip_percent(ex,var)` replaces all variable names  starting with `%` with those in `var`.
There are two ways to use this.

1. if `var` is a list then take the variables in the list in order.
2. if `var` is a variable name, then Maxima returns unevaluated list entries,

For example

    stack_strip_percent(y = (%c-cos(x))/x^3,k);

returns

    y = (k[1]-cos(x))/x^3;

This is displayed in STACK using subscripts, which is natural.
The unevaluated list method also does not need to know how many % signs appear in the expression.
The other usage is to provide explicit names for each variable, but the list must be longer than the number of constants in `ex`, e.g.

    stack_strip_percent(y = (%c-cos(x))/x^3,[c1,c2]);

which returns

    y = (c1-cos(x))/x^3;

The following example question variables can be used within STACK.

    assume(x>0);
    ode : x^2*'diff(y,x) + 3*y*x = sin(x)/x;
    sol : stack_strip_percent(ode2(ode,y,x),[k]);
    ta  : rhs(ev(sol,nouns));

Note, you may need to use the Option "assume positive" to get ODE to evaluate the integrals formally and hence "solve correctly".

If you need to create a list of numbered variables use

    vars0:stack_var_makelist(k, 5);
    vars1:rest(stack_var_makelist(k, 6));

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

To find which constants are present in an expression use Maxima's [`listofvars`](http://maxima.sourceforge.net/docs/manual/en/maxima_6.html#IDX163|) command.
In particular, to find if `c` appears in an expression `ans` we can use the predicate `member`

    member(c,listofvars(ans))

However, it is unusual to want to specify the name of a constant.  A student may choose another name.  The example below may be helpful here.

Sometimes students use the \(\pm\) operator, e.g. instead of typing in \( Ae^{\lambda t} \) they type in \( \pm Ae^{\lambda_1 t} \) as `+-A*e^(lambda*t)`.  The \(\pm\) has a somewhat ambiguous status in mathematics, but it is likely that many people will want to condone its use here.

Internally, the \(\pm\) operator is represented with an infix (or prefix) operation `#pm#`, which is part of STACK but not core Maxima.  Instead of `a+-b` teachers must type `a#pm#b`.  Students' answers get translated into this format.
Mostly when dealing with expressions you need to remove the \(\pm\) operator.  To remove the \(\pm\) operator STACK provides the function `pm_replace(ex)` which performs the re-write rules
\[ a\pm b \rightarrow (a+b) \vee (a-b) \]
\[ \pm a \rightarrow a \vee -a \]
(actually using STACK's `nounor` operator to prevent evaluation).

If you simply want to implement the re-write rule \( a\pm b \rightarrow a+b, \) i.e. ignore the \(\pm\) operator, then you can use `subst( "+","#pm#", ex)`.  For example, this substitution can be done in the feedback variables on a student's answer.  If you would like to test code offline with `#pm#` then you will need to make use of the [Maxima sandbox](../CAS/STACK-Maxima_sandbox.md).

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

\[ p(x,y)\cdot \dot{y}(x) + q(x,y) =0.\]

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

## See also

[Maxima reference topics](index.md#reference) 
