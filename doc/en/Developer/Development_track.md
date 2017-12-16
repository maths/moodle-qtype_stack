# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Version 4.1

Done for version 4.1:

* Add in support for the syntaxHint in the matrix input.
* On the questiontestrun page, have options to (a) delete all question variants.
* Make SVG the default image format in Maxima generate plots.
* Add in a `size` option to set the size of a plot.
* Add in an answer test which accepts "at least" n significant figures. (See issue #313)
* Add in the "string" input type.
* Add test which checks if there are any rational expressions in the denominator of a fraction.  (Functionality added to LowestTerms test, which looks at the form of rational expressions).
* Add an option to remove hard-coded "not answered" option from Radio input type. (See issue #304)
* Add in a "numerical" input type which requires a student to type in a number.  This has various options, see the [docs](../Authoring/Numerical_input.md).
* Specify numerical precision for validation in numerical and units input types.
* Refactor the inputs so that extra options can be added more easily, and shared between inputs.

## Version 4.2

To do:

* Refactor inputs to have extra options shared between inputs.

## Version 4.2

* Add in a version number to STACK questions.
* Update MCQ to accept units.
* Enable individual questions to load Maxima libraries.  (See issue #305)
* Add an answer test to check if decimal separator is in the wrong place (See issue #314)
* Sort out the "addrow" problem. (See issue #333)
* Add support for matrices with floating point entries, and testing numerical accuracy.
* Expand support for input validation options to matrices (e.g. floatnum, rationalize etc.)
* Refactor equiv_input and MCQ to make use of the new extra options mechanism.
* Add a base N check to the numeric input.
* Add in full parser, to address issue #324.
