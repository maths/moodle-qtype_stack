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

### Augmented matrix inputs

Use an augmented model answer directly:

    A:matrix([1,2],[3,4]);
    b:c(5,6);
    ta:aug(A,b);

Choose the fixed matrix input with model answer `ta`. Its instantiated value is
`aug_matrix(matrix([1,2],[3,4]),c(5,6))`: the grid automatically has two rows, three columns
and an internal separator after column two. Multiple blocks such as `aug(A,B,C)` work in the
same way. Blocks may be matrices or `c(...)`/`r(...)` vectors and must be nonempty with equal
row counts. No separate boundary option is required.

The student fills one grid. Submission and validation reconstruct the same augmented constructor
and block types, and the validation echo displays the augmented matrix again. Saved responses,
model answers and syntax hints are mapped back to the same cells. Ordinary matrix inputs retain
their current behaviour.

The teacher and student values can be compared as augmented objects. For answer tests which require
an ordinary matrix, use `de_aug(ans1)` (and `de_aug(ta)` where appropriate). `aug`, its display rules
and `de_aug` are available in the linear-algebra core; no contributed-library load is required.

API consumers receive `casValueType: "aug_matrix"` and a `blocks` array, for example
`[{"type":"matrix","columns":2},{"type":"c","columns":1}]`. Use these widths for grid boundaries
and reconstruct each block with its indicated constructor. Field names and the native Moodle AJAX
grid format do not change.

## Matrix of variable size input ###

The matrix of variable size input is a textarea into which students type in their answer. For a matrix, students separate entries in each row with spaces and separate rows with line breaks.

As with the fixed grid, a `c(...)` or `r(...)` model answer makes the input return the same vector form. For these vector types, the textarea is treated as a one-dimensional list of components, with spaces and line breaks as equivalent separators. Thus both `1 2 3` and the same three components on separate lines are interpreted as `c(1,2,3)` for a `c(...)` model answer, or `r(1,2,3)` for an `r(...)` model answer. The model-answer type, not the student's choice of whitespace, determines the vector orientation. STACK displays the interpreted column vector with one component per line and the interpreted row vector on one line.

Input box size is used to determine the starting width of the input.

If you use the `allowempty` option then an empty answer is indicated by the `EMPTYANSWER` tag.  This is a different _type_ than a matrix.  (We could have chosen `matrix()` as the empty matrix, but `EMPTYANSWER` is more in keeping with other inputs.)

### Augmented matrices of variable size

The variable-size matrix textarea also accepts an augmented model answer such as `aug(A,b)`.
Students separate entries with spaces, rows with newlines, and blocks with `|` on every row:

```text
1 2 | 5
3 4 | 6
```

Rows and matrix-block widths are student-selected; the model does not fix their dimensions. The model
does fix the number and constructor types of blocks. A `c(...)` block must remain one column and an
`r(...)` block must remain one row. Matrix blocks can have any positive number of columns. Every row
must have the same nonempty block widths. Missing or inconsistent separators are validation errors.
For a malformed block structure, validation shows a short multi-line example:

```text
1 ... | 2
3 ... | 4
```

The feedback explicitly leaves the row count open. Models containing a row-vector block show only one row.
The dots stand for optional extra entries in a matrix or row-vector block; a column-vector block
contains just one entry. The example uses neither the teacher's entries nor matrix-block dimensions.
Feedback reminds students to keep block widths consistent between rows, or use only one row when
the model contains a row-vector block.

Submitted answers preserve `aug_matrix(...)` and the block constructors, and validation renders the
augmented matrix with the selected outer bracket style. Model answers, syntax hints, saved responses
and AJAX validation use the same bar-separated textarea representation. Ordinary matrices retain
their existing space/newline syntax. The row/column-vector behavior previously introduced in #1847
is preserved.

The textarea wrapper exposes `data-stack-input-value-type="aug_matrix"`. API clients receive
`casValueType`, `blockTypes` and `blockSeparator` metadata. These are input-surface hooks; a dedicated
CSS wrapper around augmented MathJax output is not introduced here.
