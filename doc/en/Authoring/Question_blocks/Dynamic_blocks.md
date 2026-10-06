# Dynamic blocks

Dynamic blocks deal with dynamic content such as Javascript and JSXGraphs.  Many of the dynamic blocks are designed for use with specialist tools.

## Reveal block ##

This block allows sections of text to be shown or hidden based on the value of an input.

```
[[reveal input="ans1" value="true"]]
Text shown when the value of input `ans1` is `true`.
[[/reveal]]
```

The block will only do singular direct string match, and so use of this block is most likely to be useful when combined with true/false or other multiple choice input types.  An example question using this feature is provided in the sample questions.

There is currently no "else" clause available with this block.

An example question is available by importing `Doc-Examples\Authoring-Docs\Question-blocks\Reveal_block_example.xml`.

***Note** the contents of all reveal blocks are within the page.  Some may be visible and some hidden, controlled by JavaScript.  Therefore, a student can inspect the page and see all blocks.  While this doesn't matter too much in formative settings, be aware of the possibility of revealing useful information in online exam settings.


### Interaction with MCQ input types

The reveal block can be used in conjunction with [MCQ](../../Authoring/Inputs/Multiple_choice_input.md) input types to provide an input, e.g. algebraic, for "other".  Here is a very minimal example.  Put the following in the question variables.

    ta1:[[a,false],[b,false],[c,false],[d,false],[X,true,"Other"]];
    ta2:x^2;

Use the following question text.

    [[input:ans1]] [[validation:ans1]]
    [[reveal input="ans1" value="5"]] [[input:ans2]] [[validation:ans2]] [[/reveal]]

1. Create input `ans1` as a radio input, with teacher's answer `ta1`.  Don't require or show validation.
2. Create input `ans2` as an algebraic input, with teacher's answer `ta2`.  Use the extra option `allowempty`.
3. In the PRT the first node should check `ans1=X` and, if so check that `ans2=ta2`.

**Notice that the reveal block has the condition `value="5"`, _not_ `value="X"`.**  This is because the reveal block executes client-side, using javascript, and the values of the options are simply numbered, and mapped back to Maxima values server-side.

## Hint block ##

This block allows sections of text to be shown or hidden with a press of an additional button.

```
[[hint title="button text"]]
Text shown when the button is pressed.
[[/hint]]
```

Notes

1. hint blocks can be nested.
2. the content of the hint is styled within a `stack-hint-content` div tag.

## Adapt block ##

The Adapt Block allows you to show or hide sections of text either by clicking a button (created with the `adaptbutton` block) or automatically (controlled by the `adaptauto` block). This functionality works anywhere you can use CASText, including in feedback nodes.

Each Adapt Block requires a unique ID. You can reference this ID in an `adaptbutton` or `adaptauto` block using the attributes `show_ids` and `hide_ids`.

***Note** the contents of all adapt blocks are within the page.  Some may be visible and some hidden, controlled by JavaScript.  Therefore, a student can inspect the page and see all blocks.  While this doesn't matter too much in formative settings, be aware of the possibility of revealing useful information in online exam settings.

An example question is available by importing `Doc-Examples\Authoring-Docs\Question-blocks\Adapt_button_block.xml`.

### Adaptbutton

With the `adaptbutton` block you can control the visibility of `adapt` blocks with a press of a button. The button needs a `title` attribute. Note: Using Language blocks within titles is not yet supported.
When a user clicks the button, the system shows and hides `adapt` blocks corresponding to the `show_ids` and `hide_ids` attributes and saves this action in an input you can set with the `save_state` attribute.
You can control multiple adapt blocks by separating IDs with semicolons, e.g. `hide_ids='1;2;3'`.

```
[[adapt id='1']]
This text will be shown until the adaptbutton has been clicked. When it is clicked, the value of the input 'ans1' is set to 'true'.
[[adaptbutton title='Click me' hide_ids='1' save_state='ans1' show_ids='3;4'/]]
[[/adapt]]
[[adapt id='2' hidden='true']]
This text is hidden if you did not press the adaptbutton.
[[/adapt]]
```

The Adaptbutton block has no contents within the block, so you may use the form `[[adaptbutton ... /]]` rather than `[[adaptbutton ... ]][/adaptbutton]]`.

### Adaptauto

The `adaptauto` block automatically shows or hides `adapt` blocks when the `adaptauto` block is reached and the whole page finishes loading.

```
[[adapt id='1']]
The text will be displayed until adaptauto is loaded.
[[/adapt]]
[[adapt id='2' hidden='true']]
This text is hidden until adaptauto is loaded. Can be used as feedback.
[[/adapt]]
<!-- Should be placed in a true/false feedback node -->
[[adaptauto show_ids='2' hide_ids='1'/]]
```

