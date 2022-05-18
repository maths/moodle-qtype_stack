# Levenshtein distance and strings

The Levenshtein distance is a metric, or edit distance, for measuring the difference between two strings. Informally, the Levenshtein distance is the minimum number of single-character edits (insertions, deletions or substitutions) required to change one string into the other. It is named after [Vladimir Levenshtein](https://en.wikipedia.org/wiki/Levenshtein_distance).  Support for Levenshtein distance was added to STACK in 2022 by Achim Eichhorn and Andreas Helfrich-Schkarbanenko.

The use of this distance automatically includes an element of spell checking, which is one of its significant advantages.  Indeed, the Levenshtein distance is widely used for spell checking and auto-complete.

## Basic usage example

In STACK `levenshtein(s, t)` takes strings `s`, `t` to compare and returns an integer, the Levensthein distance between `s` and `t`.  This is basically the number of single-character edits.  For example `levenshtein("Add", "And")` gives 1, since one letter needs to be changed. `levenshtein("Add", "and")` gives 2, since two leters need to be changed.  Adding or removing letters are each a single edit, so that `levenshtein("Subtract", "Subtraction");` gives 3.

Rather than using this "distance" a tolerance is defined by the normalised "Levenshtein closeness" between 0 (unequal) and 1 (equal).  The Maxima code for this function is simple enough to list here explicitly.  `levenshtein_plv(s, t)` weights the levenshtein closeness between 0 (unequal) and 1 (equal).  In Maxima code this function is defined as

     levenshtein_plv(s, t) := 1.0-levenshtein(s, t)/max(slength(s), slength(t));

For example "Add" and "Addition" have a distance of 5, and the longest string is 8, so `levenshtein_plv("Add", "Addition")` gives 0.375.

These functions are part of STACK, not core Maxima, and so to use them you will need to make use of the [Maxima sandbox](../CAS/STACK-Maxima_sandbox.md).

In a practical situation a teacher will likely have to specify a range of acceptable strings to ensure the student is sufficiently close to something acceptable.

## Answer test

STACK provides an answer test based on the Levenshtein distance/closeness.  This test seeks to match the student's answer (a string) with two lists of strings and a numerical tolerance option. The first list (`allow`) is the list of acceptable strings.  The second list (`deny`) is the list of unacceptable strings.  By default, the test is case insensitive.

When you test the student's answer the test finds the closest string in `allow` and the closest string in `deny` and the corresponding values of the normalised Levenshtein closeness.  The test seeks to ensure that

1. the closest string is within the `allow` list, and then that
2. the closeness of the student's string to the closest allow string is better than the specified tolerance.

Notes on using the answer test in STACK.

1. The first argument to the test (the "student's answer") must be a string.
2. The second argument to the test (the "teacher's answer") must be a list in the form `[allow, deny]` where both `allow` and `deny` are themselves lists of strings.  The `allow` list must be non-empty, but the `deny` list could be empty.
3. The option must be used.  Either give the numerical tolerance as a number, or a list of options.  The numerical tolerance must be the first element of the options list.
4. By default the test is case-insensitive.  If you include the atom `CASE` in the list of options then the matching is case sensitive, potentially increasing the Levenshtein distance between strings.  E.g. use answer test option `[0.9, CASE]` for a case-senstive test with a tolerance of 0.9.

The current answer test provides feedback indicating which of the allow strings was closest.  The test does not provide feedback indicating which of the deny strings was closest, but if you can find a use-case which needs deny based feedback please contact the developers and we will add an option.

The answernote records the closest allow string, the closest deny string and the corresoding tolerance values.  It is likley that a teacher will need to examine students' answers in the fist use cycle and fine tune the `allow`, `deny` and tolerance values (perhaps with a regrade) to reach an acceptable level of test reliability: the use of the tolerance means this test is not as objective as some other STACK assessments.

It is likely this test will benefit from a wide range of text pre-processing options prior to the test being executed, e.g. using functions from Maxima's stringproc library.  For example

* remove all non-alphabetic symbols (e.g punctuation)
* remove (ignore) accents and diacritical marks
* remove whitespace

At this point we do not propose to add these as options to the test itself as the pre-processing can be done in the feedback variables as required.  However, pre-processing does affect the feedback given by the test and so test options might be very useful.  If you create such processing functions and have compelling use-cases we would appreciate an opportinity to document, and support them as core functionality: please contact the developers.

## STACK functions

You can test in other ways using the feedback variables and the following functions.

`levenshtein(s, t)` takes strings `s`, `t` to compare and returns an integer, the Levensthein distance between `s` and `t`.  This is basically the number of single-character edits.

`levenshtein_plv(s, t)` weights the levenshtein closeness between 0 (unequal) and 1 (equal).  In Maxima code this function is defined as

     levenshtein_plv(s, t) := 1.0-levenshtein(s, t)/max(slength(s), slength(t));

`compare_strings(needle, haystack, upper)` looks for the string `needle` in the list of stings `haystack` with the boolean option `uppper` to control case sensitivity.  It returns `[maxscore, haystack_found]` where `maxscore` is the value of the closest match and `haystack_found` is the string found.  The `maxscore` is rounded to five decimal places to help with comparison of floats later.

