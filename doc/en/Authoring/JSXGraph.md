# JSXGraph

This page documents use of JSXGraph to display visuals. This is a somewhat advanced topic. For basic plots and visuals you may prefer to [use Maxima to plot graphs](../CAS/Plots.md). 

STACK supports inclusion of dynamic graphs using JSXGraph: [http://jsxgraph.uni-bayreuth.de/wiki/](http://jsxgraph.uni-bayreuth.de/wiki/).

Note, we strongly recommend you do not use an HTML-aware editor when using JSXGraph questions.  Instead turn off the editor within Moodle and edit the raw HTML.

    Site administration > Plugins > Text editors > Manage editors

Individual users can also set their editor preferences:

    User Dashboard > Preferences > Editor preferences > Manage editors

## Include basic plots

This example is based on the documentation for [curve](http://jsxgraph.uni-bayreuth.de/wiki/index.php/Curve) and the [even simpler function plotter](http://jsxgraph.uni-bayreuth.de/wiki/index.php/Even_simpler_function_plotter) example.

To include a basic dynamically-generated sketch into a STACK question, first define the expression of the graph to be plotted in the question variables.  For example

    a:rand(6)-3;
    fx:sin(x)+a;

Then include the following question text, which includes a simple `[[jsxgraph]]` [block](Question_blocks.md).  In particular note the lack of `<script>` tags which you might expect to include.

    <p>Type in an algebraic expression which has the graph shown below.</p>
    [[jsxgraph]]
      // boundingbox:[left, top, right, bottom]
      var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-10, 5, 10, -5], axis: true, showCopyright: false});
      var f = board.jc.snippet('{#fx#}', true, 'x', true);
      board.create('functiongraph', [f,-10,10]);
    [[/jsxgraph]]
    <p>\(f(x)=\) [[input:ans1]] [[validation:ans1]]</p>

Note the code `board.jc.snippet('{#fx#}', true, 'x', true);` which turns a reasonable expression for a function into the JavaScript function.  You cannot just plot the `functiongraph` on its own.

To make a working question, you will then need to add in `fx` as the model answer to input `ans1`, a question note (e.g. `\({@fx@}\)`) and an appropriate potential response tree.

## Block options

You can control the size of the JSXGraph board with the `width` and `height` options.  E.g. 

    [[jsxgraph width="200px" height="200px"]]

## Automatic identifier for the div-element

As initialisation of the JSXGraph board requires you to give it a reference to the div-element that will contain the graph you will need to know what that id is. With the JSXGraph-block that identifier is present in a variable named `divid`. Since, 4.3.3 we also provide that same identifier in a variable named `BOARDID` to match the behaviour of the JSXGraph Moodle filter. We generate that identifier automatically to allow one to have multiple plots even multiple copies of the same question on the same page without anyone having to worry about accidental identifier clashing.

## Interactive elements

In this example define the question variables as

    fx:int(expand((x-1)*(x+1)*(x-2)),x);

This question contains an interactive sliding element.

    <p>A graph, together with the tangent line and its slope, are shown below.  Find an algebraic expression for the graph shown below.</p>
    [[jsxgraph]]
      // boundingbox:[left, top, right, bottom]
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
      // boundingbox:[left, top, right, bottom]
      var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-5, 10, 5, -10], axis: true, showCopyright: false});
      var a = board.create('slider',[[-3,6],[2,6],[0,2,6]],{name:'a'}); 
      var curve = board.create('functiongraph', [function(x) {return a.Value()**x}], {strokeWidth:2});
      board.unsuspendUpdate();
    [[/jsxgraph]]

## General considerations when building interactive graphs

In general you should pay attention on how your graph reacts to the student returning to the page/question later. For example, will your graph 
reset to display the original situation or will it at least move all movable things to the positions the student last left them? If 
the student can do things that are not actually considered as part of the answer, e.g. zoom out or pan the view, do you also remember 
those actions? If your graph is not used for inputting answers then this is not a major issue but if it is then you will need to solve 
this issue. Basically, storing the state of the interactive graph is a key thing that the author of that graph needs to deal with.

The basic structure of such graph logic is as follows:

 1. Load existing state or if not found initialise with defaults.
 2. Draw the graph based on that state.
 3. Attach listeners to everything that can be changed in the graph and store those changes into the state in those listeners.

The simplest solution for storing state is to add a `string` type input field to the question. 

 1. Create and hide an input with CSS, e.g. `<p style="display:none">[[input:stateStore]] [[validation:stateStore]]</p>` (but probably not while you develop the question!)
 2. Make the input a "string" type.
 3. Turn off the validation and verification of the field. 
 4. Use the extra option `hideanswer` to make sure the teacher's answer is not shown to students.
 5. That input field should not be connected to any PRTs.
 6. You can use the syntax hint feature to pass in a default value but only if that is not parametric (currently the syntax hint is not CASText: see the todo list).