Like the `adaptbutton` block, the `adaptauto` block can control multiple adapt blocks by separating IDs with semicolons, e.g. `hide_ids='1;2;3'`.

Like the `adaptbutton` block, the `adaptauto` block has no contents within the block.

The `adaptauto` block also accepts an optional `delay` parameter that specifies a time delay in milliseconds before showing or hiding the adapt blocks. The value must be a whole number (integer). This allows for timed presentation of content.

Example with delay:
```
[[adaptauto show_ids='2' hide_ids='1' delay='3000'/]]
```
This will show adapt block with ID '2' and hide adapt block with ID '1' after a 3 second delay.

An example question is available by importing `Doc-Examples\Authoring-Docs\Question-blocks\Adapt_delay_block.xml`.

## Interactive repeat ##

The "interactive repeat" block allows a question author to create a block of static content which the student can opt to repeat by pressing a corresponding button.  The contents is copied, client side, by javascript.  The basic purpose of the interactive repeat is to allow question authors to create questions without specifying the precise number of inputs.  Students can add another input/inputs as needed.

```
[[repeat id="1"]]
\(x = \) [[input:ans1]] [[validation:ans1]]
[[/repeat]]
[[repeatbutton title="Add another root" repeat_ids="1" save_state="state1" /]]
[[input:state1]] [[validation:state1]]
```

The student can repeat the contents of the block by using the corresponding "repeat button".  When the question is first shown the contents of each repeat block is shown once.

The `[[repeatbutton]]` block has the following parameters, all of which are required.

* `title` is the text on the button.
* `repeat_ids` lists the ids of the repeat blocks controlled by this button, separated by `;` (or spaces), e.g. `repeat_ids="1;3"`.  Each press of the button adds one copy of each of these blocks.
* `save_state` is the name of an input of type [repeat](../Inputs/Compound_input.md#repeat-inputs).  This input holds what the student enters into all the copies, and is the input you use in the PRTs.  Include it in the question text as `[[input:state1]] [[validation:state1]]`.  The input itself is hidden from students, and its validation is only shown if there is a general problem.

Notes and restrictions.

* There must be exactly one repeat button for each repeat block id, and the button must be _outside_ the repeat block.
* Repeat blocks may contain inputs.  If so, the repeat block must also contain the corresponding validation tags.
* Currently only inputs which are a single text box can be repeated, e.g. algebraic, numerical, units and string inputs.  Dropdown, radio, checkbox, matrix and textarea inputs are not yet supported.
* Repeat blocks may _not_ contain any other interactive blocks, including nested repeat blocks, JSXGraph, adapt etc.  (This may change in future versions.)
* Repeat blocks may _not_ be used to add rows (`<tr>`) to a table (`<table>`) which starts outside of the repeat block.  This is due to limitations in the current javascript implementation.  You can put whole tables inside a repeat block, however.
* Repeat blocks may contain a special tag `<repeatindex>`.  This tag acts as a counter for the block.  Client-side JS replaces this tag with the numerical value of the counter (integer, starting at 1).  It can be used in text and in maths, e.g. `\(x_{<repeatindex>} = \)`; use braces in subscripts, so that e.g. `10` is typeset correctly.  This tag cannot be used inside CAS calculations (e.g. `{@...@}`), which are evaluated _before_ the page is served to the student.  The purpose of this tag is simple enumeration of input boxes, not seeding of complex CAS calculations.
* Students cannot remove copies once added.

### Validation and the student's answer

When a student interacts with an input inside a repeat block, this is validated exactly as would be the case for the input as normal, using all the options of that input.  The validation is shown next to that copy of the input.

* Rows (i.e. copies of the repeat blocks added by one press of the button) which the student leaves completely empty are ignored.  If all rows are empty, the question has not been answered.
* If only some of the inputs in a row are filled in, the row is invalid, unless the empty input has the `allowempty` option, in which case its value is `EMPTYANSWER` as usual.

When the student's answer is valid, it becomes a _list_ of expressions for each input in the repeat blocks.  For an input `ans1` this list is called `repeatedans1`, and this is what you refer to in the PRTs.  Do not use `ans1` itself in the PRTs.  See the [repeat input](../Inputs/Compound_input.md#repeat-inputs) for details, and for how to write the teacher's answer.

### Basic use case

STACK's sample question library contains questions similar to this typical example: Students are asked to list the roots of a polynomial. Knowing how many roots a given polynomial has is something that you might want to assess instead of giving it away with the number of provided input fields. The question text could then look as follows:
```
Find all roots of the polynomial \(P(x) = x^3 - x\). Add as many input fields as required.

[[repeat id="1"]]
\(x_{<repeatindex>} = \) [[input:ans1]] [[validation:ans1]]
[[/repeat]]
[[repeatbutton title="Add another root" repeat_ids="1" save_state="state1" /]]
[[input:state1]] [[validation:state1]]
```

With question variables

```
ta:[-1,0,1];
```

set up the inputs as follows.

* `ans1` is an algebraic input, configured as if there was only one such input field.  It needs a teacher's answer, which should be a single valid value, e.g. `ta[1]`.  This answer is not shown to students, since the input is not used directly.
* `state1` is a repeat input, with teacher's answer `repeat_encode([["ans1",ta]])`.

STACK will take care of copying the field multiple times, validating each copy and collecting the answers.  In the PRT, compare `setify(repeatedans1)` with `setify(ta)`.

More advanced examples could ask for several different inputs, e.g. a combination of an eigenvalue and an eigenvector or the coordinates of a critical point of a function.  If a repeat block contains inputs `ans1` and `ans2`, the PRTs can use `repeatedans1` and `repeatedans2`, which always have the same length: the n-th elements belong to the same row.

## JSXGraph block ##

STACK supports inclusion of dynamic graphs using JSXGraph: [http://jsxgraph.uni-bayreuth.de/wiki/](http://jsxgraph.uni-bayreuth.de/wiki/). The key feature of this block is the ability to bind elements of the graph to inputs of the question. See the specific documentation on including [JSXGraph](../../Specialist_tools/JSXGraph/index.md) elements.

    [[jsxgraph]]
      // boundingbox:[left, top, right, bottom]
      var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 2, 3, -2], axis: true, showCopyright: false});
      var f = board.jc.snippet('sin(1/x)', true, 'x', true);
      board.create('functiongraph', [f,-3,3]);
    [[/jsxgraph]]

