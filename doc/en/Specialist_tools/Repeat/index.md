# Repeat questions

This page documents questions where the user can control the number of separate inputs.  In particular, by pressing a button a section of the question text is repeated and this section can contain inputs.

When all these inputs are valid, the contents are collected together and sent to Maxima as a list.

A repeat question has three parts.

1. One or more [`[[repeat]]` blocks](../../Authoring/Question_blocks/Dynamic_blocks.md#interactive-repeat) containing the inputs to be repeated, each with their validation tag.
2. A `[[repeatbutton]]` block, which adds another copy of these repeat blocks when pressed.
3. A [repeat input](../../Authoring/Inputs/Compound_input.md#repeat-inputs), named by the `save_state` parameter of the button, which collects the student's answers as lists `repeatedans1`, `repeatedans2`, ... for use in the PRTs.

For example

```
[[repeat id="1"]]
\(x = \) [[input:ans1]] [[validation:ans1]]
[[/repeat]]
[[repeatbutton title="Add another root" repeat_ids="1" save_state="state1" /]]
[[input:state1]] [[validation:state1]]
```

Each copy of an input is validated exactly as the input on its own would be.  Copies the student leaves empty are ignored.

Examples can be found in the sample questions, in `samplequestions/stacklibrary/Doc-Examples/Authoring-Docs/Question-blocks/Repeat_block_*.xml`.
