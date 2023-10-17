# Maintaining questions and question banks

This section of the documentation provides information on testing questions and maintaining question banks for the long term. Many functions related to maintaining STACK questions are available from the question setting page or from the "adminui" page

    [...]/question/type/stack/adminui/index.php

To make use of these tools (in Moodle) users require the capability `qtype/stack:usediagnostictools` via Moodle's capability system.  If your institution restricts site admin status, then this capability will allow a subset of users to access these functions. If that is also not possible, you might be able to convince your site administrators to run the tests themselves and give you the results.

When you upgrade, or before you upgrade, please check the [release notes](../Developer/Development_history.md) carefully.

The foundation of long-term maintenance is testing.  ___We strongly recommend all questions have [question tests](../Authoring/Testing.md).___  As a minimum we recommend the following test cases.

1. The answer a teacher recommends as the correct answer.
2. Ensure not every answer is assessed as correct.
3. Ideally, each separate feedback intended for students would be covered by a test case.

Encourage question authors to [future proof](../Authoring/Future_proof.md) their materials.

We have separate advice on [fixing broken questions](Fixing_broken_questions.md) in a live quiz, or on upgrade.

## Bulk testing STACK questions on your site

You can bulk test all question tests on all variants of all question by using the bulk-test script.  This is available from the question setting page or from the "adminui" page

    [...]/question/type/stack/adminui/index.php

STACK questions store the version of the STACK plug-in _last used_ to edit the question.  The bulk tester runs all question tests, and also checks for changes with the current STACK plug-in version.

It is possible to [bulk test materials on other sites](Running_question_tests_other_site.md).

## Identifying STACK questions using particular blocks

It is possible to identify questions for dependencies, such as use of JSXGraph or inclusion of external maxima code.

This is available from the question setting page or from the "adminui" page

    [...]/question/type/stack/adminui/index.php

See also the notes on [local usage](Local_Usage.md) of STACK questions on your server.

## Bulk change of the default settings

You may need to [upgrade question defaults](UpgradeDefaults.md) over a range of questions.


