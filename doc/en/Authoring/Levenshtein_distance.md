# Damerau-Levenshtein distance and strings

The Damerau-Levenshtein (Levenshtein for short) distance is a metric, or edit distance, for measuring the difference between two strings. Informally, the Levenshtein distance is the minimum number of single-character edits (insertions, deletions, transition or substitutions) required to change one string into the other. It is named after [Vladimir Levenshtein](https://en.wikipedia.org/wiki/Levenshtein_distance).  Support for Levenshtein distance was added to STACK in 2022 by Achim Eichhorn and Andreas Helfrich-Schkarbanenko.

The use of this distance automatically includes an element of spell checking, which is one of its significant advantages.  Indeed, the Levenshtein distance is widely used for spell checking and auto-complete.
(See, e.g., F. J. Damerau, _A technique for computer detection and correction of spelling errors_, Communications of the ACM (7)3, March 1964 pp 171–176. [https://doi.org/10.1145/363958.363994](https://doi.org/10.1145/363958.363994))

## Basic usage example

In STACK `levenshtein(s, t)` takes strings `s`, `t` to compare and returns an integer, the Levensthein distance between `s` and `t`.  This is basically the number of single-character edits.  For example `levenshtein("Add", "And")` gives 1, since one letter needs to be changed. `levenshtein("Add", "and")` gives 2, since two leters need to be changed.  Adding or removing letters are each a single edit, so that `levenshtein("Subtract", "Subtraction");` gives 3.

Rather than using this "distance" a tolerance is defined by the normalised "Levenshtein similarity" between 0 (totally different) and 1 (identical).  The Maxima code for this function is simple enough to list here explicitly. 

     levenshtein_plv(s, t) := 1.0-levenshtein(s, t)/max(slength(s), slength(t));

For example "Add" and "Addition" have a distance of 5, and the longest string is 8, so `levenshtein_plv("Add", "Addition")` gives similarity of 0.375.

These functions are part of STACK, not core Maxima, and so to use them you will need to make use of the [Maxima sandbox](../CAS/STACK-Maxima_sandbox.md).

In a practical situation a teacher will likely have to specify a range of acceptable strings to ensure the student is sufficiently similar to something acceptable.

## Answer test

STACK provides an answer test based on the Levenshtein distance/similarity.  This test seeks to match the student's answer (a string) with two lists of strings and a numerical tolerance option. The first list (`allow`) is the list of acceptable strings.  The second list (`deny`) is the list of unacceptable strings.  By default, the test is case insensitive.

When you test the student's answer the test finds the most similar string in `allow` and the most similar string in `deny` and the corresponding values of the normalised Levenshtein similarity.  The test seeks to ensure that

1. the most similar string is within the `allow` list, and then that
2. the similarity of the student's string to the most similar allow string is better than the specified tolerance.

Notes on using the answer test in STACK.

1. The first argument to the test (the "student's answer") must be a string.
2. The second argument to the test (the "teacher's answer") must be a list in the form `[allow, deny]` where both `allow` and `deny` are themselves lists of strings.  The `allow` list must be non-empty, but the `deny` list could be empty.
3. The option must be used.  Either give the numerical tolerance as a number, or a list of options.  The numerical tolerance must be the first element of the options list.
4. By default the test is case-insensitive.  If you include the atom `CASE` in the list of options then the matching is case sensitive, potentially increasing the Levenshtein distance between strings.  E.g. use answer test option `[0.9, CASE]` for a case-senstive test with a tolerance of 0.9.
5. By default this test consolidates whitespace, e.g. replaces tab and newline characters with a single space, trims whitespace from each end and separates with at most one space character.  If you include the atom `WHITESPACE` in the list of options then whitesapace is not consolidated.

The current answer test provides feedback indicating which of the allow strings was most similar.  The test does not provide feedback indicating which of the deny strings was most similar, but if you can find a use-case which needs deny based feedback please contact the developers and we will add an option.

The answernote records the most similar allow string, the most similar deny string and the corresoding tolerance values.  It is likley that a teacher will need to examine students' answers in the fist use cycle and fine tune the `allow`, `deny` and tolerance values (perhaps with a regrade) to reach an acceptable level of test reliability: the use of the tolerance means this test is not as objective as some other STACK assessments.

It is likely this test will benefit from a wide range of text pre-processing options prior to the test being executed, e.g. using functions from Maxima's stringproc library.  For example

* remove (ignore) accents and diacritical marks

At this point we do not propose to add these as options to the test itself as the pre-processing can be done in the feedback variables as required.  However, pre-processing does affect the feedback given by the test and so test options might be very useful.  If you create such processing functions and have compelling use-cases we would appreciate an opportinity to document, and support them as core functionality: please contact the developers.

## Advice on processing strings in this context.

1. To trim whitespace and full stops from each end of a string, you can define `sans1:strim(" .",ans1);` in the feedback variables.
2. STACK provides a function `sremove_chars(rem, st)` which removes all occurances of each _character_ in the string `rem` from the string `st`.  For example to remove all selected punctuation characters use `sremove_chars(".,!?", ans1)`.
3. STACK provides a function `ssquish` which changes tabs and newlines to spaces; trips whitespace at the ends; and replaces multiple whitespaces with a single whitespace.

## Writing a STACK question

Here is a very simple question using the Damerau-Levenshtein distance.  Define the question variables as follows.

    allow1:["Completing the square", "Complete the square"];
    deny1:["Factoring", "Factorising", "Expanding", "Square"];
    p1:(x-1)^2-3;
    p0:expand(p1);
    ta:first(allow1);

Define the question text as

    <p>What is the following transformation called? \[ {@p0@} \quad{\color{blue}\rightarrow}\quad {@p1@}\]</p>
    <p>[[input:ans1]] [[validation:ans1]]</p>

Then:

1. The input `ans1` should be a string input, with `ta` as the teacher's answer.
2. Decide if the students should validate and whether you want validation feedback (probably not).
3. The PRT uses a single node and single answer test: `Levenshtein(ans1, [allow1,deny1], 0.8)` here 0.8 is the (somewhat arbitrary) similarity.
4. Add in question tests, but remember the test cases should be entered as strings, e.g. `"complete square"`.

With this set of allow strings we have `ans1:"complete square"` gives the following answer note from the potential response tree

    ATLevenshtein_far: [[0.78947,"Complete the square"],[0.4,"Square"]]. 

The note `ATLevenshtein_far` means the closest string was in the allow list, but it was too far away.  The rest of the note means that the closest string found in `allow1` was "Complete the square" with similarity 0.78947. The closest string found in `deny1` was "Square" with similarity 0.4.  If you want to accept "complete square" as a correct answer you have two choices: (i) add it to `allow1`, or (ii) reduce the required similarity below 0.789.

## STACK functions

You can test in other ways using the feedback variables and the following functions.

`levenshtein(s, t)` takes strings `s`, `t` to compare and returns an integer, the Levensthein distance between `s` and `t`.  This is basically the number of single-character edits.

`levenshtein_plv(s, t)` weights the levenshtein most similar between 0 (totally different) and 1 (identical).  In Maxima code this function is defined as

     levenshtein_plv(s, t) := 1.0-levenshtein(s, t)/max(slength(s), slength(t));

`levenshtein_compare_strings(needle, haystack)` looks for the string `needle` in the list of stings `haystack`.  It returns `[maxscore, index_haystack_found]` where `maxscore` is the value of the closest match and `index_haystack_found` is the index to the string found.  The `maxscore` is rounded to five decimal places to help with comparison of floats later.

