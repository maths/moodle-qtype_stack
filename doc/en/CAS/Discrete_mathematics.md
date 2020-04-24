# Discrete mathematics and graph theory.

A graph in discrete mathematics is a set of edges and vertices.  Maxima has a "graphs" package, which we do not currently support (see github issue #572 for a discussion of why).

## Generating points for a graph

The following question variables produce the complete graph on `m` vertices.

    /* Choose the number of points. */
    m:7;
    /* Distribute the points around a circle. */
    pts:makelist([decimalplaces(float(cos(2*%pi*k/m)),4),decimalplaces(float(sin(2*%pi*k/m)),4)],k,0,m-1);
    
    /* Create the complete graph of m elements. */
    every_pair(ex):=block(
        if length(ex)<2 then return([]),
        if length(ex)=2 then return([ex]),
        append(maplist(lambda([ex2],[first(ex),ex2]),rest(ex)), every_pair(rest(ex)))
    );
    edges:every_pair(makelist(k,k,0,m-1));

## Displaying graphs with `plot()`

It is possible to plot simple discrete graphs directly using STACK's [`plot`](Plots.md) command by building a combination of discrete and line plots.  If you want to do this, we _strongly_ recommend you work offline first in Maxima using `plot2d` to ensure your Maxima code works.

In general it is better to (1) separate connections of points from the coordinates of points, and (2) deal with lists of coordinates.  That way we can pass a connection, or the coordinates of a point into a function more easily.  Create a list of coordinates, where each coordinate is a point [x,y].

    nk:5;
    /* Position nk coordinates evenly round the unit circle. */
    pc1:float(makelist([cos(%pi/(2*nk)+2*k*%pi/nk), sin(%pi/(2*nk)+2*k*%pi/nk)], k, 0, nk));
    /* Extract individual coordinates. */
    x1:maplist(first, pc1); 
    y1:maplist(second, pc1); 
    p1:[discrete,x1,y1];

Plot must have floating point numbers to deal with.

Now use the CASText:

    {@plot([p1,p1],[x,-1,1],[y,-1,1],[style,points,lines],[box,false],[axes,false])@}

Notice we plot a list, containing `p1` twice, once with style `points` and once with `lines`.

The following takes a list of edge connections, `[a,b]`, and a list of co-ordinate points of the form `[x,y]` and produces the discrete plots of the edge connections. 
Note the two stage process.

1. Turn the list of edge connections (`edgel`) into lists of points to connect. (The inner `maplist`)
2. Turn two points to connect into a discrete plot of the form `[discrete [x1, x2], [y1, y2]]`.

The following code could be combined, but two separate `maplist` applications separate out the processes with more clarity.

    pedges(edgel, pts):= maplist(lambda([ex], [discrete, maplist(first, ex), maplist(second, ex)]), maplist(lambda([ex], [pts[first(ex)], pts[second(ex)]]), edgel));

As an example we will create a simple (disconnected) graph as follows.

    g1:[[2,3], [3,4], [4,2], [1,5]];
    /* Plot this graph, using points in positions pc1. */
    p2:pedges(g1, pc1);

    /* Set colours. */
    pcols2:makelist(red, k, 1, length(p2));

    /* Set Style */
    pstyle2:makelist(lines, k, 1, length(p2));

    /* Add in points, as before. */
    p2:append([p1], p2);
    pstyle2:append([points], pstyle2);
    pcols2:append([blue], pcols2);

    /* Create a single plot. */
    pcols2:append([color], pcols2);
    pstyle2:append([style], pstyle2);

And add in the castext

    {@plot(p2,[x,-1,1],[y,-1,1], pstyle2, pcols2, [box,false], [axes,false])@}


To create a complete graph, we need code to create every pair of edges [a,b], as follows.

    /* Return a list of edge pairs n1 to a list of points. */
    pedgesto(n1, nl) := maplist(lambda([ex], [n1, ex]), nl);
    /* Return every pair of points in nl. */
    palledges(nl) := if is(length(nl)=1) then [] else append(pedgesto(first(nl), rest(nl)), palledges(rest(nl)));

    /* The complete graph on nk edges. */
    knk:palledges(makelist(k,k,1,nk));

    /* Plot this graph, using points in positions pc1. */
    p3:pedges(knk, pc1);

    /* Set colours. */
    pcols3:makelist(red, k, 1, length(p3));

    /* Set Style */
    pstyle3:makelist(lines, k, 1, length(p3));

    /* Add in points, as before. */
    p3:append([p1], p3);
    pstyle3:append([points], pstyle3);
    pcols3:append([blue], pcols3);

    /* Create a single plot. */
    pcols3:append([color], pcols3);
    pstyle3:append([style], pstyle3);

And add in the castext

    {@plot(p3,[x,-1,1],[y,-1,1], pstyle3, pcols3, [box,false], [axes,false])@}

In the above code I have tried to separate out all the issues into individual steps.  Clearly there is significant scope here for utility/convenience functions.

## Displaying graphs with JSXGraph

[JSXGraph](../Authoring/JSXGraph.md) can be used to display discrete graphs.

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

