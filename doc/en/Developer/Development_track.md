# Development track

These are the major tasks we still need to complete in approximate order and importance.

## Milestone 0

1. **DONE** Get STACK in Moodle to connect to Maxima, and clean-up CAS code.
2. **DONE** Moodle-style settings page for STACK's options.
3. **DONE** Re-implement caschat script in Moodle.
4. **DONE** Re-implement healthcheck script in Moodle.
5. **DONE** Make all the answer-tests work in Moodle.
6. **DONE** Make the answer-tests self-test script work in Moodle.
7. **DONE** Make all the input elements work in Moodle.
8. **DONE** Make the input elements self-test script work in Moodle.
9. **DONE** Add all the docs files within the Moodle question type.
10. **DONE** Clean up the PRT code, and make it work within Moodle.
11. **DONE** Code to generate the standard test-_n_ question definitions within Moodle, to help with unit testing.
12. **DONE** Basic Moodle question type that ties all the components together in a basically working form.

## Milestone 1

1. **DONE** Caching of Maxima results, for performance reasons.
2. **DONE** Database tables to store all aspects of the question definitions.
3. **DONE** Question editing form that can handle multi-input and multi-PRT questions, with validation.
4. **DONE** Re-implement question tests in Moodle.
 1. **DONE** Except that the test input need to be evaluated expressions, not just strings.
5. **DONE** Get deploying, and a fixed number of variants working in Moodle.
6. Make multi-part STACK questions work exactly right in Adaptive behaviour.
 1. Evaluate some PRTs if possible, even if not all inputs have been filled in.
 2. Correct computation of penalty for each PRT, and hence overall final grade.
 3. **DONE** Problem with expressions in feedback CAS-text not being simplified.

## Milestone 2

1. Reporting
 1. Make sure that STACK questions work as well as possible in the standard Moodle reports.
 2. Consider what additional custom STACK reporting we need.
2. **DONE** Implement the Moodle backup/restore code for stack questions.
3. **DONE** Implement Moodle XML format import and export.
4. Investigate ways of running Maxima on a separate server.
5. **DONE** Implement random seed control like for varnumeric.

At this point STACK will be "ready" for use with students, although not all features will be available.

## Milestone 3

1. Finish STACK 2 importer: ensure all fields are imported correctly by the question importer.
2. Implement additional reporting as determined above.
3. Make STACK respect all Moodle behaviours.
4. Add back in all input types, including dragmath/NUMBAS.
5. Add sample_questions, and update question banks for STACK 3.0.
6. Link the STACK documentation to Moodle's help icons on the editing form, etc.

## Known bugs/issues

1. Validation of student inputs has too many brackets.  This is a Maxima issue.  To reproduce it type `simp:false;` then `-3*x^2`.  We get unneeded brackets.

### Editing form

1. Form validation should reject a PRT where Node x next -> Node x. Actually, it should validate that we have a connected DAG.
2. Button to remove way to rename PRTs and inputs.
3. Button to remove a node from a PRT.
4. UI to add a new PRT, so you don't have to know to edit the question text to add it.
5. When validating the editing form, actually evaluate the Maxima code.
6. When validating the editing form, ensure there are no @ and $ in the fields that expect Maxima code.

### Testing questions

1. There is no check that SUM(question values of all PRTs) = 1. Either we need to enforce that, or we need to compute question fraction as a weighed average of PRT scores.
2. With a question like test-3, if all the inputs were valid, and then you change the value for some inputs, the corresponding PRTs output the 'Standard feedback for incorrect' when showing the new inputs for the purpose of validation.
3. Images added to prt node true or false feedback do not get displayed. There is a missing call to format_text.

## Future plans

