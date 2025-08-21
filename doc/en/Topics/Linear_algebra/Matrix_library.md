# Matrix manipulation and matrix predicate functions for STACK

STACK has a [contributed library](https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/contrib/matrix.mac) for matrix manipulation and matrix predicate functions for STACK.  To use this library you must load it into the question variables.

* To use the latest code from github: `stack_include_contrib("matrix.mac");`
* Loading this library automatically declares `stack_linear_algebra_declare(true);` to provide context.

This page contains reference documentation on functions in this library.

## Working with matrices

You can find more information on how Maxima handles matrices in the [Matrix](../../CAS/Matrix.md) section of the documentation. There is more discussion in the [Vectors](Vectors.md) and [Linear Algebra](Linear_algebra_core.md) pages with more of a focus on writing questions or dealing with student input.

The `matrix.mac` contributed library adds even more functions to help manipulate matrices. 

## Row and column operations

The Maxima library `linearalgebra` (loaded by default in STACK) provides the `rowop`, `rowswap`, `columnop` and `columnswap` functions, and STACK adds `rowadd` and `rowmul`. These are described in the [matrix page](../../CAS/Matrix.md). `matrix.mac` introduces `rowscale` and `columnscale`. When `simp:true`, `rowscale` is identical to `rowmul`, but `rowscale` also works when `simp:false`, which could be useful for making model answers. 

* `(simp:false, rowscale(matrix([1,2,3],[4,5,6],[7,8,9]),2,100))` will return `matrix([1,2,3],[100*4,100*5,100*6],[7,8,9])`

## Changing the entries of a matrix

Maxima provides the `setelmx` function to change an element of a matrix and return the adjusted matrix. This is described in the [matrix page](../../CAS/Matrix.md). `matrix.mac` provides a set of complementary functions to replace multiple entries at once.

* `setrowmx(r,i,M)` will replace row `i` of matrix `M` with `r` and return the resulting matrix. `r` can be a matrix, a list, or a single variable/number. In the latter case, `r` will populate every entry of the designated row.
  * `M[i]: r` will do the same thing, but returns the row `r` instead of the matrix. Also, `r` must be a literal list in this case.
  * Unlike `setelmx`, this does not change the original matrix. Users may wish to use code such as `M: setrowmx(r,i,M)` to achieve this. 
* `setcolmx(c,i,M)` does the equivalent thing for columns
  * There is no way to set the values of a column directly like for rows. Users may prefer to take the transpose of the matrix, adjust the row, and then transpose the result.
* `setdiagmx(L,M,k)` will replace the diagonal of matrix `M` with the list or number `L`. If `L` is a list, the function will do its best to insert `L` appropriately. i.e. if `L` is too long, it will ignore overhanging entries, and if `L` is too short it will leave remaining entries untouched. `k` indicates which diagonal to replace, where the main diagonal is `k=0`, the immediate superdiagonal is `k=1` and immediate subdiagonal is `k=-1`. If `k` is omitted, the main diagonal is assumed. Some examples:
  * `setdiagmx(1,zeromatrix(2,3))` will return `matrix([1,0,0],[0,1,0])`
  * `setdiagmx(1,zeromatrix(2,3),1)` will return `matrix([0,1,0],[0,0,1])`
  * `setdiagmx([1,2,3,4,5],ident(3),-1)` will return `matrix([1,0,0],[1,1,0],[0,2,1])`

`matrix.mac` also provides some functions to shape a given matrix.

* `triu(M)` will create a matrix that is the same as `M` with all entries below the main diagonal set to zero. This does not edit the original matrix.
* `tril(M)` will create a matrix that is the same as `M` with all entries above the main diagonal set to zero. This does not edit the original matrix.
* `diagonal(M)` will create a matrix that is the same as `M` with all off-diagonal entries set to zero. This does not edit the original matrix.

These functions could be useful for setting \(PLU\) decomposition questions. 

* `diag_entries(M)` will extract the elements on the diagonal of `M` and return them as a list.
* `diagmatrix_like(L,m,n)` will create an \(m\times n\) diagonal matrix with list `L` as the diagonal entries. The list `L` behaves much like the list `L` in `setdiagmx` above, except it _must_ be a list.

## Predicate functions for matrices

* `triup(M)` tests whether `M` is an upper triangular matrix. That is: is every below-diagonal entry of `M` exactly equal to 0?
* `trilp(M)` tests whether `M` is a lower triangular matrix. That is: is every above-diagonal entry of `M` exactly equal to 0?
* Core STACK provides `diagp` to check whether `M` is diagonal.

Note that all of these predicates are checking whether the relevant entries are exactly equal to zero. That produces the following behaviour:

* `(simp: false, trilp(matrix([1,1-1],[2,3])))` returns `false`
* `(simp: true, trilp(matrix([1,1-1],[2,3])))` returns `true`

This is intentional. After all, it is slightly ambiguous whether the matrix \[{\left[\begin{array}{cc} 1 & 1-1 \\ 2 & 3 \end{array}\right]}\] is lower triangular, and this ambiguity gets worse with more complicated expressions than simply `1-1`. This allows teachers to check whether students have simplified their answers.

