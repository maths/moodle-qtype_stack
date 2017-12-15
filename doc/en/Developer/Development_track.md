# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Version 4.1

Done for version 4.1:

* Add in support for the syntaxHint in the matrix input.
* On the questiontestrun page, have options to (a) delete all question variants.
* Make SVG the default image format in Maxima generate plots.
* Add in a `PLOT_SIZE` option to set the size of a plot.
* Add in an answer test which accepts "at least" n significant figures. (See issue #313)
* Add in the "string" input type.
* Add test which checks if there are any rational expressions in the denominator of a fraction.  (Functionality added to LowestTerms test, which looks at the form of rational expressions).
* Add an option to remove hard-coded "not answered" option from Radio input type. (See issue #304)

To do for version 4.1:

* Add in test to require a certain number of significant figures/decimal places at validation stage.
* Refactor inputs to have extra options shared between inputs.


## Version 4.2

* Add in a version number to STACK questions.
* Sort out the "addrow" problem. (see issue #333)
* Update MCQ to accept units.
* Add support for matrices with floating point entries, and testing numerical accuracy.
* Enable individual questions to load Maxima libraries.  (See issue #305)

