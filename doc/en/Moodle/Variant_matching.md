# Variant matching

This is a relatively advanced feature which links together random variables between questions in the Moodle quiz.

Some teachers regularly want to create "random groups" with questions based on each other in which students might be presented with a set of questions that are all based on some common question variables.  This can be achieved with multi-input questions or variant matching.

1. Multi-input questions keep all the information inside one STACK question.  This encapsulation is ideal for clarity of the relationship between parts/questions and sharing a single "STACK question", but questions quickly become complex to maintain.  When questions are only weakly linked this can also look strange.
2. Variant matching allows separate questions to synchronise random variables.  However, the question author is now responsible for making sure their questions actually match!

_There is currently no way to formally record that two or more questions enjoy variant matching within the questions._  In particular, there is no way two questions can share a fragment of question variables, question notes, or a pool of seeds!  This makes variant matching currently rather fragile for long-term support.

To create matched variants, create two or more STACK questions in the normal way.

1. Random variable generation must be identical for all questions.  The complete question variables need not be identical, but anything which affects randomisation must.  E.g. any randomisation can be in a block at the start of the question variables which can be copied between all questions.
2. The "Random Group" field in each question must be non-empty and identical for all questions in each group sharing randomisation.
3. Formally, question notes need not be identical for all questions.  We recommend they at least have one common element uniquely identifying all random variables which contribute to the random group!
4. [Deploy](../Authoring/Deploying.md) variants of one question in the normal way.  This should ensure question notes not being deployed more than once (see point 2 above). 
5. Copy the list of seeds from one deployed questions to deploy all the other questions in the random group.

If question notes are duplicated in a subsequent question this will not stop students using the question.
Preventing duplicate question notes normally ensures statistics are grouped correctly.  Statistics are grouped by _variant_, which a teacher will actually want since the underlying variant will be a single member of the random group.

### Developer notes

Internally a "variant" is matched to the integer seed.  `get_variants_selection_seed` is a Moodle-side feature syncing the seed we get from Moodle based on the group names we have in the questions.