`REFp(M)` tests whether a given matrix is in row echelon form. [There is some disagreement about exactly what constitutes row echelon form](https://en.wikipedia.org/wiki/Row_echelon_form#:~:text=of%20the%20article.-,(General)%20row%20echelon%20form,-%5Bedit%5D). For clarity, `REFp` tests the following properties: 

* Do all non-zero rows appear above any zero rows (that is, have all the redundant rows been moved to the bottom?)
* Does the pivot in each row appear strictly to the right of the pivot in the above row?
* `REFp` does **not** check whether the pivots are equal to 1 by default, but `REFp(M, true)` will additionally require this property.

There is no `RREFp` function because this is unique for each matrix. Users can use `is(M = rref(M))` to test this. 

Note: question authors may like to pair `REFp` with `row_equivp` from the [`vectorspaces.mac`](Vector_space.md) contributed library when writing questions on row reduction. Alternatively, `REFp(ans1) and is(rref(ans1) = rref(ta))` would be sufficient. 

* `symp(M)` tests whether `M` is a symmetric matrix. That is, is `M` a square matrix such that `transpose(M)` is equal to `M`?
  * `symmetricp(M,n)` can check whether the \(n\times n\) submatrix of `M` is symmetric.
* `invertiblep(M)` tests whether `M` is an invertible matrix. 
* `diagonalizablep(M)` tests whether `M` is a diagonalisable matrix. That is, is `M` an \(n\times n\) matrix with \(n\) linearly independent eigenvectors?
* `orthogonal_columnsp(M)` tests whether the columns of `M` are orthogonal to each other. That is, is `transpose(M) . M` a diagonal matrix?
* `orthonormal_columnsp(M)` tests whether the columns of `M` form an orthonormal set. That is, are the columns all orthogonal to each other, and do the columns all have a Euclidean length of 1? Or: is `transpose(M) . M` the identity matrix?
* `orth_matrixp(M)` tests whether `M` is an orthogonal matrix. That is, are `transpose(M) . M` and `M . transpose(M)` both equal to the identity matrix?

The last three predicates above will all accept an optional argument, `sp`, that defines a scalar product. This can either be a bilinear function (takes two vectors and returns a scalar), or a symmetric positive definite matrix. If given, the checks for orthogonality and length will utilise this scalar product instead of the standard inner product (dot product). 

## Functions for displaying matrices and systems of equations

### Augmented matrices 

It is sometimes convenient to be able to display an augmented matrix. `matrix.mac` adds some limited support for this. 

Perhaps you have a question in which students are asked to solve the matrix equation \(A\underline{\mathbf{x}} = \underline{\mathbf{b}}\) using Gaussian elimination and you wanted to display this problem as an augmented matrix. Then, with matrix `A` and right hand side vector (really a matrix) `b` already defined, you could use `aug(addcol(A,b))` to display

\[{\left[\begin{array}{cc} 1 & 2 \\ 4 & 5 \end{array}\right|\left.\begin{array}{c} 3 \\ 6 \end{array}\right]}\]

Really what is happening here is that `aug` is converting a matrix with concatenated columns `A` and `b` to an `aug_matrix`, which then displays as a matrix with its final column separated by a vertical bar. `aug_matrix` is an inert function that exists only in this library for display purposes. You can save this to a variable, perhaps `Ab`, but Maxima doesn't know that this is a matrix at all and so matrix operations won't work on it. To turn it back into a matrix, you can use `de_aug(Ab)`. 

### Systems of equations

Sometimes it is nice to have a system of equations with all the variables and coefficients vertically aligned appropriately whilst still allowing for randomisation. `matrix.mac` provides some support for this. 

`disp_eqns(eqns, vars)` will display the system of linear equations `eqns` with variables `vars` (in order!) with everything vertically aligned. It will omit variables with a coefficient of 0, omit unitary coefficients, use negative signs appropriately, and can handle parameters. An example is probably the easiest way to show this. 

    `disp_eqns([2*x+y-z+(-3)*w = 7,-x-2*y+(-7)*w = -1,3*z = 0,x+w = 0,0 = 0],[x,y,z,w])`

\[ \begin{array} {rcrcrcrcr}2x& + & y& - & z& - & 3w& = &7\\-x& - & 2y& & & - & 7w& = &-1\\& & &  & 3z& & & = &0\\x& & & & & + & w& = &0\\& & & & & & 0& = &0\end{array} \]

The function `mat_disp_eqns(A,b,vars)` will produce the same output using coefficient matrix `A`, right-hand side vector `b`, and list of variables `vars`. The below produces the same output as the above. 

    `mat_disp_eqns(matrix([2,1,-1,-3],[-1,-2,0,-7],[0,0,3,0],[1,0,0,1],[0,0,0,0]),matrix([7],[-1],[0],[0],[0]),[x,y,z,w])`

Both functions also work with variables. For example:

    `mat_disp_eqns(matrix([-2, k-1, -1],[0, 2*k+2, 0], [-1, k, -2]),matrix([k-1],[1],[-1]),[x,y,z])`

\[ \begin{array} {rcrcrcr}-2x& + & \left(k-1\right)y& - & z& = &k-1\\& & \left(2\, k+2\right)y& & & = &1\\-x& + & ky& - & 2z& = &-1\end{array} \]
