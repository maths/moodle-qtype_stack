# Randomisation in linear algebra

This page is a discussion of randomisation when setting linear algebra problems. This can be a particularly difficult problem to tackle in this subject: matrices have many degrees of freedom, and problems that seem very similar at a glance can have dramatically different solutions. On top of that, we often need to choose between posing a problem that contains "nice" numbers and requiring an answer that contain "nice" numbers; both at once is difficult or impossible in general! 

There are two tools that this article will use liberally: 

- The [`rand_matrix.mac`](Random_Matrices.md) library. This contains many functions that can be used to generate random matrices with defined structure or with defined properties.
- The `scale_nicely` function. This takes a list or vector and returns the "simplest" list or vector that is parallel to the input. This was originally designed with basis problems in mind (i.e. it is often preferable to use \((2,1,1)\) than \((1,1,0.5)\)), but we will use it in more ways than just this.

## Principles

### Should we even care about "nice" numbers?

The rest of this discussion is focused on _how_ to generate certain problems and not whether it is actually a good idea. As teachers we need to carefully balance the degree to which we present learners with accessible, clear problems, and the degree to which we present students with realistic problems. It is not reasonable for a student to come away from a linear algebra course with the perception that matrix factorisations or solution to linear systems will always have integer values, or that any unit vectors they come across will usually have many zeros and a scaling factor smaller than \(\sqrt{10}\). However, if their first exposure to these concepts requires most of the brainpower to tackle difficult computation (or requires the use of calculators to manage them), then they will likely struggle to identify any patterns or symmetries, or to develop their intuition more than they otherwise would. 

Therefore, this article is intended to give you, the teacher, some tools to develop "nice" questions that allow your student to work through the processes without the distraction of challenging arithmetic. Please consider also ignoring all these tools once your students have developed some level of expertise and giving them something nastier!

### Variants

STACK provides us with an incredibly powerful randomisation tool in the deployed variants identified by the question note. All of the strategies below can still sometimes create very challenging arithmetic, especially when the "level of complexity" input into the `rand_matrix` functions is higher. Even more commonly, trivial examples can appear. Thus, it is extremely important to write meaningful question notes and filter out any variants that are still inappropriate. In some cases you may wish to include some or all of the expected student working in the question note so that you can check that intermediate steps maintain "nice _enough_" numbers. The author finds it helpful to deploy many variants, copy the handful of seeds that generate the best variants into a separate notepad, and then utilise the "undeploy all and redeploy from list" functionality to filter the list in bulk. 

Another less elegant but highly functional strategy to any of those below is to brute force randomisation ahead of time. For instance, suppose you wanted a \(2\times 2\) matrix that has no zeros and a determinant of \(\pm 1\). Running the following code, e.g. in the maxima sandbox, will produce such a list of matrices very inefficiently: 

    stack_include_contrib("rand_matrix.mac");
    mats: {};
    for ii: 1 thru 1000 do block([mat, _good],
      mat: rand_matrix(2,2,10),
      _good: is(abs(determinant(mat)) = 1),
      _good: _good and every(lambda([ex], not(is(ex=0))),list_matrix_entries(mat)),
      if _good then mats: union(mats, {mat})
    );

If `mats` is formatted appropriately, perhaps with `{#mats#}` in question text somewhere (or in the CASchat), this list can be copied and pasted into another question. Then you can select `A: rand(mats)`. This was very inefficient one time (and could have been achieved other ways without much difficulty), but is extremely efficient when deploying variants and doesn't require too much creativity. 

### Check every random choice before you deploy

Random constructions usually give a good matrix, but "usually" can be weaker than it looks. For example, take \(A = PDP^{-1}\) with `P: rand_integer_invertible(n,1)` and \(D\) diagonal with distinct non-zero integer eigenvalues from \(-3\) to \(4\). Over 200 draws, the share of matrices with every entry at most \(10\) in absolute value was 97.5% for \(2\times 2\), 77% for \(3\times 3\) and 48.5% for \(4\times 4\), and the largest entries seen were \(14\), \(42\) and \(71\). For \(2\times 2\) matrices, \(P\) was the identity in 8 draws out of 200, so \(A\) was already diagonal and there was nothing to do.

