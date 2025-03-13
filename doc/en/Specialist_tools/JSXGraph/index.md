# JSXGraph

This page documents use of JSXGraph to display visuals. This is a somewhat advanced topic. For basic plots and visuals you may prefer to [use Maxima to plot graphs](../../CAS/Maxima_plot.md).

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

## General considerations when building interactive graphs {#manual_binding}

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
 3. Turn off the validation ("Show the validation") and verification ("Student must verify") of the input field.
 4. Use the extra option `hideanswer` to make sure the teacher's answer is not shown to students.
 5. That input field should not be connected to any PRTs.
 6. You can use the syntax hint feature to pass in a default value but only if that is not parametric (currently the syntax hint is not CASText: see the todo list).

It is possible to store state in other input types, but this input will therefore be subject to validation.  E.g. you could use an algebraic input to store a number but you probably then need to allow floats.

You can use that input field to store the state of the graph as a string, for example as a JSON-encoded structure. For example, assuming the name of the String input is named `stateStore`, we can store the position of a point as follows:


    [[jsxgraph input-ref-stateStore="stateRef"]]
      /* Note that the input-ref-X attribute above will store the element identifier of the input X in
         a variable named in the attribute, you can have multiple references to multiple inputs. */

      /* Create a normal board. */
      var board = JXG.JSXGraph.initBoard(divid, {axis: true, showCopyright: false});

      /* State represented as a JS-object, first define default then try loading the stored values. */
      var state = {'x':4, 'y':3};
      var stateInput = document.getElementById(stateRef);
      if (stateInput.value && stateInput.value != '') {
        state = JSON.parse(stateInput.value);
      }

      /* Then make the graph represent the state */
      var p = board.create('point',[state['x'],state['y']]);

      /* And finally the most important thing, update the stored state when things change. */
      p.on('drag', function() {
        var newState = {'x':p.X(), 'y':p.Y()};
        /* Encode the state as JSON for storage and store it */
        stateInput.value = JSON.stringify(newState);
        /* Since the STACK-JS system one needs to also remember to tell others
           about the changed value. Do this by dispatching an event. */
        stateInput.dispatchEvent(new Event('change'));
      });
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
      /* Create a board like normal. */
      var board = JXG.JSXGraph.initBoard(divid, {axis: true, showCopyright: false});

      /* Create a point, its initial position will be the default position if no state is present. */
      var p = board.create('point', [4, 3]);

      /* Bind it to the input and state stored in it. */
      stack_jxg.bind_point(stateRef, p);
    [[/jsxgraph]]

For sliders you use the function `stack_jxg.bind_slider(inputRef, slider)` and it stores the sliders value as a raw float. Sliders will however require that you call `board.update()` after binding to them, otherwise the graph may not display the stored state after reload.

You should check the sample questions about JSXGraph binding for examples of these functions in action.

Starting from version 4.3 there are three functions for dealing with pairs of points. Basically, if you want to represent vectors, lines or circles or anything that can be defined with just two points. `stack_jxg.bind_point_dual(inputRef, point1, point2)` will store the positions of the points into a single input as a list of lists, `stack_jxg.bind_point_relative(inputRef, point1, point2)` will also generate a list but in it the second point is represented relative to the first, and finally `stack_jxg.bind_point_direction(inputRef, point1, point2)` will provide the first point as coordinates and the second point as an angle and distance from the first.

Starting from 4.4 there is only one new bind function `stack_jxg.bind_list_of(inputRef, list)` which takes a list of points and/or sliders and stores it into a single input. It only works if the size or order of the list does not change during page loads, however the list can change its shape for variants of the question. The primary use target for this are the vertices of polygons, but one can probably come up with something else as well, it does work as a quick and dirty way of storing the whole graph state if the graph can be defined just by points and sliders.

There are also two new functions related to dealing with groups of objects and matching inputs. For situations where the answer consists of multiple elements and it is possible that not all get moved one can use `stack_jxg.define_group(list)` which takes a list of points and/or sliders and makes it so that touching any one of them will trigger them all to be considered as touched and thus generates inputs. There is also `stack_jxg.starts_moved(object)` which takes a point or a slider and marks it as touched from the start, this may be of use if the graph is an optional part and the actual grading depends of other parts or if one wants to use PRT feedback as a way for describing the status of the graph and needs the objects to be transferred onto the CAS side without interaction from the student.

