# Importing STACK 2 questions

The importer enables STACK 2 questions which have been exported in STACK's "xml" format to be imported into STACK 3.  Both individual questions and lists of questions can be imported.

There have been a number of changes between STACK 2 and STACK 3.  These are detailed in the [development track](Development_track.md).

## Notes

Please note the following.

* If you have used an input type which has not yet been implemented in STACK 3 the the importer will throw an exception.  E.g. Matrix input types are not yet available in STACK 3, and so questions containing these cannot be imported.
* The following question level options are now ignored by the importer
 * Display (Reason: this should not be set at the question level anyay).
 * Worked Solution on Demand (Reason: the quiz behaviours are the right place to deal with this.  Providing this option was always a hack in the first place...).
 * Feedback shown (Reason: again, the quiz behaviours are the right place to deal with this.)
* From the old MetaData only the `name` is preserved.  All other MetaData is lost on import.
* STACK 2 exporter does not seem to export some of the interaction element options correctly, in particular the options which ask the student to verify and to show validation feedback.
* Questions with a single potential response tree import with the PRT feedback in the specific feedback slot, not in the question text.  We envisage this will enable single part questions to respect a wider variety of Moodle question behaviours.
* When importing question tests, the new testing mechanism in STACK 3 enables the teacher to specifiy a score and penalty, not just an answernote.  Since we have to set defaults on import, most question tests now fail and this information will need to be added by hand.  A good opportunity to confirm questions have imported correctly.....
