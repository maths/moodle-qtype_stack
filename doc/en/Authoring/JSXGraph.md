# JSXGraph

STACK supports inclusion of dynamic graphs using JSXGraph: [http://jsxgraph.uni-bayreuth.de/wiki/](http://jsxgraph.uni-bayreuth.de/wiki/).

Note, we strongly recommend you do not use an HTML aware editor when using JSXGraph questions.  Instead turn off the editor within Moodle and edit the raw HTML.

## Include basic plots.

This example is based on the documentation for [curve](http://jsxgraph.uni-bayreuth.de/wiki/index.php/Curve) and the [even simpler function plotter](http://jsxgraph.uni-bayreuth.de/wiki/index.php/Even_simpler_function_plotter) example.

To include a basic dynamically generated sketch into a STACK question, first define the expression of the graph to be plotted in the question variables.  For example

    a:rand(6)-3
    fx:sin(x)+a

Then include the following question text, which includes a simple `[[jsxgraph]]` block.  In particular note the lack of `<script>` tags which you might expect to include.

    <p>Type in an algebraic expression which has the graph shown below.</p>
    [[jsxgraph]]
      // boundingbox:[left, top, right, bottom]
      var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-10, 5, 10, -5], axis: true});
      var f = board.jc.snippet('{#fx#}', true, 'x', true);
      board.create('functiongraph', [f,-10,10]);
    [[/jsxgraph]]
    <p>\(f(x)=\) [[input:ans1]] [[validation:ans1]]</p>

Note the code `board.jc.snippet('{#fx#}', true, 'x', true);` which turns a reasonable expression for a function into the Javascript function.  You cannot just plot the `functiongraph` on its own.

To make a working question, you will then need to add in `fx` as the model answer to input `ans1`, a question note (e.g. `\({@fx@}\)`) and an appropriate potential response tree.

## Interactive elements

In this example define the question variables as

    fx:int(expand((x-1)*(x+1)*(x-2)),x)

This question contains an interactive sliding element.

    <p>A graph, together with the tangent line and its slope, are shown below.  Find an algebraic expression for the graph shown below.</p>
    [[jsxgraph]]
      // boundingbox:[left, top, right, bottom]
      var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-5, 10, 5, -10], axis: true});
      var f = board.jc.snippet('{#fx#}', true, 'x', true);
      curve = board.create('functiongraph', [f,-10,10], {strokeWidth:2});
      dcurve = board.create('functiongraph', [JXG.Math.Numerics.D(f),-10,10], {strokeColor:'#ff0000', strokeWidth:1, dash:2});
      var p = board.create('glider',[1,0,curve], {name:'Drag me'});
      board.create('tangent',[p], {name:'Drag me'});
      var q = board.create('point', [function(){return p.X();}, function(){return JXG.Math.Numerics.D(f)(p.X());} ], {withLabel:false});
      board.unsuspendUpdate();
    [[/jsxgraph]]
    <p>\(f(x)=\) [[input:ans1]] [[validation:ans1]]</p>

In this example the student can interact with a dynamic diagram to help them understand what is going on.


