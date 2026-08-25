# Matrix inputs

STACK provides three ways to let students input a matrix:

1. The matrix input is a fixed grid, one box for each element.
2. The matrix of variable size input is a textarea into which students type in their answer.
3. Students can type in the maxima `matrix` command into another input, e.g. the default algebraic input.

## Matrix input ###

The size of the matrix is inferred from the model answer. STACK then adds an appropriate grid of boxes (of size Box Size) for the student to fill in. This is easier than having students type in [Maxima](../../CAS/Maxima_background.md)'s `matrix` command, but does give the game away about the size of the required matrix.

The grid normally returns a Maxima `matrix(...)`. If its model answer is instead a column vector `c(...)` or row vector `r(...)`, the grid infers the corresponding shape and returns that same vector form. This keeps row vectors, column vectors, and one-row or one-column matrices distinct. Use `stack_linear_algebra_declare(true)` in the question variables to display `c(...)` and `r(...)` as vectors, and use `vec_convert(...)` in a PRT when a matrix representation is needed for an answer test.

_The student may not fill in part of a matrix._  If they do so, the remaining entries will be completed with `?` characters which render the attempt invalid. STACK cannot cope with empty boxes here.

We cannot use the `EMPTYANSWER` tag for the teacher's answer with the matrix input, because the size of the matrix is inferred from the model answer.  If a teacher really wants a correct answer to be a completely empty input then they must use a correctly formatted matrix with `null` values

    ta:transpose(matrix([null,null,null]));

The brackets around the HTML inputs are taken from the question-level `matrixparens` option. The left and right
delimiters are also exposed as labelled mathematical elements for assistive technologies.

With `stack_linear_algebra_declare(true)`, the TeX display of `c(...)` and `r(...)` also follows `matrixparens` through `stack_matrix_disp(...)`. This gives matrix and vector inputs and their resulting expressions one common delimiter choice. Independent matrix and vector delimiter settings are discussed in [#1848](https://github.com/maths/moodle-qtype_stack/issues/1848).

## Matrix of variable size input ###

The matrix of variable size input is a textarea into which students type in their answer. For a matrix, students separate entries in each row with spaces and separate rows with line breaks.

As with the fixed grid, a `c(...)` or `r(...)` model answer makes the input return the same vector form. For these vector types, the textarea is treated as a one-dimensional list of components, with spaces and line breaks as equivalent separators. Thus both `1 2 3` and the same three components on separate lines are interpreted as `c(1,2,3)` for a `c(...)` model answer, or `r(1,2,3)` for an `r(...)` model answer. The model-answer type, not the student's choice of whitespace, determines the vector orientation. STACK displays the interpreted column vector with one component per line and the interpreted row vector on one line.

Input box size is used to determine the starting width of the input.

If you use the `allowempty` option then an empty answer is indicated by the `EMPTYANSWER` tag.  This is a different _type_ than a matrix.  (We could have chosen `matrix()` as the empty matrix, but `EMPTYANSWER` is more in keeping with other inputs.)
