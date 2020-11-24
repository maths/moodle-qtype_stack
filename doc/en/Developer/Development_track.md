# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Version 4.3.8

Done:

* Autoload `trigtools` to provide better simplification options for trig expressions and fix long-standing bug with `trigrat`.
* Make it much easier for colleagues to construct a test case using the "teacher's answer" input values as test case inputs.
* Allow users to modify the `notanswered` message in dropdown and radio inputs.
* Move all answer tests to Maxima.
* Separate out Maxima commands in the install process.

## Version 4.4


## Maxima side PRTs.

* Change behaviour of UnitsAbsolute in response to discussion of issue #448.

## For "inputs 2"?

* Better CSS, including "tool tips".  May need to refactor JavaScript.  (See issue #380)
* Add support for matrices with floating point entries, and testing numerical accuracy.
* Expand support for input validation options to matrices (e.g. floatnum, rationalize etc.)
* Update MCQ to accept units.
* Add a base N check to the numeric input.
* Refactor DB of 'insterStars' and remove stack_input_factory::convert_legacy_insert_stars.  Really use new values throughout.  See [Future plans for syntax of answers and STACK](Syntax_Future.md)
* Refactor numerical answer tests to make proper use of ast.

## Other

* Better install code (see #332).
* Move find_units_synonyms into the parser more fully?
* 1st version of API.
* Enable individual questions to load Maxima libraries.  (See issue #305)
* Markdown support?

