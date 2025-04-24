# Vector/matrix functions defined by STACK in the core code

STACK extends Maxima's functionality with a number of very useful functions for manipulating matrices.
The functions documented here are loaded by default and are available in every question. 

A key feature are convenience functions `c` and `r` which convert their arguments into column and row matrices respectively.

* `stack_linear_algebra_declare(true)` Provides a linear algebra context, including TeX support for `c` and `r`.
* `vec_convert(ex)` Converts `c` and `r` convenience functions into matrices.
* `un_vec_convert(ex)` Given a row or column vector, convert it to `c()` or `r()` form.

For more detail, see the page on [Vectors](Vectors.md).

In STACK 4.9.0 this functionality was moved into the core of STACK.  For earlier versions of STACK
you can use the [latest code from github](https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/contrib/linearalgebra_contrib.mac): `stack_include_contrib("linearalgebra_contrib.mac");`

## Predicate functions for vectors

* `vec_convertedp(ex)` A predicate to determine whether an expression has been converted to matrix form.
* `col_vecp(ex)` Predicate for determining whether a given object is an \(M\times 1\) matrix (a column vector). Note: does not consider `c` a column vector. Use `vec_convert` before `col_vecp`.
* `row_vecp(ex)` Predicate for determining whether a given object is a \(1\times N\) matrix (a row vector). Note: does not consider `r` a row vector. Use `vec_convert` before `row_vecp`.
* `vectorp(ex):= col_vecp(ex) or row_vecp(ex);`

## Predicate functions for matrices

* `squarep(M)` Is a given object a square matrix?
* `diagp(M)` Predicate to determine whether a matrix is diagonal. `M` need not be square. 

## Functions to manipulate matrices and solve systems of linear equations

Note, the Maxima functions `addrow` and `addcol` appends rows/columns onto the matrix.  For row operations use

* `rowswap(M,i,j)` Swaps rows `i` and `j`.
* `rowadd(M,i,j,k)` Returns matrix `M` where `M[i]: M[i] + k * M[j]`.
* `rowmul(M,i,k)` Returns matrix `M` where `M[i]: k * M[i]`.
* `rref(M)` Returns the reduced row echelon form of `M`.

For more functions that can be used to perform matrix operations, see the documentation for the contributed [matrices library](Matrix_library.md).

`linsolve(eqns,vars)` is a Maxima function that will solve a list of linear equations `eqns` for variables 'vars'. It returns a list of equations in the form `var = solution` and will utilise free variables if needed. If expressions are given instead of equations, Maxima will assume that the expression is equal to zero.

Some examples are:
* `linsolve([x+y,x-y=2],[x,y])` will produce `[x = 1, y = -1]`
* `linsolve([x+y,2*y+2*x=0],[x,y])` will produce `[x = -%r1,y = %r1]`
* `linsolve([x+y,x+y=1],[x,y])` will produce `[]`

You can remove any percent variables using `stack_strip_percent`, documented in more detail in the section on [differential equations](../Differential_equations/index.md)

`mat_solve(A,b,[lstsq])` is a STACK-provided function that is mostly just a wrapper for `linsolve` that expects matrices instead of lists of equations. 
 * Input: `A` An \(m\times n\) matrix
 * Input: `b` A \(m\times 1\) matrix (or a list with m entries)
 * Optional input: `lstsq` if given true then a least squares solution will be obtained. If `false` or omitted, only exact solutions obtained.
 * Output: The general solution to \(Ax = b\). If no solution exists and lstsq is not true, then \(matrix([])\) is returned.
 
Some examples are:
* `mat_solve(matrix([1,2],[3,4]),[3,7])` returns the unique solution `matrix([1],[1])`
* `mat_solve(matrix([1,-1],[1,-1]),[0,0])` returns a general solution `matrix([%r1],[%r1])`
* `mat_solve(matrix([1,-1],[1,-1]),[1,0])` returns `matrix([])`, indicating that there was no solution.
* `mat_solve(matrix([1,-1],[1,-1]),[1,0],true)` returns `matrix([(2*%r1+1)/2],[%r1])`, because a least squares solution was requested.

If the unique minimal least squares solution to \(A\mathbf{x} = \mathbf{b}\) is desired, then \(\tilde{\mathbf{x}} = A^{+}\mathbf{b}\) can be computed using `pinv(A) . b`. `pinv` calls the `moore_penrose_pseudoinverse` function

## Operations for Vectors

STACK provides the `scale_nicely(v)` function. Given a vector or list, it will return the smallest possible parallel vector or list with integer entries. It will leave zero vectors untouched, and vectors that contain all negative values will be negated. This is useful in a number of situations, including:
* Eigenvector problems where the length of the vector is not important information and keeping the numbers small makes it easier for students to work by hand
* Simplifying expressions for lines or planes with coefficients like `-2*x - 2*y = -4` to `x + y = 2`
* Scaling a whole list of fractions by their smallest common denominator

### Vector cross product

The wedge product operator is denoted by the tilde `~`.  This is the `itensor` package.  This package is not normally loaded by STACK, and in any case the package takes lists and not matrices.  For convenience, the following function has been added which requires \(3\times 1\) matrices.

`crossproduct(a,b)` returns the vector cross product of `a` and `b`.

Another advantage of this function is the ability to return an un-simplified version with `simp:false`.

## Utility functions to convert objects into standard forms
Several contrib libraries rely on either a list of lists or a matrix as a "canonical form" when analysing vector spaces etc. There are several functions included in core STACK to allow for these transformations between forms. For more details see [linearalgebra_core.mac](https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/linearalgebra_core.mac)

* `matrix_to_cols(M)` Takes a matrix and returns a list of its column vectors.
* `make_list_of_lists(ex)` Takes a collection (a list, set, ntuple, span, matrix) of vectors (as lists, matrices, sets, ntuples, `c` or `r`) and returns a list of lists
  * If given a matrix, it returns a list of its column vectors, which are given as lists.
* `convert_to_colvec(ex)` Tries to convert a given object, e.g. list, matrix, `c` or `r`, ntuples, etc. to a column vector. Returns `matrix([null])` if input is invalid.
* `cols_to_matrix(L)` Tries to create a matrix with a given list of columns. The columns can be a variety of different objects determined by `convert_to_colvec`. Returns `matrix([null])` if input is invalid.
* `cols_to_cols(L)` Tries to convert a collection of potential columns to a list of Maxima matrices. Returns `[]` if input is invalid.
