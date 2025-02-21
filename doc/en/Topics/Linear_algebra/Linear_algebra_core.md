# Vector/matrix functions defined by STACK in the core code

STACK extends Maxima's functionality with a number of very useful functions for manipulating matrices.  These are loaded by default and are available in every question.

* `vec_convert(ex)` Converts `c` and `r` convenience functions into matrices.
* `un_vec_convert(ex)` Given a row or column vector, convert it to `c()` or `r()` form.

## Predicate functions for vectors

* `vec_convertedp(ex)` A predicate to determine whether an expression has been converted to matrix form.
* `col_vecp(ex)` Predicate for determining whether a given object is an \(M\times 1\) matrix (a column vector). Note: does not consider `c` a column vector. Use `vec_convert` before `col_vecp`.
* `row_vecp(ex)` Predicate for determining whether a given object is a \(1\times N\) matrix (a row vector). Note: does not consider `r` a row vector. Use `vec_convert` before `row_vecp`.
* `vectorp(ex):= col_vecp(ex) or row_vecp(ex);`

## Predicate functions for matrices

* `diagp(M)` Predicate to determine whether a matrix is diagonal.
* `squarep(M)` Is a given object a square matrix?

## Utility functions to convert objects into standard forms

* `matrix_to_cols(M)` Takes a matrix and returns a list of its column vectors.
* `scale_nicely(v)` Given a vector (or list) return the shortest possible parallel vector with integer entries.
* `pinv(M)` Moore-penrose pseudoinverse.  Bascially applies `moore_penrose_pseudoinverse(M)` with simplification.

`make_list_of_lists(ex)`

 * `@param[]` ex A list, set, ntuple or span containing vectors as matrices, lists, c, r, sets or ntuples, or a matrix.
 * `@return[list]` A list of lists.

Takes collections of vectors and returns a list of lists.
The vectors themselves may be different objects. Supported objects include:

* \(1\times N\) or \(M\times 1\) matrices.
* Lists.
* Vectors using `c` or `r` notation.
* Sets, ntuples
* Other inert functions like sequence should work too, but 

The collection may be a list, set, ntuple, span, or matrix.  If given a matrix, then returns the columns as a list, with each column also being a list.

`convert_to_colvec(ex)`

 * `@param[ex]` ex Input object.
 * `@return[matrix]` A \(M\times 1\) matrix representing the column vector, or `matrix([null])` if input was invalid.

Try to convert object to a column vector, as a maxima matrix.
Supported objects include:

 * \(M\times 1\) matrices.
 * Vectors using `c` notation
 * Flat lists, ntuples (with entries interpreted as a column vector entries)
 
If conversion is not possible, return `matrix([null])`.

`cols_to_matrix(L)`

 * `@param[]` A list, set, ntuple or span containing vectors as matrices, lists, `c`, `r`, sets or ntuples, or a matrix.
 * `@return[matrix]` A matrix containing the input elements as columns, or `matrix([null])` in case of invalid input.

Takes a collection of vectors and return the matrix that has those vectors as columns.
If the input is already a matrix, the matrix itself is returned.
If the input in invalid, matrix([null]) is returned.
The rationale is that further operations can still be performed on the output without crashing in the feedback variables, and we can catch this case in the first PRT node.
The vectors themselves may be different objects. Supported objects include:

* \(M\times 1\) matrices
* Vectors using `c` notation
* Lists, ntuples (representing a column vector)

The collection may be a list, set, ntuple, span, or matrix.

`cols_to_cols(L)`

* `@param[]` A list, set, ntuple or span containing vectors as matrices, lists, `c`, `r`, sets or ntuples, or a matrix.
* `@return[list]` List of \(M\times 1\) matrices (column vectors), or `[]` in case of invalid input.

Convert input to a list of column vectors of the same dimension.
The vectors themselves may be different objects. Supported objects include:

* \(M\times 1\) matrices
* Vectors using `c` notation
* Lists, ntuples (representing a column vector)

The collection may be a list, set, ntuple, span, or matrix.

## Functions to solve, manipulate and re-write matrices to standard forms

Note, the Maxima functions `addrow` and `addcol` appends rows/columns onto the matrix.  For row operations use

* `rowswap(M,i,j)` Swaps rows `i` and `j`.
* `rowadd(M,i,j,k)` Returns matrix `M` where `M[i]: M[i] + k * M[j]`.
* `rowmul(M,i,k)` Returns matrix `M` where `M[i]: k * M[i]`.
* `rref(M)` Returns the reduced row echelon form of `M`.


`mat_solve(A,b,[lstsq])`

 * `@param[matrix]` `A` An \(m\times n\) matrix
 * `@param[matrix]` `b` A \(m\times 1\) matrix (or a list with m entries)
 * `@param[boolean]` `lstsq` Optional: if given true then a least squares solution will be obtained. If `false` or omitted, only exact solutions obtained.
 * `@return[matrix]` The general solution to \(Ax = b\). If no solution exists and lstsq is not true, then \(matrix([])\) is returned.
 
Solve the matrix equation \(Ax = b\) given matrix \(A\) and column vector (or list) \(b\).
Optionally will find a least squares solution.
Always returns a general solution if one exists, even in the least squares case.
If a single solution is required, use `moore_penrose_pseudoinverse(A)` instead.

Examples.

* `mat_solve(matrix([1,2],[3,4]),[3,7])` returns the unique solution
* `mat_solve(matrix([1,-1],[1,-1]),[0,0])` returns a general solution
* `stack_strip_percent(mat_solve(matrix([1,-1],[1,-1]),[1,0],true),[r])`



### Vector cross product ###

The wedge product operator is denoted by the tilde `~`.  This is the `itensor` package.  This package is not normally loaded by STACK, and in any case the package takes lists and not matrices.  For convenience, the following function has been added which requires `3*1` matrices.

`crossproduct(a,b)` returns the vector cross product of `a` and `b`.

Another advantage of this function is the ability to return an un-simplified version with `simp:false`.



