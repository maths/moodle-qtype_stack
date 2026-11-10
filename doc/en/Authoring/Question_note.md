# Question note

The question note is [CASText](CASText.md).  The question note is used to decide if randomly generated variants are the same or different.

_Two question variants are equal if and only if the question notes are equal._

In particular, when we generate statistics about students' attempts we group attempts according to the equality of their question notes. Two variants are not necessarily different if their [question variables](Variables.md#Question_variables)
are different, and hence a note is useful.  The teacher needs to choose what identifies each unique variant - this cannot be automated.

The teacher can also leave useful information about the answer in the question note.
For example they might use a note such as

    \[ \frac{d}{d{@v@}}{@p@} = {@diff(p,v)@} \]

This is very helpful, particularly when students ask about the variant they were given.  The teacher only need look at the question note to get both the question, and answer.

The question note is used when [deploying](../STACK_question_admin/Deploying.md) question variants.

## Rules for the question note

1. If the question variables use a random function, the note must not be empty.  The question editing form will not save such a question with an empty note.  A question imported from XML is not checked, so check its note yourself.
2. Show every value which distinguishes one variant from another, so that different variants have different notes.  If two different variants share a note, STACK treats them as one variant.
3. When STACK deploys a number of variants for you it skips any seed whose note duplicates one already deployed.  Deploying a single seed, or importing deployed seeds, is not checked.  The variants tab on the question dashboard warns about duplicate notes.
4. Use text and mathematics only.  Images, plots and files are not displayed in most places the note appears, such as reports.
5. The note of each attempt is stored when the attempt starts.  Editing the note later does not change the note of existing attempts until they are regraded.

A question without randomisation does not need a note.  If an imported question omits the question note entirely, it is set to `{@ta1@}`.
