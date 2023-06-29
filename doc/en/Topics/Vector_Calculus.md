# Vector Calculus in STACK

Setting vector calculus problems using STACK does not require too much more thinking than setting regular calculus or linear algebra problems. There are a few extra additions, but a passing familiarity with Maxima's notation for derivatives, integrals and vectors will usually suffice.

# Calculus with more than one variable

To find derivatives of functions of more than one variable you can use the normal `diff(f,x)` function, which returns the partial derivative of \(f\) with respect to \(x\). To find the gradient vector, you might choose to put all the partial derivatives in a list or matrix using something like

    grad(f,vars):= block(
      grad_vec: map(lambda([ex], 'diff(f,vars[ex])), ev(makelist(ii,ii,1,length(vars)), simp)),
      return(transpose(matrix(grad_vec)))
    );

Then `grad(f,[x,y,z])` would return
\[
 \left[\begin{array}{c} \frac{\mathrm{d} f}{\mathrm{d} x} \\ \frac{\mathrm{d} f}{\mathrm{d} y} \\ \frac{\mathrm{d} f}{\mathrm{d} z} \end{array}\right]
\]
Setting Maxima's `derivabbrev:true` changes the display of derivatives to subscripts.  Now `grad(f,[x,y,z])` would return
\[
\left[\begin{array}{c} f_{x} \\ f_{y} \\ f_{z} \end{array}\right]
\]

If you would like to work with differentials, using `diff(f)` without specifying a variable will return `del(f)`. For example, 

    (%i1) f: x*y*z;
    (%o1) x y z;
    (%i2) diff(f);
    (%o2) x y del(z) + x z del(y) + y z del(x)

The Maxima differentials will display as \(d(x)\).  If you prefer notation such as \(\mathrm{d}x\) you might like to redfine the tex function associated with `del` e.g.

    texput(del,lambda([ex],sconcat("\\mathrm{d}",tex1(first(ex)))));

If you need to assert that one or more variables are constant, you can use `declare([a,b,c],constant)` means that, using the previous example, `diff(f*c)` would return `c x y del(z) + c x z del(y) + c y z del(x)` rather than `c x y del(z) + c x z del(y) + c y z del(x) + x y z del(c)`.

If you want to assert that one variable depends on the other, you can use `depends([f,g],[x,y])` to mean that \(f\) and \(g\) both depend on \(x\) and \(y\). If you just want to note that \(y\) depends on \(x\), then `depends(y,x)` is sufficient. This is useful when asking students to perform implicit partial differentiation and solve for the given derivative. Here is an example that wants students to find a partial derivative of one variable with respect to another, holding another variable constant: 

    declare([a,b,c,d],constant);
    eqn1: x*y = a*t^b*z;
    eqn2: c*z*y^d*t = sin(x);
    [eqn1, eqn2]: random_permutation([eqn1, eqn2]);

    [dep1,dep2,indep,const]: random_permutation([x, y, z, t]);
    depends([dep1,dep2],indep);

    eqn1d: diff(eqn1,indep);
    eqn2d: diff(eqn2,indep);

    ta: rhs(flatten(solve([eqn1d,eqn2d],['diff(dep1,indep),'diff(dep2,indep)]))[1]);
 
This example generates two equations in \(x\), \(y\), \(z\) and \(t\), assigns each variable a role as either a dependent variable, independent variable, or a variable being held constant (also independent, but behaving differently in this context), differentiates both equations implicitly with respect to the independent variable, and then solves the equations simultaneously for the desired derivative. The original question had values assigned to \(a\), \(b\), \(c\) and \(d\), but it is interesting to declare them constant here. 

Note that these questions can be a bit volatile, as Maxima simply cannot establish equality when various substitutions are made. It may be prudent to make rearrangement difficult, and/or tell students to not substitute values in their answer. 

# Using vectors

Maxima does not distinguish between vectors and matrices, with both being defined using the `matrix` command. You can put whatever you like in a matrix, so like the example above with a gradient vector, you can easily define a vector function as a matrix with expressions as elements. Some key points: 

1. Lists are treated very similarly to row vectors (which means that things like the dot product operator work on them). It also means that it is sufficient to use `transpose([a,b,c])` to get a column vector.
2. `*` is used for element-wise multiplication of lists and matrices, whereas `.` is used as both a dot product and matrix product. This leads to slightly confusing behaviour for vectors, where `[x,y] . [z,t]`, `[x,y] . transpose([z,t])` and `transpose([x,y]) . transpose([z,t])` will all return `x*z + y*t`, but `transpose([x,y]) . [z,t]` will return the outer product matrix `matrix([x*z, t*x], [y*z, t*y])`.
3. Matrices are indexed as `A[i,j]`, which means that when dealing with vectors you must be careful to index both the row and the column. For example, `matrix([1],[2],[3])[1]` will return the list `[1]` rather than the value `1`, and `matrix([1,2,3])[1]` will return the whole row `[1,2,3]`. One way around this is to use the `list_matrix_entries` function to return the list of all matrix entries, which is often easier to work with when considering vectors. Note that this function returns an error when given a list. 
4. The function `crossproduct(a,b)` is defined in STACK (not core Maxima), but must take two \(3\) by \(1\) column vectors as the input, not lists or row vectors. 
5. The function `jacobian([f1,f2],[x,y])` is defined, and takes two lists as arguments. The first is the vector function, and the second is the list of variables. 

Some useful functions that are _not_ defined in core Maxima are defined in STACK's contributed `vectorcalculus.mac` file in the contrib directory.

1. divergence `div(u, vars)`. Accepts \(u\) as either a matrix or a list.
2. curl `curl(u, vars)`.  Accepts \(u\) as either a matrix or a list.

These functions can be [included](../Authoring/Inclusions.md) with `stack_include`.

# Lagrange Multipliers

Finding critical points using the method of Lagrange multipliers is as simple as finding the stationary points to the system of equations given by \(f - \lambda c\) where \(f\) is the multivariable function you are investigating and \(c\) is a constraint curve. There are a few new considerations that come along with questions like this, such as what input type to use and how to account for a variable number of solutions.  

Here is some example code that shows how you might procedurally generate solutions to a problem like this, where `func` is our function \(f\) and `cons` is our constraint curve \(c\) given as a single algebraic expression to be set to zero. 

    equations: [diff(func, x) = L*diff(cons, x), diff(func, y) = L*diff(cons, y), cons = 0];
    solns: map(lambda([ex],map(rhs,firstn(radcan(ex),2))),solve(equations, [x, y, L]));
    ta: sublist(solns,lambda([ex],real_numberp(first(ex)) and real_numberp(second(ex))));

This code will generate all correct solutions, trim out the value of \(\lambda\) which typically does not concern us, removes any complex solutions, and returns the final answer as a list of ordered pairs. This is useful because it allows us to use the TextArea input type. To enter their answers, students can give as many or as ordered pairs as they like.

To mark this question, we could use some code like the following:

    ans1_set: setify(map(simplify, ans1));
    ta_set: setify(map(simplify, ta));

    num_correct: length(intersection(ans1_set, ta_set));
    prop_correct: num_correct/length(ta);
    
Now we can compare the two sets directly in the PRT using AlgEquiv or Sets. If this test fails, we might like to award `prop_correct` marks as the score (so that if a student found 3 out of 4 critical points they would be awarded 0.75 marks). We might also find it prudent to award a flat penalty for any superfluous answers in this case too, or perhaps only if the student gives more answers than expected (to avoid double-penalising typos), though this is now a discussion of grading. 

# Vector Plots

Newer versions of JSXGraph provide support for vector plots natively, but STACK currently does not use this version of JSXGraph. However, it is not too difficult to create a bespoke vector plot function that can be adapted to a variety of circumstances.

There are several considerations when creating a vector plot:

1) How densely packed should the vectors be? 
2) How long should the vectors be, and how much variation in length should there be? 
3) What aesthetic choices are important to aid readability? 
4) How should function information be passed from Maxima to JSXGraph?

