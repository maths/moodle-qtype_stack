# Development track for STACK 3.3

This page describes the major tasks we still need to complete in order to be
able to release the next version: STACK 3.3. Plans looking
further into the future are described on [Future plans](Future_plans.md). The
past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## STACK custom reports

Basic reports now work.

* *done* Add titles and explanations to the page, and document with examples.
* Really ensure "attempts" list those with meaningful histories.  I.e. if possible filter out navigation to and from the page etc.
* Add better maxima support functions for off-line analysis.
 * A fully maxima-based representation of the PRT?

## Expanding CAStext features

* Expand the CASText format to enable us to embed the _value_ of a variable in CASText, not just the displayed form.
* Conditionals in CASText adaptive blocks. (Aalto) See [question blocks](../Authoring/Question_blocks.md) for our plans.
* Hints.  Currently code is in the "hints" branch.
 * Make sure the syntax is updated to [hint:...] in line with the new format.
 * Provide a list of hints, and an interface through the docs.

## Expanding CAS features

* *done* Refactor the Maxima plot command to include "discrete" and "parametric plots"
* *done* Refactor the Maxima plot command to include options, e.g., xlabel, ylabel, legend, color, style, point_type.
* *done* Enable a function as an answer type, e.g. improve validation.
* *done* Refactor answer test unit testing to distinguish "test fail" from "zero".
* *done* Reject things like sin*(x) and sin^2(x) as invalid
* *done* Expand the numerical tests
* *done* Provide a new option on how parentheses are displayed for matrices
* Provide an extra syntax checking option to enable stars to be inserted between single characters, e.g. xy -> x*y.

## Assorted minor improvements

* Improve the way questions are deployed.
 1. Auto deploy.  E.g. if the first variable in the question variables is a single a:rand(n), then loop a=0..(n-1).
 2. Remove many versions at once.
* When validating the editing form, also evaluate the Maxima code in the PRTs, using the teacher's model answers.
* You cannot use one PRT node to guard the evaluation of another, for example Node 1 check x = 0, and only if that is false, Node 2 do 1 / x. We need to change how PRTs do CAS evaluation.
* *done* Review the list of forbidden keywords.
* *done* Add input parameter `allowwords` to enable the teacher to specify some permitted words of more than 2 symbols length.

