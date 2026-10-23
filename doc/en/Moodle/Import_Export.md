# Import and Export of STACK questions

STACK questions use the "Moodle XML format" to import and export.

## Importing STACK questions

Go to

    Question bank => Import

Choose "Moodle XML format" and the question bank category you wish to import your questions to.  Then select files to import. 

STACK also comes with a large [question bank](Question_bank.md).

## The STACK version stamp

Every STACK question stores the version of the STACK plug-in last used to edit it, in the `<stackversion>` field of the Moodle XML.

    <stackversion><text>2026080600</text></stackversion>

This is the plug-in's version number, not the release name: `2026080600` is STACK 4.13.1.  The [STACK version map](../Installation/STACK_versions.md) gives the number for each release.

* Saving a question in Moodle's edit form stamps it with the version of STACK on the site.
* Importing a question keeps whatever the XML file says.  If the file has no `<stackversion>`, the question is stored without one.

Every question must have a version number.  A question without one, or with something other than a number such as `2026080600`, has an upgrade error: the question dashboard reports it, and the bulk tester and the API's test route do not run the question's tests.  To fix it, save the question in the editing form.

If you write or edit Moodle XML outside Moodle, set `<stackversion>` to the version of STACK on the site the question is written for.  Do not copy the value from another question.

The stamp is a record, and does not change how the question runs: STACK always uses the code installed on the site.  The question dashboard shows the stamp beside the site's current version, and the [bulk tester](../STACK_question_admin/Bulk_testing.md) uses it to decide which upgrade checks apply to older questions.

## Writing Moodle XML outside Moodle

Most elements of a STACK question's XML are optional.  On import, an omitted element takes a default value.  Some defaults are fixed in the code, for example a penalty of `0.1`.  Others come from the site's STACK settings, for example the input box size and whether an input must be validated before it is marked.  A site administrator can change these, so the same file can behave differently on two sites.  Write out every element the question depends on.  A question exported from Moodle lists every element.

The question's name, and the name of every input, PRT and PRT node, must be present.  Wrap any HTML in `<![CDATA[ ... ]]>`.  The file `agents/stack_essentials.md` in the STACK source gives a minimal complete question and the rules for writing one.

## Exporting STACK questions

There are two ways to export STACK questions.

1. The normal Moodle procedure is to export whole category of questions at one time through the Moodle question bank.  To export a selection of questions, you need to move them into a separate category.  This can be any mix of STACK and other Moodle questions.  You must choose "Moodle XML format" as the file format.
2. To export a single STACK question as "Moodle XML format".
   1. Preview the question.
   2. Follow the link to "Question tests & deployed variants".
   3. Export this question.

   This export mechanism is only available to STACK questions and no other question types in Moodle.

## Migrating STACK 2/3 questions to STACK 4

STACK 4.0 has one important change in the question authoring.  [CASText](../Authoring/CASText.md) now uses `{@...@}` in include mathematics in the text.  The change from `@...@` to `{@...@}` gives us matching parentheses to parse, which is much better.  The `{..}` will not break LaTeX.

You will need to update all your existing questions which include CAS calculations. This includes all fields, e.g. in the feedback as well.  To help with this process we have an automatic conversion script.  As an admin user navigate to

    Site administration ->
    Plugins ->
    Question Types ->
    STACK

Then choose the link "The fix maths delimiters script".

To Import STACK 2 questions you will need to [install](../Installation/index.md) the `qformat_stack` importer before you can import STACK 2 questions.  When you import choose "STACK2.0 format" rather than Moodle XML.
