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

Notice that the reveal block has the condition `value="5"`, _not_ `value="X"`.  This is because the reveal block executes client-side, using javascript, and the values of the options are simply numbered, and mapped back to Maxima values server-side.

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
