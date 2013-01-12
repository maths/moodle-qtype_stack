# Development track

This page describes the major tasks we still need to complete in order to be
able to release the next version of STACK. That is STACK 3.1. Plans looking
further into the future are described on [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## STACK custom reports ##

Basic reports now work.

* Add titles and explanations to the page, and document with examples.
* **DONE** Split up the answer notes to report back for each PRT separately.
* Really ensure "attempts" list those with meaningful histories.  I.e. if possible filter out navigation to and from the page etc.
* Introduce "validation notes".
* Add better maxima support functions for off line analysis.
 * A fully maxima-based representation of the PRT?

## Assorted minor improvements ##

* Improve the way questions are deployed.
 1. Auto deploy.  E.g. if the first variable in the question variables is a single a:rand(n), then loop a=0..(n-1).
 1. Remove many versions at once.
* Facility to import test-cases in-bulk as CSV (or something). Likewise export.
* Add back remaining input types
 1. Dragmath (actually, probably use javascript from NUMBAS instead here).
 2. Sliders.
 3. Dropdown/MCQ input type.
* Refactor answer tests.
 1. They should be like inputs. We should return an answer test object, not a controller object.
 2. at->get_at_mark() really ought to be at->matches(), since that is how it is used.
* When validating the editing form, also evaluate the Maxima code in the PRTs, using the teacher's model answers.
* A way to set defaults for many of the options on the question edit form. There are two ways we could do it. We could make it a system-wide setting, controlled by the admin, just like admins can set defaults for all the quiz settings. Alternatively, we could use user_preferences, so the next time you create a STACK question, it uses the same settings as the previous STACK qusetion you created.
* Introduce a variable so the maxima code "knows the attempt number". [Note to self: check how this changes reporting]
* You cannot use one PRT node to guard the evaluation of another, for example Node 1 check x = 0, and only if that is false, Node 2 do 1 / x. We need to change how PRTs do CAS evaluation.
* Consolidation of mailing lists, forum, wiki etc.
 1. Update forum.
 2. Announcements on Moodle mailing lists.
 3. Re-install demonstration servers.
* Review the list of forbidden keywords.

