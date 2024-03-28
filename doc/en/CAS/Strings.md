# Strings in STACK questions

If your answer is a language string, then please consider using the [Damerau-Levenshtein distance](../Topics/Levenshtein_distance.md) rather than a string match.

Strings are a basic data type in Maxima.  The predicate function `stringp(ex)` determines whether an expression is a string.  The function `string(ex)` takes a Maxima expression and returns a string representation.  We do not support Maxima's `parse_string` function.  There is no way to turn a string into a Maxima expression through STACK.  For example, if you use the string input you cannot later parse the student's answer into a Maxima expression.  Therefore, only use the string input if your answer is actually a string.

_The whole point of STACK is that teachers should seek to establish mathematical properties and the string match tests are provided for completeness (and because they are trivial to implement).  Experienced question authors almost never use the string match tests.  If you find yourself needing to use the string match tests for something mathematical please contact the developers._

There are 4 [string-related answer tests](../Authoring/Answer_Tests/String.md).

* String
* StringSloppy
* Levenshtein
* SRegExp
