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

### Augmented matrix grids

Use the input extra option `columnseparators:3` to draw a vertical separator after column 3,
for example for a 3-by-4 augmented system. For multiple blocks, use semicolons:
`columnseparators:2;4` draws separators after columns 2 and 4. Column numbers are one-based,
must be strictly increasing, and must be smaller than the instantiated matrix width.

The teacher answer and student response remain ordinary Maxima matrices. The separators are
presentation metadata: they do not add fields or change validation, algebra, grading or field names.
The validation echo still uses ordinary matrix notation; display the augmented form separately in
question text or feedback where needed. This option does not infer boundaries from `aug(A,b)`.
API clients receive the optional `columnseparators` array with the same one-based column numbers
and should draw these boundaries in their own matrix grid.

## Matrix of variable size input ###

The matrix of variable size input is a textarea into which students type in their answer.

Students must separate their matrix elements by spaces, and newline characters.

Input box size is used to determine the starting width of the input.

If you use the `allowempty` option then an empty answer is indicated by the `EMPTYANSWER` tag.  This is a different _type_ than a matrix.  (We could have chosen `matrix()` as the empty matrix, but `EMPTYANSWER` is more in keeping with other inputs.)