1. Find a way to make the answer test test-suite and input test test-suite available to question authors.
3. A button on the create test-case form, to fill in the expected results to automatically make a passing test-case.
4. Facility to import test-cases in-bulk as CSV (or something). Likewise export.
5. Change unit tests to use PHPunit, which is the new standard in Moodle 2.3.
6. If stack is installed on a site with a _ in the URL (e.g. http://localhost/moodle_head/) then plots do not work. Maxima seems to escape the _ to \_ for some reason.

We have a dedicated page for [future plans](Future_plans.md).

---
# Other tasks

These tasks also need to be done, but do not block progress towards getting STACK basically working in moodle.

1. Refactor the way STACK surrounds mathematics with LaTeX  environments.  Really we need a function

stack_maths($ex,$format = INLINE/DISPLAY)

which takes the castring $ex, and surrounds it by strings  depending on whether we want an inline or displayed equation.   Similar to the translator function...

Some miscellaneous things
* Answer tests should be like inputs. We should return an answer test object, not a controller object.
* $at->get_at_mark() really ought to be $at->matches(), since that is how it is used.
* Finish cleaning up stack_utils.
* Make sure error messages on the authoring form sit next to the appropriate field.  Currently this is a limitation of moodle forms.
* Expand the capabilities of CASText to enable the value of a variable (not just its displayed LaTeX form) to be included in the HTML.  E.g. using a tag such as `#x^2#` now this syntax is available again.  This is needed for the Google charts.

## Languages

* Add in other languages.   Copy over only those strings which are really needed.  NOTE: the new format of the language strings containing parameters.  In particular, strings {$a[0]} need to be changed to {$a->m0}, etc.

## Maxima

1. Update the list of forbidden keywords....
2. Investigate better ways of connecting to Maxima.
  *  <http://code.google.com/p/remote-maxima/>
  *  <http://www.lon-capa.org/maximaasserver.html>
3. Refactor Maxima code to change from $'s and $$'s to \[ \] and \( and \).

## Documentation system

1. Ensure links from the editing form end up at the STACK docs.
2. 404 error does not add an entry to the log.
3. fix `maintenance.php`.
4. Update the file

     \stack\www\lib\maxima\maximafun.php

---
# History of previous versions of STACK

STACK is a direct development of the CABLE project which ran at the University of Birmingham. CABLE was a development of the AiM computer aided assessment system.

## Version 3.0

_Not yet released_.  Target, September 2012.

Major re-engineering of the code by the Open University, The  University of Birmingham and the University of Helsinki.  Reporting and documentation added by Ben Holmes.

The most important change is the decision to re-work STACK as a question type for the Moodle quiz.  There is no longer a separate front end for STACK, or (currently) a mechanism to include STACK questions into other websites via a SOAP webservice. This round of development does not plan to introduce major new features, or to make major changes to the core functionality. An explicit aim is that "old questions will still work".

Key features

* __Major difference:__ Integration into the quiz of Moodle 2.3 as a question type.
* Support for Maxima up to 5.26.0.
* Documentation moved from the wiki to within the code base.
* Move from CVS to GIT.

### Changes in features between STACK 2 and STACK 3.

* Key-val pairs, i.e. Question variables and feedback variables, now use Maxima's assignment syntax, e.g. `n:5` not the oldstyle `n=5`.
* Interaction elements, now called inputs, are indicated in questions as `[[input:ans1]]` to match the existing style in Moodle.  Existing questions will be converted when imported.
* A number of other terminology changes have brought STACK's use into line with Moodle's, e.g. Worked solution has changed to "general feedback".
* Change in the internal name of one answer test `Equal_Com_ASS` changed to `EqualComASS`.
* Feature "allowed words" dropped from inputs (i.e. interaction elements).
* Input "Dropdown" list -> should be automatically imported to "list"
* JSMath is no longer under development, and hence we are no longer providing an option for this in STACK.  However, in STACK 2 we modified JSMath to enable inputs within equations.  Display now assumes the use of a Moodle filter and we recommend (and test with) MathJax, which does not currently support this feature.  If it is important for you to use this feature you will need to copy and modify the load.js file from STACK 2 and use JSMath.
* Worked solution on demand feature has been removed.  This was a hack in STACK 2, and the use of Moodle quiz has made this unnecessary.
* We have lost some of the nice styling on the editing form, compared to Stack 2.
* Answer tests no longer return a numerical mark, hence the "+AT" option for mark modification method has been dropped.

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

* [Precision](../Authoring/Answer_tests.md#Precision) answer test added to allow significant to be checked.
* [Form](../Authoring/Answer_tests.md#Form) answer test added to test if an expression is in completed square form.
* List interaction element expanded to include checkboxes.  See [List](../Authoring/Inputs.md#List).
* Move to Maxima's `random()` function, rather then generate our own pseudo random numbers
* [Conditionals in CASText](https://sourceforge.net/tracker/?func=detail&aid=2888054&group_id=119224&atid=683351)
* Support for Maxima 5.20.1
* New option added: OptWorkedSol.  This allows the teacher to decide whether the tick box to request the worked solution is available.
* Sample resources included as part of the FETLAR project.


## Version 2.0

Released, September 2007.  Developed by Jonathan Hart and Chris Sangwin at the University of Birmingham.

Key features

* Display of mathematics taken care of by JSMath.
* Integrated into Moodle.
* Variety of interaction elements.
* Multi-part questions.
* Cache.
* Item tests.

### Version 1.0

Released, 2005.  Developed by Chris Sangwin at the University of Birmingham.

