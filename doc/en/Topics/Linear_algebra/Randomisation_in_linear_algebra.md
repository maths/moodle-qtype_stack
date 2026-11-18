# Randomisation in linear algebra

This page is a discussion of randomisation when setting linear algebra problems. This can be a particularly difficult problem to tackle in this subject: matrices have many degrees of freedom, and problems that seem very similar at a glance can have dramatically different solutions. On top of that, we often need to choose between posing a problem that contains "nice" numbers and requiring an answers that contain "nice" numbers; both at once is difficult or impossible in general! 

There are two tools that this article will use liberally: 

- The [`rand_matrix.mac`](Random_matrices.md) library. This contains many functions that can be used to generate random matrices with defined structure or with defined properties.
- The `scale_nicely` function. This takes a list or vector and returns the "simplest" list or vector that is parallel to the input. This was originally designed with basis problems in mind (i.e. it is often preferable to use \((2,1,1)\) than \((1,1,0.5)\)), but we will use it more liberally.

## Eigenvectors and eigenvalues

Two matrices are called _similar_ when their eigenvalues are identical (including the structure of the generalised eigenspaces). Therefore, if we want to prescribe this structure for a matrix, we can start by generating a "simple" matrix with the eigenvalues we want and then convert it to a similar matrix using a similarity transformation of the form \(A = PMP^{-1}\). The \(P\) matrix here can be anything invertible, but you might find it useful to use `rand_integer_invertible` from the aforementioned library, as (alongside an integer-valued \(M\)), we guarantee that \(A=PMP^{-1}\) is integer-valued.

    P: rand_integer_invertible(4,1)

\[P = \left[\begin{array}{cccc} 1 & -1 & -1 & 1 \\ 0 & 1 & -1 & 1 \\ -1 & 1 & 2 & -1 \\ 1 & -1 & -2 & 2 \end{array}\right]\]

The best option is to use a Jordan matrix for \(M\). This is a block diagonal matrix consisting of one or more _Jordan blocks_. A Jordan block is a square matrix whose diagonal is filled with a single, possibly repeated, digit, and whose super-diagonal is populated by ones. The digit on the diagonal is the eigenvalue of \(M\) and the size of the block is the number of times this eigenvalue is repeated without adding a dimension to the corresponding eigenspace.

    /* Choose a diagonal matrix */
    D: matrix([1,0,0,0],[0,1,0,0],[0,0,2,0],[0,0,0,-1]);
    A1: P . D . P^^-1;

    /* Choose a matrix that is defective by one dimension */
    J: matrix([1,1,0,0],[0,1,0,0],[0,0,2,0],[0,0,0,-1]);
    A2: P . J . P^^-1;

\[A_1 = \left[\begin{array}{rrrr} -1 & 0 & -3 & -1 \\ -2 & 1 & -3 & -1 \\ 3 &  0 & 5 & 1 \\ -4 & 0 & -6 & -1 \end{array}\right]\qquad
  A_2 = \left[\begin{array}{rrrr}  0 & 1 & -3 & -2 \\ -2 & 1 & -3 & -1 \\ 2 & -1 & 5 & 2 \\ -3 & 1 & -6 & -2 \end{array}\right]\]

Diagonal matrices are easy to generate, perhaps using `rand_diag` for a truly random choice, or `diagmatrix_like` with a pre-defined list of eigenvalues. Non-diagonal Jordan matrices are harder to generate, but perhaps something like the following (using `setdiagmx` from `matrix.mac`) could be used: 

    L1: rand([-1,1,2,3]);
    M: L1*ident(4);
    M: setdiagmx(rand_selection([0,0,1,1,1],3),M,1);

In the diagonal case, the columns of \(P\) are the eigenvectors of \(A\) and in the non-diagonal case they are the generalised eigenvectors of \(A\). This method of generation gives us no control over the eigenvectors, but if we are willing to either have non-integer matrices \(A\) or a lot of trial and error, we can construct \(P\) manually to give us full control over both the eigenvalues and the eigenvectors. If a low "level of complexity" (the second input to `rand_integer_invertible`) is used when generating \(P\), the matrix tends to be very simple and student working is not too bad. 

In general, Jordan blocks can have non-real eigenvalues on the diagonal. For a random modal matrix \(P\), \(A = PMP^{-1}\) will, with non-real eigenvalues, not be a real matrix. It is common for undergraduate courses to focus on real matrices, so we need a workaround if we want to produce a real matrix with non-real eigenvalues. We can do this by using the real Jordan form. We do this by grouping any complex conjugate pairs along the diagonal and then replacing blocks of the form

\[\left[\begin{array}{cc}a+b\mathrm{i} & 0 \\ 0 & a - b\mathrm{i}\end{array}\right] \xrightarrow{\text{becomes}} \left[\begin{array}{rr}a & -b \\ b & a\end{array}\right].\]

We can produce repeated non-real eignenvalues if we repeat this block along the diagonal with an identity matrix above it. An example is provided below to hopefully make this clearer.

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
    for mat in mats do block([mv,s],
      mv: list_matrix_entries(mat), /*convert to list*/
      s: abs((scale_nicely(mv) . mv) / (mv . mv)), /*determine scaling factor*/
      ss: append(ss, [s]),
      S[ii,ii]: ss
    );
    A: ss . mats;
    

The second line within the above loop finds the projection coefficient for the scaled list onto the original list. This avoids needing to determine whether a value is 0 before performing a division.

Each singular value can be scaled by an integer if desired, but it is not necessary. The above code gives 

\[{\left[\begin{array}{cccc} 1 & 3 & 1 & -2 \\ -1 & 0 & 2 & -1 \\ -1 & 0 & 2 & -1 \end{array}\right]} = {\left[\begin{array}{ccc} \frac{2}{\sqrt{6}} & \frac{1}{\sqrt{3}} & 0 \\ \frac{1}{\sqrt{6}} & -\frac{1}{\sqrt{3}} & \frac{1}{\sqrt{2}} \\ \frac{1}{\sqrt{6}} & -\frac{1}{\sqrt{3}} & -\frac{1}{\sqrt{2}} \end{array}\right]}{\left[\begin{array}{cccc} \sqrt{18} & 0 & 0 & 0 \\ 0 & 3 & 0 & 0 \\ 0 & 0 & 0 & 0 \end{array}\right]}{\left[\begin{array}{cccc} 0 & \frac{1}{\sqrt{3}} & \frac{1}{\sqrt{3}} & -\frac{1}{\sqrt{3}} \\ \frac{1}{\sqrt{3}} & \frac{1}{\sqrt{3}} & -\frac{1}{\sqrt{3}} & 0 \\ \frac{2}{\sqrt{6}} & -\frac{1}{\sqrt{6}} & \frac{1}{\sqrt{6}} & 0 \\ 0 & \frac{1}{\sqrt{6}} & \frac{1}{\sqrt{6}} & \frac{2}{\sqrt{6}} \end{array}\right]}\]

This can, of course, be leveraged in simpler cases. By making \(U\) and \(V\) the same (and possibly multiplying the singular values by \(-1\)), we can generate "nice" symmetric matrices.
