Predicates for matrix shape and algebraic properties, available in core STACK.

Use `col_vecp`, `row_vecp` and `vectorp` to distinguish column and row matrices;
use `squarep`, `diagp`, `triup` and `trilp` to check their structure. A diagonal
matrix may be rectangular, and a triangular matrix need not be invertible or
in row echelon form.

Use `REFp` for row echelon form, `symp` for symmetry, `invertiblep` for
invertibility, and `diagonalizablep` for diagonalizability. The orthogonality
predicates test columns using the ordinary dot product or a supplied scalar
product.

Most predicates require a Maxima matrix. `orthogonal_columnsp` and
`orthonormal_columnsp` also accept lists of column vectors through
`cols_to_matrix`. Consult each function for its accepted inputs and optional
arguments. Simplification and Maxima's ability to recognise zero expressions
can affect the result; add explicit size or entry constraints when needed.
