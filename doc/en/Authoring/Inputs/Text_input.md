# Text-based inputs in STACK

Sometimes it is necessary for students to type in text, rather than an interpreted mathematical expression.

Some of the dynamic question blocks, e.g. the [Parsons block](../../Specialist_tools/Drag_and_drop/index.md), create a JSON object.  The only way to store state in STACK is via an input, and so a text-based input is used to store this state.

#### String input ####

This is a normal input into which students may type whatever they choose.  It is always converted into a Maxima string internally.
Notes

1.  There is no way whatsoever to parse the student's string into a Maxima expression.  If you accept a string, then it will always remain a string! You can't later check for algebraic equivalence. The only tests available will be simple string matches, etc.
2.  An empty answer will be blank unless you use the `allowempty` option in which case the answer will be interpreted as an empty string, i.e. `""` rather than `EMPTYANSWER` as would be the case with other inputs.
3.  STACK does some sanitation on students' input within strings to stop students typing in HTML code.  For example, you may find that a string <code>"a<b"</code> actually ends up in Maxima with the less-than sign inside the string changed into an html entity <code>&amp;lt;</code>, so your string inside Maxima becomes <code>"a&amp;lt;b"</code>.  In cases where string matches unexpectedly fail, look at the testing page to see what is actually being compared within the PRT and re-build the teacher's answer to match.

#### Notes input ####

This input is a text area into which students may type whatever they choose.  It can be used to gather their notes or "working".  However, this input is always considered as "invalid", so that any potential response tree which relies on this input will never get evaluated!

This input type can be used for

1. surveys;
2. answers which are not automatically marked, contributing to [semi-automatic marking](../../Moodle/Semi-automatic_Marking.md).

The notes input has a special extra option `manualgraded`, and the default option value is `manualgraded:false`.  If you specify `manualgraded:true` then the _whole STACK question_ will require manual grading!

Note, for consistency with other inputs the teacher must still supply an answer, e.g. the empty string `""`.  This answer is not used.

### Parsons input ###

A special [Parsons input](../../Specialist_tools/Drag_and_drop/index.md), is available to store the JSON object generated by the Parsons block.