You can use that input field to store the state of the graph as a string, for example as a JSON-encoded structure. For example, assuming the name of the String input is named `stateStore`, we can store the position of a point as follows:


    [[jsxgraph input-ref-stateStore="stateRef"]]
      // Note that the input-ref-X attribute above will store the element identifier of the input X in 
      // a variable named in the attribute, you can have multiple references to multiple inputs.
    
      // Create a normal board.
      var board = JXG.JSXGraph.initBoard(divid, {axis: true, showCopyright: false});
    
      // State represented as a JS-object, first define default then try loading the stored values.
      var state = {'x':4, 'y':3};
      var stateInput = document.getElementById(stateRef);
      if (stateInput.value && stateInput.value != '') {
        state = JSON.parse(stateInput.value);
      }
    
      // Then make the graph represent the state
      var p = board.create('point',[state['x'],state['y']]);
    
      // And finally the most important thing, update the stored state when things change.
      p.on('drag', function() {
        var newState = {'x':p.X(), 'y':p.Y()};
        // Encode the state as JSON for storage and store it
        stateInput.value = JSON.stringify(newState);
      });
    
      // As a side note, you typically do not want the state storing input to be directly visible to the user
      // although it may be handy during development to see what happens in it. You might hide it like this:
      stateInput.style.display = 'none';
    [[/jsxgraph]]

Note, in the above example in `[[jsxgraph input-ref-stateStore="stateRef"]]` the `stateStore` part of this tag directly relates to, and must match, the name of the input.

In that trivial example you only have one point that you can drag around but the position of that point will be stored and it will be where 
you left it when you return to the page. However, the position has been stored in a String encoded in JSON format and cannot directly be 
used in STACK-side logic. The JSON format is however very handy if you create objects to store dynamically and want to represent things 
of more complex nature, but in this example we could have just as well have had two separate Numeric inputs storing just the raw 'x' 
and 'y' coordinates separately as raw numbers and in that case we could have used them directly in STACK's grading logic.

If needed, JSON is not impossible to parse in STACK, but it is not easy (as in JavaScript) because Maxima has no map 
data-structures and is not object oriented. In any case, the JSON string generated in the previous example would look like this:

    stateStore:"{\"x\":4,\"y\":3}";

To parse and manipulate it you can use STACK's custom JSON parsing functions:

    tmp:stackjson_parse(stateStore); /* This returns a STACK-map: ["stack_map", ["x", 4], ["y", 3]] */
    x:stackmap_get(tmp,"x");         /* 4 */
    y:stackmap_get(tmp,"y");         /* 3 */
    tmp:stackmap_set(tmp,"z",x*y);   /* ["stack_map", ["x", 4], ["y", 3], ["z", 12]] */
    json:stackjson_stringify(tmp);   /* "{\"x\":4,\"y\":3,\"z\":12}" */


## Convenience tools for building graphs

The previous section covered the general case of storing the state of the graph and acting on it. Typically, you only need to handle a few points and maybe some sliders and for this task we provide ready-made functions that bind those primitive objects to STACK input fields. As these binding functions only deal with singular sliders and points they will also use simpler forms of passing around the values.

The example in the previous section about moving the point around and storing the points position as an JSON object can be redone with the convenience functions in much simpler form. The only major differences being that the value is stored as a raw list of float values, and the input field should not be of the String type instead we can store it as Algebraic input and directly access the values, just make sure you allow float values in that input.

    [[jsxgraph input-ref-stateStore="stateRef"]]
      // Create a board like normal.
      var board = JXG.JSXGraph.initBoard(divid, {axis: true, showCopyright: false});
    
      // Create a point, its initial position will be the default position if no state is present.
      var p = board.create('point', [4, 3]);
    
      // Bind it to the input and state stored in it.
      stack_jxg.bind_point(stateRef, p);
    
      // As a side note, you typically do not want the state storing input to be directly visible to the user
      // although it may be handy during development to see what happens in it. You might hide it like this:
      var stateInput = document.getElementById(stateRef);
      stateInput.style.display = 'none';
    [[/jsxgraph]]

For sliders you use the function `stack_jxg.bind_slider(inputRef, slider)` and it stores the sliders value as a raw float. Sliders will however require that you call `board.update()` after binding to them, otherwise the graph may not display the stored state after reload.

You should check the sample questions about JSXGraph binding for examples of these functions in action.

Starting from version 4.3 there are three functions for dealing with pairs of points. Basically, if you want to represent vectors, lines or circles or anything that can be defined with just two points. `stack_jxg.bind_point_dual(inputRef, point1, point2)` will store the positions of the points into a single input as a list of lists, `stack_jxg.bind_point_relative(inputRef, point1, point2)` will also generate a list but in it the second point is represented relative to the first, and finally `stack_jxg.bind_point_direction(inputRef, point1, point2)` will provide the first point as coordinates and the second point as an angle and distance from the first.

## Convenience tools for generating lists of values.

If you want to output a list of values without Maxima's normal bracket symbols you can use

    stack_disp_comma_separate([a,b,sin(pi)]);

This function turns a list into a string representation of its arguments, without braces.
Internally, it applies `string` to the list of values (not TeX!).  However, you might still get things like `%pi` in the output.

You can use this with mathematical input: `{@stack_disp_comma_separate([a,b,sin(pi)])@}` and you will get the result `a, b, sin(%pi/7)` (without the string quotes) because when a Maxima variable is a string we strip off the outside quotes and don't typeset this in maths mode.


## Discrete mathematics and graph theory.


A graph can be displayed with JSXGraph, see [discrete mathematics](../CAS/Discrete_mathematics.md) for examples.