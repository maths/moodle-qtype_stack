# Eigenvalue/vector functions

STACK has a [contributed library](https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/contrib/eigenlib.mac) for eigenvector/value functions.  To use this library you must load it into the question variables.

* To use the latest code from github: `stack_include_contrib("eigenlib.mac");`
* Loading this library automatically declares `stack_linear_algebra_declare(true);` to provide context. See the documentation on the core [linear algebra](Linear_algebra_core.md) for more information. 

This page contains reference documentation on functions in this library.

## Predicates for eigenvector-eigenvalue problems

`eigenlib.mac` provides two functions for checking whether a given vector or scalar is an eigenvector or eigenvalue respectively. 

* `eigenvectorp(v,M)` tests whether the vector `v` is an eigenvector of the matrix `M`. The zero vector is not considered an eigenvector. `v` can be a matrix, list or ntuple.
* `eigenvectorp(v,M,L)` will run the above function, but specifically checks that `v` is an eigenvector that corresponds to the eigenvalue `L`.
* `eigenvaluep(L,M)` tests whether `L` is an eigenvalue of `M`.
* `eigenvaluep(L,M,v)` will run the above function, but specifically checks that `L` is the eigenvalue corresponding the eigenvector `v`. `v` can be a matrix, list or ntuple.

There are some more helpful functions in the contributed [vector spaces library](Vector_space.md) such as `unit_vecp`, `lin_indp` and `subspace_equivp` to test whether a given vector is a unit vector, whether a given collection of vectors are linearly independent, and whether two given sets of vectors span the same subspace. These may be helpful when examining eigenspaces or orthogonal decomposition (among other topics). 

## Extracting eigenvectors and eigenvalues

Maxima provides the functions `eigenvectors` and `eigenvalues` to extract the eigenvectors and eigenvalues of a square matrix. 

* `eigenvalues(M)` will return a list of two elements. The first element is a list containing all of the eigenvalues of `M`. The second element is a list of the algebraic multiplicities of these eigenvalues (in order).
  * For example, `eigenvalues(matrix([3,1,0],[0,5,0],[0,0,5]))` will return `[[3,5],[1,2]]` because the eigenvalue \(3\) has an algebraic multiplicity of \(1\) and the eigenvalue \(5\) has an algebraic multiplicity of \(2\).
* `eigenvectors(M)` will also return a list of two elements. The first element is exactly the output of `eigenvalues(M)`. The second element is a list, where each element is a list containing the linearly independent eigenvectors corresponding to the eigenvalues from `eigenvalues(M)`. The eigenvectors are given as lists, _not_ matrices.
  * For example, `eigenvalues(matrix([3,1,0],[0,5,0],[0,0,5]))` will return `[[[3,5],[1,2]],[[[1,0,0]],[[1,2,0],[0,0,1]]]]`. The first sublist is the same as above. The second is a list with two entries; the former is `[[1,0,0]]` indicating that the eigenvalue \(3\) has exactly one eigenvector, `[1,0,0]`; the latter is `[[1,2,0],[0,0,1]]` indicating that the eigenvalue \(5\) has two linearly independent eigenvectors, `[1,2,0]` and `[0,0,1]`.
 
These are useful for getting _all_ relevant eigenvalue or eigenvector information, but they are a bit cumbersome to use if you only need a small piece of that information. `eigenlib.mac` provides some extra functions to help with this. 

* `get_eigenvalue(v,M)` will return the corresponding eigenvalue of eigenvector `v` for matrix `M`. If `v` is not an eigenvector of `M`, this function returns `false`.
* `get_eigenvector(L,M)` will return a basis for the eigenspace of `M` corresponding to eigenvalue `L`. It will always return a list of linearly independent column vectors unless `L` is not an eigenvalue of `M`, in which case it will return the empty list `[]`.
* `get_eigenvector(L,M,true)` indicates that the basis should be orthonormalised. This is likely to produce some ugly vectors if used on a random matrix and you are not using floats.
* `alg_mult(M,L)` gives the algebraic multiplicity of eigenvalue `L` for matrix `M`. If `L` is not an eigenvalue of `M`, returns 0.
* `geo_mult(M,L)` gives the geometric multiplicity of eigenvalue `L` for matrix `M`. If `L` is not an eigenvalue of `M`, returns 0.

## Diagonalisation

The contributed `matrixfactorizations.mac` file contains functions for diagonalising matrices or computing Jordan normal forms. Check [the documentation](Matrix_factorisation.md) for more details. 
 
## Miscellaneous useful functions

The contributed function `Rayleigh(M,v)` computes the Rayleigh quotient of a matrix `M` and `v`: \(\displaystyle \frac{\underline{\mathbf{v}}^{*}\,M\,\underline{\mathbf{v}}}{\underline{\mathbf{v}}^{*}\,\underline{\mathbf{v}}}\). This is an eigenvalue estimate for approximate eigenvector `v` and is sometimes used in power method calculations.
 
## Some suggestions for writing eigenvector-eigenvalue questions

These functions provide teachers with the ability to work with eigenvectors and eigenvalues of arbitrary matrices, but this is perhaps not best practice when expecting students to work by hand. STACK also provides a library of [matrix randomisation functions](Random_Matrices.md) that can help to generate sensible problems. In particular, teachers may find `rand_diag` and `rand_integer_invertible` useful to generate \(P\) and \(D\) such that \(A = PDP^{-1}\) has reasonably well-controlled eigenvectors and eigenvalues. `rand_diagonalizable` and `rand_defective` may also be good options. As usual, check your deployed variants carefully! 

If you like to keep numbers simple to work with, consider using the STACK function `scale_nicely` to remove fractions or common factors from eigenvector elements. 
