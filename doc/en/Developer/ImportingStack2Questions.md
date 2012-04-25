# Importing STACK 2 questions

## Installing the importer

## Notes

Please not the following.

* The following question level options are now ignored by the importer
** Display (Reason: this should not be set at the question level anyay).
** Worked Solution on Demand (Reason: the quiz behaviours are the right place to deal with this.  Providing this option was always a hack in the first place...).
** Feedback shown (Reason: again, the quiz behaviours are the right place to deal with this.)
* From the old MetaData only the `name` is preserved.  All other MetaData is lost on import.
* STACK 2 exporter does not seem to export some of the interaction element options correctly, in particular the options which ask the student to verify and to show validation feedback.

