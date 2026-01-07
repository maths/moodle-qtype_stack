# Bulk testing STACK questions on your site

You can bulk test all question tests on all variants of all STACK questions by using the bulk-test script.  Access this functionality from STACK's "adminui" page (or the plugin setting page)

    [...]/question/type/stack/adminui/index.php

To make use of the bulk test users require the capability `qtype/stack:usediagnostictools` via Moodle's capability system.

The bulk-test index page lists all Moodle contexts which contain STACK questions.  You can bulk-test by context.

Bulk testing does the following.

1. Validate the STACK question against it's `stackversion` number.  STACK questions store the version of the STACK plug-in _last used_ to edit the question.  The bulk tester checks for changes with the current STACK plug-in version.
2. Runs all question tests on all variants of each STACK question.
3. Catch any run-time errors generated in the above.
4. List questions with deficiencies:
  * No question tests
  * No deployed variants
  * Missing general feedback (worked solution)

A report is generated with links to the question dashboard for each question.

## Bulk test all STACK questions

Moodle site-admins can bulk test all STACK questions on a site. This is typically slow, and so is not available to general users.  Indeed, we recommend this is done using the command line.

    [...]/question/type/stack/cli/bulkrestall.php

On linux consider using "screen" to run this bulk test in the background.  Typically this process is done as part of upgrade acceptance testing.

Site admins will find a link "Run all the question tests for all the questions in the system (slow, admin only)" at the bottom of the bulk-test web index page.  However, running all the tests on a large site is typically too slow.

## Adding `[[todo]]` blocks

Running question tests is slow, and not all users have the capability `qtype/stack:usediagnostictools`.  For this reason, it is useful to be able to "tag" questions requiring attention with [`[[todo]]` blocks](../Authoring/Question_blocks/Static_blocks.md).

You can add in `[[todo]]` blocks to the question description listing problems as a one-off process.

* For the web page run the bulk-test on the chosen context, then run the page again with the option `&addtags=true` in the URL.  There is no web-form interface for this (advanced) option.
* For the CLI use the flag `--addtags`.

The purpose of adding `[[todo]]` blocks is (1) to find questions more easily without running the whole bulk test again, (2) enabling people without the capability `qtype/stack:usediagnostictools` to find questions which we know need work.

The `[[todo]]` blocks are added for broken questions, problems with upgrade, runtime errors, missing seeds, missing tests and failing question tests.  The `[[todo]]` blocks are _not_ added for empty worked solutions.

These `[[todo]]` blocks are only added once, but it does change the DB entry for each question.  Use with care.

Any teacher with Moodle capability `moodle/question:editall` for a given context can find all questions with any `[[todo]]` blocks from STACK's "adminui" page. 

    [...]/question/type/stack/adminui/index.php

## Bulk test materials on other sites

It is possible to [bulk test materials on other sites](Testing_questions_on_other_sites.md).
