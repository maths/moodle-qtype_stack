# String matching

On rare occasions string matching is useful.

Note that rather than using a string match a useful feature is the [Damerau-Levenshtein distance](../../Topics/Levenshtein_distance.md).

If the argument to these tests is _not_ a Maxima string then (1) whitespace is stripped off and (2) the expression is turned into a string.  The conversion to a Maxima string happens inside PHP: if you use `ATString` in the sandbox without a string argument, the Maxima function will throw an error.

### ATString ###

This test performs a string match.  Whitespace anywhere within a string is unchanged.

If you use a string input, and want to trim whitespace from each end before using this answer test, you can process the string in Maxima, e.g. using `strim(" ", ans1)`.  This can be used as an argument to `ATString` in the PRT.

### ATStringSloppy ###

This test removes all spaces, newlines and tabs within the string and then performs a string match.

### ATSRegExp ###

This test uses Maxima's `regex_match` function.

* Both arguments to the test must be Maxima strings.  If you have a general expression, turn it into a string in the feedback variables with Maxima's `string` function.
* The first argument should be the string, and the second argument should be the pattern to match.
* It yields true if the pattern is matched anywhere within the student answer and false otherwise. Testing for full equality of the answer string can be achieved via regex anchoring by use of `^` or `$`.
* Don't forget to escape within the pattern strings as needed. Note that there is a function `string_to_regex()` that will handle escaping of characters that would otherwise have meaning in the pattern. Also remember that you need to escape the backslashes like normal in Maxima-strings.  That is to say, if you want to use `\s` in a pattern you need to double up the backslashes. For example `"(Alice|Bob)\\s+went\\s+to\\s+the\\s+(bank|market)"`.
* One can read more about the patterns possible from [here](http://ds26gte.github.io/pregexp/index.html). Case-insensitivity may be something worth noting there.

For example, write a STACK question with the following question variables.

    s1:"(Alice|Bob)\\s+went\\s+to\\s+the\\s+(bank|market)";
    s2:"Alice went to the market";
    s3:"Bob       went to the    bank";

Then

1. Use the string input (`ans1`) with teacher's answer `s2`.
2. In the PRT use the node `ATSRegExp(ans1, s1)`.  This will use the pattern in the string `s1` against the student's answer.

This will match both strings `s2` and `s3`, and many others.

### `regex_match_exactp` ###

STACK also provides a helper function `regex_match_exactp(regex, str)` to check if the string equals the pattern matched by the regular expression.

    Regex           String      Result
    (aaa)*(b|d)c    aaaaaabc    true
    (aaa)*(b|d)c    dc          true
    (aaa)*(b|d)c    aaaaaaabc   false
    (aaa)*(b|d)c    cca         false

Currently this is not provided as a separate answer test so you will need to use this predicate in the question variables and check the result against the expected value, or supply the predicate as an argument to an answer test.

For example, using the question variables above you can define the following in the feedback variables.

    sa:regex_match_exactp(s1, ans1);

Then you can use the `AlgEquiv` answer test to check the result is true/false, e.g. `ATAlgEquiv(sa, true)`.

`(RegExp)` **NOTE:** this test was removed in STACK version 4.3.
