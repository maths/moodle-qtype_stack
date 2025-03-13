# Multiline input

Two input types allow students to enter multipule lines.  Each line must be a valid algebraic expression.  The result in Maxima is a list of expressions, one for each line.

The syntax hint is initially processed as castext.  This is then assumed to be a Maxima list and re-processed as a Maxima expression. If valid, STACK removes "noun" operators, e.g. `nounand` will be converted to `and` before the syntax hint is displayed.  Therefore, you can add in a syntax hint of the form `[2x+x=?]`.  This is unchanged by castext processing.  It is a list, but the contents are not valid Maxima and so are just displayed.

#### Text area ####

This input allows the user to type in multiple lines, where each line must be a valid algebraic expression.  STACK passes the result to [Maxima](../../CAS/Maxima_background.md) as a list. Note, the teacher's answer and any syntax hint must be a list, of valid Maxima exprssions!  If you just pass in an expression strange behaviour may result.

If the `allowempty` tag is used the student's answer will be `[EMPTYANSWER]` to ensure the type of the student's answer is always a list.

#### Equivalence reasoning input ####

The purpose of this input type is to enable students to work line by line and reason by equivalence.
See the specific documentation for more information:  [Equivalence reasoning](../../Specialist_tools/Equivalence_reasoning/index.md).
Note, the teacher's answer and any syntax hint must be a list!  If you just pass in an expression strange behaviour may result.

If the `allowempty` tag is used the student's answer will be `[EMPTYANSWER]` to ensure the type of the student's answer is always a list.
