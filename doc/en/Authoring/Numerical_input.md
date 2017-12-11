# Numerical input

This is documentation for the numerical [input type](Inputs.md).

This input type _requires_ the student to type in a number of some kind.  Any expression with a variable will be rejected as invalid.

While variable names are forbidden, by default function names are not forbidden.  `sin(pi/2)` represents a number.  If you actually want the student to type in `1` you need to forbid `sin` in the normal way using the forbidden words.

This input type will preserve trailing zeros in a student's answer.  For example, `0.00100` will keep the two trailing zeros. By default Maxima removes these, and these will also be removed in the algebraic input.

Note, some things (like forbid floats) can be applied to any numbers in an algebraic input, other tests (require n decimal places) cannot and can only be applied to a single number in this input type.


## Options

The "Extra options" field on the input must be a comma separated list of the following tags.

`floatnum`:  requires the student's answer to be a floating point number, as judged by Maxima's `floatnump` predicate.

`rationalnum`:  requires the student's answer to be a rational number (i.e. a fracion), as judged by STACK's `rational_numberp` predicate.  Integers are excluded here!

`rationalized`:  requires the demoninator of any fractions in the student's answer to be free of surds and \(i\), as judged by STACK's `rationalized` function.

`mindp(n)`: requires the student to type in `n` or more decimal places.

`maxdp(n)`: requires the student to type in at most `n` decimal places.

## TODO

1. add in support for significant figure checking.
2. add in support for checking number bases.