## JSString block ##

The `[[jsstring]]` block makes it simpler to produce JavaScript string values out of CASText content. This may be useful for example when generating labels in JSXGraph. The block takes its content and evaluates it as normal CASText and then escapes it as JavaScript string literal.

```
var label = [[jsstring]]{@f(x)=sqrt(x)@}[[/jsstring]];
/* Would generate, without the need to manually escape things. */
var label = "\\({f\\left(x\\right)=\\sqrt{x}}\\)";
```

Note, this block is _not_ designed to output Maxima expressions in JS format. For example, this block will not convert `x^2` into `x**2`.

## GeoGebra block ##

STACK supports inclusion of dynamic graphics using GeoGebra: [https://geogebra.org](https://geogebra.org) both as static visuals and as a STACK input.  This block is documented fully on the [GeoGebra page](../../Specialist_tools/GeoGebra/index.md).

## Parsons block ##

[Drag and drop problems](../../Specialist_tools/Drag_and_drop/index.md) can be created using the [Parsons block](../../Specialist_tools/Drag_and_drop/Question_block.md).  For example this allows users (e.g. students) to assemble pre-written text into a correct order.  This block can be linked with an input to create a [Parsons problem](../../Specialist_tools/Drag_and_drop/Parsons.md) or as matching problems, such as [grid](../../Specialist_tools/Drag_and_drop/Grid.md) and [grouping](../../Specialist_tools/Drag_and_drop/Grouping.md).

## JavaScript block ##

This block creates a hidden [`[[iframe]]`-block](Iframe_blocks.md) with the [STACK-JS](../../Specialist_tools/STACK-JS/index.md) library already imported inside a `<script type="module">`-container. The block also supports the same input referencing attributes as the `[[jsxgraph]]`-block, you can also do input referencing through STACK-JS if that better suits your needs.

```
[[javascript input-ref-ans1="ans1ref"]]
let input = document.getElementById(ans1ref);
input.addEventListener("change", () => {
  if (input.value == 'foo') {
    stack_js.switch_content('[[quid id="messagebox"/]]', input.value + "bar");
    stack_js.toggle_visibility('[[quid id="messagebox"/]]', true);
  } else {
    stack_js.toggle_visibility('[[quid id="messagebox"/]]', false);
  }
});
[[/javascript]]
```
Do note that the use of `input-ref-...` attributes will lead to rewriting parts of the JavaScript code. Basically, the contents of the block are wrapped as a function that will be called after the input references have been fully registered. During that wrapping, all `import`-statements in that code will be lifted outside of that function, that lifting is unaware of JS-comments. If such rewriting causes trouble for your logic, you may choose to not use the `input-ref-...` feature and instead do any access to inputs through [STACK-JS](../../Specialist_tools/STACK-JS/index.md). No rewriting happens, if those attributes are not used.