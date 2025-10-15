A collection of functions meant for dealing with base-N numbers coming
through the expanded syntax mode and being presented as inert functions.

Like `stackunits`, `stackbasen` "objects" need to be handled with tools.

Unlike `stackunits`, `stackbasen` "objects" can be nonsense from the start,
the expanded syntax does not restrict the student from inputting something
like `0b012345` where the digits are most definitely not binary digits. Thus
one should always decide when to validate the objects, you may do it in
input validation or you might do it later in grading.

A `stackbasen` object is an inert function with exactly three arguments:
 - The first argument is the raw input form of the number in a given base 
   and format.
 - The second argument defines the format. Currently, there are two options
   for format:
    - `"C"` for `0b101010` style binary, `0xF00F` style hexadecimal and
      `0777` for octal. Note that `b` and `x` are always lower case in
      the second char of those two formats, otherwise digits may be upper
      or lower case or even mixed. Hexadecimal digits are 0-9 and A-F.
    - `"S"` for `AB2Z_36` for freely chosen bases from 2-36. The digits
      aro from 0-9 and A-Z (or a-z, case does not matter). The base after
      `_` is always presented in base-10.
 - The third option is the raw base-10 integer defining the base, even when
   it is already interpretable from the two previous arguments we carry
   it as a helpful easily accessible part of the object.

Note that there are overridable functions defining the presentation of these
formats. You do not need to replace the whole `stackbasen` `texput` logic. By
default format `"C"` is being rendered as `\texttt` and `"S"` with `\textrm`.

Also note that while it may seem like one could simply replace all the logic
defining the digits and the hard limit of 36, changing the logic at this level
is not enough. Student input will only turn to `stackbasen` objects if lexer
level rules and AST-filters agree on the acceptable digits. And one cannot
adjsut those through author accessible means.