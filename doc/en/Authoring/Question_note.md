# Question note

The question note is [CASText](CASText.md).  The question note is used to decide if randomly generated variants are the same or different.

_Two question variants are equal if and only if the question notes are equal._

In particular, when we generate statistics about students' attempts we group attempts according to the equality of their question notes. Two variants are not necessarily different if their [question variables](Variables.md#Question_variables)
are different, and hence a note is useful.  The teacher needs to choose what identifies each unique variant - this cannot be automated.

The teacher can also leave useful information about the answer in the question note.
For example they might use a note such as

    \[ \frac{d}{d{@v@}}{@p@} = {@diff(p,v)@} \]

This is very helpful, particularly when students ask about the variant they were given.  The teacher only need look at the question note to get both the question, and answer.

The question note is used when [deploying](Deploying.md) question variants.
