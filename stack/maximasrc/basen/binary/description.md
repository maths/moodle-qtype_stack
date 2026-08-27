Functions specific for working with binary `stackbasen` objects.

Basically, the `stackbasen` system does not directly understand negative
numbers nor are there general bit-wise logical operators in the system. For
these purposes we have special binary tools in this category. The common thing
for these tools is that you need to describe the number of bits in your numbers
otherwise things won't work.

Some of these tools also consider octal and hexadecimal numbers as "binary".
For example `sbasen_bitwidth` will accept bases 2, 8 and 16 and provide
the number of bits required for the value with its paddings. Likewise
`sbasen_bitwise_eval` will also act on numbers in those bases. To identify
numbers in those bases use `sbasen_is_cs`.