There is no single answer to these, and your answer will likely differ per question. Below is one approach to creating a vector plot question. Note that it assumes you are working with a plaintext editor and so `<`, `>`, and `&&` are all viable options. If you work in a different editor (such as a the Moodle default) you may like to define your own functions to prevent conversion to `&lt;`, `&gt;` or `&amp;` upon saving. 

Let us first assume a general vector function, called `F`. In the question variables, we could define `fx` and `fy` and then define `F: [fx,fy]`. If we were wanting to instead plot a gradient, we could define a function `f` and then define its gradient as `F: [diff(f,x),diff(f,y)]`. We will also need a version of `F` that uses valid JavaScript syntax. You might like to convert manually using something like `fx_jsx: ssubst("**","^",string(fx))`, though this relies on you knowing the form of the function and can cause problems with the unary minus. A better option could be to use [JXG.JessieCode](https://jsxgraph.org/docs/symbols/JXG.JessieCode.html). Lastly, we want a JavaScript function to output the direction at a given point. Something like the the following suffices.

    var F = function(x,y) { return [{@fx_jsx@},{@fy_jsx@}] };

Next, we want to know where we are plotting. Keeping everything in terms of the plotting axes and number of vectors will prove convenient. If we choose the number of vectors in the x-direction to be `num_x` and similarly for y to be `num_y`, we can set up the following: 

    var xi = -2, yf = 2, xf = 2, yi = -2
    var axes = [xi, yf, xf, yi]
    var num_x = 20, num_y = 20
    var dx = 1/num_x*(xf - xi)
    var dy = 1/num_y*(yf - yi)
    
Our goal will be to plot vectors at every grid point defined by these steps in x and y called `dx` and `dy`.

Before we iterate through this, we need to define the function that will actually plot the vectors.

    var createVector = function(px, py) {
        var vec = F(px,py) /* Compute the value of the function here */
        var vecScaler = 1/Math.sqrt(vec[0]**2 + vec[1]**2)
        var plotVec = [dx*vec[0]*vecScaler, dy*vec[1]*vecScaler]
        var tip = [px + plotVec[0], py + plotVec[1]]
        board.create('arrow',[[px,py],tip],{
            color: "#1f77b4", opacity: 0.66, lastArrow: {type: 2, size: 4},
            fixed: true, highlight: false
        });
        board.create('point',[px,py],{
            color: "#1f77b4", size: 0.1, strokeOpacity: 0.66, fillOpacity: 0.75,
            highlight: false, fixed: true, withLabel: false
        });
    }
    
A couple of things to note: Firstly, the variable `vecScaler` is always equal to the reciprocal of the length of the vector, which means that in this example it will always plot a "unit" vector (where a unit here is the grid size). This can (and perhaps should) be changed unless you wish to use colour to denote different vector length (which is poor accessibility practice on its own). Secondly, these vectors are eminating from the grid point, which is plotted itself with a transparent dot. In fact, everything is partially transparent to avoid excessive busyness in the plot. You could easily set this up to instead plot with the centre of the arrow being the location, or could tweak any of the other visual settings. In general, it is good to indicate where the vector originates for clarity. 

Now we can finally plot the vectors. 

    var i, j;  
    for (i = xi/dx - 1; i <= xf/dx + 1; i++) {
      for (j = yi/dy - 1; j <= yf/dy + 1; j++) {
        createArrow(i*dx,j*dy);
      }
    }
    
Note that the vectors are being plotted from just outside the desired area so that they can enter the plot from outside if needed. Now we have a vector plot! 

The scaling factor `vecScaler` is the last thing to consider seriously. It is often important to distinguish between different vector lengths, but just setting `vecScaler = 1` will usually render the plot illegible with huge vectors shooting all over the place. There is no shortage of ways to scale, but one way the author has found helpful is to use a sigmoidal curve like \(\tan^{-1}(x)\). Here is one option: 

    var myScaler = function(vec,a,b) { return a*(Math.PI/2 - Math.atan(a/b*Math.sqrt(vec[0]**2 + vec[1]**2))) }
    
for some tuning constants a and b. Note that this function produces a multiplying factor, not the desired length directly. b is the maximum length of the vector relative to the grid size, which is often set to either 1 or 1.414 (the former being good for vectors that tend to point along gridlines and the latter being good for vectors that tend to point in the direction of the diagonals, or if you like a little bit of overlap). a is a measure of how fast the vector reaches the maximum length. You could tweak the values of a and b for each question, or you could pair up functions and scaling factors in the question variables when randomising. Regardless, it might be a good idea to copy-paste the graphing code into the question note so that you can check the scaling when deploying variants.
