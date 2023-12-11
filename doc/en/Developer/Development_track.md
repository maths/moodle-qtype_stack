# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

## Version 4.6.0

This version will require moodle 4.0+, and will no longer support Moodle 3.x (which ended its general support on 14 November 2022, and security support ended on 11 December 2023.)

TODO:

1. Change 'core/event' to 'core_filters/events' in input.js and stackjsvle.js.
2. Strip out parallel DB support in reporting etc.  Search for `stack_determine_moodle_version()`
3. Bring the API into the core of STACK for longer term support, and better support for ILIAS.
4. Major code tidy: Moodle code style now requires (i) short forms of arrays, i.e. `[]` not `array()`, and (ii) commas at the end of all list items.
5. Fix markdown problems. See issue #420.
6. Error messages: use caserror.class more fully to use user information to target error messages.
7. Remove all "cte" code from Maxima - mostly install.

## Parson's block development track

Essential (v 4.6.0)

1. Add in an option "fixed".  When we have "submit all and finish" we don't want to allow users to then drag things.  This is an edge case for after the quiz.  I think we can achive this by adding in an argument to the JSON in the student's input "fixed", and this will get sent to the block.  We can talk about this.
2. Polish up the "use once" or "clone" strings.
3. Use syntax hint to set up a non-empty starting point.
4. Check sortable for keyboard accessibility (SM: Not built-in to Sortable currently: https://github.com/SortableJS/Sortable/issues/1951; however, it looks like it is do-able with some work https://robbymacdonell.medium.com/refactoring-a-sortable-list-for-keyboard-accessibility-2176b34a07f4)
5. CSS styling fix for automated feedback

Later

1. Hashing keys
2. Different proof types -- iff, induction, etc. how do we indicate the different scaffolding for this?
2. Create templates from the start for different proof types
4. Restrict blocks to fixed number of steps
5. Other arrangements, e.g. fill in a 2*2 grid (for matching problems)
   Nested lists (flat list vs. nested/tree)
6. Allow student to select proof style (e.g. iff, contradiction) and pre-structure answer list accordingly
7. Allow some strings in the correct answer to be optional. Allow authors to input a weight for each item and use weighted D-L distance, e.g., weight of 0 indicates that a step is not required, but will not be considered incorrect if included.
8. Hover over a proof step to reveal more information (e.g., this could come from the third item in the list and give a hint/definition)
9. Allow students to mark items (e.g. as used or unneeded) or tick used items
10. Confirmation for delete all?
11. Alternative styling/signalling for clone mode?
12. Better support (and documentation) for bespoke grading functions.


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