The best remedy is to choose the random values so that every choice is good, and to check every combination in the sandbox before you deploy. Build backwards from a short list of good cases, for example an invertible \(P\) with determinant \(1\) taken from a list, so that \(A\) always has integer entries:

    P: rand([matrix([1,1,0],[0,1,1],[0,0,1]), matrix([1,0,0],[1,1,0],[0,1,1]), matrix([1,0,1],[0,1,0],[1,1,2])]);
    d: rand_selection([-3,-2,-1,1,2,3], 3);
    A: P . apply(diag_matrix, d) . P^^-1;

Here there are \(3 \times 120 = 360\) combinations, few enough to check them all in the sandbox: every one gives distinct eigenvalues, and the largest entry of \(A\) is \(12\). If the good cases are hard to construct, generate and check them in advance, as in the brute-force example above, and pick from that list with `rand`.

#### A last resort: draw, check, fall back

Only when neither is practical, the check can be made inside the question variables, so that every seed is checked, not only the ones you deploy. Avoid this if you can: the loop runs every time a variant is generated, and it can make the question variables slow or time out. It has three steps:

1. **Draw** a candidate in the usual way.
2. **Check** it with a test that says what the question needs.
3. **Repeat** a fixed number of times, and **fall back** to a choice you have checked yourself if no candidate passes.

