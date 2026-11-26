# Setting linear algebra questions in STACK

Linear algebra, next to calculus, is one of the pillars of modern mathematics and an important application in STACK is supporting questions which test understanding of linear algebra.

Core functionality and question design:

1. General [matrix manipulations](../../CAS/Matrix.md) in Maxima.
2. Core [vector/matrix functions](Linear_algebra_core.md) defined by STACK in the core code.
3. Using [vectors](Vectors.md).
4. Assessment of matrices with [answer tests](Answer_tests.md).
5. Practical strategies for [randomisation in linear algebra](Randomisation_in_linear_algebra.md), including generating linear systems, matrix factorisations and eigenvalue problems with "nice" answers.
6. [Validators for linear algebra](../../CAS/Validator.md#linear-algebra), which reject answers such as a parametrised or complex vector before a property test can accept them.

Reference documentation for contributed libraries:

1. Creating [random matrices](Random_Matrices.md) in contributed `rand_matrix.mac`.
2. [Matrix functions](Matrix_library.md) in contributed `matrix.mac`.
3. [Vector and vector space functions](Vector_space.md) in contributed `vectorspaces.mac`.
4. [Vector geometry functions](Vector_geometry.md) in contributed `vectorgeometry.mac`.
5. [Eigenvalue/vector functions](Eigen.md) in contributed `eigenlib.mac`.
6. [Matrix factorisations](Matrix_factorisation.md) in contributed `matrixfactorizations.mac`

If using an earlier version of STACK than 4.9.0, 2. through 6. above require an extra inclusion (see the [linear algebra page](Linear_algebra_core.md)).

## Solving systems of linear equations

Using solve can throw errors, so use `linsolve` instead.  For example.

    /* Decide if a vector is in W */
    point_in_space(W, wx):= linsolve(flatten(args(W))-first(args(transpose(wx))), listofvars(W));

If the above is the empty list, there is no solution.  Otherwise a solution is returned.

    /* Calculate the canonical form of a column space of a system. */
    cspace(ex):= block([M],
      M: coefmatrix(flatten(args(ex)), listofvars(ex)),
      ev(transpose(rref(transpose(M))), simp)
    );
