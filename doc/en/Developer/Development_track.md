# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Version 4.1

Done for version 4.1:

* Add in support for the syntaxHint in the matrix input.
* On the questiontestrun page, have options to (a) delete all question variants.
* Make SVG the default image format in Maxima generate plots.
* Add in an answer test which accepts "at least" n significant figures. (See issue #313)
* Add in the "string" input type.
* Add test which checks if there are any rational expressions in the denominator of a fraction.  (Functionality added to LowestTerms test, which looks at the form of rational expressions).
* Add an option to remove hard-coded "not answered" option from Radio input type. (See issue #304)

To do for version 4.1:

* Add support for matrices with floating point entries, and testing numerical accuracy.
* Enable individual questions to load Maxima libraries.  (See issue #305)

### Numbers input type

Require a number, written in a particular form, at validation stage

* A fraction (in lowest terms?)
* A rationalised number (no variables)
* A floating point number, given to certain number of significant figures/decimal places.
* Base N (Next version when this functionality becomes available).
* Check if decimal separator is in the wrong place #314

Some of this functionality will cascade to the units and matrix input types.