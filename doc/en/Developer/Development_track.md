# Development track for STACK 3.2

This page describes the major tasks we still need to complete in order to be
able to release the next version: STACK 3.2. Plans looking
further into the future are described on [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## STACK custom reports

Basic reports now work.

* Add titles and explanations to the page, and document with examples.
* Really ensure "attempts" list those with meaningful histories.  I.e. if possible filter out navigation to and from the page etc.
* Add better maxima support functions for off-line analysis.
 * A fully maxima-based representation of the PRT?

## Expanding CAStext features

* Expand the CASText format to enable us to embed the _value_ of a variable in CASText, not just the displayed form.
* Conditionals in CASText adaptive blocks. (Aalto) See [question blocks](../Authoring/Question_blocks.md) for our plans.
* Hints.  Currently code is in the "hints" branch.
 * Make sure the syntax is updated to [hint:...] in line with the new format.
 * Provide a list of hints, and an interface through the docs.

## Assorted minor improvements

* Improve the way questions are deployed.
 1. Auto deploy.  E.g. if the first variable in the question variables is a single a:rand(n), then loop a=0..(n-1).
 2. Remove many versions at once.
* When validating the editing form, also evaluate the Maxima code in the PRTs, using the teacher's model answers.
* You cannot use one PRT node to guard the evaluation of another, for example Node 1 check x = 0, and only if that is false, Node 2 do 1 / x. We need to change how PRTs do CAS evaluation.
* Review the list of forbidden keywords.

## Other changes since STACK 3.1
