# STACK Development History

For current and future plans, see [Development track](Development_track.md) and [Future plans](Future_plans.md).

## Version 4.8.5

Released March 2025.

Bring forward bug fixes to include in an official release.

## Version 4.8.3

Released January 2025.

Bring forward bug fixes to include in an official release.  Update JSXGraph.

## Version 4.8.1

Released November 2024.

Rename directories and files in the STACK library to avoid problems with the auto-generated .zip file.

## Version 4.8.0

Released November 2024.

1. Add in the ability to insert stars for "unknown functions" in inputs.  E.g. `x(t+1)` becomes `x*(t+1)`.  This only affects "unknown" functions, not core functions such as `sin(x)`.
2. Add in tags to the `[[todo]]` blocks to help with multi-authoring [workflow](../STACK_question_admin/Authoring_workflow.md).
3. Add in a [question library](../AbInitio/Authoring_quick_start_6.md) page which allows users to load question from the sample question folder on the server.  This gives users ready access to openly released sample materials.
4. Major update of the docs to separate out referenece, know how and topics.
5. Better document the sample proofs, and provide a generic Parsons question to make it easier to use them.  See the [Proof samples](../Topics/Proof/Proof_samples.md) documentation.
6. Allow the feedback variables to stop the execution of the PRT.  This is equivalent to one of the inputs being "invalid" or "blank".  The PRT does not get executed.  See the discussion in [issue #1227](https://github.com/maths/moodle-qtype_stack/issues/1227).
7. Allow use of Maxima `let` commands, to generate [rules and patterns](../CAS/Rules.md) for bespoke simplification.
8. Provisional support for STACK questions in the Moodle App. STACK questions of all input types now function in the Moodle App while online, complete with input validation. Dropdowns and checkboxes use native elements. This feature should be considered as in beta and under trial while we gather feedback on compatability with a wider range of devices, Moodle installations and questions. Existing questions may need work to fit better on a mobile screen. (Be sure to use App version 4.5+ to avoid a Moodle bug with MathJax in non-STACK questions.)
9. Improve Parsons blocks by (i) hashing for keys, and (ii) Add ability to log history of an attempt (for research).
10. Add in a new [answer test based on a validator function](../Authoring/Answer_Tests/Other.md).


## Version 4.7.0

Released July 2024.

1. Fix [issue #1160](https://github.com/maths/moodle-qtype_stack/issues/879) Allow configuring the MathJax URL
2. Add in stack preamble via `%_stack_preamble_end;` in the question variables to allow some variables to be available in inputs.  This fixes [issue #1207](https://github.com/maths/moodle-qtype_stack/issues/1207) and [issue #1133](https://github.com/maths/moodle-qtype_stack/issues/1133).
3. Allow Maxima code in keyvals to terminate expressions with a `$` (as in Maxima) [issue #1019](https://github.com/maths/moodle-qtype_stack/issues/1019).  This will allow better copy/paste to and from desktop maxima.
4. Add in an option to fine-tune the multiplication sign used for scientific units:  `multsgnstackunits`.  See discussion in [issue #1080](https://github.com/maths/moodle-qtype_stack/issues/1080).
5. Add in the "Deploy from n to m" deature to systematically deploy seeds.
6. Restyle response analysis page.

## Version 4.6.0

Released June 2024.

This version will require moodle 4.0+. Moodle 3.x is no longer supported.

1. Alter list of acceptible expressions.  Unicode super/subscripts now are invalid.  Use 150_replace filter in students' input.
2. Add in the extra input option `feedback` to run in parallel with validators to give opportunities for bespoke messages.
3. Load the `functs` Maxima package, i.e. `load("functs");` to give access to some useful functions.
4. Fix display and simplification of binomial coefficients (issue #931).
5. Add in the `CT:...` and `RAW:...` options for test case construction to enable tests of invalid input (e.g. missing stars).
6. STACK now has an [API](../Installation/API.md) to provide STACK questions as a web service.
7. Improve the display of floats.  Numbers of decimal places are now respected in all parts of expressions, and floats such as `1.7E-9` are displayed at \(1.7 \times 10^{-9}\).   There is a new question option to choose between \(1.7 \times 10^{-9}\) and \(1.7E-9\).
8. Add in support for drag and drop matching problems, as [grid](../Specialist_tools/Drag_and_drop/Grid.md) and [grouping](../Specialist_tools/Drag_and_drop/Grouping.md).


## Version 4.5.0-hf2

Fix critical bug in Javascript.
Released January 2024.

## Version 4.5.0

Released December 2023.

Please note, this is the _last_ version of STACK which will support Moodle 3.x.

1. Re-factor the healthcheck scripts, especially to make unicode requirements for maxima more prominent.
2. Shape of brackets surrounding matrix/var matrix input types now matches question level option for matrix parentheses.  (TO-DO: possible option to change shape at the input level?)
3. Allow users to [systematically deploy](../STACK_question_admin/Deploying_systematically.md) all variants of a question in a simple manner.
4. Tag inputs with 'aria-live' is 'assertive' for better screen reader support.
5. Add an option to support the use of a [comma as the decimal separator](Syntax_numbers.md).
6. Confirm support for PHP 8.2, (fixes issue #986).
7. Add in a [GeoGebra block](../Specialist_tools/GeoGebra/index.md).  Thanks to Tim Lutz for contributing this code as part of the AuthOMath project.
8. Add in an option `margin` to control margins around STACK-generated plots.
9. Add in better support for proof as [Parson's problems](../Specialist_tools/Drag_and_drop/Parsons.md).  (First version, but still more to do including syntax hints, and locking after the quiz is closed.)

There are also numerous minor improvements and bug fixes.

## Version 4.4.6

Released October 2023.

This is a bug-fix release.

## Version 4.4.5

Released July 2023.

1. Add in the `s_assert` function to allow teachers to unit-test individual question variable values.
2. Add in the `[[hint]]` [question block](../Authoring/Question_blocks/Dynamic_blocks.md).  Fixes issue #968, thanks to Michael Kallweit.
3. Add in the `stack_include_contrib()` for easier inclusion of libraries.
4. Add in the `[[todo]]` [question block](../Authoring/Question_blocks/Static_blocks.md).
5. Caschat page now saves question variables and general feedback back into the question.  Fixes issue #984.
6. Confirm support for Maxima 5.46.0 and 5.47.0.
7. All inputs now "allowempty" and "hideanswer" as extra options.  Fixes issue #997.

## Version 4.4.4

Released June 2023.

This is a bug-fix release.

## Version 4.4.3

Released May 2023.

__Action required__ Check that all your materials using `[[jsxgraph]]` continue to work with this version.  See the section on identifying questions using particular blocks in the [Maintaining questions](../STACK_question_admin/index.md) section.

1. Rename testing page as "STACK question dashboard" and make it much easier to add a test case based on the teacher's answer.
2. Better cleaning of unicode from students' input strings.
3. Add link to the dependency checker to the plugin page.
4. Add in descriptions to the question (castext), the PRT nodes and the question tests.
5. Add in the input extra option `validator` to allow user-defined validation functions.
6. Reorganise the [answer test](../Authoring/Answer_Tests/index.md) documentation.

Major re-working of Javascript in STACK.  Specifically

1. STACK-JS a VLE agnostic JavaScript system that moves all script execution into sandbox iframes and restricts the things those scripts can do outside that sandbox. Basically, replaces the `[[jsxgraph]]`-block and provides ways for doing other scripting.
2. Initial implementation of the `[[reveal]]`-block (#570) using the STACK-JS system.
3. Various related blocks like `[[iframe]]`, `[[javascript]]`, `[[style]]`, `[[script]]`, and `[[cors]]`
4. This version does not yet forbide all JavaScript outside STACK-JS, but do prepare future updates to do so and start migrating existing scripts into STACK-JS.

These changes are significant and we strongly recommned you test all affected questions.

## Version 4.4.2

Released January 2023.

This is mainly a bug fix version.

1. Add in functionality to display Maxima expressions as [trees](../Authoring/Expression_tree_display.md) with CSS using  `disptree`.
2. Load the `diag` package for better linear algebra support.
3. Better layout on the question testing page.
4. Question variables and feedback variables are now in monospace.
5. Add in the `onum` option, i.e. `make_multsgn("onum")` in the [options](../Authoring/Question_options.md).

## Version 4.4.1

Released August 2022.

This is mainly a bug fix version.  Version 4.4.0 has a number of major re-writes and also supports Moodle 4.0.

## Version 4.4.0

Released July 2022.

Major rewrite of the PRT and CASText systems, focus on performance and limitations of the previous systems.  This release has changed/tightened up some question authoring causing some problems with existing questions.  For this reason we have written dedicated [release notes](../Installation/Release_notes_4_4_x.md) for v4.4.0.

1. Release documentation under CC-BY-SA.
2. Caching validation.
3. Compiled PRTs, which are now true `if`-statements in the CAS and issue #150 is now handled.
4. The marks and penalty fields in the PRTs can be numbers, or other variables defined elsewhere in the question.
5. Change behaviour of UnitsAbsolute in response to discussion of issue #448.
6. CASText2 is the new [CASText-system](../Authoring/Question_blocks/index.md) and it supports mixed formats and provides new blocks for declaring formats.
7. Markdown is now a supportted format and value injections into it will get correctly escaped. Use triple slashes for math-mode...
8. There are now means of [including external](../Authoring/Inclusions.md) code and CASText fragments from an URL.
9. One can now generate [text-files for download](../Authoring/Serving_out_data.md) with a special block in the question-text. CSV:s of student specific random data etc.
10. Inline CASText is now a feature, it will become more relevant in the future input-system. For now you may [use it in MCQ labels](../Authoring/Inputs/Multiple_choice_input.md#castextlabels).
11. A new CASText block `[[jsstring]]` for generating JavaScript strings to be used in scripting is now available. It should help when one wants to construct complex values.
12. There now exists a built in [language localisation system](../Authoring/Languages.md), that allows access to the language setting over at the CAS side. This mixes well with inline castext and allows localisation of MCQ labels.
13. The number of CAS-evaluation sessions has been cut down significantly. Conversely, the amount of things happening in a single CAS-session has grown significantly. This may affect your Maxima load and the size of the CAS-cache. You may need to retune your operation if you have fine tuned it based on those details. This will also affect cache keys and values so tuning caching may also matter if one tunes everything.
14. The security system now does runtime checks and no longer tries to catch evil things through static analysis in advance.
15. Added `checkvars` option to inputs.
16. Add in support for the [Damerau-Levenshtein distance](../Topics/Levenshtein_distance.md).
17. Add in suppprt for the display of [Complex Numbers](../CAS/Complex_numbers.md).
18. Add in basic solving of expressions with the not equals.  E.g. `x-1#0` is now considered equivalent to `x#1`.
19. Add in support for Moodle 4.0.

## Version 4.3.11

Released June 2022.

1. This release contains an update of JSXGraph (in advance of the forthcoming 4.4 release) to facilitate immediate materials development.
2. Adopt moodle-ci.

## Version 4.3.10

Released December 2021.

1. Add in filter `420_consolidate_subscripts` to consolidate students' input with subscripts from `M_1` to `M1`.
2. Support [variant matching](../STACK_question_admin/Deploying_matched_variants.md).
3. Add in the option `arccos(x)/arcosh(x)` for display of trig.  This notation exists becase `arcsin` gives the arc length on the unit circle for a given y-coordinate. `arsinh` gives an area enclosed by a hyperbola and two rays from the origin for a given y-coordinate.
4. Allow students to type `arccos` etc. and treat these as synonyms of the trig functions.
5. Substantially improve the basic question usage report.

## Version 4.3.9

Released July 2021.

Mostly minor bug fixes.

* Add in additional cache `compiledcache` to reduce validation overheads by compiling questions.
* Improve checking of teacher's code for better security, this requires a new admin setting `caspreparse` for back compatibility.
* Add in local.mac for site specific code.
* Move STACK admin UI scripts to `adminui`.
* Add in ATEqualComAssRules
* Filter student's input so groups are turned into `ntuple` inert functions.  At last students can type in coordinates as `(x,y)` as input!
* Add in warnings of language mismatch in parts of a question.
* Add in warnings where the answer test needs a raw input but appears to get a calculated value.
* Expand `rand` to accept sets and make a random selection from sets.  Add `rand_selection_with_replacement`.

## Version 4.3.8

Released December 2020.

* Introduce "context variables" which propagate throughout a question, enabling `texput` to operate in inputs as well as general castext.
* Autoload `trigtools` to provide better simplification options for trig expressions and fix long-standing bug with `trigrat`.
* Make it much easier for colleagues to construct a test case using the "teacher's answer" input values as test case inputs.
* Allow users to modify the `notanswered` message in dropdown and radio inputs.
* Move all answer tests to Maxima.
* Separate out Maxima commands in the install process.


## Version 4.3.7

Released October 2020.

Bug fixes and minor improvements.

## Version 4.3.6

Released October 2020.

Bug fixes and minor improvements.

## Version 4.3.5

* Add in the [HELM](../Reference/HELM.md) styles.

## Version 4.3.5

Released August 2020.

Bug fixes and minor improvements.

* Remove all strictSyntax functionality (DB, import/export functions remain).
* Add in further styles for proof with numbered lists.

## Version 4.3.4

Released June 2020.

Bug fixes and minor improvements to units.

## Version 4.3.3

Released May 2020.

Bug fixes and documentation.

## Version 4.3.2

Released May 2020.

* Document and support for simple manipulation of [real intervals](../CAS/Real_Intervals.md) which Maxima does not have a library for.
* Document and support for simple manipulation of [propositional logic](../Topics/Propositional_Logic.md) based on Maxima's logic package.
* Document and support for simple manipulation of [tables](../Authoring/Tables.md) mainly to support easy display of truth tables in logic..
* Better support for [semi-automatic marking](../Moodle/Semi-automatic_Marking.md).
* Add in the resizable matrix input type (varmatrix).
* Fixed bug with javascript on pages with more than one matrix.

## Version 4.3.1

Released April 2020.  Bug fixes and features which require DB changes.

* Add in STACK option "logicsymbol" to control how logical symbols are displayed.
* Add in formative potential response trees.
* Add in option `feedbackstyle` to potential response trees.
* Add in a new answer test AlgEquivNouns.

## Version 4.3

Released April 2020.  This has been tested successfully during the spring semester, at three insitutions with large groups.  STACK 4.3 is, because of the new parser, slightly slower than previous releases.

Version 4.3 requires the PHP package `mbstring` (which will be required from Moodle 3.9 anyway).  Do not attempt to upgrade without checking you have `mbstring` on your server.  Navigate to this page on Moodle to confirm.

    /admin/environment.php

Version 4.3 represents a major internal re-engineering of STACK, with a new dedicated parser and an updated mechanism for connecting to Maxima.  This is a significant improvement, refactoring some of the oldest code and unblocking progress to a wide range of requested features.

There have been a number of changes:

* In the forbidden words we now match whole words not substrings.
* Removed the RegExp answer test.  Added the SRegExp answer test using Maxima's `regex_match` function.
* Use of units is now reflected throughout a question.  This reduces the need to declare units in all contexts.
* Internally, the "+-" operator has been replaced with a new infix operation "#pm#".  Instead of `a+-b` teachers now must type `a#pm#b`.  This change was necessary to deal with differences between versions of Maxima when dealing with expresions.

New features in v4.3:

* Add in full parser, to address issue #324.
* Add in input option 'align'.
* Add in input option 'nounits'.
* Add in input option 'compact' to input "Show the validation" parameter.
* Add in a [basic question use report](../Authoring/../STACK_question_admin/Reporting.md) page, linked from the question testing page.
* Add in house styles to help typeset [proof](../Topics/Proof/Proof_styles.md).
* Add cache to help reduce parsing overhead.


## Version 4.2.3

Released January 2020.  This release is a marker before the release of STACK v4.3.

## Version 4.2.2

Released September 2019.

* Removed the Maxima MathML code (which wasn't connected or used).
* Add in metadata system to the documentation (Thanks to Malthe Sporring for this suggestion).
* Add in extra option `simp` to inputs.
* Add in extra options in the input `allowempty` and `hideanswer`.
* Review and updated documentation (thanks to Malthe Sporring).

## Version 4.2.1

Released August 2018.

* Add in privacy subsystem classes for GDPR compliance.

## Version 4.2

Released July 2018.

Note: newer versions of Maxima require that a variable has been initialised as a list/array before you can assign values to its indices.  For this reason some older questions may stop working when you upgrade to a new version of Maxima.  Please use the bulk test script after each upgrade!  See issue #343.

Note: the behaviour of the Maxima `addrow` function has changed.  Use the bulk test script to identify questions which are affected. Note, once you save a question you will update the version number, and this will prevent questions using `addrow` from being identified.

* Add support for using JSXGraph  `http://jsxgraph.org` for better support of interactive graphics, and as part of an input type.  See [JSXGraph](../Specialist_tools/JSXGraph/index.md)
* Add in a version number to STACK questions.
* Update reasoning by equivalence.  This includes the following.
  1. Equating coefficients as a step in reasoning by equivalence. E.g. \( a x^2+b x+c=r x^2+s x+t \leftrightarrow a=r \text{ and } b=s \text{ and } c=t\). See `poly_equate_coeffs` in assessment.mac
  2. Solving simple simultaneous equations.  (Interface)
  3. Include simple calculus operations (but constant of integration needs some further thought.)
* Refactor internal question validation away from Moodle editing, and into the question type.  Add in a "warning" system.
* Add in native multi-language support, to separate out languages in the question text.  This is needed so as not to create spurious validation errors, such as "input cannot occur twice".
* Output results of PRTs in the `summarise_response` method of `question.php`.  Gives more information for reporting.
* Sort out the "addrow" problem. (See issue #333).  This is changed to "rowadd".
* Add in check for "mul" (see issue #339) and better checking of input options.
* Refactor equiv_input and MCQ to make use of the new extra options mechanism.
* Add in support for the Maxima `simplex` package.
* Add an answer test to check if decimal separator is in the wrong place (See issue #314).
* Add an answer test to check sets and provide better feedback.
* Significant improvements to the bulk testing, returning runtime errors and identifying all questions without tests or worked solutions.
* Better CSS.  (See issue #380)


## STACK 4.1

Released December 2017.

Numerous minor bug fixes and improvements.

* Add in support for the syntaxHint in the matrix input.
* On the questiontestrun page, have options to (a) delete all question variants.
* Add in a `size` option to set the size of a plot.
* Add in an answer test which accepts "at least" n significant figures. (See issue #313)
* Add in the "string" input type.
* Add test which checks if there are any rational expressions in the denominator of a fraction.  (Functionality added to LowestTerms test, which looks at the form of rational expressions).
* Add an option to remove hard-coded "not answered" option from Radio input type. (See issue #304)
* Add in a "numerical" input type which requires a student to type in a number.  This has various options, see the [docs](../Authoring/Inputs/Numerical_input.md).
* Specify numerical precision for validation in numerical and units input types.
* Refactor the inputs so that extra options can be added more easily, and shared between inputs.

## STACK 4.0.1

Released August 2017.

This is a bug-fix release, mostly associated with the upgrade process from version 3.X to 4.X.

* Fix a bug in the upgrade script.
* Fix a bug in the testing procedure in the "question test" script, and improve the way deployed variants are tested.
* Make SVG the default image format for pictures created by Maxima.  (Old .png code left in place in this release, but no user option to access this functionality.)

## STACK 4.0

Released August 2017.

**STACK 4.0 represents a major release of STACK and is a non-reversible change, with important differences which break back-compatibility.**

Note that much of the underlying code in this development have been used at Aalto for many years, with complex questions.  We believe these are battle tested improvements in the functionality.

STACK 4.0 includes the block features and other important changes in CASText.

* To generate the LaTeX displayed form of a CAS variable in CASText you must use `{@...@}`.  Note the curly braces which now must be used.  We have an upgrade script for existing questions.
* To generate the Maxima value of a CAS variable in CASText you can use `{#...#}`. This is useful when interfacing with other software, or showing examples to students.
* CASText now supports conditional statements and adaptive blocks. See [question blocks](../Authoring/Question_blocks/index.md).

Other changes.

* The question note is no longer limited in size.
* Mathematics in LaTeX can no longer be supported with `$..$` or `$$..$$`.  This usage has been discouraged for many years, and we have a long-standing "fix" script to convert from dollars to the forms `\(..\)` and `\[..\]`.
* Remove the artificial limit on the size of CASText.  We now rely on surrounding limits, like POST requests and database limits.  This may result in ugly errors, but we need larger limits to accommodate interactive elements embedded into text fields.

## STACK 3.6

Released July 2017.

This release developed the first version of an input to assess line by line "reasoning by equivalence" input.  See the documentation on [equivalence reasoning](../Specialist_tools/Equivalence_reasoning/index.md).

Other new features and enhancements in this release.

* Modify the text area input so that each line is validated separately.
* Add a "scratch working" input type in which students can record their thinking etc. alongside the final answer.
* Support for intervals in STACK, using the Maxima syntax `oo(a,b)` for an open interval \((a,b)\), `cc(a,b)` for an open interval \([a,b]\) and `oc(a,b)`, `co(a,b)` for the half open intervals.
* Much better support for solving and dealing with single variable inequalities.

## Version 3.5.7

Released June 2017.

Numerous minor bug fixes and improvements.

## Version 3.5.6

Released December 2016.

Numerous minor bug fixes and improvements, particularly with numerical tests and scientific units.

1. Change the display so that the underscore in atoms is displayed using subscripts.
2. Added support for logarithms to an arbitrary base.
3. Added `SigFigsStrict` answer test.
4. Better support for floating point numbers, including the preservation and display of trailing zeros in numerical tests.

Note, many of these changes have resulted in stricter rules on the acceptability of strings and stricter validation rules.

1 You can no longer have a feedback variable, or a question variable, with a name that is the same as an input.
2. `log10` function and `log_b` functions are now handled by STACK, by manipulating the CAS string before it is sent to Maxima. Therefore, if your question previously defined a function with names like that, it will now break.
3. Variable names with a digit in the middle `eqn1gen` no longer work. (They should never have been used, but used not to break questions.)
4. Previously, unnecessary `\` in CAS text were ignored. E.g. if you have a question variable called `vangle2` then `{@\vangle2@}` used to work, it does not any more.

## Version 3.5.5

Released August 2016.

Numerous minor bug fixes and improvements, particularly with numerical tests and scientific units.

1. Expose functionality of `printf` to better control the display of integers and floats.
2. Expand the "units" answer test to allow authors to use other numerical answer tests, see [units](../Topics/Units.md).
3. Add a mechanism to allow spaces in inputs.  Trial functionality, which might change.
4. Improve the mechanism to create a Maxima image and update the options in one go.
5. Numerous options for units and the display of fractions.
6. Added an xMaxima file to give more direct access to the sandbox.

## Version 3.5

Numerous minor bug fixes and improvements.

1. Added an export mechanism for single stack questions through a link on the "Question tests & deployed variant" page.
2. Modify the text area input so that each line is validated separately.
3. Support for plot2d "label" command.
4. Added support for `grid2d` for plot in newer versions of Maxima only.
5. Add the `NOCONST` option to the ATInt answertest.
6. Added support for optional Maxima packages through the config settings.
7. Added the dropdown, radio and checkbox input types.
8. Added basic support for scientific [units](../Topics/Units.md), including a new input type and science answer tests.

## Version 3.4

Released September 2015.

This contains numerous minor bug fixes and improvements.

1. Expand the capability of ATInt options to accept the integrand to improve feedback.
2. When validating a student's expression, add the option to show a list of variables alongside the displayed expression.
3. The install process now attempts to auto-generate a Maxima image.
4. Support for the stats package added.
5. Change in the behaviour of the CASEqual answer test.  Now we always assume `simp:false`.
6. Add support for more AMS mathematics environments, including `\begin{align}...\end{align}`, `\begin{align*}...\end{align*}` etc.
7. STACK tried to automatically write an optimised image for Linux.


## Version 3.3

Released September 2014.

This contains numerous minor bug fixes and improvements.

 1. Added in the [Question blocks](../Authoring/Question_blocks/index.md)
 2. Changes to validation of casstrings. We now *allow* syntax such as 3e2 to represent floating point numbers.  The strict syntax settings still flag 3e2 as "missing stars".
 3. Improvements to catching common syntax errors with trig functions, e.g. sin^-1(x) or cos[x]
 4. Refactored the numerical tests.  This means they are now standard Maxima tests, not using PHP.
 5. Allow the use of the Maxima orderless and ordergreat in cassessions.  This helps with display, without turning off simplification.
 6. Expanding CASText features.
   *  Enable a function as an answer type, e.g. improve validation.
   *  Refactor answer test unit testing to distinguish "test fail" from "zero".
   *  Reject things like sin*(x) and sin^2(x) as invalid
   *  Provide a new option on how parentheses are displayed for matrices
   *  Provide an extra syntax checking option to enable stars to be inserted between single characters, e.g. xy -> x*y.
 7.  Add the input parameter `allowwords` to enable the teacher to specify some permitted words of more than 2 symbols length.
 8.  Reinstate the STACK 2 feature called "Hints".  This has been done as a "Fact sheet" to avoid ambiguity with other Moodle features.  See [Fact sheet](../Authoring/Question_blocks/Fact_sheets.md) documentation.  
 9.  Better install (auto OS detection), healthcheck and testing.
 10. When using the Maxima Pool servlet, it is now possible to use any type of HTTP authentication

    (e.g. basic or digest), and there is a separate configuration option, so that you don't need to put the username and password in the URL.


## Version 3.2

Released January 2014. This is mainly a bugfix release, and is updated to work with more recent versions of Moodle and Maxima 5.31.3.

Changes since 3.1:

 1. Better support for inequalities
 2. Better support for reporting, e.g. more consistent tagging of errors, validation notes etc.
 3. Support for "discrete" and "parametric" plots.  Support for plot Alt text.
  *  Refactor the Maxima plot command to include "discrete" and "parametric plots"
  *  Refactor the Maxima plot command to include options, e.g., xlabel, ylabel, legend, color, style, point_type.
 4. Enable the student's answer to be a function.
 5. Minor accessibility improvements to underline all terms generated by Maxima in red, in addition to just using colour.
 6. Removal of the "MaximaPool" and "MaximaPool (optimised)" options for the platform type.  We just now have the "server" type.

## Version 3.1

Released July 2013. This includes all the bugs found and fixed during the first
year of use at Birmingham, and the first six months at the OU.

Changes since 3.0:

### STACK custom reports

* Split up the answer notes to report back for each PRT separately.
* Introduce "validation notes".  This should work at the PHP level, recording reasons for invalidity.  Since we already connect to the CAS, this should also record whether the student's input is equivalent to the teacher's, in what sense, and what form their answer is in.  Maybe too slow?  Useful perhaps for learning analytics.

### Expanding CASText features

* Add in support for strings within CASText.  These are currently supported only when the contents is a valid casstring, which is overly restrictive.

### Improvements to the editing form

 2. A way to set defaults for many of the options on the question edit form. There are two ways we could do it. We could make it a system-wide setting, controlled by the admin, just like admins can set defaults for all the quiz settings. Alternatively, we could use user_preferences, so the next time you create a STACK question, it uses the same settings as the previous STACK question you created.
 3. Display inputs and PRTs in the order they are mentioned in the question text + specific feedback.
 4. Allow an arbitrary PRT node to be the root node, rather than assuming it is the lowest numbered one.
 5. Display a graphical representation of each PRT, that can be clicked to jump to that Node on the editing form.
 6. When cloning a question with the 'Make copy' button, also clone the question tests.

### Other improvements

* Create a "tidy question" script that can be used to rename Inputs, PRTs and/or Nodes everywhere in a question.
* Add CASText-enabled ALT tags to the automatically generated images. For example, adding a final, optional, string argument to the "plot" command that the system uses as the ALT text of the image. That way, we can say the function that the graph is of.
* New option for how inverse trig functions are displayed.
* A script to run question tests in bulk.
* Add a new answer test to deal with decimal places.
* STACK questions with no inputs, and/or no PRTs now work properly.

### Bug fixes

* Fix instant validation for text-area inputs.
* With "Check the type of the response" set to "Yes", if an expression is given and an equation is entered, the error generated is: "Your answer is an equation, but the expression to which it is being compared is not. You may have typed something like "y=2*x+1" when you only needed to type "2*x+1"." This might confuse students. They don't know what " the expression to which it is being compared" is! Perhaps this warning could be reworded something like: "You have entered an equation, but an equation is not expected here. You may have typed something like "y=2*x+1" when you only needed to type "2*x+1"." We should have more messages for each type of failed situation....
* Alt tags in images generated by plots has changed.  The default value now includes a string representation of the function plotted.  See [plots](../CAS/Maxima_plot.md#alttext) for more details.
* Assorted other accessibility fixes.
* Standard PRT feedback options are now processed as CAS text.
* There was a bug where clearing the CAS cache broke images in the question text. Now fixed.


## Version 3.0

Released January 2013.  This has been tested successfully for two semesters, with groups of up to 250 university students and a variety of topics.

Major re-engineering of the code by the Open University, The  University of Birmingham and the University of Helsinki.  Documentation added by Ben Holmes.

The most important change is the decision to re-work STACK as a question type for the Moodle quiz.  There is no longer a separate front end for STACK, or (currently) a mechanism to include STACK questions into other websites via a SOAP webservice. This round of development does not plan to introduce major new features, or to make major changes to the core functionality. An explicit aim is that "old questions will still work".

Key features

* __Major difference:__ Integration into the quiz of Moodle 2.3 as a question type.
* Support for Maxima up to 5.28.0.
* Documentation moved from the wiki to within the code base.
* Move from CVS to GIT.

### Changes in features between STACK 2 and STACK 3.

* Key-val pairs, i.e. Question variables and feedback variables, now use Maxima's assignment syntax, e.g. `n:5` not the oldstyle `n=5`.  The importer automatically converts old questions to this new style.
* Interaction elements, now called inputs, are indicated in questions as `[[input:ans1]]` to match the existing style in Moodle.  Existing questions will be converted when imported.
* A number of other terminology changes have brought STACK's use into line with Moodle's, e.g. Worked solution has changed to "general feedback".
* Change in the internal name of one answer test `Equal_Com_ASS` changed to `EqualComASS`.
* Feature "allowed words" dropped from inputs (i.e. interaction elements).
* JSMath is no longer under development, and hence we are no longer providing an option for this in STACK.  However, in STACK 2 we modified JSMath to enable inputs within equations.  Display now assumes the use of a Moodle filter and we recommend (and test with) MathJax, which does not currently support this feature.  If it is important for you to use this feature you will need to copy and modify the load.js file from STACK 2 and use JSMath.
* Worked solution on demand feature has been removed.  This was a hack in STACK 2, and the use of Moodle quiz has made this unnecessary.
* Some options are no longer needed.  This functionality is now handled by the "behaviours", so are unnecessary in STACK 3.
 * The "Feedback used".
 * The "Mark modification".
* We have lost some of the nice styling on the editing form, compared to Stack 2.
* Answer tests no longer return a numerical mark, hence the "+AT" option for mark modification method has been dropped.
* The STACK Maxima function `filter` has been removed.  It should be replaced by the internal Maxima function `sublist`.  Note, the order of the arguments is reversed!
* STACK can now work with either MathJax, the Moodle TeX filter, or the OU's maths rendering filter.
* The Maxima libraries `powers` and `format` have been removed.
* We now strongly discourage the use of dollar symbols for denoting LaTeX mathematics environments.  See the pages on [MathJax](../Installation/Mathjax.md#delimiters) for more information on this change.
* The expressions supplied by the question author as question tests are no longer simplified at all.  See the entry on [question tests](../STACK_question_admin/Testing.md#Simplification).

### Full development log

#### Milestone 0

1. Get STACK in Moodle to connect to Maxima, and clean-up CAS code.
2. Moodle-style settings page for STACK's options.
3. Re-implement caschat script in Moodle.
4. Re-implement healthcheck script in Moodle.
5. Make all the answer-tests work in Moodle.
6. Make the answer-tests self-test script work in Moodle.
7. Make all the input elements work in Moodle.
8. Make the input elements self-test script work in Moodle.
9. Add all the docs files within the Moodle question type.
10. Clean up the PRT code, and make it work within Moodle.
11. Code to generate the standard test-_n_ question definitions within Moodle, to help with unit testing.
12. Basic Moodle question type that ties all the components together in a basically working form.

#### Milestone 1

1. Caching of Maxima results, for performance reasons.
2. Database tables to store all aspects of the question definitions.
3. Question editing form that can handle multi-input and multi-PRT questions, with validation.
4. Re-implement question tests in Moodle.
 1. Except that the test input need to be evaluated expressions, not just strings.
5. Get deploying, and a fixed number of variants working in Moodle.
6. Make multi-part STACK questions work exactly right in Adaptive behaviour.
 1. Evaluate some PRTs if possible, even if not all inputs have been filled in.
 2. Correct computation of penalty for each PRT, and hence overall final grade.
 3. Problem with expressions in feedback CAS-text not being simplified.

#### Milestone 2

1. Make sure that STACK questions work as well as possible in the standard Moodle reports.
2. Implement the Moodle backup/restore code for stack questions.
3. Implement Moodle XML format import and export.
4. Investigate ways of running Maxima on a separate server.
5. Implement random seed control like for varnumeric.

At this point STACK will be "ready" for use with students, although not all features will be available.

#### Milestone 3

1. Finish STACK 2 importer: ensure all fields are imported correctly by the question importer.
2. Make STACK respect all Moodle behaviours.
 1. Deferred feedback
 2. Interactive
 3. Deferred feedback with CBM
 4. Immediate feedback
 5. Immediate feedback with CBM - no unit tests, but if the others work, this one must.
3.  Add sample_questions, and update question banks for STACK 3.0.
4. Improve the way questions are deployed.
 1. Only deploy new variants.
5. Editing form: a way to remove a given PRT node.
6. Fix bug: penalties and other fields being changed from NULL to 0 when being stored in the database.
7. Add back Matrix input type.
9. In adaptive mode, display the scoring information for each PRT when it has been evaluated.

Once completed we are ready for the **Beta release!**

#### Beta testing period

1. Do lots of testing, report and fix bugs.
2. Eliminate as many TO-DOs from the code as possible.
3. Add back other translations from STACK 2.0, preserving as many of the existing strings as possible. NOTE: the new format of the language strings containing parameters.  In particular, strings {$a[0]} need to be changed to {$a->m0}, etc.
4. Add back all questions from the diagnostic quiz project as further examples.
5. Deploy many variants at once.

#### Editing form

1. Form validation should reject a PRT where Node x next -> Node x. Actually, it should validate that we have a connected DAG.
2. Add back the help for editing PRT nodes.
3. When validating the editing form, actually evaluate the Maxima code.
4. When validating the editing form, ensure there are no @ and $ in the fields that expect Maxima code.
5. Ensure links from the editing form end up at the STACK docs. This is now work in progress, but relies on http://tracker.moodle.org/browse/MDL-34035 getting accepted into Moodle core. In which case we can use this commit: https://github.com/timhunt/moodle-qtype_stack/compare/helplinks.
6. Hide dropdown input type in the editing form until there is a way to set the list of choices.

#### Testing questions

1. **DOES NOT HAPPEN ANY MORE** With a question like test-3, if all the inputs were valid, and then you change the value for some inputs, the corresponding PRTs output the 'Standard feedback for incorrect' when showing the new inputs for the purpose of validation.
2. Images added to prt node true or false feedback do not get displayed. There is a missing call to format_text.
3. A button on the create test-case form, to fill in the expected results to automatically make a passing test-case.
4. Singlechar input should validate that the input is a single char. (There is a TO-DO in the code for this.)
5. Dropdown input should make sure that only allowed values are submitted. (There is a TO-DO in the code for this.)
6. Dropdown input element needs some unit tests. (There is a TO-DO in the code for this.)
7. We need to check for and handle CAS errors in get_prt_result and grade_parts_that_can_be_graded. (There is a TO-DO in the code for this.)
8. Un-comment the throw in the matrix input.
9. Unit tests for adaptive mode score display - and to verify nothing like that appears for other behaviours.
10. Duplicate response detection for PRTs should consider all previous responses.
11. It appears as if the phrase "This submission attracted a penalty of ..." isn't working.  It looks like this is the *old* penalty, not the *current*.
12. PRT node feedback was briefly not being treated as CAS text.
13. Improve editing UI for test-cases https://github.com/maths/moodle-qtype_stack/issues/15

##### Optimising Maxima

1. Since I have optimized Maxima, I removed write permissions to /moodledata/stack/maximalocal.mac. This makes the healthcheck script unrunnable, and hence I cannot clear the STACK cache.
2. Finish off the system for running Maxima on another server (https://github.com/maths/moodle-qtype_stack/pull/8)

##### Documentation system

1. fix `maintenance.php`.


## Version 2.2

Released: October 2010 session.

* Enhanced reporting features.
* Enhanced question management features in Moodle.  E.g. [import multiple questions](https://sourceforge.net/tracker/?func=detail&aid=2930512&group_id=119224&atid=683351)
  from AiM/Maple TA at once, assign multiple questions to Moodle question banks.
* Slider interaction elements.

## Version 2.1

Developed by Chris Sangwin and Simon Hammond at the University of Birmingham.
Released: Easter 2010 session.

Key features

* [Precision](../Authoring/Answer_Tests/index.md#Precision) answer test added to allow significant to be checked.
* [Form](../Authoring/Answer_Tests/index.md#Form) answer test added to test if an expression is in completed square form.
* List interaction element expanded to include checkboxes.
* Move to Maxima's `random()` function, rather than generate our own pseudo random numbers
* [Conditionals in CASText](https://sourceforge.net/tracker/?func=detail&aid=2888054&group_id=119224&atid=683351)
* Support for Maxima 5.20.1
* New option added: OptWorkedSol.  This allows the teacher to decide whether the tick box to request the worked solution is available.
* Sample resources included as part of the FETLAR project.


## Version 2.0

Released, September 2007.  Developed by Jonathan Hart and Chris Sangwin at the University of Birmingham.

Key features

* Display of mathematics taken care of by JSMath.
* Integrated into the Moodle quiz using the ["remote question protocol"](https://docs.moodle.org/dev/Open_protocol_for_accessing_question_engines).  The RQP was designed with STACK, and similar systems, in mind.
* Variety of interaction elements.
* Multi-part questions.
* Cache.
* Item tests.

### Version 1.0

Released, 2005.  Developed by Chris Sangwin at the University of Birmingham.

### Pre-history

STACK is a direct development of the CABLE project which ran at the University of Birmingham. CABLE was a development of the design of the AiM computer aided assessment system.
