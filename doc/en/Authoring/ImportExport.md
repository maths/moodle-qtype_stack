# Import and Export of STACK questions

STACK questions use the "Moodle XML format" to import and export.

## Importing STACK questions

Go to

    Question bank => Import

Choose "Moodle XML format" and the question bank category you wish to import your questions to.  Then select files to import. Some [sample questions](Sample_questions.md) are supplied with STACK in this format.

## Exporting STACK questions

There are two ways to export STACK questions.

1. The normal Moodle procedure is to export whole category of questions at one time through the Moodle question bank.  To export a selection of questions, you need to move them into a separate category.  This can be any mix of STACK and other Moodle questions.  You must choose "Moodle XML format" as the file format.
2. To export a single STACK question as "Moodle XML format".
   1. Preview the question.  
   2. Follow the link to "Question tests & deployed variants".
   3. Export this question.

   This export mechanism is only available to STACK questions and no other question types in Moodle.

## Migrating STACK 3 questions to STACK 4

STACK 4.0 has one important change in the question authoring.  [CASText](../Authoring/CASText.md) now uses `{@...@}` in include mathematics in the text.  The change from `@...@` to `{@...@}` gives us matching parentheses to parse, which is much better.  The `{..}` will not break LaTeX.

You will need to update all your existing questions which include CAS calculations. This includes all fields, e.g. in the feedback as well.  To help with this process we have an automatic conversion script.  As an admin user navigate to 

    Site administration -> 
    Plugins ->
    Question Types ->
    STACK

Then choose the link "The fix maths delimiters script". 

## Importing STACK 2 questions

You will need to [install](../Installation/index.md) the `qformat_stack` importer before you can import STACK 2 questions.

Go to

    Question bank => Import

Choose "STACK2.0 format" and the question bank category you wish to import your questions to.  The importer enables STACK 2 questions which have been exported in STACK's "xml" format to be imported into STACK 3.  Both individual questions and lists of questions can be imported.

There have been a number of changes between STACK 2 and STACK 3.  These are detailed in the [development track](../Developer/Development_track.md).

* The following question level options are now ignored by the importer
  * Display (Reason: this should not be set at the question level anyway).
  * Worked Solution on Demand (Reason: the quiz behaviours are the right place to deal with this.  Providing this option was always a hack in the first place...).
  * Feedback shown (Reason: again, the quiz behaviours are the right place to deal with this.)
* From the old MetaData only the `name` is preserved.  All other MetaData is lost on import.
* STACK 2 exporter does not seem to export some of the interaction element options correctly, in particular
  * the options which ask the student to verify and to show validation feedback.
  * question level penalty option.
* Questions with a single potential response tree import with the PRT feedback in the specific feedback slot, not in the question text.  We envisage this will enable single part questions to respect a wider variety of Moodle question behaviours.
* When importing question tests, the new testing mechanism in STACK 3 enables the teacher to specify a score and penalty, not just an answer note.  Since we have to set defaults on import, most question tests now fail and this information will need to be added by hand.  A good opportunity to confirm questions have imported correctly.....
