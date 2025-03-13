# Other answer tests

There are a small number of answer tests for specific situations.

### Sets ###

This test deals with equality of sets.  The algebraic equivalence functions give very minimal feedback.  This test is designed to give much more detailed feedback on what is and _is not_ included in the student's answer.  Hence, this essentially tells the student what is missing.  This is kind of feedback is tedious to generate without this test.

The test simplifies both sets, and does a comparison based on the simplified versions.  The comparison relies on `ev(..., simp, nouns)` to undertake the simplification.  If you need stronger simplification (e.g. trig) then you will need to add this to the arguments of the function first.

### Equiv and EquivFirstLast ###

These answer tests are used with [equivalence reasoning](../../Specialist_tools/Equivalence_reasoning/index.md).  See the separate documentation.

### PropLogic ###

An answer test designed to deal with [propositional logic](../../Topics/Propositional_Logic.md).  See the separate documentation.

### Validator ###

STACK allows question authors to create [bespoke validator](../../CAS/Validator.md) functions to validate students' input.  These functions must take a single argument (the student's answer) and return a string.  The empty string `""` indicates the string is "valid", and a non-empty string indicates the string is invalid.  The non-empty string is then an error message.

Validator functions can be re-used in the validator answer test.

1. The `SAns` field is passed as an argument to the answer test named in the test _options_.
2. The `TAns` field must be non-empty, but is ignored by the test.
3. The Test options must be the _name_ of a validator function.  Do not apply the function.
4. If the result is the empty string, the test returns `true`, and you proceed down the true PRT branch.
5. If the result is a non-empty string, the test returns `false`, and you proceed down the false PRT branch.  The non-quiet version of the answer test appends the validator output to the feedback message.

For example, if you want to test if a student has used a general, undefined, function (such as \(f(x)\)) rather than a known mathematical function (such as \(\sin(x)\)) then you can use the supported validator `validate_nofunctions`.