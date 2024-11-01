# Multiline input

Two input types allow students to enter multipule lines.  Each line must be a valid algebraic expression.  The result in Maxima is a list of expressions, one for each line.


#### Text area ####

This input allows the user to type in multiple lines, where each line must be a valid algebraic expression.  STACK passes the result to [Maxima](../../CAS/Maxima_background.md) as a list. Note, the teacher's answer and any syntax hint must be a list, of valid Maxima exprssions!  If you just pass in an expression strange behaviour may result.

If the `allowempty` tag is used the student's answer will be `[EMPTYANSWER]` to ensure the type of the student's answer is always a list.

#### Equivalence reasoning input ####

The purpose of this input type is to enable students to work line by line and reason by equivalence.
See the specific documentation for more information:  [Equivalence reasoning](../../Specialist_tools/Equivalence_reasoning/index.md).
Note, the teacher's answer and any syntax hint must be a list!  If you just pass in an expression strange behaviour may result.

If the `allowempty` tag is used the student's answer will be `[EMPTYANSWER]` to ensure the type of the student's answer is always a list.
