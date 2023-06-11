# Vector Calculus in STACK

Setting vector calculus problems using STACK does not require too much more thinking than setting regular calculus or linear algebra problems. There are a few extra additions, but a passing familiarity with Maxima's notation for derivatives, integrals and vectors will usually suffice.

# Calculus with more than one variable

To find derivatives of functions of more than one variable you can use the normal `diff(f,x)` function, which returns the partial derivative of \(f\) with respect to \(x\). To find the gradient vector, you might choose to put all the partial derivatives in a list or matrix using something like

    grad(f,vars):= block(
      grad_vec: makelist(diff(f,vars[ii]),ii,1,length(vars)),
      return(transpose(matrix(grad_vec)))
    );

so that `grad(f,[x,y,z])` would return \(\nabla f\) as the column vector \(\begin{bmatrix}f_x \\ f_y \\ f_z\end{bmatrix}\).

If you would like to work with differentials, `diff(f)` without specifying a variable will return `del(f)` or \(\mathrm{d}f\). For example, 

    (%i1) f: x*y*z;
    (%o1) x y z;
    (%i2) diff(f);
    (%o2) x y del(z) + x z del(y) + y z del(x)
    
The differentials will display as \(d(x)\), so if you prefer notation such as \(\mathrm{d}x\) you might need to use some workarounds like `df: subst([del(x)=dx,del(y)=dy,del(z)=dz],diff(f))`.

Additionally, you might like to use texput to display the differential operator similarly to how STACK will display derivatives. Some options would include

    texput(dx,"\\mathrm{d}x");
    texput(d,lambda([ex],sconcat("\\mathrm{d}",tex1(first(ex)))));
    
Where the former simply displays the two-letter variable `dx` with the given TeX, and the latter will do the same for any variable written as `d(x)`. 

If you need to assert that one or more variables are constant, you can use `declare([a,b,c],constant)` means that, using the previous example, `diff(f*c)` would return `c x y del(z) + c x z del(y) + c y z del(x)` rather than `c x y del(z) + c x z del(y) + c y z del(x) + x y z del(c)`.

If you want to assert that one variable depends on the other, you can use `depends([f,g],[x,y])` to mean that f and g both depend on x and y. This is useful when asking students to perform implicit partial differentiation and solve for the given derivative. Here is an example that wants students to find a partial derivative of one variable with respect to another, holding another variable constant: 

    declare([a,b,c,d],constant);
    eqn1: x*y = a*t^bb*z;
    eqn2: c*z*y^d*t = sin(x);
    [eqn1, eqn2]: random_permutation([eqn1, eqn2]);

    [dep1,dep2,indep,const]: random_permutation([x, y, z, t]);
    depends([dep1,dep2],indep);

    eqn1d: diff(eqn1,indep);
    eqn2d: diff(eqn2,indep);

    ta: rhs(flatten(solve([eqn1d,eqn2d],['diff(dep1,indep),'diff(dep2,indep)]))[1]);
    
This example generates two equations in x, y, z and t, assigns each variable a role as either a dependent variable, independent variable, or a variable being held constant (also independent, but behaving differently in this context), differentiates both equations implicitly with respect to the independent variable, and then solves the equations simultaneously for the desired derivative.

Note that these questions can be a bit volatile, as Maxima simply cannot establish equality when various substitutions are made. 

# Using vectors

Maxima does not distinguish between vectors and matrices, with both being defined using the `matrix` command. You can put whatever you like in a matrix, so like the example above with a gradient vector, you can easily define a vector function as a matrix with expressions as elements. Some key points: 

1. Lists are interpreted as row-vectors (which means that things like the dot product operator work on them). It also means that it is sufficient to use `transpose([a,b,c])` to get a column vector.
2. `*` is used for element-wise multiplication of lists and matrices, whereas `.` is used as both a dot and product. This leads to slightly confusing behaviour for vectors, where `[x,y] . [z,t]`, `[x,y] . transpose([z,t])` and `transpose([x,y]) . transpose([z,t])` will all return `x*z + y*t`, but `transpose([x,y]) . [z,t]` will return the outer product matrix `matrix([x*z, t*x], [y*z, t*y])`.
3. Matrices are indexed as `A[i,j]`, which means that when dealing with vectors you must be careful to index both the row and the column. For example, `matrix([1],[2],[3])[1]` will return the list `[1]` rather than the value `1`, and `matrix([1,2,3])[1]` will return the whole row `[1,2,3]`. One way around this is to use the `list_matrix_entries` function to return the list of all matrix entries, which is often easier to work with when considering vectors. 
4. The function `crossproduct(a,b)` is defined, but must take two 3 by 1 column vectors as the input, not lists or row vectors. 
5. The function `jacobian([f1,f2],[x,y])` is defined, and takes two lists as arguments. The first is the vector function, and the second is the list of variables. 

Some useful functions that are _not_ defined are divergence and curl. Luckily, these are relatively easy to make.

    div(u,vars):= block(
      funcs: flatten(makelist(u[ii],ii,1,length(vars))),
      divList: makelist(diff(funcs[ii],vars[ii]),ii,1,length(vars)),
      return(apply("+",divList))
    );
    
which takes a vector function u and a list of variables vars and returns the divergence of u. It will accept u as either a matrix or a list.

    curl(u,vars):= block(
      [ux,uy,uz]: flatten(makelist(u[ii],ii,1,3)),
      cux: diff(uz,vars[2]) - diff(uy,vars[3]),
      cuy: diff(ux,vars[3]) - diff(uz,vars[1]),
      cuz: diff(uy,vars[1]) - diff(ux,vars[2]),
      return(transpose(matrix([cux,cuy,cuz])))
    );
    
which is similar but for curl. **Note: should these be core STACK functionality? **

# TODO: Chain Rule, Line Integrals, Lagrange Multipliers and VECTOR PLOTS
