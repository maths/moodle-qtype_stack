# Testing, using and maintaining questions

This section assumes you have working questions, and it provides information on testing, using and maintaining questions.

* [Creating question tests](Testing.md),
* [Deploying variants](Deploying.md),
* [Reporting](Reporting.md),
* [Ensuring questions work in the future](Future_proof.md).

This section of the documentation provides information on testing questions and maintaining question banks for the long term.  Access to functions related to testing STACK questions and maintaining question banks for the long term is through the "adminui" page

    [...]/question/type/stack/adminui/index.php

(or available from the qtype_stack plugin setting page).  To make use of these tools (in Moodle) users require the capability `qtype/stack:usediagnostictools` via Moodle's capability system.  We strongly recommend anyone who regularly writes STACK questions across more than one Moodle course be given this capability.  It enables the following:

* Bulk testing of questions, and efficient follow-up via direct links to the "STACK question dashboard" for questions of interest
* Identifying STACK questions using particular blocks, e.g. the "todo" block, or includes.
* Bulk change of the default settings.
* Direct connection to Maxima (with normal teacher privileges in place) through a "Chat" script
* Ability to view unit test results for STACK answer tests online, which acts as comprehensive documentation for the intended behaviour, with commentary.

If your institution restricts site admin status, then this capability will allow a subset of users to access these functions. If it is not possible to get this capability, then Moodle site administrators will need to run the tests themselves and give you the results.

When you upgrade, or before you upgrade, please check the [release notes](../Developer/Development_history.md) carefully.

The foundation of long-term maintenance is testing.  ___We strongly recommend all questions have [question tests](../STACK_question_admin/Testing.md).___  As a minimum we recommend the following test cases.

1. The answer a teacher recommends as the correct answer.
2. Ensure not every answer is assessed as correct.
3. Ideally, each separate feedback intended for students would be covered by a test case.

Encourage question authors to [future proof](../STACK_question_admin/Future_proof.md) their materials.

We have separate advice on [fixing broken questions](Fixing_broken_questions.md) in a live quiz, or on upgrade.

## Bulk testing STACK questions on your site

You can bulk test all question tests on all variants of all question by using the bulk-test script.  This is available from the question setting page or from the "adminui" page

    [...]/question/type/stack/adminui/index.php

STACK questions store the version of the STACK plug-in _last used_ to edit the question.  The bulk tester runs all question tests, and also checks for changes with the current STACK plug-in version.

It is possible to [bulk test materials on other sites](Testing_questions_on_other_sites.md).  (Site admins will have the option to bulk test all materials, and there is also a command line bulk test option.)

## Identifying STACK questions using particular blocks

It is possible to identify questions for dependencies, such as use of JSXGraph, inclusion of external maxima code, or "todo" blocks.

The dependency checker is available from the question setting page or from the "adminui" page

    [...]/question/type/stack/adminui/index.php

See also the notes on [local usageAdvanced_reporting.mdmd) of STACK questions on your server.

## Bulk change of the default settings

You may need to [upgrade question defaults](UpgradeDefaults.md) over a range of questions.

## Import and replace questions

The STACK community developed an [import question as new version](https://github.com/maths/moodle-qbank_importasversion) plugin for Moodle.  This plugin allows you to import a question from a Moodle XML file as a new version of an existing question.  This is useful when a question is fixed/updated on an external site.
