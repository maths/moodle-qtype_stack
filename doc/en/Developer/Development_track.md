# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Version 4.3

Version 4.3 represents a major internal re-engineering of STACK, with a new dedicated parser and an updated mechanism for connecting to Maxima.  This is a significant improvement, refactoring some of the oldest code and unblocking progress to a wide range of requested features.

There have been a number of changes:

* In the forbidden words we now match whole words not substrings.
* Removed the RegExp answer test.  Added the SRegExp answer test using Maxima's `regex_match` function.
* Use of units is now reflected throughout a question.  This reduces the need to declare units in all contexts.
* Internally, the "+-" operator has been replaced with a new infix operation "#pm#".  Instead of `a+-b` teachers now must type `a#pm#b`.  This change was necessary to deal with differences between versions of Maxima when dealing with expresions.

New features in v4.3:

* Add in full parser, to address issue #324.
* Add in input option 'align'.
* Add in input option 'nounits'.
* Add in option 'compact' to input "Show the validation" parameter.
* Add in a basic question use report page, linked from the question testing page.

## Version 4.4

## For "inputs 2"?

* Better CSS, including "tool tips".  May need to refactor JavaScript.  (See issue #380)
* Re-sizable matrix input.  See Aalto/NUMBAS examples here, with Javascript.
* Add support for matrices with floating point entries, and testing numerical accuracy.
* Expand support for input validation options to matrices (e.g. floatnum, rationalize etc.)
* Update MCQ to accept units.
* Add a base N check to the numeric input.
* Refactor DB of 'insterStars' and remove stack_input_factory::convert_legacy_insert_stars.  Really use new values throughout.  See [Future plans for syntax of answers and STACK](Syntax_Future.md)
* Refactor numerical answer tests to make proper use of ast.

## Other

* Better install code (see #332).
* Make use of the Maxima function `sig_figs_from_str(strexp)` in utils.mac which returns the number of decimal places/significant figures in a variable (useful when providing feedback).  Needed for the refactoring.
* Move find_units_synonyms into the parser more fully?
* 1st version of API.
* Enable individual questions to load Maxima libraries.  (See issue #305)

