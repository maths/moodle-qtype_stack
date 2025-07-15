# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

We use the [github issue tracker](https://github.com/maths/moodle-qtype_stack/issues) to track "milestones".

## Version 4.10.0

Done:

1. Add in a `style` attribute to the JSXGraph block to load local CSS styles.
2. Add in the `json` input type.  This better supports JSON for JSXGraph, and better debugging in the existing GeoGebra and Parsons blocks.
3. Allow the bulk tester to add `[[todo]]` blocks to the question descriptions with the `addtags` option.

Issues with [github milestone 4.10.0](https://github.com/maths/moodle-qtype_stack/issues?q=is%3Aissue+milestone%3A4.10.0) include

1. Fix [issue #406](https://github.com/maths/moodle-qtype_stack/issues/406)
2. Remove all "cte" code from Maxima - mostly install.
3. Resolve [issue #1363] to download students data in json format.
4. Add in `nounadd`, `nounmul` etc. to the parser to provide users access to these functions.

## Future Adapt block development ideas

1. Add in a "counter" option to the button.  If set to true, then the value of the counter changes from true/false to the number of times the button has been pressed.

## Future equivalence reasoning development track.

1. Allow bespoke validation (actually quite difficult).
2. Specify a variable to solve for.  E.g.  `a*x=0`, currently needs `a=0 or x=0`, but when solving for `x` we have just `x=0`.

## Future Parson's block development track

1. Nested lists (flat list vs. nested/tree) and different proof types -- iff, induction, etc. how do we indicate the different scaffolding for this?
2. Use syntax hint to set up a non-empty starting point.
3. Validate `proof_steps` for multiple keys having the same tag.
4. Restrict blocks to fixed number of steps.
5. Allow student to select proof style (e.g. iff, contradiction) and pre-structure answer list accordingly
6. Allow some strings in the correct answer to be optional. Allow authors to input a weight for each item and use weighted D-L distance, e.g., weight of 0 indicates that a step is not required, but will not be considered incorrect if included.
7. Making use of third item in other ways? Hover over a proof step to reveal more information (e.g., this could come from the third item in the list and give a hint/definition)
8. Allow students to mark items (e.g. as used or unneeded) or tick used items.
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
