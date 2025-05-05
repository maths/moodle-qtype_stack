# Differential Equations

## Question Variables

This page provides examples of how to manipulate ordinary differential equations (ODEs) in [Maxima](../../CAS/Maxima_background.md) when writing STACK questions.

### Manipulating ODEs in Maxima

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

#### Laplace Transforms ####

Constant coefficient ODEs can also be manipulated in STACK using Laplace Transforms. An example of a second-order constant coefficient differential equation is given below with initial conditions set and the result of the Laplace Transform is stored.

    ode: 5*'diff(x(t),t,2)-4*'diff(x(t),t)+7*x(t)=0;
    sol: solve(laplace(ode,t,s), 'laplace(x(t), t, s));
    sol: rhs(sol[1]);
    sol: subst([x(0)=-1,diff(x(t), t)=0],sol);

The `laplace` command will Laplace Transform the ode (more information in [maxima docs](https://maxima.sourceforge.io/docs/manual/maxima_104.html#index-laplace)), but it will still be in terms of the Laplace Transform of `x(t)`, which is symbolic. The `solve` command then solves the algebraic equation for this symbolic Laplace Transformed function, and on the right-hand side of the equals sign, the desired answer is obtained using the `rhs` command. Lastly, the initial conditions need to be specified for `x(t)`. The Laplace Transform symbolically specifies values for `x(0)` and `x'(0)` and these can be replaced with the `subst` command as shown above.

### Randomly generating ODE problems ###

When randomly generating questions we could easily generate an ODE which cannot be solved in closed form, so that in particular using ode2 may be problematic.
It is much better when setting any kind of STACK question to start with the method and work backwards to generate the question.
This ensures the question remains valid over a whole range of parameters.
It also provides many intermediate steps which are useful for a worked solution.

#### % characters from solve and ode2 {#Solve_and_ode2}

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

## Next

- [Assessing solutions to differeniial equations](../../Topics/Differential_equations/Assessing_Responses.md)

