# JSXGraph question block

JSXGraph blocks are included with the `[[jsxgraph ...]]` block.  This page provides reference documentation of all features of that block.

## Block options

You can control the size of the JSXGraph board with the `width` and `height` options.  E.g.

    [[jsxgraph width="200px" height="200px"]]

The option `aspect-ratio` combined with the ability to use relative dimensions allows for graphs to resize and maintain its shape if the viewport changes. When using `aspect-ratio` it is necessary to define one and only one of the above lengths.

    [[jsxgraph width="80%" aspect-ratio="3/2"]]

If no size is defined the default is to have `width="500px" height="400px"` and these are also the dimensions used if values are missing and no `aspect-ratio` has been defined.

## Automatic identifier for the div-element

As initialisation of the JSXGraph board requires you to give it a reference to the div-element that will contain the graph you will need to know what that id is. With the JSXGraph-block that identifier is present in a variable named `divid`. Since, 4.3.3 we also provide that same identifier in a variable named `BOARDID` to match the behaviour of the JSXGraph Moodle filter. We generate that identifier automatically to allow one to have multiple plots even multiple copies of the same question on the same page without anyone having to worry about accidental identifier clashing.

## Interactive elements

In this example define the question variables as

    fx:int(expand((x-1)*(x+1)*(x-2)),x);

This question contains an interactive sliding element.

    <p>A graph, together with the tangent line and its slope, are shown below.  Find an algebraic expression for the graph shown below.</p>
    [[jsxgraph]]
      /* boundingbox:[left, top, right, bottom] */
      var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-5, 10, 5, -10], axis: true, showCopyright: false});
      var f = board.jc.snippet('{#fx#}', true, 'x', true);
      var curve = board.create('functiongraph', [f,-10,10], {strokeWidth:2});
      var dcurve = board.create('functiongraph', [JXG.Math.Numerics.D(f),-10,10], {strokeColor:'#ff0000', strokeWidth:1, dash:2});
      var p = board.create('glider',[1,0,curve], {name:'Drag me'});
      board.create('tangent',[p], {name:'Drag me'});
      var q = board.create('point', [function(){return p.X();}, function(){return JXG.Math.Numerics.D(f)(p.X());} ], {withLabel:false});
      board.unsuspendUpdate();
    [[/jsxgraph]]
    <p>\(f(x)=\) [[input:ans1]] [[validation:ans1]]</p>

In this example the student can interact with a dynamic diagram to help them understand what is going on.

## An example with a slider

In this example we provide a simple slider.  Notice in this example we use the JavaScript notation `a**x` for \(a^x\) and not Maxima's `a^x`.

    [[jsxgraph]]
      /* boundingbox:[left, top, right, bottom] */
      var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-5, 10, 5, -10], axis: true, showCopyright: false});
      var a = board.create('slider',[[-3,6],[2,6],[0,2,6]],{name:'a'});
      var curve = board.create('functiongraph', [function(x) {return a.Value()**x}], {strokeWidth:2});
      board.unsuspendUpdate();
    [[/jsxgraph]]
