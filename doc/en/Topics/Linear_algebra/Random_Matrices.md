# Random Matrices

The paper [Setting linear algebra problems](https://www.researchgate.net/publication/228855146_Setting_linear_algebra_problems) by John Steele (2003) provides interesting mathematical background.

STACK has a contributed library for creating structured random matrices.  The code is online in the [contributed library](https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/contrib/rand_matrix.mac).  To use this library you must load it into the question variables.

* To use the latest code from github: `stack_include_contrib("rand_matrix.mac");`

## Structured Random Matrices

`rand_matrix.mac` includes a set of functions that will generate random matrices of a certain size and shape.

* `rand_matrix(m, n, k)` will generate an \(m \times n\) matrix with entries chosen from integers between \(-k\) and \(k\) (inclusive).
* `rand_diag(m, n, k)` will generate an \(m \times n\) diagonal matrix with diagonal entries chosen from integers between \(-k\) and \(k\) (inclusive). All off-diagonal entries are set to zero.
* `rand_triu(m, n, k)` will generate an \(m \times n\) upper triangular matrix with entries chosen from \(-k\) to \(k\) (inclusive). All entries below the diagonal are set to zero.
* `rand_tril(m, n, k)` will generate an \(m \times n\) lower triangular matrix with entries chosen from \(-k\) to \(k\) (inclusive). All entries above the diagonal are set to zero.

Notes.

1. You may omit the third argument, `k`, and STACK will assume \(k = 1\).
2. \(0\) may appear as a matrix entry in any of the above functions, so you cannot guarantee properties like invertibility even with `rand_diag`. 
3. For non-negative entries apply `abs`, e.g. use `abs(rand_matrix(m, n, k))`.

The above functions have `_list` and `_list_no_replacement` varieties. 

* `rand_matrix_list(m, n, L)` will generate an \(m \times n\) matrix with entries selected from provided list `L`. Items in `L` may appear more than once in the resulting matrix.
* `rand_matrix_list_no_replacement(m, n, L)` will generate an \(m \times n\) matrix with entries selected from provided list `L`. Items in `L` will not appear more than once. If `L` is too short to fill the resulting matrix, `matrix([null])` is returned instead.

You can also use `rand_diag_list`, `rand_triu_list`, `rand_tril_list`, `rand_diag_list_no_replacement`, `rand_triu_list_no_replacement` and `rand_tril_list_no_replacement` as expected. 

## Random Matrices with Certain Properties

`rand_matrix.mac` also includes functions for matrices with useful properties. e.g. `rand_invertible(n)` will generate an invertible \(n \times n\) matrix.

In many of these functions you can optionally add an extra input, `k`, which can be read as a "level of complexity". It defaults to `1`. For example, `rand_invertible(3)` might produce something like \[\left[\begin{array}{ccc} 1 & -1 & 1 \\ 0 & -1 & -1 \\ -1 & 0 & -3 \end{array}\right]\] `whilst rand_invertible(3,5)` might produce something like \[\left[\begin{array}{ccc} 20 & -20 & 8 \\ 5 & -9 & 2 \\ -15 & 11 & -22 \end{array}\right]\].

* `rand_invertible(n, k)` will generate an integer \(n \times n\) invertible matrix. `k` is the optional "level of complexity" and defaults to 1.
* `rand_integer_invertible(n, k)` will generate an integer \(n \times n\) invertible matrix whose inverse is also an integer matrix. `k` is the optional "level of complexity" and defaults to 1.
* `rand_orth(n, k)` will generate an \(n \times n\) orthogonal matrix. `k` is the optional "level of complexity" and defaults to 1.
* `rand_orthcols(m, n, k)` will generate an \(m \times n\) matrix whose columns are an orthonormal set. If `m` is less than `n`, then it instead returns `matrix([null])`. `k` is the optional "level of complexity" and defaults to 1.
* `rand_diagonalizable(n, k)` will generate an integer \(n \times n\) diagonalizable matrix. `k` is the optional "level of complexity" and defaults to 1. `k` determines both the level of complexity for eigenvalues and eigenvectors, so finer control can be achieved with `D: rand_diag(...)` in conjunction with `P: rand_integer_invertible(...)` and then `M: P . D . P^^-1`.
* `rand_defective(n, k)` will generate an integer \(n \times n\) defective matrix. `k` is the optional "level of complexity" and defaults to 1. `k` determines both the level of complexity for eigenvalues and generalised eigenvectors.
* `rand_perm_matrix(n)` will generate a random \(n \times n\) permutation matrix.

Under the hood, all of these functions with optional `k` are produced by first multiplying two triangular matrices with non-zero diagonal entries. `k` plays the same role in the generation of _those_ matrices as it does in the similar function `rand_triu`.

## Some advice on the use of these functions

Careful use of [deployed variants](../../STACK_question_admin/Deploying.md) are key to success here. The "level of complexity" `k` is quite volatile, and unexpectedly complicated or simple matrices can appear with some regularity. In general, the author believes it is better to err on the side of higher `k` and then aggressively trim the variants. 

Don't forget to check that intermediate steps of working are appopriate too! It's quite common in linear algebra to generate a problem that has "nice" numbers at the beginning and "nice" numbers in the final answer, but with some horrid working in the middle.

A good general principle is to work backwards from a solution.  E.g. if you want to randomly generate a linear system with "nice" row-reduction steps, consider randomly generating elementary matrices and multiplying the answer by these. This ensures an answer with the desired properties (e..g unique integer solutions), finely controls the steps needed, and the intermediate working complexity.
