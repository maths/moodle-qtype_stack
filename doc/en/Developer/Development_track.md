# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

We use the [GitHub issue tracker](https://github.com/maths/moodle-qtype_stack/issues) to track "milestones".

## Version 4.16.0

Issues with [GitHub milestone 4.16.0](https://github.com/maths/moodle-qtype_stack/issues?q=is%3Aissue+milestone%3A4.16.0).

1. The copy of the docs inside Moodle now displays [admonitions](Documentation.md) (`!!! warning` and so on) as styled boxes, as the docs site does, rather than as code.
2. The command line bulk tester can now test the STACK question library, as a check before a release.  `php cli/bulktestall.php --library-php` imports each file in `samplequestions/stacklibrary` as a temporary question, runs the PHP validation and the upgrade checks, and deletes the question again.  `--library` also runs all the question tests on all deployed seeds, and `--path=dir` tests only part of the library.  The results are those you would get by importing the whole library into a question bank and bulk testing it.
3. The bulk tester now makes a report on each question first, and formats it for people second.  The report gives the question's name, its Moodle id (or its file in the library), and the details of any import errors, upgrade errors, validation warnings, `[[todo]]` blocks and failing tests.  `php cli/bulktestall.php --json` prints these reports as JSON, for scripts to read, with a unique `key` for each question and the full details of every test case.  `--dir=folder` tests a folder of question files other than the library, for tools that check questions before they reach a site, and `--exitcode` makes failures give a non-zero exit status.  The web and command line output is unchanged.
4. HTML tag check.  The question warnings (shown when a question is saved, and on the question dashboard) now include a minimal check of the HTML tags in the question text, general feedback, specific feedback, question description and PRT feedback, in fields written in HTML (or with no format).  It reports a tag which is not closed, a closing tag with no opening tag, tags which are not nested properly, and the branches of an `[[if]]` block which leave different tags open.  STACK's own syntax is left out of the check: blocks are ignored but their contents are checked, and injected CAS (`{@...@}`, `{#...#}`), inputs, LaTeX, and the contents of blocks which are not HTML (such as `[[jsxgraph]]`, `[[comment]]`, `[[escape]]`, `[[markdownformat]]` and `[[moodleformat]]`) are not checked.  Elements whose end tag HTML allows to be left out, such as `<p>`, `<li>` and `<td>`, are not checked either.  This is a warning, not an error, and needs no extra libraries.

## Better testing

### Add in a new keyvals field "test variables"

Add in a new keyvals field "test variables". The test execution would then take three CAS sessions.

   1. Loading up the seed, question variables, test variables and all input definitions, generating input "strings".
   2. Those "strings" would then go through PHP side input validation and CAS side validation in the second CAS session.
   3. Finally, the valid strings would be fed to the PRT functions in the last session, and logic to check if the PRT output matches would be included in that session, so that we do not need to output the full PRT function output for potentially a large number of tests, instead just booleans.

Other ideas

1. It would be fun (student project?!) to graphically illustrate the expected and actual route through the tree by expanding the current PRT graph library, or writing something else. (I get ahead of myself of course....)
2. Introduce new keyword `any` in score/penalty effectively ignoring that field for the purposes of this test case.

Currently, the DB fields for score and penalty are numbers, specifically

        <FIELD NAME="expectedscore" TYPE="number" LENGTH="12" NOTNULL="false" SEQUENCE="false" DECIMALS="7" COMMENT="The expected score."/>
        <FIELD NAME="expectedpenalty" TYPE="number" LENGTH="12" NOTNULL="false" SEQUENCE="false" DECIMALS="7" COMMENT="The expected penalty."/>

So this requires a DB change as well.

--------------------------------------

## Future Adapt block development ideas

1. Add in a "counter" option to the button.  If set to true, then the value of the counter changes from true/false to the number of times the button has been pressed.

## Future ASCII block development ideas

Note, there is an ecosystem for markdown extensions here: https://mdit-plugins.github.io/
These include meta plugins like

* Define inline rules: https://mdit-plugins.github.io/inline-rule.html#custom-syntax
* Define block rules: https://mdit-plugins.github.io/field.html

1. Support an option so that `#` can be used instead of backticks to delimit AsciiMath.
2. Write an AsciiMath to Maxima parser to ensure the `lastblock` and `lastexpr` extractors create correct syntax for the input.  Note math.js already has the necessary _parser_ so de-pasting the AST created by math.js is the first line of attack here.  See calculator.js filter for an example of traversing this tree.
3. Add options to calculator blocks for degrees, and to support physics with scientific units (which math.js supports).
 
## Future equivalence reasoning development track

1. Allow bespoke validation (actually quite difficult).
2. Specify a variable to solve for.  E.g.  `a*x=0`, currently needs `a=0 or x=0`, but when solving for `x` we have just `x=0`.

## Future Parsons block development track

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
