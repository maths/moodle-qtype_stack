# Reviewing

Computer aided assessment of mathematics works in the following phases.

1. [Authoring](../Authoring/)
2. [Testing](Testing.md)
3. [Deploying](Deploying.md)
4. [Reviewing](Reviewing.md)

**NOTE** most of the review features are not yet implemented.

Reviewing students answers closes the learning cycle for teachers by allowing them to understand what students are doing.

To review work we need to use two important parts of the question.
Please read the following two entries before continuing with this article.

* The [question note](Question_note.md).
* The [answer note](Potential_response_trees.md#Answer_note).

# User's activity profile #

_NOT YET IMPLEMENTED_
This report tells us what someone has been doing.

1. Choose a user (link to Moodle database to get names?)
2. Choose a range of time (default all up to now)
3. Optional: choose a specific item (lists all versions)

Then return a simple table with columns for

* Time (order by)
* Item id
* Item name
* Links to try question version user got at that point.
* Question note
* All inputsinputs
* All outcomes, i.e. valid, score for each input, answer notes etc.

When a user changes their Item id, or question note

* format table with e.g. double line
* Time on task etc.

# Individual item analysis #

* Analysis of different versions.  Differences in frequencies of answer notes?
* Analysis of item as a whole, e.g. combine all the answer notes from each deployed version
  * Tree view of student's attempts at this item.  Each "branch" to indicate
* List of invalid answers.
* Stats on
  * Answers ordered by, marks, popularity (by version),

# Comparison of items #

# Developer #

Ideas for creating better representations of the items:

* Create tree graphics in PHP:  <http://www.phpclasses.org/browse/package/4254.html>
* Attractive bar, pie and line charts in PHP. <http://pchart.sourceforge.net> (GPL)
* Javascript:  <http://thejit.org>  (Note BSD).
