# Levenshtein distance and strings

The Levenshtein distance a metric, or edit distance, for measuring the difference between two strings. Informally, the Levenshtein distance is the minimum number of single-character edits (insertions, deletions or substitutions) required to change one string into the other. It is named after [Vladimir Levenshtein](https://en.wikipedia.org/wiki/Levenshtein_distance).  Support for Levenshtein distance was added to STACK in 2022 by Achim Eichhorn and Andreas Helfrich-Schkarbanenko.

The use of this distance automatically includes an element of spell checking, which is one of its significant advantages.

## Basic usage example

TODO:!

## Answer test

STACK provides an answer test based on the Levenshtein distance.  Rather than "distance" the code uses a tolerance defined by the "closeness" between 0 (unequal) and 1 (equal).


This test seeks to match the student's answer (a string) with two lists of strings and a numerical tolerance option. The first list (`allow`) is the list of acceptable strings.  The second list (`deny`) is the list of unacceptable strings.

When you test the student's answer the test finds the closest string in `allow` and the closest string in `deny` and the corresponding normalised Levenshtein closeness (see `levenshtein_plv(s, t)` below).  The test makes sure that the closest string is within the `allow` list, and then that the closeness of the student's string to an allow string is better than the specified tollerance.  

1. The first argument to the test (the "sudent's answer") must be a string.
2. The second argument to the test (the "teacher's answer") must be a list in the form `[allow, deny]` where both `allow` and `deny` are themselves lists of strings.  The `allow` list must be non-empty, but the `deny` list could be empty.
3. The option must be used.  Either give the numerical tolerance as a number, or a list of options.  The numerical tolerance must be the first element of the list.
4. By default the test is case-insensitive.  If you include the atom `CASE` in the list of options then the matching is case sensitive.

## STACK functions

You can test in other ways using the feedback variables and the following functions.

`levenshtein(s, t)` takes strings `s`, `t` to compare and returns an integer, the Levensthein distance between `s` and `t`.  This is basically the number of single-character edits.

`levenshtein_plv(s, t)` weights the levenshtein closeness between 0 (unequal) and 1 (equal).  In Maxima code this function is defined as

     levenshtein_plv(s, t) := 1.0-levenshtein(s, t)/max(slength(s), slength(t));

`compare_strings(needle, haystack, upper)` looks for the string `needle` in the list of stings `haystack` with the boolean option `uppper` to control case sensitivity.  It returns `[maxscore, haystack_found]` where `maxscore` is the value of the closest match and `haystack_found` is the string found.  The `maxscore` is rounded to five decimal places to help with comparison of floats later.

