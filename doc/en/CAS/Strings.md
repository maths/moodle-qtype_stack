# Maxima strings in STACK questions

Strings are a basic data type in Maxima.  The predicate function `stringp(ex)` determines whether an expression is a string.  The function `string(ex)` takes a Maxima expression and returns a string representation.  We do not support Maxima's `parse_string` function.  There is no way to turn a string into a Maxima expression through STACK.  For example, if you use the string input you cannot later parse the student's answer into a Maxima expression.  Therefore, only use the string input if your answer is actually a string.

_The whole point of STACK is that teachers should seek to establish mathematical properties and the string match tests are provided for completeness (and because they are trivial to implement).  Experienced question authors almost never use the string match tests.  If you find yourself needing to use the string match tests for something mathematical please contact the developers._

There are 4 [string-related answer tests](../Authoring/Answer_Tests/String.md).

* String
* StringSloppy
* Levenshtein
* SRegExp

If your answer is a language string, then please consider using the [Damerau-Levenshtein distance](../Topics/Levenshtein_distance.md) rather than a string match.

## LaTeX within Maxima strings

You have to protect LaTeX backslashes in Maxima strings.  This is tedious, tricky and error prone!

For example, you have to define Maxima strings such as "\&#8203;\&#8203;( f(&#8203;n)=\&#8203;\&#8203;sin(n\&#8203;\&#8203;pi) \&#8203;\&#8203;)"

To help with this there is a tool to automatically add in these extra slashes as a one-off process.

The adminui tools have a chat page.  ou can find the tool under the "STACK question dashboard" -> "Send general feedback to the CAS".  At the bottom of this page is an option "Protect slashes within Maxima string variables".

The "Protect slashes within Maxima string variables" option will add slashes _every time_ the option is selected, so this is effectively a one-off process.  However, you can write the strings in normal LaTeX and proof-read. Move these to maxima strings, before converting to Maxima strings.
