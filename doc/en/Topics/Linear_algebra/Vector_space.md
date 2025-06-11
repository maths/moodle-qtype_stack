# Vector space functions for STACK

STACK has a [contributed library](https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/contrib/vectorspaces.mac) for vector space functions.  To use this library you must load it into the question variables.

* To use the latest code from github: `stack_include_contrib("vectorspaces.mac");`
* Loading this library automatically declares `stack_linear_algebra_declare(true);` to provide context. See the documentation on the core [linear algebra](Linear_algebra_core.md) for more information. 

This page contains reference documentation on functions in this library.

## Student vector input

`vectorspaces.mac` allows students to use the notation `c()` and `r()` to easily input column or row vectors. For more information, see the page on [vectors](Vectors.md)

## Unit vectors and scalar products

`vectorspaces.mac` provides a few functions to manipulate vectors. 

* `unit_vecp(ex)` is a predicate function that tests whether a given input is a unit vector. `ex` must be a matrix (not `c` or `r`) with exactly one row or one column.

The following functions have the optional argument `sp`. If this argument is omitted then the standard inner product (dot product) is used. Otherwise, `sp` must be the name of a function that performs a scalar product of two vectors; i.e. it takes two vector inputs and outputs a scalar. If given, this scalar product function will be used in place of the standard inner product.
  
* `proj(u, v, [sp])` will compute the projection of `u` onto `v` using scalar product `sp` (dot product if omitted).
* `orthogonalize(L, [sp])` will orthogonalize a list of vectors `L` with respect to scalar product `sp` (dot product if omitted)
* `normalize(L, [sp])` will normalize a list of vectors `L` with respect to scalar product `sp` (dot product if omitted)

## Equivalence functions

* `lin_indp(ex)` is a predicate function that tests whether a set of vectors is linearly independent. It can take many forms of input (see `cols_to_matrix` in the [core linear algebra documentation](Linear_algebra_core.md)). If given a matrix, it tests whether it has full column rank.
* `row_equivp(ex,ta)` checks whether matrix `ex` is row equivalent to matrix `ta`. Literally, this checks whether the row-reduced echelon form of each matrix are exactly equal.
* `col_equivp(ex,ta)` does the same for column equivalence.
* `subspace_equivp(ex,ta)` checks whether two collections of column vectors span the same subspace. i.e. Is each element of `ex` linearly dependent on `ta` and vice versa? Like `lin_indp(ex)`, this function accepts many forms of input. It does not check whether each set of vectors is linearly independent. To check whether two bases are equivalent, use this function in conjunction with `lin_indp`.

## Manipulating subspaces

Maxima provides `columnspace` and `nullspace` functions natively. This will return a `span` object, where `span` is an inert function. `vectorspaces.mac` also provides `rowspace` and `nullTspace` functions to extract that row space and cokernel (left null space or null space of the transpose). Literally, these functions just call `columnspace` and `nullspace` on the transpose matrix.

* `columnspace(matrix([1,2,3],[4,5,6],[7,8,9]))` gives `span(matrix([1],[4],[7]),matrix([2],[5],[8]))`
* `rowspace(matrix([1,2,3],[4,5,6],[7,8,9]))` gives `span(matrix([1],[2],[3]),matrix([4],[5],[6]))`
* `nullspace(matrix([1,2,3],[4,5,6],[7,8,9]))` gives `span(matrix([-3],[6],[-3]))`
* `nullTspace(matrix([1,2,3],[4,5,6],[7,8,9]))` gives `span(matrix([-3],[6],[-3]))`

`vectorspaces.mac` also provides functions to "trim out" linearly dependent vectors from a set and to extend a basis to \(\mathbb{R}^m\).

* `remove_dep(ex)` takes exactly a list of lists or a matrix (it is not flexible like `lin_indp`) and removes any linearly dependent vectors (or columns if given a matrix). It works from left to right but keeps the left-most vector when comparing vectors.
  * For example, `remove_dep(matrix([1,2,3],[1,2,4],[1,2,5]))` gives `matrix([1,3],[1,4],[1,5])`
* `basisify(ex)` will generate a basis for \(\mathbb{R}^m\) where each vector in `ex` has \(m\) elements. This will first remove any dependent vectors using `remove_dep` and then attempt to extend the basis by adding \(\mathbf{\hat{e}}_i\) for \(i\) from \(1\) to \(m\) one at a time.
  * It will take an optional argument that, when true, orthonormalises the basis, i.e. `basisify(ex, true)`. This is only really useful when `ex` is already orthonormal (or at least orthogonal), because if not Gram Schmidt orthogonalisation will remove most of the original vectors anyway.
  * For example, `basisify(matrix([1,1],[1,-1],[0,1]))` will give

    \({\left[\begin{array}{ccc} 1 & 1 & 1 \\ 1 & -1 & 0 \\ 0 & 1 & 0 \end{array}\right]}\)

    but `basisify(matrix([1,1],[1,-1],[0,1]),true)` will give

    \({\left[\begin{array}{ccc} \frac{1}{\sqrt{2}} & \frac{1}{\sqrt{3}} & \frac{1}{\sqrt{2}\cdot \sqrt{3}} \\ \frac{1}{\sqrt{2}} & -\frac{1}{\sqrt{3}} & -\frac{1}{\sqrt{2}\cdot \sqrt{3}} \\ 0 & \frac{1}{\sqrt{3}} & -\frac{\sqrt{2}}{\sqrt{3}} \end{array}\right]}\)

  * The intended use of this function is for making the \(Q\) in \(QR\) factorisations orthogonal or for computing a full SVD
 
## Projection matrices

* `projection_matrix(A)` will produce the symmetric, idempotent matrix that orthogonally projects vectors in \(\mathbb{R}^m\) onto the column space of \(m\times n\) matrix `A`. 
