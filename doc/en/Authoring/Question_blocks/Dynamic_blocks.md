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

An example question is available by importing `Doc-Examples\Reveal_block_example.xml`.

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

## JSXGraph block ##

STACK supports inclusion of dynamic graphs using JSXGraph: [http://jsxgraph.uni-bayreuth.de/wiki/](http://jsxgraph.uni-bayreuth.de/wiki/). The key feature of this block is the ability to bind elements of the graph to inputs of the question. See the specific documentation on including [JSXGraph](../../Specialist_tools/JSXGraph/index.md) elements.

    [[jsxgraph]]
      // boundingbox:[left, top, right, bottom]
      var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 2, 3, -2], axis: true, showCopyright: false});
      var f = board.jc.snippet('sin(1/x)', true, 'x', true);
      board.create('functiongraph', [f,-3,3]);
    [[/jsxgraph]]

## JSString block ##

A new feature in 4.4 is the `[[jsstring]]` which makes it simpler to produce JavaScript string values out of CASText content. This may be useful for example when generating labels in JSXGraph. The block takes its content and evaluates it as normal CASText and then escapes it as JavaScript string literal.

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
