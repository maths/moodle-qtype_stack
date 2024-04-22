# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

## Version 4.6.0

This version will require moodle 4.0+. Moodle 3.x is no longer supported.

1. Alter list of acceptible expressions.  Unicode super/subscripts now are invalid.  Use 150_replace filter in students' input.
2. Add in the extra input option `feedback` to run in parallel with validators to give opportunities for bespoke messages.
3. Load the `functs` Maxima package, i.e. `load("functs");` to give access to some useful functions.
4. Fix display and simplification of binomial coefficients (issue #931).
5. Add in the `CT:...` and `RAW:...` options for test case construction to enable tests of invalid input (e.g. missing stars).
6. STACK now has an [API](../Installation/API.md) to provide STACK questions as a web service.
7. Improve the display of floats.  Numbers of decimal places are now respected in all parts of expressions, and floats such as `1.7E-9` are displayed at \(1.7 \times 10^{-9}\).
8. Release first version of the API for longer term support, and better support for ILIAS.

TODO:

1. Major code tidy: Moodle code style now requires (i) short forms of arrays, i.e. `[]` not `array()`, and (ii) commas at the end of all list items.
2. Fix markdown problems. See issue #420.
3. Fix [issue #879](https://github.com/maths/moodle-qtype_stack/issues/879)

## Version 4.7.0

TO-DO:

1. Fix issue #1160: Allow configuring the MathJax URL.
2. Release "Adapt" block. [issue #975](https://github.com/maths/moodle-qtype_stack/issues/975)
3. Error messages: use caserror.class more fully to use user information to target error messages.
4. Remove all "cte" code from Maxima - mostly install.
5. Review and fix [issue #1063](https://github.com/maths/moodle-qtype_stack/issues/1063): "Extra options" set to "simp" and number of decimals shown in validation field


## Parson's block development track

Next (v4.6.0)

1. Add in an option "fixed".  When we have "submit all and finish" we don't want to allow users to then drag things.  This is an edge case for after the quiz.  I think we can achive this by adding in an argument to the JSON in the student's input "fixed", and this will get sent to the block. E.g. input type changes html attr to readonly, sortable version disable? Note: other input types use readonly attr after submit all and finish.
2. Use syntax hint to set up a non-empty starting point.
3. Check sortable for keyboard accessibility (SM: Not built-in to Sortable currently: https://github.com/SortableJS/Sortable/issues/1951; however, it looks like it is do-able with some work https://robbymacdonell.medium.com/refactoring-a-sortable-list-for-keyboard-accessibility-2176b34a07f4)
4. CSS styling fix for automated feedback
5. Other arrangements, e.g. fill in a 2*2 grid (for matching problems)
   Nested lists (flat list vs. nested/tree)

Later

1. Different proof types -- iff, induction, etc. how do we indicate the different scaffolding for this?
2. Create templates from the start for different proof types
3. Restrict blocks to fixed number of steps
4. Allow student to select proof style (e.g. iff, contradiction) and pre-structure answer list accordingly
5. Allow some strings in the correct answer to be optional. Allow authors to input a weight for each item and use weighted D-L distance, e.g., weight of 0 indicates that a step is not required, but will not be considered incorrect if included.
6. Making use of third item in other ways? Hover over a proof step to reveal more information (e.g., this could come from the third item in the list and give a hint/definition)
7. Allow students to mark items (e.g. as used or unneeded) or tick used items
8. Confirmation for delete all?
9. Alternative styling/signalling for clone mode?
10. Better support (and documentation) for bespoke grading functions.
11. Hashing keys


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
* SBCL on the continuous integration does not seem to have support for unicode.  There are examples in the inputs fixtures and walkthrough adapctive tests.  Search for SBCL.
