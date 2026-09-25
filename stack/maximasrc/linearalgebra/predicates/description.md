Predicates for the shape of Maxima matrices. These functions are loaded by
STACK and are available in question variables, answer tests and feedback.

Use `col_vecp`, `row_vecp` and `vectorp` to distinguish column and row matrices;
use `squarep` to check equal dimensions and `diagp` to check off-diagonal entries.
These checks are separate: a diagonal matrix can be rectangular, and a square
matrix need not be diagonal or invertible.

They accept Maxima matrices, not lists or the inert `c`/`r` vector notation.
When the `c`/`r` forms are allowed, convert them with `vec_convert` first.
The individual references describe empty shapes and simplification behaviour;
add explicit size or entry constraints when the question requires them.