## Convenience tools for generating lists of values.

If you want to output a list of values without Maxima's normal bracket symbols you can use

    stack_disp_comma_separate([a,b,sin(pi)]);

This function turns a list into a string representation of its arguments, without braces.
Internally, it applies `string` to the list of values (not TeX!).  However, you might still get things like `%pi` in the output.

You can use this with mathematical input: `{@stack_disp_comma_separate([a,b,sin(pi)])@}` and you will get the result `a, b, sin(%pi/7)` (without the string quotes) because when a Maxima variable is a string we strip off the outside quotes and don't typeset this in maths mode.

## Custom binding

In the event that you wish to bind a JSXGraph object that is *not* a point or a slider (or a group of these), you can build your own binding function using `stack_jxg.custom_bind(inputRef, serializer, deserializer, [object(s)])`. The `serializer` function is used to generate the value for the input. The `deserializer` is used to extract the value in the input and subsequently update the state of the JSXGraph.

One use case of this could be tying together a STACK `[[input]]` with an Input object in JSXGraph. This is probably a rare use case, but one could imagine a scenario where it it useful to have an input box in the graph, such as labelling a probability tree diagram, or wanting a draggable input box for some reason. In any case, this is a particularly simple example of using the `custom_bind()` function, shown below.

    <p>Enter \(\sin(x)\)</p>
    <span hidden="">[[input:ans1]]</span><span>[[validation:ans1]]</span><br>
    [[jsxgraph width="200px" height="100px" input-ref-ans1="ans1Ref"]]
    	let board = JXG.JSXGraph.initBoard(divid, {
          boundingbox: [-1,1,1,-1], axis: false
        });
      	let inputBox = board.create('input',[0,0,'',''],{}); /*Create input box we want to bind to the STACK input*/

        /* We want to create our own binding function using custom_bind as a base.
           Our function, inputBinder, will take the reference to the STACK input and the object we want to bind to it as inputs.
           The serializer function doesn't take any inputs, but will refer to the object given to inputBinder directly.
           The deserializer function takes exactly one input: the data with which it will update the graph.
           Lastly, we run the custom_bind function. */

        let inputBinder = function(inputRef, object) {
            let serializer = function() {return object.Value()} /*Simply returns the value in the inputBox*/
            let deserializer = function(data) {object.set(data)} /*Given some data, put this data into the inputBox*/
            stack_jxg.custom_bind(inputRef, serializer, deserializer, [object])
        }

        /* Now we run the function */
        inputBinder(ans1Ref, inputBox)
	[[/jsxgraph]]

In most cases the `serializer` and `deserializer` functions will be a bit more complicated, and will probably need to use functions like `JSON.stringify` or `JSON.parse` as in the earlier examples on this page.

Sometimes you may wish to bind a STACK input to something in the JSXGraph IFRAME that isn't an object, in which case the `stack_jxg.custom_bind` will not work. One example of this would be asking students to identify or shade in a certain region in a graph, such as part of a Venn diagram, identifying the region of integration for an iterated integral, or showing that (for example), two sixths is equal to one third. In this case, you will need to write the binding more explicitly, using the steps listed above as a framework. An example is given below, in which we ask the student to "shade" in one third of a circle divided into six equal segments.

Let us first assume that we will hard-code this question to always ask students to shade in one third of a circle divided into sixths. This is not too difficult to generalise, and it keeps the code clean. Then let us define a model answer as the list:

    ta: [1,1,0,0,0,0];

We interpret this as two of the six sectors in our eventual graph being shaded, and four of them being unshaded, with 1 representing on and 0 representing off. Our student input, `ans1`, will then be a normal algebraic input.

