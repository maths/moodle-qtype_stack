# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

We use the [github issue tracker](https://github.com/maths/moodle-qtype_stack/issues) to track "milestones".

## Version 4.12.0

Important release notes:

* STACK 4.12.0 requires PHP 8.1 or newer.  PHP 7.4 will not work.
* We no longer support Moodle 4.1.
* We now require the plugin [import as new version](https://github.com/maths/moodle-qbank_importasversion/).
* We suggest you change the STACK options as follows to match the new default settings.

1. Option `qtype_stack | prtcorrect` should now be

    [[commonstring key="symbolicprtcorrectfeedback"/]] [[commonstring key="defaultprtcorrectfeedback"/]]

2. Option `qtype_stack | prtpartiallycorrect` should now be

    [[commonstring key="symbolicprtpartiallycorrectfeedback"/]] [[commonstring key="defaultprtpartiallycorrectfeedback"/]]

3. Option `qtype_stack | prtincorrect` should now be 

    [[commonstring key="symbolicprtincorrectfeedback"/]] [[commonstring key="defaultprtincorrectfeedback"/]]

These options can be changed on the STACK plugin page (before or after upgrade).

Changes and new features.

1. Add in new parser (parser2): major re-engineering of the Maxima code.
2. Add in support for number bases.
3. Validation of XML imports. XML files that fail validation will be marked as broken but still imported where possible.
4. Editing of question XML within STACK from link in STACK dashboard. STACK now requires the importasversion plugin to make this possible.
5. Much code tidying to comply with updated to Moodle Code Checker.
6. Add in suppport for `style` in Parsons blocks.  E.g. you can now use `style="compact"` to get smaller, tighter items.
7. Facilitate bulk test for questions in a particular quiz.  (Issue #1521)  Follow the link from the question dashboard to see quizzes in which that question is used.
8. STACK library now imports whole quizzes - look for a `.json` file in the library.
9. Add in support for [local stack libraries](../STACK_question_admin/Library/index.md) of questions in the `stack/sitelibrary` directory within the Moodle data directory.
10. Change the *Generic feedback* defaults to use the common language strings.  __Users upgrading their site in place will need to change the settings in the plugin setting page to the new default.__
11.The Generic feedback and decimals options have been removed from questions in the question library.  When importing library questions, the current site defaults will be used.

Issues with [github milestone 4.12.0](https://github.com/maths/moodle-qtype_stack/issues?q=is%3Aissue+milestone%3A4.12.0) include

1. Fix [issue #406](https://github.com/maths/moodle-qtype_stack/issues/406)
2. Remove all "cte" code from Maxima - mostly install.

--------------------------------------
## Testing a node, not a whole tree

This section is a detailed design proposal to improve question testing to test a node, not a whole tree, and address issue #1703.

### Change of `expectedanswernote`

Make DB changes to considerably lengthen `expectedanswernote` from 255 chars:  https://github.com/maths/moodle-qtype_stack/blob/master/db/install.xml#L178

This field now holds any expected notes from the branches, e.g.  `prt1-1-F | prt1-3-F | prt1-4-F`. Note in this example, there is no expectation from node 2. Individual notes are separated by `|` as in the current output, but this becomes part of the formal note syntax.

* Currently `|` is not permitted in answer notes (see https://github.com/maths/moodle-qtype_stack/blob/master/questiontype.php#L2284)
* When we split over `|` we should trim whitespace before comparison.

The test passes if and only if the PRT produces those notes in order. We condone any notes from the PRT which don't appear in the test case expectation. Hence, with the expectation  `prt1-1-F | prt1-3-F | prt1-4-F` all the following will pass as tests.

    prt1-1-F | prt1-2-T | prt1-3-F | prt1-4-F
    prt1-1-F | prt1-2-F | prt1-3-F | prt1-4-F
    prt1-1-F | prt1-2-T | prt1-3-F | prt1-4-T | prt1-5-F | prt1-6-F

This is simple and I think authors will actually be able to write tests like this and understand what is being tested when they read other people's test.

1. The current test setup is a special case of this (retaining back compatibility).
2. We now test non-leaf nodes (the underlying purpose of issue #1703).
3. Simple to author - you don't have to specify the whole route through the tree, but you _can_ specify the whole route through the tree.
4. This can ignore output from answer tests. By design some answer tests give information about the specific input (e.g. missing items in a set). This is super helpful to teachers and for statistics, but wrecks testing of questions (each input gets a different note).

TODO: add a button "confirm current test behavior" which copies the current note into a text input as the test expectation, and allows a teacher to edit (delete) any individual PRT notes which should not form part of the test case. (The authoring interface changes from a dropdown (prevents author errors) to a string input (more fragile).)

The "confirm current test behavior" does not remove notes from answer tests.

TODO: auto-remove notes from answer tests which contain calculations?  Easy enough to tag those notes and auto-detect them.

* We __anchor__ these notes to specify an expected start/end note.   Use the range notations: `[...]` for both ends fixed, `(...]` for the end, `[...)` for the start, `(...)` for neither.
* `(...]` would be the default if no wrapping defined (retains back compatibility).
* Empty answer notes are not permitted, but the special string `()` indicates any answer note is accepted (rather than using the `any` string).

TODO: update the _Tidy inputs and PRTs_ script.

### Add in a new keyvals field "test variables".

Add in a new keyvals field "test variables". The test execution would then take three CAS sessions.

   1.  Loading up the seed, question variables, test variables and all input definitions, generating input "strings".
   2. Those "strings" would then go through PHP side input validation and CAS side validation in the second CAS session.
   3. Finally, the valid strings would be fed to the PRT functions in the last session, and logic to check if the PRT output matches would be included in that session, so that we do not need to output the full PRT function output for potentially a large number of tests, instead just booleans.

### More flexibility in testing score/penalty.

Introduce new keyword `any` in score/penalty effectiveley ignoring that field for the purposes of this test case.

### Other ideas (later)

1. It would be a fun (student project?!) to graphically illustrate the expected and actual route through the tree by expanding the current PRT graph library, or writing something else. (I get ahead of myself of course....)

--------------------------------------

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

* Better CSS, including "tool tips".  May need to re-factor JavaScript.  (See issue #380)
* Add support for matrices with floating point entries, and testing numerical accuracy.
* Expand support for input validation options to matrices (e.g. floatnum, rationalize etc.)
* Update MCQ to accept units.
* Add a base N check to the numeric input.
* Refactor DB of 'insterStars' and remove stack_input_factory::convert_legacy_insert_stars.  Really use new values throughout.  See [Future plans for syntax of answers and STACK](../../dev/Syntax_Future.md)

## Other

* SBCL on the continuous integration does not seem to have support for unicode.  There are examples in the inputs fixtures and walkthrough adaptive tests.  Search for SBCL.
