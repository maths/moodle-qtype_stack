# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Version 4.3

Version 4.3 represents a major internal re-engineering of STACK, with a new dedicated parser and an updated mechanism for connecting to Maxima.  This is a significant improvement, refactoring some of the oldest code and unblocking progress to a wide range of requested features.

CHANGES:
* In the forbidden words we now match whole words not substrings.
* Removed the RegExp answer test.

DONE:

* Removed the Maxima MathML code (which wasn't connected or used).
* Add in metadata system to the documentation (Thanks to Malthe Sporring for this suggestion).
* Add in extra option `simp` to inputs.
* Add in extra options in the input `allowempty` and `hideanswer`.

TODO:

* 1st version of API.
* Better install code (see #332).
* Better CSS, including "tool tips".  May need to refactor JavaScript.  (See issue #380)
* A STACK Maxima function which returns the number of decimal places/significant figures in a variable (useful when providing feedback).  Needed for the refactoring.
* Enable individual questions to load Maxima libraries.  (See issue #305)
* Re-sizable matrix input.  See Aalto/NUMBAS examples here, with Javascript.
* Add support for matrices with floating point entries, and testing numerical accuracy.
* Update MCQ to accept units.
* Add a base N check to the numeric input.
* Expand support for input validation options to matrices (e.g. floatnum, rationalize etc.)

## Add in full parser, to address issue #324.

1. Refactor numerical answer tests to make proper use of ast
  1. Functions on ast, such as "this is an integer".
2. Parse multiple plus minus operators.  "a+-b+-c" and "a+-b=c+-d"
3. Move find_units_synonyms into the parser more fully?

## Other things to fix

1. Matrix instant validation does not appear to work.