Now we can create the question text. Firstly, we state the instructions for the student and create the board and associated objects.

    <p>Shade some regions of the diagram below so that it represents the fraction \(\dfrac{1}{3}\). Click a region to shade it, and click a second time to un-shade it if needed.</p>
    [[jsxgraph width="500px" height="500px" input-ref-ans1="ans1Ref"]]
        var board = JXG.JSXGraph.initBoard(divid, {
	        boundingbox: [-1.2,1.2,1.2,-1.2], axis: false,
            showNavigation: false, showCopyright: false});

        var plotColours = ["#1f77b4", "#ff7f0e"];
        var numSectors = 6;

        var origin = board.create('point',[0,0],{visible:false}); /* This will be referenced multiple times as we create the sectors */

        var points = [];
        var sectors = [];

        /* Create 7 points (doubling up the start and end) and then between each pair of adjacent points, define a sector. */

        var sectorAttr = {strokeColor:plotColours[0],strokeOpacity:0.5,strokeWidth: 2,fillColor:plotColours[1],fillOpacity:0, highlight: false}
        for(let ii=0;ii<numSectors+1;ii++) {
            points[ii] = board.create('point',[Math.cos(ii*2*Math.PI / numSectors),Math.sin(ii*2*Math.PI / numSectors)],{visible:false});
            if (ii>0) {
                sectors[ii-1] = board.create('sector',[origin,points[ii-1],points[ii]],sectorAttr);
            }
        }

Now that the graph has been drawn, we need to initialise the shading based on existing student input. This means that if a student has given an answer and then refreshed the page, the graph should show the correct sectors shaded or unshaded based on that answer.

    var shading = [0,0,0,0,0,0];
    var shadingInput = document.getElementById(ans1Ref);
    if (shadingInput.value && shadingInput.value != '') { /* If the student has given an input and it is not an empty string: */
        shading = JSON.parse(shadingInput.value) /* Over-write the current shading array with the student input */
        for (var ii=0;ii<numSectors;ii++) { /* and then update the shading to match. */
            sectors[ii].setAttribute({fillOpacity:0.3*shading[ii]})
        }
    }

The graph should now have the appropriate shading applied. We have completed the first two out of three steps as outlined above. To accomplish the last step we will write three functions; one that will return the coordinates of a location that is clicked, another that will update the graph given those coordinates, and a third that will listen to a click event and then run these functions.

    /* The below code is adapted from an example found at https://jsxgraph.org/wiki/index.php/Browser_event_and_coordinates */

    var getMouseCoords = function(e, i) {
        var cPos = board.getCoordsTopLeftCorner(e, i),
            absPos = JXG.getPosition(e, i),
            dx = absPos[0]-cPos[0],
            dy = absPos[1]-cPos[1];

        var coords = new JXG.Coords(JXG.COORDS_BY_SCREEN, [dx, dy], board);
        return [coords.usrCoords[1], coords.usrCoords[2]]
    };

    var shadeSectors = function(x,y) { /* Given a coordinate pair x,y */
        var r = Math.sqrt(x**2 + y**2); /* convert to polar form r,angle */
        var angle = Math.atan2(y,x);
        if (angle<0) {angle = angle + 2*Math.PI} /* Ensure argument is from 0 to 2π */

        if (r<1) { /* If inside the unit circle */
            var whichSector = Math.floor(angle*numSectors/(2*Math.PI)); /* read which sextant the coordinates are in */
            shading[whichSector] = 1 - shading[whichSector]
            sectors[whichSector].setAttribute({fillOpacity:0.3*shading[whichSector]})

            shadingInput.value = JSON.stringify(shading); /* Update the input value */
            shadingInput.dispatchEvent(new Event('change')); /* Tell the STACK input outside the JSXGraph to look for this updated value */
        }
    }

    var onClick = function(e) {
        var [x, y] = getMouseCoords(e, 0);
        shadeSectors(x,y)
    }

    board.on('down',onClick);
    [[/jsxgraph]]

Finally, we finish the question by adding the appropriate answer box inside a hidden div (as well as setting "Student must verify" to No).

    <div hidden="">[[input:ans1]] [[validation:ans1]]</div>

The student's input, `ans1`, is now exactly a Maxima list of ones and zeros, and to mark the students answer we could check that `apply("+",ans1)` is exactly equal to 2.
