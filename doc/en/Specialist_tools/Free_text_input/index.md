# Free text input

At it's core, STACK provides input boxes into which students can type algebraic expressions. The "equivalence reasoning" input type extends this, allowing multi-line input but that's also limited to algebraic expressions. What's missing is the facility to type a complete mathematical argument.

The free-text input allows students to type a complete mathematical argument.  By default:

1. Students type unicode text. Text-formats give complete transparency over what the student types and are future-proof.
2. Students use markdown for document structure and formatting.
3. Students can include [AsciiMath](https://asciimath.org/)/Space Math for mathematics.
4. Students optionally include LaTeX mathematics environments (using brackets `\(...\)`, leaving `$` for currency).

Sometimes, but not always, students may include [calculations](Calculation.md) between <code>{@...@}</code>.  Rather than reaching for an external calculator, the software will replace the requested calculation with the answer.

This can be used with [semi-automatic marking](../../Moodle/Semi-automatic_Marking.md).  Or, (algebraic) expressions can be extracted and used in standard STACK inputs for automatic marking.

The processing and display of free text inputs is rather flexible and is controlled by the [`[[ascii]]` block](../../Authoring/Question_blocks/ASCII.md).

The default is for students to use unicode text in markdown format, extended by AsciiMath.

* [Authoring free-text questions](Authoring.md)
* [Authoring free-text questions with extractors](Extractors.md)
* [Authoring free-text questions with calculation](Calculation.md)

An example question which uses regular expressions for assessment is 

    Doc-Examples/Specialist-Tools-Docs/Free-text-input/Free-text_with_regex.xml


### Help for students

Built-in help for students can be provided with either

    [[facts:free_text]]

or

    [[hint title="Input help"]][[commonstring key="free_text_fact"/]][[/hint]]

Help with how to embed calculations can be given by `[[commonstring key="free_text_calc"/]]` (Since teachers can't type `{@...@}` into the question text without it being treated as castext, `[[commonstring key="free_text_calc_min"/]]` is just `{@...@}`.)

There is also [documentation for students](../../Students/Free_text.md).

### CSS Style

The free-text input and `[[ascii]]` block can be contained in the `<div class="free-text-container">` so they appear side by side.
