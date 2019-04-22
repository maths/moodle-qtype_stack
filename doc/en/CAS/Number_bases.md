# Number bases

STACK has dedicated support for number bases.

## Maxima functions

In Maxima, we represent base \(b\) numbers as with an intert form `basen(n, b)`.  This is similar to the other intert/ephemeral forms such as used in the [scientific units](../Authoring/Units.md).  Most of the work in STACK questions should be done with this function.

We also have the following functions for converting to and from a string representation.

    frombasen(str, b);
    frombasen("1111", 2);

Takes the string `str` and base `b` and returns the base 10 representation.

    tobasen(n, b);
    tobasen(15, 2);

Takes the integer `n` and base `b` and returns the base \(b\) representation of \(n\) as a string. This function is designed for converting from a number to a base n representation string.

    tobasen(n, b, mode, mindigits) 

* `n` is the integer number to convert.
* `b` is the radix of the number; must be \(2 \leq b \leq 36\).
* `mindigits` is the minimum number of figures outputted.

`mode` is a string controlling the format:

*    `D`    STACK compatible format; base must be < 11.
*    `D<`   Variation of D syntax where the number reads as if padded from the right with
         zeroes, i.e. the most significant digit is fixed as maximum value. Useful
         for processing fixed point numbers.
*    `M`    Maxima syntax: number will be 0 prefixed if base is 11+; This is the default.
*    `M<`   Variation of M syntax where the number reads as if padded from the right with
         zeroes, i.e. the most significant digit is fixed as maximum value. Useful
         for processing fixed point numbers.
*    `G`    Greedy format: number is output without any prefix regardless of base. The simplest
         format for literal value base conversion questions but does not play nicely with
         others; i.e. base 11+ numbers will be confused with variables and some floats.
         e.g. abcd or 1e0 in base 16.
*    `G<`   Variation of G syntax where the number reads as if padded from the right with
         zeroes, i.e. the most significant digit is fixed as maximum value. Useful
         for processing fixed point numbers.
*    `B/B*` Visual Basic number syntax: &HFF &o77 &b11. Only bases 2,8,10 and 16 are valid.
*    `C/C*` C/C++/Java number syntax: 0xff 077 0b11. Only bases 2,8,10 and 16 are valid.
*    `S`    Suffix syntax; number will appear with the radix as a subscripted suffix (123_8).
*    `_/_*` Suffix syntax; number will appear with the radix as a subscripted suffix (123_8).
         Numbers base 11+ will be prefixed with a zero.
*    `S/S*` Greedy Suffix syntax; as `_/_*`, but no prefix generated for base 11+

## Input options

Students need to type in their answer, If the teacher's answer uses the intert `basen` function then the input automatically adapts and assumes the students' answer will also be in the same base.

## Adding test cases to questions

Question authors should add [question tests](../Authoring/Testing.md) to their questions.  There are two options.

1. Enter the answer as you would expect the student to type it.  E.g. if you expect seven in binary then enter `111` as the test case.  The input will automatically detect the `basen` function in the teacher's answer and interpret the student's answer accordingly.
2. Enter an answer using the `basen` function, e.g. `basen(7,2)`.  This allows the inclusion of random variables.
