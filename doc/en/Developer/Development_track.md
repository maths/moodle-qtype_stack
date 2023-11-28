# Development track for STACK

Requests for features and ideas for developing STACK are all recorded in [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

## Version 4.6.0

This version will require moodle 4.0+, and will no longer support Moodle 3.x (which ends its general support on 14 November 2022, and security support ends on 11 December 2023.)

Todo: 

1. Change 'core/event' to 'core_filters/events' in input.js and stackjsvle.js.
2. Strip out parallel DB support in reporting etc.  Search for `stack_determine_moodle_version()`
3. Bring the API into the core of STACK for longer term support, and better support for ILIAS.
4. Major code tidy: Moodle code style now requires (i) short forms of arrays, i.e. `[]` not `array()`, and (ii) commas at the end of all list items.

## Version 4.5.0

Please note, this is the _last_ version of STACK which will support Moodle 3.x.

1. Refactor the healthcheck scripts, especially to make unicode requirements for maxima more prominent.
2. Shape of brackets surrounding matrix/var matrix input types now matches question level option for matrix parentheses.  (TODO: possible option to change shape at the input level?)
3. Allow users to [systematically deploy](../CAS/Systematic_deployment.md) all variants of a question in a simple manner.
4. Tag inputs with 'aria-live' is 'assertive' for better screen reader support.
5. Add an option to support the use of a [comma as the decimal separator](Syntax_numbers.md).
6. Confirm support for PHP 8.2, (fixes issue #986).
7. Add in a [GeoGebra block](../Authoring/GeoGebra.md), and see [GeoGebra authoring](../Topics/GeoGebra.md).  Thanks to Tim Lutz for contributing this code as part of the AuthOMath project.
8. Add in an option `margin` to control margins around STACK-generated plots.
9. Add in better support for proof as [Parson's problems](../Authoring/Parsons.md).

TODO: 

1. Fix markdown problems. See issue #420.
2. Error messages: use caserror.class more fully to use user information to target error messages.
3. Remove all "cte" code from Maxima - mostly install.

## Parson's block development track

Essential (v 4.5.0)

1. Auto-size: js iframe dynamic rescaling? (best guess won't work...)
2. Choose and document default options for Sortable.js and document them. Make sure any overwritten options are warned as being 
overwritten, user should know when and why their input options are being discarded.
3. Unit tests
4. Make sure STACK CSS is loaded, i.e. https://github.com/maths/moodle-qtype_stack/blob/master/styles.css
   This contains some styles such as proof and trees we'd like within the iframe.
   Test with {@disptree(1+x^2)@}.
5. Confirm MathJax default:   typeset  v2, v3? 
6. Add in an option "fixed".  When we have "submit all and finish" we don't want to allow users to then drag things.  This is an edge case for after the quiz.  I think we can achive this by adding in an argument to the JSON in the student's input "fixed", and this will get sent to the block.  We can talk about this.
7. Polish up the "use once" or "clone" strings.
8. Style suggestion: Move the bin icon to be next to, and the same size as, the "refresh" icon.  This preserves the full width for useful material (used/unused).
9. Add a "clear used list" option in clone mode, to delete all items in the used list. Include an are you sure prompt
10. Use syntax hint to set up a non-empty starting point....
11. Check sortable for keyboard accessibility
12. CSS styling fix for automated feedback
13. Tick used items. Or allow student to mark items as used or unneeded.
14. Better signaling for clone mode
15. Change red colouring for the available list
16. Validate JSON in javascript and expose errors to users on the page (rather than in the console).

Later

1. Hashing keys
2. Different proof types -- iff, induction, etc. how do we indicate the different scaffolding for this?
2. Create templates from the start for different proof types
4. Restrict blocks to fixed number of steps
5. Other draggable arrangements, e.g. fill in a 2*2 grid (for matching problems)
   Nested lists (flat list vs. nested/tree)
6. Allow student to select proof style (e.g. iff, contradiction) and pre-structure answer list accordingly
7. Allow some strings in the correct answer to be optional. Allow authors to input a weight for each item and use weighted D-L distance, e.g., weight of 0 indicates that a step is not required, but will not be considered incorrect if included.
8. Hover over a proof step to reveal more information (e.g., this could come from the third item in the list and give a hint/definition)


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

