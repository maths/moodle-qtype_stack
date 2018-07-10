# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Version 4.2

Goal: summmer 2018 for release in time for 2018-19 teaching cycle (Aalto: mid Aug, Edinburgh: 1st Aug).

Note: newer versions of Maxima require that a variable has been initialised as a list/array before you can assign values to its indices.  For this reason some older questions may stop working when you upgrade to a new version of Maxima.  Please use the bulk test script after each upgrade!  See issue #343.

Note: the behaviour of the maxima `addrow` function has changed.  Use the bulk test script to identify questions which are affected. Note, once you save a question you will update the version number, and this will prevent questions using `addrow` from being identified.

### Major features

* _done_ Add in a version number to STACK questions.
* _done_ Add support for using JSXGraph  `http://jsxgraph.org` for better support of interactive graphics, and as part of an input type.  See [JSXGraph](../Authoring/JSXGraph.md)
* _done_ Add in a version number to STACK questions.
* _done_ Update reasoning by eqivalence.
* Better install code (see #332).
* 1st version of API.

### Other minor issues:

* _done_ Refactor internal question validation away from Moodle editing, and into the question type.  Add in a "warning" system.
* _done_ Add in native multi-language support, to separate out langagues in the question text.  This is needed so as not to create spurious validation errors, such as "input cannout occur twice".
* _done_ Output results of PRTs in the `summarise_response` method of `question.php`.  Gives more information for reporting.
* _done_ Sort out the "addrow" problem. (See issue #333).  This is changed to "rowadd".
* _done_ Add in check for "mul" (see issue #339) and better checking of input options.
* _done_ Refactor equiv_input and MCQ to make use of the new extra options mechanism.
* _done_ Add in support for the maxima `simplex` package.
* _done_ Add an answer test to check if decimal separator is in the wrong place (See issue #314)

* Re-sizable matrix input.  See Aalto/NUMBAS examples here, with Javascript.
* Add support for matrices with floating point entries, and testing numerical accuracy.
* Update MCQ to accept units.
* Add a base N check to the numeric input.* Expand support for input validation options to matrices (e.g. floatnum, rationalize etc.)
* Add in full parser, to address issue #324.


## Version 4.3

* Enable individual questions to load Maxima libraries.  (See issue #305)
