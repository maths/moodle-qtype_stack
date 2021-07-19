# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Version 4.3.9

Mostly minor bug fixes.

* Done: Add in additional cache `compiledcache` to reduce validation overheads by compiling questions.
* Done. Add in local.mac for site specific code.
* Done. Move STACK admin UI scripts to `adminui`.
* Done. Add in ATEqualComAssRules
* Done. Filter student's input so groups are turned into `ntuple` inert functions.  At last students can type in coordinates as `(x,y)` as input!
* Done. Add in warnings of language mismatch in parts of a question.
* Done. Add in warnings where the answer test needs a raw input but appears to get a calculated value.
* Done. Expand `rand` to accept sets and make a random selection from sets.  Add `rand_selection_with_replacement`.
* Done. Improve checking of teacher's code for better security, this requires a new admin setting `caspreparse` for back compatibility.


## Version 4.4

1. Caching validation.
2. Compiled PRTs.
3. CASText2.

## Maxima side PRTs.

* Change behaviour of UnitsAbsolute in response to discussion of issue #448.

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

