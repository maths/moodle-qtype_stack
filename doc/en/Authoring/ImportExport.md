# Import and Export of STACK questions

STACK 3 questions now use the "Moodle XML format" to import and export.

## Importing STACK 3 questions

Go to

    Question bank => Import

Choose "Moodle XML format" and the question bank category you wish to import your questions to.  Then select files to import. Some [sample questions](Sample_questions.md) are supplied with STACK in this format.

## Exporting STACK 3 questions

Note that we may only export a whole category of questions at one time.  To export a selection of questions, you need to move them into a separate category (at least until we improve the Moodle question bank further.)

## Importing STACK 2 questions

You will need to [install](../Installation/index.md) the `qformat_stack` importer before you can import STACK 2 questions.

Go to

    Question bank => Import

Choose "STACK2.0 format" and the question bank category you wish to import your questions to.  The importer enables STACK 2 questions which have been exported in STACK's "xml" format to be imported into STACK 3.  Both individual questions and lists of questions can be imported.

There have been a number of changes between STACK 2 and STACK 3.  These are detailed in the [development track](../Developer/Development_track.md).

### Notes

Please note the following.

* If you have used an input type which has not yet been implemented in STACK 3 the the importer will throw an exception.  E.g. Any of the MCQ or list input types are not yet available in STACK 3, and so questions containing these cannot be imported.
* The following question level options are now ignored by the importer
 * Display (Reason: this should not be set at the question level anyay).
 * Worked Solution on Demand (Reason: the quiz behaviours are the right place to deal with this.  Providing this option was always a hack in the first place...).
 * Feedback shown (Reason: again, the quiz behaviours are the right place to deal with this.)
* From the old MetaData only the `name` is preserved.  All other MetaData is lost on import.
* STACK 2 exporter does not seem to export some of the interaction element options correctly, in particular
 * the options which ask the student to verify and to show validation feedback.
 * question level penalty option.
* Questions with a single potential response tree import with the PRT feedback in the specific feedback slot, not in the question text.  We envisage this will enable single part questions to respect a wider variety of Moodle question behaviours.
* When importing question tests, the new testing mechanism in STACK 3 enables the teacher to specifiy a score and penalty, not just an answernote.  Since we have to set defaults on import, most question tests now fail and this information will need to be added by hand.  A good opportunity to confirm questions have imported correctly.....

