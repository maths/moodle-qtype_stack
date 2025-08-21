# Matrix factorisation functions

STACK has a [contributed library](https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/contrib/matrixfactorizations.mac) for matrix factorisation functions.  To use this library you must load it into the question variables.

* To use the latest code from github: `stack_include_contrib("matrixfactorizations.mac");`
* Loading this library automatically declares `stack_linear_algebra_declare(true);` to provide context.

This page contains reference documentation on functions in this library.

## Some comments on question design

This package provides functions that find factorisations of arbitrary matrices. In practical applications, these sorts of computations are performed with floating point operations and utilise highly optimised algorithms. Firstly, `matrixfactorizations.mac` makes no claims to optimisation or efficiency. On matrices much bigger than 4×4, you might notice significant slowdowns when using some of these functions. Secondly, if you are using STACK to assess numerical linear algebra techniques, then this author suggests you might look elsewhere, such as to [CodeRunner](https://coderunner.org.nz/).

However, toy problems are a very useful way to get students engaged with basic concepts of linear algebra, and so it still important to provide support for things like matrix factorisations. For small matrices (that is, matrices that one can tackle when working by hand), these functions work nicely. 

It is still worth being careful when generating toy problems, though. Running the code `SVD(matrix([1,2],[3,4]))` will correctly give you two orthogonal and one diagonal matrix, but it is _not_ nice to find these by hand. It is often sensible to begin randomising a question by generating the _answer_ and working back to create the question. In this regime, these factorisation functions are mostly useful for checking answers, or for generating answers when using matrices that are already known to be "nice". 

The [`rand_matrix.mac` library](Random_Matrices.md) has some functions that might be useful for generating sensible matrices. 

## Diagonalisation

The Maxima package `diag` (included in STACK by default) provides some useful functions for diagonalisation of matrices and functions of matrices. `matrixfactorizations.mac` utilises these to create some easy-to-use factorisation functions.

* `[P, D]: diagonalize(M)` will construct an invertible matrix `P` and diagonal matrix `D` such that `M` is equal to `P . D . P^^-1`.
  * `diagonalize(M)` explicitly returns a list containing these two matrices.
  * If `M` is a symmetric matrix, then an orthogonal `P` will be chosen.
  * If `M` is defective (not diagonalisable) then an empty list `[]` is returned instead.
* `[P, J]: get_Jordan_form(M)` will construct an invertible matrix `P` and "almost diagonal" matrix `J` in Jordan normal form such that `M` is equal to `P . J . P^^-1`.
  * If `M` is diagonalisable, this is equivalent to `diagonalize(M)`.

In particular, the `rand_diag`, `rand_integer_invertible`, `rand_diagonalizable` and `rand_defective` functions in [`rand_matrix.mac`](Random_Matrices.md) might be useful for generating these problems. 

## Gaussian Elimination and PLU Decomposition

The Maxima library `linearalgebra.mac` provides many useful functions, including `lu_factor` and `get_lu_factors`.  The former will compute the \(PLU\) decomposition of a square matrix and return a list of which the first element is a packed \(LU\) decomposition and the second is a permutation. The latter converts the output of the former into the list `[P, L, U]` where `P` is a permutation matrix, `L` is lower triangular, `U` is upper triangular, and `P . L . U` is equal to the original matrix. It is often convenient to run something like: 

    [P, L, U]: get_lu_factors(lu_factor(matrix([1,2,3],[2,4,6],[3,6,9])))

Be careful with this function. The above example (at time of writing) generates an incorrect `U` matrix (with 9 in row 2, column 3) such that `P . L . U` is no longer equal to the original matrix. 

For other basic row and column operations, Maxima provides several useful functions, and STACK's core provides more. See the documentation on [core linear algebra functions](Linear_algebra_core.md) for more information. The contributed library [`matrix.mac`](Matrix_library.md) provides even more functions. 

In particular, the `rand_diag`, `rand_triu`, `rand_tril` and `rand_perm_matrix` functions in [`rand_matrix.mac`](Random_Matrices.md) might be useful for generating these problems. 

## Gram-Schmidt Orthogonalisation and QR Factorisation

Maxima provides the function `gramschmidt` to perform Gram-Schmidt orthogonalisation on either a matrix or a list. Notably, if given a matrix, the function will orthogonalise the _rows_ rather than the _columns_ of the matrix. Optionally, a non-standard inner product can be provided as a second argument, which allows for use on function spaces. 

* `gramschmidt(matrix([1,2,3],[4,5,6],[7,8,9]))` produces `[[1,2,3],[12/7,3/7,−6/7],[0,0,0]]`
* `gramschmidt([1,x,x^2],lambda([f,g],int(f*g,x,0,1)))` produces `[1,(2*x-1)/2,(6*x^2-6*x+1)/6]`

`matrixfactorizations.mac` also provides `QR(M)` to perform \(QR\) factorisation. The provided matrix must have full column rank or the function will return `[]`. Otherwise it will return `[Q, R]` where `Q` has orthonormal columns that span the column space of `M`, `R` is upper triangular, and `Q . R` is equal to `M`. 

In particular, the `rand_orthcols` and `rand_triu` functions in [`rand_matrix.mac`](Random_Matrices.md) might be useful for generating these problems. 

## Singular Value Decomposition

`matrixfactorizations.mac` provides two SVD functions: 

* `[U, S, VT]: SVD_red(M)` will give three matrices such that `U . S . VT` is equal to `M`, `U` has orthonormal columns, `VT` has orthonormal rows, and `S` is non-negative, invertible and diagonal. This is a _reduced_ SVD.
* `[U, S, VT]: SVD(M)` will perform a full SVD, where `U` and `VT` are now orthogonal matrices and `S` is non-negative and diagonal, but not necessarily invertible (or even square).

In particular, the `rand_orth` and `rand_diag` functions in [`rand_matrix.mac`](Random_Matrices.md) might be useful for generating these problems. 
