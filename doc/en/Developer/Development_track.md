# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

We use the [github issue tracker](https://github.com/maths/moodle-qtype_stack/issues) to track "milestones".

## Version 4.7.0

Issues with [github milestone 4.7.0](https://github.com/maths/moodle-qtype_stack/issues?q=is%3Aissue+milestone%3A4.7.0) include

1. Fix [issue #1160](https://github.com/maths/moodle-qtype_stack/issues/879) Allow configuring the MathJax URL
2. Release "Adapt" block. [issue #975](https://github.com/maths/moodle-qtype_stack/issues/975)
3. Fix [issue #406](https://github.com/maths/moodle-qtype_stack/issues/406)
4. Error messages: use caserror.class more fully to use user information to target error messages.
5. Remove all "cte" code from Maxima - mostly install.
6. Review and fix [issue #1063](https://github.com/maths/moodle-qtype_stack/issues/1063): "Extra options" set to "simp" and number of decimals shown in validation field

## Parson's block development track

For v4.7.0:

1. Nested lists (flat list vs. nested/tree) and different proof types -- iff, induction, etc. how do we indicate the different scaffolding for this?
2. Use syntax hint to set up a non-empty starting point.
3. Create templates from the start for different proof types

Later

1. Restrict blocks to fixed number of steps
2. Allow student to select proof style (e.g. iff, contradiction) and pre-structure answer list accordingly
3. Allow some strings in the correct answer to be optional. Allow authors to input a weight for each item and use weighted D-L distance, e.g., weight of 0 indicates that a step is not required, but will not be considered incorrect if included.
4. Making use of third item in other ways? Hover over a proof step to reveal more information (e.g., this could come from the third item in the list and give a hint/definition)
5. Allow students to mark items (e.g. as used or unneeded) or tick used items
6. Confirmation for delete all?
7. Alternative styling/signalling for clone mode?
8. Better support (and documentation) for bespoke grading functions.
9. Hashing keys
10. Check sortable for keyboard accessibility (SM: Not built-in to Sortable currently: https://github.com/SortableJS/Sortable/issues/1951; however, it looks like it is do-able with some work https://robbymacdonell.medium.com/refactoring-a-sortable-list-for-keyboard-accessibility-2176b34a07f4)


## For "inputs 2"?

* Better CSS, including "tool tips".  May need to refactor JavaScript.  (See issue #380)
* Add support for matrices with floating point entries, and testing numerical accuracy.
* Expand support for input validation options to matrices (e.g. floatnum, rationalize etc.)
* Update MCQ to accept units.
* Add a base N check to the numeric input.
* Refactor DB of 'insterStars' and remove stack_input_factory::convert_legacy_insert_stars.  Really use new values throughout.  See [Future plans for syntax of answers and STACK](Syntax_Future.md)

## Other

* SBCL on the continuous integration does not seem to have support for unicode.  There are examples in the inputs fixtures and walkthrough adapctive tests.  Search for SBCL.