The number of attempts must be bounded: the question variables have to finish inside the CAS time limit, and an unbounded loop with a strict test may never stop. The following produces a \(3\times 3\) integer matrix with distinct eigenvalues and small entries.

    stack_include_contrib("rand_matrix.mac");
    d: rand_selection([-3,-2,-1,1,2,3], 3);
    D: apply(diag_matrix, d);
    /* What this question needs from P. */
    nice(P) := block([A, e],
      A: P . D . P^^-1,
      e: list_matrix_entries(A),
      is(lmax(map(abs, e)) <= 10)                            /* small entries */
      and is(length(sublist(e, lambda([x], is(x = 0)))) <= 3) /* not nearly diagonal */
      and is(A # P^^-1 . D . P)                               /* a wrong method gives a different matrix */
    );
    P: false;
    for k: 1 thru 20 while is(P = false) do block([Q],
      Q: rand_integer_invertible(3, 1),
      if nice(Q) then P: Q
    );
    fallback: is(P = false);
    if fallback then P: matrix([1,1,0],[0,1,1],[1,1,1]);
    A: P . D . P^^-1;

Over 200 seeds the first draw passed 154 times (77%), no seed needed more than five draws, and none used the fallback. The variants that would otherwise have been rejected by hand are simply never seen.

Some advice on writing the test:

- _Check the working, not only the answer._ Here the entries of \(A\) are what the student works with. If the intermediate steps matter, as in row reduction, test those too.
- _Rule out accidents._ A random \(P\) can be the identity, \(A\) can have so many zeros that the problem falls apart, and two random eigenvalues can coincide. Decide which of these would spoil the question and test for them.
- _Make sure misconceptions give different answers._ If the feedback recognises a common mistake, such as computing \(P^{-1}DP\) instead of \(PDP^{-1}\), the mistake must give a different answer from the correct method in every variant. Otherwise a student using the wrong method gets full marks, and the feedback cannot tell the two apart. The third condition in `nice` tests this.
- _Check the fallback._ The fallback must pass the same test for every value the other random parts can take. Because the test is a function, you can run it in the sandbox on the fallback with every choice of `d`: here all 120 choices pass, and the largest entry is \(8\).
- _Record the fallback in the question note_, for example with `{@fallback@}`, so that you can see which variants used it.

## Linear systems with given fundamental subspaces

It is simple to construct a matrix with a given row space and column space. Let \(R\) be a matrix whose rows form a basis for the desired row space and let \(C\) be a matrix whose columns form a basis for the desired column space. Then \(A = CR\) will have the desired fundamental subspaces. This also allows us to create a matrix with whichever rank we like. Indeed, we can force certain rows or columns to be dependent on each other by forcing the relevant rows of \(C\) or columns of \(R\) to be linearly dependent. This can be useful when asking students to find bases for the row or column space of a matrix and we don't want them to just pick the first \(r\) rows or columns. 

Consider a case where we want a \(4\times 5\) matrix of rank \(r=3\) whose pivot columns are in position \(1\), \(3\) and \(5\) and whose pivot rows are \(1\), \(3\) and \(4\). All of these numbers could be randomised (with \(r\leq\operatorname{min}(m,n)\)). A possible way to generate such a matrix is as follows: 

    [m,n,r]: [4,5,3];
    C: args(rand_invertible(r,1));
    C: insert(C[1]*rand([-2,-1,1,2]), C, 2);
    C: apply(matrix, C);
    R: matrix_to_cols(ident(r));
    R: insert(R[1]*rand([-2,-1,1,2]), R, 2);
    R: insert(R[1]*rand([-2,-1,1,2]) + R[3]*rand([-2,-1,1,2]), R, 4);
    R: cols_to_matrix(R);
    A: ev(C . R,simp);

where `insert` is a custom function `insert(ex, L, n):= append(firstn(L, n-1),[ex],lastn(L, length(L)-n+1));` based on the currently forbidden `linsert` function. 

## Linear systems with "nice" least squares solutions

Once we are able to construct matrices with predetermined column and null space we have full control of the existence and shape of solutions. If we want students to find a "nice" exact solution with a given "nice" system, we simply generate the solution \(\underline{\mathbf{x}}\) and set \(\underline{\mathbf{b}} = A\underline{\mathbf{x}}\). Note that if we want a unique solution we must select \(n = r\).

If, instead, we want a least squares (but not exact) solution, we can add a "nice" vector from the left null space to \(\underline{\mathbf{b}}\). Then the previously selected \(\underline{\mathbf{x}}\) will be a least squares solution. For example, with the same \(A\) as above: 

    x: transpose(rand_selection([-1,-1,0,0,1,1,1,2,2],n));
    b: A . x + scale_nicely(first(nullspace(transpose(C))));
    x_general: x + [t, s] . map(scale_nicely,args(nullspace(R)));

Note that this last step optionally gives the _general_ least squares solution. This was hard-coded for the particular shape and rank of this problem, but these could also be randomised. 

It is still possible that the intermediate steps deviate through some less "nice" numbers, but with a carefully written question note you can usually catch these variants and remove them. 

## LU decomposition

To generate a "nice" \(LU\) decomposition it is best to generate the factors first and then compute \(A = LU\). Using `rand_matrix.mac` this is simple; `L: rand_tril(n,n,k)` and `U:  rand_triu(n,n,k)`. Using `L: setdiagmx(1, L)` from `matrix.mac` is also helpful here. 

Including pivoting is a bit more challenging. You can generate a random \(P\) using `rand_perm_matrix(n)`, but finding a "nice" matrix that requires a specific pivoting strategy takes some work.

Let's look at partial pivoting, in which we want to swap the largest possible value into the pivot position each time. This directly implies that all entries in \(L\) are between \(-1\) and \(1\) (inclusive), with the choice of pivot row forced if all below-diagonal entries are strictly between these values. Suppose that we want to require the maximum amount of pivoting, so we pick a permutation of order \(n-1\). After all this, \(U\) can be chosen freely, but will typically have non-integer entries. The following code is one attempt to force integer entries in a \(4\times 4\) case: 

    stack_include_contrib("rand_matrix.mac");
    stack_include_contrib("matrix.mac");
    P: rand([
      matrix([0,1,0,0],[0,0,1,0],[0,0,0,1],[1,0,0,0]),
      matrix([0,1,0,0],[0,0,0,1],[1,0,0,0],[0,0,1,0]),
      matrix([0,0,1,0],[0,0,0,1],[0,1,0,0],[1,0,0,0])
    ]);
    P: rand([P, transpose(P)]);
    L: rand_tril_list(4,4,[1/2,0,-1/2]);
    L: setdiagmx(1, L);
    U: rand_triu_list(4,4,[-1,1]);
    f: apply(lcm,map(denom,L[4]));
    U[3]: U[3]*f;
    f: apply(lcm, append([f], map(denom,L[3])));
    U[2]: U[2]*f;
    f: apply(lcm, append([f], map(denom,L[2])));
    U[1]: U[1]*f;
    A: transpose(P) . L . U;

Note that the sub-diagonal entries were restricted to being \(0\) or  \(\pm\frac{1}{2}\) here to keep the values small. This list can be extended, but each unique denominator introduced will likely cause the average entry in \(U\) to grow. The resulting matrix \(A\) will have integer entries and each row will have a unique largest pivot at each step. Nice! 

## QR Factorisation

It is not very reasonable to assume that a \(QR\) factorisation will have truly "nice" numbers due to the requirement that \(Q\) has columns of unit length. If we're willing to allow surds throughout but still want to keep the numbers small, we could try an approach like the following: 

    stack_include_contrib("rand_matrix.mac");
    Q: rand_orthcols(4,3,1);
    Q: cols_to_matrix(map(scale_nicely,matrix_to_cols(Q)));
    R: rand_triu_list(3,3,[-2,-1,1,2,3]);
    A: Q . R;

This generates an integer matrix \(Q\) with orthogonal (but probably not orthonormal) columns, and an integer upper triangular \(R\). A true \(QR\) factorisation will scale down each column of \(Q\) by its length and scale up the corresponding row of \(R\) by the same. Using `rand_orthcols` with a level of complexity of \(1\) gives a good chance that the original \(Q\) will have quite small entries and thus keep the overall system relatively simple.

To _really_ keep things simple, we can leverage pythagorean triples, quadruples or quintuples. For instance, it is easy to construct a matrix with orthogonal columns using the pythagorean quadruple \((1, 2, 2, 3)\). Then we just ensure that entries in \(R\) are multiples of \(3\) and we are done. Even simpler, we can use \((1,1,1,1,2)\) and ensure that all entries in \(R\) are even: 

    stack_include_contrib("rand_matrix.mac");
    Q3: matrix([2,2,1],[2,-1,-2],[1,-2,2])/3;
    /* Overkill perhaps: */
    Q3: rand_perm_matrix(3) . rand_diag_list(3,3,[1,-1]) . Q3 . rand_diag_list(3,3,[1,-1]) . rand_perm_matrix(3);
    R3: rand_triu_list(3,3,[-6,-3,3,6]);
    
    Q4: matrix([1,1,1,1],[1,-1,1,-1],[1,1,-1,-1],[1,-1,-1,1])/2;
    Q4: rand_perm_matrix(4) . rand_diag_list(4,4,[1,-1]) . Q4 . rand_diag_list(4,4,[1,-1]) . rand_perm_matrix(4);
    R4: rand_triu_list(4,4,[-4,-2,2,4]);

## Eigenvectors and eigenvalues

Two matrices are called _similar_ when their eigenvalues are identical (including the structure of the generalised eigenspaces). Therefore, if we want to prescribe this structure for a matrix, we can start by generating a simple matrix with the eigenvalues we want and then convert it to a similar matrix using a similarity transformation of the form \(A = PMP^{-1}\). The \(P\) matrix here can be anything invertible, but you might find it useful to use `rand_integer_invertible` from the aforementioned library, as (alongside an integer-valued \(M\)), we guarantee that \(A=PMP^{-1}\) is integer-valued.

    P: rand_integer_invertible(4,1)

For instance: 

\[P = \left[\begin{array}{cccc} 1 & -1 & -1 & 1 \\ 0 & 1 & -1 & 1 \\ -1 & 1 & 2 & -1 \\ 1 & -1 & -2 & 2 \end{array}\right]\]

The best option is to use a Jordan matrix for \(M\). In the simplest case, this is simply a diagonal matrix with each diagonal entry being an eigenvalue of \(A\). In the general case, \(A\) may not be similar to a diagonal matrix, and in this case the Jordan matrix is a block diagonal matrix consisting of one or more _Jordan blocks_. A Jordan block is a square matrix whose diagonal is filled with a single, possibly repeated value, and whose super-diagonal is populated by ones. The digit on the diagonal is the eigenvalue of \(M\) and the size of the block is the number of times this eigenvalue is repeated without adding a dimension to the corresponding eigenspace. Note that the diagonal matrix is simply a special case of this where every Jordan block is \(1\times 1\). 

    /* Choose a diagonal matrix */
    D: matrix([1,0,0,0],[0,1,0,0],[0,0,2,0],[0,0,0,-1]);
    A1: P . D . P^^-1;

    /* Choose a matrix that is defective by one degree */
    J: matrix([1,1,0,0],[0,1,0,0],[0,0,2,0],[0,0,0,-1]);
    A2: P . J . P^^-1;

\[A_1 = \left[\begin{array}{rrrr} 0 & 0 & -3 & -2 \\ -1 & 1 & -3 & -2 \\ 2 &  0 & 5 & 2 \\ -2 & 0 & -6 & -3 \end{array}\right]\qquad
  A_2 = \left[\begin{array}{rrrr}  1 & 1 & -3 & -3 \\ -1 & 1 & -3 & -2 \\ 1 & -1 & 5 & 3 \\ -1 & 1 & -6 & -4 \end{array}\right]\]

Diagonal matrices are easy to generate, perhaps using `rand_diag` for a truly random choice, or `diagmatrix_like` with a pre-defined list of eigenvalues. Non-diagonal Jordan matrices are harder to generate, but perhaps something like the following could be used. (Older versions of STACK require `matrix.mac` to be manually included for `setdiagmx`.)  

    L1: rand([-1,1,2,3]);
    M: L1*ident(4);
    M: setdiagmx(rand_selection([0,0,1,1,1],3),M,1);

In the diagonal case, the columns of \(P\) are the eigenvectors of \(A\) and in the non-diagonal case they are the _generalised_ eigenvectors of \(A\). This method of generation gives us no control over the eigenvectors, but if we are willing to either have non-integer matrices \(A\) or a lot of trial and error, we can construct \(P\) manually to give us full control over both the eigenvalues and the eigenvectors. If a low "level of complexity" (the second input to `rand_integer_invertible`) is used when generating \(P\), the matrix tends to be very simple and student working is not too bad. 

In general, Jordan blocks can have non-real eigenvalues on the diagonal. For a random modal matrix \(P\), \(A = PMP^{-1}\) will, with non-real eigenvalues, not be a real matrix. It is common for undergraduate courses to focus on real matrices, so we need a workaround if we want to produce a real matrix with non-real eigenvalues. We can do this by using the _real Jordan form_. We do this by grouping any complex conjugate pairs along the diagonal and then replacing blocks of the form

\[\left[\begin{array}{cc}a+b\mathrm{i} & 0 \\ 0 & a - b\mathrm{i}\end{array}\right] \xrightarrow{\text{becomes}} \left[\begin{array}{rr}a & -b \\ b & a\end{array}\right].\]

We can produce repeated non-real eigenvalues if we repeat this block along the diagonal with an identity matrix above it. An example is provided below to hopefully make this clearer.

    /* Real matrix with non-real eigenvalues */
    D_complex: matrix([2,-1,0,0],[1,2,0,0],[0,0,2,0],[0,0,0,-1]);
    A3: P . D_complex . P^^-1;

    /* Real defective matrix with repeated non-real eigenvalues */
    J_complex: matrix([2,-1,1,0],[1,2,0,1],[0,0,2,-1],[0,0,1,2]);
    A4: P . J_complex . P^^-1;

\[A_3 = \left[\begin{array}{rrrr} -2 & -2 & -3 & 0 \\ 3 & 3 & -3 & -5 \\ 4 & 2 &  5 &  0 \\ -4 & -2 & -6 & -1 \end{array}\right]\qquad
  A_4 = \left[\begin{array}{rrrr}  0 & -2 &  2 & 3 \\ 4 & 3 &  3 &  0 \\ 2 & 2 & -1 & -4 \\ -1 & -2 &  4 &  6 \end{array}\right]\]

Note that the columns of \(P\) no longer represent eigenvectors but instead represent a basis for the invariant subspace that is associated with the pair of eigenvalues. 

## Singular Value Decomposition

"Nice" SVDs are quite challenging to produce. Even simple random matrices have a habit of producing nested square roots: 

\[\left[\begin{array}{cc} 1 & 2 \\ 3 & 4 \end{array}\right] = 
\left[\begin{array}{cc} \frac{11}{\sqrt{20\cdot \sqrt{13}\cdot \sqrt{17}+442}} & -\frac{11}{\sqrt{442-20\cdot \sqrt{13}\cdot \sqrt{17}}} \\ \frac{\sqrt{221}+10}{\sqrt{20\cdot \sqrt{13}\cdot \sqrt{17}+442}} & \frac{\sqrt{221}-10}{\sqrt{442-20\cdot \sqrt{13}\cdot \sqrt{17}}} \end{array}\right]
\left[\begin{array}{cc} \sqrt{\sqrt{221}+15} & 0 \\ 0 & \sqrt{15-\sqrt{221}} \end{array}\right]
\left[\begin{array}{cc} \frac{7\cdot \sqrt{2}}{\sqrt{5\cdot \sqrt{13}\cdot \sqrt{17}+221}} & \frac{\sqrt{221}+5}{\sqrt{2}\cdot \sqrt{5\cdot \sqrt{13}\cdot \sqrt{17}+221}} \\ \frac{7\cdot \sqrt{2}}{\sqrt{221-5\cdot \sqrt{13}\cdot \sqrt{17}}} & -\frac{\sqrt{221}-5}{\sqrt{2}\cdot \sqrt{221-5\cdot \sqrt{13}\cdot \sqrt{17}}} \end{array}\right]\]

As usual, the best way to approach this is to start with the answer and work backwards. However, this is still not simple, as surds have a habit of appearing in all three matrices, and not necessarily in a way that will cancel neatly to produce an integer result. The way around this is to generate the singular vectors first and choose singular values that nicely cancel out the resulting surds. 

    [m, n]: [3, 4]; /* m × n matrix */
    r: 2; /* rank 2 */
    U: rand_orth(m, 1); /*Second input is "level of complexity". We want this to be small. */
    V: rand_orth(n, 1);

Suppose this returned: 

\[U = \left[\begin{array}{ccc} \frac{2}{\sqrt{6}} & \frac{1}{\sqrt{3}} & 0 \\ \frac{1}{\sqrt{6}} & -\frac{1}{\sqrt{3}} & \frac{1}{\sqrt{2}} \\ \frac{1}{\sqrt{6}} & -\frac{1}{\sqrt{3}} & -\frac{1}{\sqrt{2}} \end{array}\right],\qquad
V^T = \left[\begin{array}{cccc} 0 & \frac{1}{\sqrt{3}} & \frac{1}{\sqrt{3}} & -\frac{1}{\sqrt{3}} \\ \frac{1}{\sqrt{3}} & \frac{1}{\sqrt{3}} & -\frac{1}{\sqrt{3}} & 0 \\ \frac{2}{\sqrt{6}} & -\frac{1}{\sqrt{6}} & \frac{1}{\sqrt{6}} & 0 \\ 0 & \frac{1}{\sqrt{6}} & \frac{1}{\sqrt{6}} & \frac{2}{\sqrt{6}} \end{array}\right]\]

In the above code we selected the rank to be \(2\). Therefore, we want our \(A = \sigma_1 \underline{\mathbf{u}}_1\underline{\mathbf{v}}_1^T + \sigma_2 \underline{\mathbf{u}}_2\underline{\mathbf{v}}_2^T\). We will force each of those rank-1 matrices to be integer matrices by selecting appropriate singular values to cancel out the surds. 

The following code will select minimal singular values for: 

    mats: makelist(col(U,ii) . row(transpose(V),ii), ii, 1, r);
    ss: [];
    S: zeromatrix(m,n);
    for ii: 1 thru r do block([mv,s],
      mv: list_matrix_entries(mats[ii]), /*convert to list*/
      s: abs((scale_nicely(mv) . mv) / (mv . mv)), /*determine scaling factor*/
      ss: append(ss, [s]),
      S[ii,ii]: s
    );
    A: ss . mats;
    

The second line within the above loop finds the projection coefficient for the scaled list onto the original list. This avoids needing to determine whether a value is 0 before performing a division.

Each singular value can be scaled by an integer if desired, but it is not necessary. The above code gives 

\[{\left[\begin{array}{cccc} 1 & 3 & 1 & -2 \\ -1 & 0 & 2 & -1 \\ -1 & 0 & 2 & -1 \end{array}\right]} = {\left[\begin{array}{ccc} \frac{2}{\sqrt{6}} & \frac{1}{\sqrt{3}} & 0 \\ \frac{1}{\sqrt{6}} & -\frac{1}{\sqrt{3}} & \frac{1}{\sqrt{2}} \\ \frac{1}{\sqrt{6}} & -\frac{1}{\sqrt{3}} & -\frac{1}{\sqrt{2}} \end{array}\right]}{\left[\begin{array}{cccc} \sqrt{18} & 0 & 0 & 0 \\ 0 & 3 & 0 & 0 \\ 0 & 0 & 0 & 0 \end{array}\right]}{\left[\begin{array}{cccc} 0 & \frac{1}{\sqrt{3}} & \frac{1}{\sqrt{3}} & -\frac{1}{\sqrt{3}} \\ \frac{1}{\sqrt{3}} & \frac{1}{\sqrt{3}} & -\frac{1}{\sqrt{3}} & 0 \\ \frac{2}{\sqrt{6}} & -\frac{1}{\sqrt{6}} & \frac{1}{\sqrt{6}} & 0 \\ 0 & \frac{1}{\sqrt{6}} & \frac{1}{\sqrt{6}} & \frac{2}{\sqrt{6}} \end{array}\right]}\]

This can, of course, be leveraged in simpler cases. By making \(U\) and \(V\) the same (and possibly multiplying the singular values by \(-1\)), we can generate "nice" symmetric matrices.
