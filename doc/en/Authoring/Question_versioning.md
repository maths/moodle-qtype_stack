# Question Versioning

## Introduction ##

Whilst it is straightforward for teachers to take existing questions and deploy them
without making any changes, in many cases they will want to adapt the questions or author their own.
This may be happening before, during or after a deployment to a quiz.

STACK 2.2 introduced support for a simple versioning system to give authors greater
confidence in developing, adapting and authoring questions at any point of the life cycle.

Versioning is intrinsically linked to question [deployment](Deploying.md).

### Motivation ###

STACK questions should be able to continuously evolve over time, so it is important for authors
to be confident in adapting a question without fear. Question tests can protect against functional
regression but authors also need to be able to creatively develop without tying themselves in knots
over different versions.  Crucially we should ensure:

* Editing a question does not cause the loss of previous versions.
* Questions are deployed consistently, using the latest version.

### Terms ###

An author may adapt a _question_ over time to create a series of _versions_ (or revisions).

Each _version_ can be used to create a number of distinct, concrete _instances_, depending
on the range of random variables that version defines.  A set of instances produced from a
particular version is called a _deployment_.

## The Lineage Model ##

A question exists through time as a series of saved versions.  Each one can, in principle,
be arbitrarily different from its predecessor but, in practice, the revisions will be relatively minor.
Any version of this lineage can be revisited (if the development occurred locally) and saved to effectively
restore it as the latest version.

Each time the question is saved _- and it differs from the previous one -_ then a new version is created.

This developmental line can be 'forked' with 'save as new' where the intent is to create a different question
rather than improve on the existing one.  To reduce ambiguity, the question name will have an additional
timestamp appended to it to indicate the point the fork was made.

## Undeploy vs. Delete ##

_Undeploying_ a question instance is not quite the same as deleting it.
If an instance is undeployed then new students will no longer be issued with it.
However, students who have _already_ started working with it will be allowed to continue to with it.
Deleting a question from the question list actually includes removal of all versions of that question.
DIAGRAM?

## Moodle Interaction ##

Moodle, via the Opaque protocol, stores and 'replays' student inputs to reproduce outcomes.
If the question that Moodle links to can be modified then student responses to an earlier (implicit)
version might be applied to a later one!  This threatens the integrity of the processing.
Versioning works with deployment (and Moodle's own logic) to fix a specific question version for an attempt.

### Continuous editing with confidence  ###

Versioning gives authors an unlimited 'undo' capability, allowing them to view or revert to any
historical version of a question they have worked on.  This enables them to experiment with questions
without cluttering up the question list with confusing, unnecessary duplicates.

## version numbers and question ids ##

Prior to version 2.2 questions were uniquely identified by their id numbers.  With versioning, those
are now question version ids, i.e. question versions are fully-fledged questions with fixed content.
These version numbers increment at the STACK system level rather than at the question level so saving
question version _i_ may not result in version _i+1_ if other questions have recently been changed.
