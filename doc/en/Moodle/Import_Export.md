# Import and Export of STACK questions

STACK questions use the "Moodle XML format" to import and export.

## Importing STACK questions

Go to

    Question bank => Import

Choose "Moodle XML format" and the question bank category you wish to import your questions to.  Then select files to import. Some [sample questions](../STACK_question_admin/Sample_questions.md) are supplied with STACK in this format.

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
