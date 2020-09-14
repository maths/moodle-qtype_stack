# Buggy rules

In order to establish that the student has done something
particular but wrong, it is useful for us to be able to apply
wrong or buggy rules to expressions.  A typical example would
be to expand out powers in the wrong way, e.g.

\[(x+y)^2=x^2+y^2.\]

# Powers obey linearity

`buggy_pow(ex)` Implements the buggy linearity rule for exponentiation, i.e.

\[(a+b)^n \rightarrow a^n+b^n.\]

This is  useful if we want to compare a student's answer to the result  of having done something wrong.

# Naive addition of fractions

`mediant(ex1,ex2)` calculates the mediant of two rational expressions.
The mediant of two fractions

\[ \mbox{mediant}\left(\frac{p_1}{q_1} , \frac{p_2}{q_2}\right)
:= \frac{p_1+p_2}{q_1+q_2}.\]

Note that both `denom` and `num` work on non-rational expressions, assuming the expression to be "over one" by implication.  Hence `mediant` will also assume the denominator is also one in such cases.

This is not always a buggy rule. It is used, for example, in connection with Farey sequences, but it is included here as in assessment this function is useful for checking a common mistake when adding fractions.


There is scope for further examples of such rules.

## See also

[Maxima reference topics](index.md#reference)
