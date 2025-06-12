# Example JSXGraph plots.

## Discrete mathematics and graph theory.

A graph can be displayed with JSXGraph, see [discrete mathematics](../../Topics/Discrete_mathematics.md) for examples.

In the question variables define your points and edges.

    /* A list of points, which are lists of coordinates.*/
    pts:[[1.0,0.0],[0.623,0.782],[-0.223,0.975],[-0.901,0.434],[-0.901,-0.434],[-0.223,-0.975],[0.623,-0.782]];

    /* A list of edge connections. */
    edges:[[0,1],[0,2],[0,3],[4,5],[4,6],[4,0]];

Then, you can use this JSXGraph block to create your vertices and edges.  Note, this code uses `board.create('arrow', [...])`, but you could as well use `segment` not `arrow` for an un-directed graph.

    [[jsxgraph]]
        /* boundingbox:[left, top, right, bottom] */
        var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-1.2, 1.2, 1.2, -1.2], axis: false, showCopyright: false});
        
        /* Notice the syntax STACK uses for putting the _value_ of a variable into the text before display. */
        var pts = {#pts#};
        var boardpts = new Array();
        var arrayLength = pts.length;
        for (var i = 0; i < arrayLength; i++) {
            boardpts.push(board.create('point', pts[i]));
        }
        
        var edges = {#edges#};
        var boardedges = new Array();
        var arrayLength = edges.length;
        for (var i = 0; i < arrayLength; i++) {
            boardedges.push(board.create('arrow', [boardpts[edges[i][0]], boardpts[edges[i][1]]]));
        }
    [[/jsxgraph]]



## A catalogue of plots

The following CASText gives representative examples of the height, width and aspect ratio options supported by STACK's jsxgraph block  Cut and paste it into the CASchat script.

````
Default options
[[jsxgraph]]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]

Absolute units
[[jsxgraph height='100px' width='200px']]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]

Relative units: 50% width, default height
[[jsxgraph width='50%']]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]

Relative units: 50% width, 50% height (use vh for relative height: todo why?!)
[[jsxgraph width='50%' height='50vh']]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]

Absolute and relative: 300px width, 50% height
[[jsxgraph width='300px' height='50vh']]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]

Relative and absolute: 50% width, 300px height
[[jsxgraph width='50%' height='300px']]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]

Aspect ratio, and absolute width
[[jsxgraph width='300px' aspect-ratio='1']]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]

Aspect ratio of 3, and absolute width
[[jsxgraph width='300px' aspect-ratio='3']]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]

Aspect ratio of 3, and relative width
[[jsxgraph width='50%' aspect-ratio='3']]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]

Aspect ratio and absolute height
[[jsxgraph height='500px' aspect-ratio='1']]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]

Aspect ratio of 3 and absolute height
[[jsxgraph height='500px' aspect-ratio='3']]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]

Aspect ratio of 3 and relative height
[[jsxgraph height='50vh' aspect-ratio='3']]
  /* boundingbox:[left, top, right, bottom] */
  var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-3, 1, 3, -1], axis: true, showCopyright: false});
  var f = board.jc.snippet('sin(x^2)', true, 'x', true);
  board.create('functiongraph', [f,-3,3]);
[[/jsxgraph]]
````

