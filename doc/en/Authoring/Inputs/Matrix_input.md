# Matrix inputs

STACK provides three ways to let students input a matrix:

1. The matrix input is a fixed grid, one box for each element.
2. The matrix of variable size input is a textarea into which students type in their answer.
3. Students can type in the maxima `matrix` command into another input, e.g. the default algebraic input.

## Matrix input ###

The size of the matrix is inferred from the model answer. STACK then adds an appropriate grid of boxes (of size Box Size) for the student to fill in. This is easier than having students type in [Maxima](../../CAS/Maxima_background.md)'s `matrix` command, but does give the game away about the size of the required matrix.

_The student may not fill in part of a matrix._  If they do so, the remaining entries will be completed with `?` characters which render the attempt invalid. STACK cannot cope with empty boxes here.

We cannot use the `EMPTYANSWER` tag for the teacher's answer with the matrix input, because the size of the matrix is inferred from the model answer.  If a teacher really wants a correct answer to be a completely empty input then they must use a correctly formatted matrix with `null` values

    ta:transpose(matrix([null,null,null]));

The shape of the parentheses surrounding the brackets is taken from the question level options, except matrix inputs cannot display curly brackets `{`.  (If you can create CSS to do this, please contact the developers!)

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

The matrix of variable size input is a textarea into which students type in their answer.

Students must separate their matrix elements by spaces, and newline characters.

Input box size is used to determine the starting width of the input.

If you use the `allowempty` option then an empty answer is indicated by the `EMPTYANSWER` tag.  This is a different _type_ than a matrix.  (We could have chosen `matrix()` as the empty matrix, but `EMPTYANSWER` is more in keeping with other inputs.)
