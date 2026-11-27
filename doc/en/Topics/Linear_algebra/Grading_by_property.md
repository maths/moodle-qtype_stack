# Grading linear algebra answers by property

Many linear algebra questions have more than one correct answer.  Any non-zero multiple of an eigenvector is an eigenvector, a subspace has many bases, and a diagonalisation \(A=PDP^{-1}\) can scale or reorder the columns of \(P\).  Comparing the student's answer with the teacher's answer marks most correct answers wrong.  Instead, the potential response tree (PRT) should test the property which makes an answer correct: "is this an eigenvector of \(M\) for the eigenvalue \(3\)?", not "is this \((1,1)\)?".

This page sets out the principles, and then gives a pattern for each common kind of answer.  The functions used come from the contributed libraries: see [eigenvectors](Eigen.md), [vector spaces](Vector_space.md) and the [matrix library](Matrix_library.md).

## Principles

1. **Validate first.**  A property test can hold for an answer the student should not be allowed to give.  For example, `eigenvectorp(matrix([t],[t]), matrix([2,1],[1,2]))` is `true`, so is the same test with `%i` in place of `t`, and a parametrised solution of \(Ax=b\) satisfies \(Ax=b\) identically.  Reject such answers as invalid with one of the [validators for linear algebra](../../CAS/Validator.md#linear-algebra), or with the `realnum` option of the [numerical input](../../Authoring/Inputs/Numerical_input.md) for a single number.
2. **Test the property, with library functions.**  Use the contributed predicates (`eigenvectorp`, `subspace_equivp`, `lin_indp`, `invertiblep`, `diagonalizablep`, ...) rather than writing your own, and check the conditions one at a time so that each failure can have its own feedback.
3. **Test every condition the definition needs.**  For example, a basis must span the space _and_ be linearly independent, and `subspace_equivp` checks only the first.  In a diagonalisation, \(AP=PD\) holds for a singular \(P\) as well, so \(P\) must also be checked to be invertible.
4. **Still give a teacher's answer.**  It is shown to students after the attempt and used in the question tests, so it should be a correct answer, preferably the simplest.
5. **Write two correct question tests with different answers**, for example \(v\) and \(-2v\) for an eigenvector.  A single correct test cannot tell a PRT which tests the property from one which compares with the teacher's answer.  Add one test for each wrong answer that has its own feedback, and one test with an invalid answer, which checks that the validator is in place.
6. **Forbid functions that compute the answer** (see [below](#forbidwords)).

In the patterns below, the code in "feedback variables" computes the conditions, and each PRT node tests one of them with `AlgEquiv` against `true`.  Feedback variables are evaluated with the question's simplification setting, which is usually on.

## An eigenvector

Ask for an eigenvector of \(M=\begin{pmatrix}2&1\\1&2\end{pmatrix}\) for the eigenvalue \(3\).  Use a matrix input of size \(2\times 1\), with the extra option `validator:validate_nummat`.

Question variables:

    stack_include_contrib("eigenlib.mac");
    stack_include_contrib("validators.mac");
    M: matrix([2,1],[1,2]);
    L: 3;
    ta1: matrix([1],[1]);

Feedback variables:

    ok: eigenvectorp(ans1, M, L);
    other: eigenvectorp(ans1, M);

Node 1 tests `ok`.  If it is false, node 2 tests `other`, which is true for an eigenvector for a different eigenvalue, for feedback such as "This is an eigenvector, but for a different eigenvalue".  `eigenvectorp` is false for the zero vector.

| Test | `ans1` | Result |
| --- | --- | --- |
| correct | `matrix([1],[1])` | full marks |
| correct, different | `matrix([-2],[-2])` | full marks |
| other eigenvalue | `matrix([1],[-1])` | node 2 feedback |
| zero vector | `matrix([0],[0])` | wrong |
| invalid | `matrix([t],[t])` | invalid |

## A basis of a subspace

Ask for a basis of \(U=\{x\in\mathbb{R}^3 : x_1+x_2+x_3=0\}\).  The number of vectors is part of the answer, so a fixed number of input boxes would give it away.  Instead, the student enters a set of column vectors in one algebraic input, for example `{c(1,-1,0), c(0,1,-1)}`.

Question variables:

    stack_include_contrib("vectorspaces.mac");
    stack_include_contrib("validators.mac");
    validate_vecset3(ex) := validate_vecset(ex, 3);
    tl: [[1,-1,0],[0,1,-1]];
    ta1: {c(1,-1,0), c(0,1,-1)};

Use the extra option `validator:validate_vecset3` in the input.  Feedback variables:

    sl: map(lambda([v], list_matrix_entries(vec_convert(v))), listify(ans1));
    sp: subspace_equivp(sl, tl);
    ind: lin_indp(sl);

Node 1 tests `sp`, and node 2 tests `ind`.  A set is put into canonical order, so do not rely on the order of `sl`.  If the order of the vectors matters, as for an ordered basis, use a list and `validate_veclist` instead.

| Test | `ans1` | `sp`, `ind` |
| --- | --- | --- |
| correct | `{c(1,-1,0), c(0,1,-1)}` | `true`, `true` |
| correct, different | `{c(1,0,-1), c(1,1,-2)}` | `true`, `true` |
| does not span | `{c(1,-1,0), c(2,-2,0)}` | `false`, `false` |
| dependent | `{c(1,-1,0), c(0,1,-1), c(1,0,-1)}` | `true`, `false` |
| not in \(U\) | `{c(1,0,0), c(0,1,-1)}` | `false`, `true` |
| invalid | `{c(1,t,0)}` | invalid |

The empty set is linearly independent, and `{}` passes the validator: it is the basis of \(\{0\}\).

## A linear map entered as formulas

Ask for a linear map \(T:\mathbb{R}^2\to\mathbb{R}^2\) whose null space is spanned by \((1,-1)\).  The student enters one formula per component, in two algebraic inputs, in the variables `x1` and `x2`.  Use the extra options `validator:validate_x2, consolidatesubscripts` in each, so that a stray parameter such as \(t\,x_1\) is invalid and `x_1` is read as `x1`.

Question variables:

    stack_include_contrib("validators.mac");
    validate_x2(ex) := validate_vars(ex, [x1, x2]);
    X: [x1, x2];
    ta1: x1+x2;
    ta2: 0;

Feedback variables:

    F: [ans1, ans2];
    lin: is(listofvars(jacobian(F, X)) = []) and is(expand(subst(map(lambda([v], v=0), X), F)) = [0,0]);
    MB: coefmatrix(F, X);
    ker: is(MB . matrix([1],[-1]) = zeromatrix(2,1));
    rk: is(rank(MB) = 1);

`lin` is true when each component has constant partial derivatives and vanishes at \(0\), that is, when the map is linear.  Test it first: `coefmatrix` ignores constant and non-linear terms, so `MB` means nothing for a map which is not linear.  Then `ker` checks that \((1,-1)\) is in the null space, and `rk` that the null space is no bigger.

| Test | `ans1`, `ans2` | Result |
| --- | --- | --- |
| correct | `x1+x2`, `0` | full marks |
| correct, different | `2*x1+2*x2`, `-x1-x2` | full marks |
| not linear | `x1+x2+1`, `0` | `lin` false |
| null space too small | `x1`, `x2` | `ker` false |
| zero map | `0`, `0` | `rk` false |
| invalid | `t*x1+x2`, `0` | invalid |

## A solution of a linear system, or a coset representative

Ask for any solution of \(Ax=b\), with \(A=\begin{pmatrix}1&2&0\\0&1&1\end{pmatrix}\) and \(b=\begin{pmatrix}3\\1\end{pmatrix}\).  The student enters one number per box, in three numerical inputs `ans1`, `ans2`, `ans3`, with the option `realnum`.

Feedback variables:

    ok: is(ratsimp(A . matrix([ans1],[ans2],[ans3]) - b) = zeromatrix(2,1));

Correct tests: \((1,1,0)\) and \((3,0,1)\).  Without the restriction to numbers, the parametrised solution \((1+2t, 1-t, t)\) would also give `true`.

A coset \(v+U\) is graded in the same way.  The student enters one representative \(s\).  Choose a matrix `MC` in the question variables whose null space is exactly \(U\), and test `MC . (s - v)` against the zero vector.  Two correct tests use \(v\) and \(v+u\) for a non-zero \(u\in U\).

## A factorisation

Ask for a diagonalisation \(A=PDP^{-1}\) of \(A=\begin{pmatrix}2&1\\1&2\end{pmatrix}\).  The student enters \(P\) and \(D\) in two matrix inputs, each with `validator:validate_nummat`.

Question variables:

    stack_include_contrib("matrix.mac");
    stack_include_contrib("validators.mac");
    A: matrix([2,1],[1,2]);
    ta1: matrix([1,1],[1,-1]);
    ta2: matrix([3,0],[0,1]);

Feedback variables:

    inv: invertiblep(ans1);
    dg: diagp(ans2);
    eq: is(A . ans1 = ans1 . ans2);

Testing \(AP=PD\) avoids computing \(P^{-1}\), which may not exist.  But it holds for a singular \(P\) too: \(P=\begin{pmatrix}1&1\\1&1\end{pmatrix}\) with \(D=3I\) passes `dg` and `eq`.  So test `inv` first.  `diagp` tests for literal zeros, so run it with simplification on, as feedback variables usually are.

| Test | `ans1`, `ans2` | `inv`, `dg`, `eq` |
| --- | --- | --- |
| correct | `matrix([1,1],[1,-1])`, `matrix([3,0],[0,1])` | all `true` |
| correct, different | `matrix([1,2],[-1,2])`, `matrix([1,0],[0,3])` | all `true` |
| \(D\) in the wrong order | `matrix([1,1],[1,-1])`, `matrix([1,0],[0,3])` | `eq` false |
| singular \(P\) | `matrix([1,1],[1,1])`, `matrix([3,0],[0,3])` | `inv` false |

Other factorisations follow the same plan: multiply the factors back, and check the defining property of each factor, for example `triup` for an upper triangular factor and `orth_matrixp` for an orthogonal one.

## A matrix with a given property

Ask for a \(2\times 2\) matrix whose only eigenvalue is \(2\) and which is not diagonalisable.  Use a matrix input with `validator:validate_nummat`.

Feedback variables:

    ev2: is(expand(charpoly(ans1, z)) = expand((z-2)^2));
    nd: not(diagonalizablep(ans1));

`diagonalizablep`, in `matrix.mac`, decides diagonalisability over \(\mathbb{C}\): it is true for the rotation `matrix([0,-1],[1,0])` and false for `matrix([%i,1],[0,%i])`.  It needs the eigenvalues exactly, which is fine for the small matrices of a typical question.  Do not test for repeated eigenvalues with `gcd(p, diff(p, z))`: Maxima's `gcd` treats `%i` as a variable, and returns \(1\) for \(p=(z-i)^2\).

Correct tests: `matrix([2,1],[0,2])` and `matrix([3,1],[-1,1])`.  Wrong: `matrix([2,0],[0,2])`, which is diagonalisable, and `matrix([1,1],[0,1])`, which has the wrong eigenvalue.

## Forbidden functions {#forbidwords}

A student may only call the functions STACK allows in student input, so functions from the contributed libraries, such as `get_eigenvector`, `eigenvectorp` or `diagonalize`, are rejected already.  But many of Maxima's own linear algebra functions are allowed, and compute an answer.  Forbid them with the [collections of forbidden words](../../Authoring/Inputs/index.md#Forbidden_Words):

* For matrix, vector and set-of-vector inputs: `[[BASIC-MATRIX]], solve`.  `[[BASIC-MATRIX]]` includes `charpoly`, `eigenvectors`, `invert`, `jordan`, `newdet`, `nullspace`, `rank` and many more.  `solve` is in `[[BASIC-ALGEBRA]]`, which you may wish to forbid in full.
* For polynomial inputs, such as a characteristic or minimal polynomial: `[[BASIC-MATRIX]]`, which includes `charpoly`, `minimalPoly`, `jordan` and `eivals`.

A forbidden word cannot be allowed again in the allowed words.  `[[BASIC-MATRIX]]` also forbids `transpose`, `col` and `dotproduct`, so if students need to type one of these, list the individual functions you want to forbid instead.
