# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Version 4.4

1. Release documentation under CC-BY-SA.
2. Caching validation.
3. Compiled PRTs.
4  Change behaviour of UnitsAbsolute in response to discussion of issue #448.
5. CASText2.
6. Added `checkvars` option to inputs.
7. Add in support for the [Damerau-Levenshtein distance](../Authoring/Levenshtein_distance.md).
8. Add in suppprt for the display of [Complex Numbers](../CAS/Complex_numbers.md).

### Add in support for Moodle 4.0

TODO:
0. Confirm STACK 4.0 still works on Moodle 3.11, etc. and confirm mechanisms for cross-version support.
1. Make sure question tests copy over for each question version (issue #805).
2. Update bulk tester with new DB queries, and confirm bulk tester works.
3, Check and confirm question basic usage report works in Moodle 4.0.

## Version 4.5

1. Error messages: use caserror.class more fully to use user information to target error messages.
2. Remove all "cte" code from Maxima - mostly install.

## For "inputs 2"?

* Better CSS, including "tool tips".  May need to refactor JavaScript.  (See issue #380)
* Add support for matrices with floating point entries, and testing numerical accuracy.
* Expand support for input validation options to matrices (e.g. floatnum, rationalize etc.)
* Update MCQ to accept units.
* Add a base N check to the numeric input.
* Refactor DB of 'insterStars' and remove stack_input_factory::convert_legacy_insert_stars.  Really use new values throughout.  See [Future plans for syntax of answers and STACK](Syntax_Future.md)

## Other

* Better install code (see #332).
* Move find_units_synonyms into the parser more fully?
* 1st version of API.
* Enable individual questions to load Maxima libraries.  (See issue #305)
* Markdown support?

