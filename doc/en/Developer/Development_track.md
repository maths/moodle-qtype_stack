# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

We use the [github issue tracker](https://github.com/maths/moodle-qtype_stack/issues) to track "milestones".

## Version 4.9.0

1. Introduce `ta` as the default teacher's answer in the question variables, and use this in the input and default prt.  Allow addition of multiple nodes in a PRT with one click.
2. Support `allowempty` for dropdown, radio and checkbox inputs.
3. Add in the `space` option, i.e. `make_multsgn("space")` in the [options](../Authoring/Question_options.md).
4. Convert input "syntax hint" to castext.
5. Include the `rand_matrix.mac` contributed library, a collection of matrix randomisation functions for use in linear algebra, with the local STACK source code.  See the [random](../Topics/Linear_algebra/Random_Matrices.md) documentation for details.
6. Add in a substantial library for dealing with [linear algebra problems](../Topics/Linear_algebra/index.md).
7. Load Maxima's `eigen` library.
8. The API now accepts moodle XML fragments, and sets default values for all other fields.  This significantly reduces the overhead in writing and maintaining XML in other external projects.

Issues with [github milestone 4.9.0](https://github.com/maths/moodle-qtype_stack/issues?q=is%3Aissue+milestone%3A4.9.0) include

1. Release "Adapt" block. [issue #975](https://github.com/maths/moodle-qtype_stack/issues/975)
2. Fix [issue #406](https://github.com/maths/moodle-qtype_stack/issues/406)
3. Remove all "cte" code from Maxima - mostly install.
4. Resolve [issue #1363] to download students data in json format.

## Future Parson's block development track

1. Nested lists (flat list vs. nested/tree) and different proof types -- iff, induction, etc. how do we indicate the different scaffolding for this?
2. Use syntax hint to set up a non-empty starting point.
3. Validate `proof_steps` for multiple keys having the same tag.
4. Restrict blocks to fixed number of steps
5. Allow student to select proof style (e.g. iff, contradiction) and pre-structure answer list accordingly
6. Allow some strings in the correct answer to be optional. Allow authors to input a weight for each item and use weighted D-L distance, e.g., weight of 0 indicates that a step is not required, but will not be considered incorrect if included.
7. Making use of third item in other ways? Hover over a proof step to reveal more information (e.g., this could come from the third item in the list and give a hint/definition)
8. Allow students to mark items (e.g. as used or unneeded) or tick used items
9. Confirmation for delete all?
10. Alternative styling/signalling for clone mode?
11. Check sortable for keyboard accessibility (SM: Not built-in to Sortable currently: https://github.com/SortableJS/Sortable/issues/1951; however, it looks like it is do-able with some work https://robbymacdonell.medium.com/refactoring-a-sortable-list-for-keyboard-accessibility-2176b34a07f4)


## For "inputs 2"?

* Better CSS, including "tool tips".  May need to refactor JavaScript.  (See issue #380)
* Add support for matrices with floating point entries, and testing numerical accuracy.
* Expand support for input validation options to matrices (e.g. floatnum, rationalize etc.)
* Update MCQ to accept units.
* Add a base N check to the numeric input.
* Refactor DB of 'insterStars' and remove stack_input_factory::convert_legacy_insert_stars.  Really use new values throughout.  See [Future plans for syntax of answers and STACK](../../dev/Syntax_Future.md)

## Other

* SBCL on the continuous integration does not seem to have support for unicode.  There are examples in the inputs fixtures and walkthrough adapctive tests.  Search for SBCL.
