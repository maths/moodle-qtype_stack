# Other answer tests

There are a small number of answer tests for specific situations.

### Sets ###

This test deals with equality of sets.  The algebraic equivalence functions give very minimal feedback.  This test is designed to give much more detailed feedback on what is and _is not_ included in the student's answer.  Hence, this essentially tells the student what is missing.  This is kind of feedback is tedious to generate without this test.

The test simplifies both sets, and does a comparison based on the simplified versions.  The comparison relies on `ev(..., simp, nouns)` to undertake the simplification.  If you need stronger simplification (e.g. trig) then you will need to add this to the arguments of the function first.

### Equiv and EquivFirstLast ###

These answer tests are used with [equivalence reasoning](../../CAS/Equivalence_reasoning.md).  See the separate documentation.

### PropLogic ###

An answer test designed to deal with [propositional logic](../../Topics/Propositional_Logic.md).  See the separate documentation.


