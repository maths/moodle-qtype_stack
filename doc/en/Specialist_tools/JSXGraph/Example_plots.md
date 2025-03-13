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

## Reacting to feedback from the STACK question in JSXGraph

In some situations, it can be quite useful to change the graph state based on the feedback that students get displayed after submitting the task.

With STACK-JS, JSXGraph is contained inside an IFRAME and thus can not directly access DOM elements from the STACK question. So if you want to check whether some feedback is present in the STACK question, you have to use the function  `stack_js.get_content(id)` from the stack_js namespace. The functions from this namespace can be called in the JavaScript code inside the JSXGraph block just like the binding functions from the `stack_jxg` namespace.

The following steps should be taken to react to feedback inside of the JSXGraph applet:

1. Include an empty span with a unique identifier inside the feedback of a PRT node, so that JSXGraph can look for that element
2. Call the function `stack_js.get_content(id)` with the id of the span you placed inside your feedback in the JSXGraph code. As this function is async and returns a promise for the content, make sure to write your code for changing the graph state inside a chained `.then()`.

A common use case for this could be that you want to make a point fixed so that the user can not drag it anymore after he submitted the question and received a certain feedback. A minimal example for this would then look like this:

In one of your PRTs, you place an empty span with an id like for example `feedback-1`


    [[jsxgraph]]

    // A sample board
    var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-5, 5, 5, -5], axis: true, showCopyright: false});
    
    // Create a point for demo purpose
    var a = board.create('point',[1,2],{name:'a'});
    
    // Here we check if there is a certain feedback span present in the STACK question  
    stack_js.get_content('feedback-1').then((content) => {

    if (content !== null) {
    // As the content is not null this means the span is present so feedback is displayed and we can react to it here
    a.setAttribute({ fixed: true, highlight: false});
    }

    });

    [[/jsxgraph]]

The function `stack_js.get_content(id)` looks for an element in the DOM of the parent document and returns a promise that will resolve to the content of that element. If the content is not `null`, that means it found the element somewhere in the question. As this operation is async, you will always have to use a callback using `.then()`.

If you want to know more about STACK-JS and the functions provided for interacting with the STACK question content (change inputs, switch content, toggle the visibility of content), then you can have a look at [STACK-JS](../../Developer/STACK-JS.md).