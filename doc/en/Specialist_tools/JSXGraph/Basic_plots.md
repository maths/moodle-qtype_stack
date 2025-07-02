# Basic JSXGraph plots

This example is based on the documentation for [curve](http://jsxgraph.uni-bayreuth.de/wiki/index.php/Curve) and the [even simpler function plotter](http://jsxgraph.uni-bayreuth.de/wiki/index.php/Even_simpler_function_plotter) example.

To include a basic dynamically-generated sketch into a STACK question, first define the expression of the graph to be plotted in the question variables.  For example

    a:rand(6)-3;
    fx:sin(x)+a;

Then include the following question text, which includes a simple `[[jsxgraph]]` [block](../../Authoring/Question_blocks/Dynamic_blocks.md).  In particular note the lack of `<script>` tags which you might expect to include.

    <p>Type in an algebraic expression which has the graph shown below.</p>
    [[jsxgraph]]
      /* boundingbox:[left, top, right, bottom] */
      var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-10, 5, 10, -5], axis: true, showCopyright: false});
      var f = board.jc.snippet('{#fx#}', true, 'x', true);
      board.create('functiongraph', [f,-10,10]);
    [[/jsxgraph]]
    <p>\(f(x)=\) [[input:ans1]] [[validation:ans1]]</p>

Note the code `board.jc.snippet('{#fx#}', true, 'x', true);` which turns a reasonable expression for a function into the JavaScript function.  You cannot just plot the `functiongraph` on its own.

To make a working question, you will then need to add in `fx` as the model answer to input `ans1`, a question note (e.g. `\({@fx@}\)`) and an appropriate potential response tree.