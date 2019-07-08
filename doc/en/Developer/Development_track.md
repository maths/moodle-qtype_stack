# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Version 4.3

CHANGES:
* In the forbidden words we now match whole words not substrings.

DONE:

* Removed the Maxima MathML code (which wasn't connected or used).
* Add in extra option `simp` to inputs.
* Add in extra options in the input `allowempty` and `hideanswer`.
* Removed the RegExp answer test.

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

1. Get errors back from the CAS.
2. Refactor answer tests to return "answer notes", "feedback" (and errors).
3. Refactor numerical answer tests to make proper use of ast
  1. Functions on ast, such as "this is an integer".
4. What to do about regex and string tests? (Need the raw values).

## Other things to fix

1. Matrix instant validation does not appear to work.