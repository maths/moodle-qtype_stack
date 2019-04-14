# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Version 4.3

DONE:

* Removed the maxima mathml code (which wasn't connected or used).
* Add in extra option `simp` to inputs.
* Add in extra options in the input `allowempty` and `hideanswer`.

TODO:

* 1st version of API.
* Better install code (see #332).
* Better CSS, including "tool tips".  May need to refactor javascript.  (See issue #380)
* A STACK maxima function which returns the number of decimal places/significant figures in a variable (useful when providing feedback).  Needed for the refactoring.
* Enable individual questions to load Maxima libraries.  (See issue #305)
* Re-sizable matrix input.  See Aalto/NUMBAS examples here, with Javascript.
* Add support for matrices with floating point entries, and testing numerical accuracy.
* Update MCQ to accept units.
* Add a base N check to the numeric input.
* Expand support for input validation options to matrices (e.g. floatnum, rationalize etc.)
* Add in full parser, to address issue #324.

