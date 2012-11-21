# Development track

This page describes the major tasks we still need to complete in order to be
able to release the next version of STACK. That is STACK 3.0. Plans looking
futher into the future are described on [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

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
6. **DONE** Make multi-part STACK questions work exactly right in Adaptive behaviour.
 1. **DONE** Evaluate some PRTs if possible, even if not all inputs have been filled in.
 2. **DONE** Correct computation of penalty for each PRT, and hence overall final grade.
 3. **DONE** Problem with expressions in feedback CAS-text not being simplified.

## Milestone 2

1. **DONE** Make sure that STACK questions work as well as possible in the standard Moodle reports.
2. **DONE** Implement the Moodle backup/restore code for stack questions.
3. **DONE** Implement Moodle XML format import and export.
4. **DONE** Investigate ways of running Maxima on a separate server.
5. **DONE** Implement random seed control like for varnumeric.

At this point STACK will be "ready" for use with students, although not all features will be available.

## Milestone 3

1. **DONE** Finish STACK 2 importer: ensure all fields are imported correctly by the question importer.
2. **DONE** Make STACK respect all Moodle behaviours.
 1. **DONE** Deferred feedback
 2. **DONE** Interactive
 3. **DONE** Deferred feedback with CBM
 4. **DONE** Immediate feedback
 5. **DONE** Immediate feedback with CBM - no unit tests, but if the others work, this one must.
3. **DONE**  Add sample_questions, and update question banks for STACK 3.0.
4. **DONE** Improve the way questions are deployed.
 1. **DONE** Only deploy new versions.
5. **DONE** Editing form: a way to remove a given PRT node.
6. **DONE** Fix bug: penalties and other fields being changed from NULL to 0 when being stored in the database.
7. **DONE** Add back Matrix input type.
9. **DONE** In adaptive mode, display the scoring information for each PRT when it has been evaluated.

Once completed we are ready for the **Beta release!**

## Beta testing period

1. Do lots of testing, report and fix bugs.
 1. But list at https://github.com/maths/moodle-qtype_stack/issues
 2. See also the list below
2. Consolidation of mailing lists, forum, wiki etc.
 1. Update forum.
 2. Announcements on Moodle mailing lists.
 3. Re-install demonstration servers.
3. Eliminate as many TODOs from the code as possible.
4. Review the list of forbidden keywords....
5. Add back other translations from STACK 2.0, preserving as many of the existing strings as possible. NOTE: the new format of the language strings containing parameters.  In particular, strings {$a[0]} need to be changed to {$a->m0}, etc.

List of bugs follows/TODOs (see also https://github.com/maths/moodle-qtype_stack/issues):

### Editing form

1. **DONE** Form validation should reject a PRT where Node x next -> Node x. Actually, it should validate that we have a connected DAG.
2. **DONE** Add back the help for editing PRT nodes.
3. **DONE** When validating the editing form, actually evaluate the Maxima code.
4. **DONE** When validating the editing form, ensure there are no @ and $ in the fields that expect Maxima code.
5. **DONE** Ensure links from the editing form end up at the STACK docs. This is now work in progress, but relies on http://tracker.moodle.org/browse/MDL-34035 getting accepted into Moodle core. In which case we can use this commit: https://github.com/timhunt/moodle-qtype_stack/compare/helplinks.
6. **DONE** Hide dropdown input type in the editing form until there is a way to set the list of choices.

### Testing questions

1. **DOES NOT HAPPEN ANY MORE** With a question like test-3, if all the inputs were valid, and then you change the value for some inputs, the corresponding PRTs output the 'Standard feedback for incorrect' when showing the new inputs for the purpose of validation.
2. **DONE** Images added to prt node true or false feedback do not get displayed. There is a missing call to format_text.
3. **DONE** A button on the create test-case form, to fill in the expected results to automatically make a passing test-case.
4. **DONE** Singlechar input should validate that the input is a single char. (There is a TODO in the code for this.)
5. **DONE** Dropdown input should make sure that only allowed values are submitted. (There is a TODO in the code for this.)
6. **DONE** Dropdown input element needs some unit tests. (There is a TODO in the code for this.)
7. **DONE** We need to check for and handle CAS errors in get_prt_result and grade_parts_that_can_be_graded. (There is a TODO in the code for this.)
8. **DONE** Un-comment the throw in the matrix input.
9. Unit tests for adative mode score display - and to verify nothing like that appears for other behaviours.
10. **DONE** Duplicate response detection for PRTs should consider all previous responses.
11. **DONE** It appears as if the phrase "This submission attracted a penalty of ..." isn't working.  It looks like this is the *old* penalty, not the *current*.
12. **DONE** PRT node feedback was briefly not being treated as CAS text.
13. You cannot use one PRT node to guard the evaluation of another, for example Node 1 check x = 0, and only if that is false, Node 2 do 1 / x. We need to change how PRTs do CAS evaluation.

### Optimising Maxima

1. **DONE** Since I have optimized Maxima, I removed write permissions to /moodledata/stack/maximalocal.mac. This makes the healthcheck script unrunnable, and hence I cannot clear the STACK cache.
2. **DONE** Finish off the system for running Maxima on another server (https://github.com/maths/moodle-qtype_stack/pull/8)

### Documentation system

1. 404 error does not add an entry to the log.
2. **DONE** fix `maintenance.php`.
