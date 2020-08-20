# Numerical input

This is documentation for the numerical [input type](Inputs.md).

This input type _requires_ the student to type in a number of some kind.  Any expression with a variable will be rejected as invalid.

While variable names are forbidden, by default function names are not forbidden.  `sin(pi/2)` represents a number.  If you actually want the student to type in `1` you need to forbid `sin` in the normal way using the forbidden words.

This input type will preserve trailing zeros in a student's answer.  For example, `0.00100` will keep the two trailing zeros. By default Maxima removes these, and these will also be removed in the algebraic input.

Note, some things (like forbid floats) can be applied to any numbers in an algebraic input, other tests (require n decimal places) cannot and can only be applied to a single number in this input type.


## Options

The "Extra options" field on the input must be a comma separated list of the following tags.  Note, these options may not depend on the question variables.

`floatnum`:  requires the student's answer to be a floating-point number, as judged by Maxima's `floatnump` predicate.  E.g. to use this and other Boolean options type `floatnum:true` etc. as a comma separated list in the extra options field.

`intnum`:  requires the student's answer to be an explicit integer.  E.g. `6` is valid, but `2*3`, `12/2` etc. are invalid.

`rationalnum`:  requires the student's answer to be a rational number (i.e. a fraction), as judged by STACK's `rational_numberp` predicate.  Integers are excluded here!

`rationalized`:  requires the denominator of any fractions in the student's answer to be free of surds and \(i\), as judged by STACK's `rationalized` function.

`mindp:n`: requires the student to type in `n` or more decimal places.

`maxdp:n`: requires the student to type in at most `n` decimal places.

`minsf:n`: requires the student to type in `n` or more significant figures.

`maxsf:n`: requires the student to type in at most `n` significant figures.

You cannot specify both decimal places and significant figures (even if they are min for one and max for the other).

If `mindp=maxdp=n` or `minsf=maxsf=n` then a student will be told to type in exactly `n` places/figures.

Note, where there is ambiguity in the number of significant figures (e.g. does 100 have 1 or 3 significant figures?) then the student will be given the benefit of the doubt.


