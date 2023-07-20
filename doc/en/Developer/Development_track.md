

# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

## Version 4.4.6

TODO: 

1. Support for PHP 8.2.  See issue #986.
2. Fix markdown problems. See issue #420.
3. Error messages: use caserror.class more fully to use user information to target error messages.
4. Remove all "cte" code from Maxima - mostly install.

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

