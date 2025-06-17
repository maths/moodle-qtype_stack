# Authoring your first JSXGraph question

The purpose of this document is to help you author your first question using JSXGraph as an input.

## Before we start

This document assumes you have worked through the following sections of the Author quick-start guide.

* [Authoring quick start 1](../../AbInitio/Authoring_quick_start_1.md): A basic question.
* [Authoring quick start 2](../../AbInitio/Authoring_quick_start_2.md): Question variables.
* [Authoring quick start 3](../../AbInitio/Authoring_quick_start_3.md): Improving feedback.

__We strongly recommend you do not use an HTML-aware editor when editing STACK questions containing JSXGraph.__  Instead turn off the editor within Moodle and edit the raw HTML.  Individual users can set their editor preferences by following:

    User Dashboard > Preferences > Editor preferences > Manage editors

_Plain text_ is a safe choice for an editor, even if it lacks a lot of convenience features. Editing a JSXGraph question in other editors can break the question, especially if it contains `<`, `>` and `&` characters.

## Drawing a basic JSXGraph plot

In this guide, we will create an interactive question that gives the student a linear function and asks for a line parallel to the function graph, which the student should input by dragging a point. You can find the complete question [here](../../../../samplequestions/stacklibrary/Doc-Examples/Specialist-Tools-Docs/JSXgraphs/Author_quickstart_Line_parallel_to_graph.xml).

JSXGraph is a Javascript library and thus plots are written in Javascript. In STACK, the definition of a JSXGraph plot needs to go inside `[[jsxgraph]]` blocks.

Create a new STACK question, and use the content below as question text. (We will initialize the maxima variable `expr` later.) When previewing the question, you should see a plot with a line.

```
<p>Drag the point \(A\) so that the line \(OA\) is parallel to the function graph of \(f(x) = {@expr@}\).</p>

[[jsxgraph]]
var board = JXG.JSXGraph.initBoard(divid, {
    axis: true,
    showCopyright: false,
    showNavigation: false,
    boundingbox: [-6, 6, 6, -6],
    keepaspectratio: true,
    grid:true
});

var pO = board.create('point', [0, 0], {name: 'O', fixed: true, color: "blue" });
var pA = board.create('point', [2, -1], { name: 'A', snapToGrid:true, snapSizeX:0.2, snapSizeY:0.2 });
var line = board.create('line', [pO, pA], { fixed: true });
[[/jsxgraph]]

<p>[[input:ans1]] [[validation:ans1]]</p>
```

We need to create a board (canvas) to draw on, which is done using the `initBoard` function. In STACK, the first argument should be `divid`, the second argument is a dictionary of properties, see [documentation](https://jsxgraph.org/docs/symbols/JXG.Board.html) for more options.

On this board, we can then create other objects, such as points and lines. Each object is created by providing the object type, a definition (e.g., a point has two coordinates; a line is defined using two points), and object properties. In this case, we make one point (A) movable while the origin (O) is fixed using the `fixed` property. As we later want students to move the point A, we snap the point coordinates to multiples of 0.2 to make it easier for students to move the point to integer coordinates. As the line is defined in terms of A and O, moving A also updates the line.

The set of properties of each object type can be looked up in the [documentation](https://jsxgraph.org/docs/index.html), e.g. for [points](https://jsxgraph.org/docs/symbols/Point.html); for commonly used objects like [points](https://jsxgraph.uni-bayreuth.de/wiki/index.php/Point) examples are also available on the [JSXGraph wiki](https://jsxgraph.uni-bayreuth.de/wiki/index.php?title=Main_Page). The [JSXGraph book](https://ipesek.github.io/jsxgraphbook/3_basics.html) gives a basic overview over commonly used objects as well. Note that these resources assume JSXGraph is used outside of STACK, so not everything is directly transferable (in particular, the creation of the board is somewhat different).

## Randomization

We may want to randomize the function graph of the function f(x) that the line should be parallel to, as well as the initial position of the point A that the student will drag around. We define the following question variables:

```
slope: rand([1/2, 3/2, 2]);
intercept: rand_with_step(-3, -1, 1);
expr: slope*x + intercept;  /* our function f(x) */
ta1: [2, 2 * float(slope)];  /* A correct answer for A with integer coordinates */

xA_init: 1 + rand(3);
yA_init: -1 - rand(3);
A_init: [xA_init, yA_init];  /* Initial position of A */
```

As our question now has randomization, we also need to choose a question note:

```
\(f(x) = {@expr@}\)<br>
\(A = {@ta1@}, A_{init} = {@A_init@}\)
```

While the function f(x) does not affect the initial plot (only the correct solution), we now need to update our plot to show the correct initial position of A. For this purpose, we use `{# #}` to embed values of maxima variables directly into our Javascript code. This is similar to `{@ @}` but just gives the raw content instead of formatting the content in a pretty way (which would not make for valid Javascript). Update the definition of the JSXGraph point A in the question text as follows:

```
var pA = board.create('point', {# A_init #}, { name: 'A', snapToGrid:true, snapSizeX:0.2, snapSizeY:0.2 });
```

Note that instead of `{# A_init #}`, we could also have used `[{# xA_init #}, {# yA_init #}]`. Both will give the two coordinates separated by a comma and inside of square brackets, which is valid Javascript syntax for a list of two elements. Be careful when using maxima constants such a `%pi` and `%e`, as these are not valid Javascript symbols. If you have numerical maxima expressions containing these, convert the result to float.

## Interactive input

Our goal is to process the point that the student is dragging around as input. For this purpose, we need to bind the JSXGraph point to a STACK input. To do this, we need to add an input reference to the JSXGraph block, and then bind the point to that reference:

```
[[jsxgraph input-ref-ans1="ref_A"]]

...

stack_jxg.bind_point(ref_A, pA);
[[/jsxgraph]]
```

The first line creates a reference `ref_A` that is linked to the STACK input `ans1`. The last line then binds the JSXGraph point `pA` that we created earlier to this new reference. (It is also possible to bind objects other than points, such as sliders, lists or custom objects, see [Binding](Binding.md).)

If you preview the question now, you will notice that as you drag the point around, the input field `ans1` reflects the current coordinates of the point. You will also notice that you get validation errors ("This answer is invalid. Your answer contains floating point numbers, ...") if you drag the point to non-integer coordinates.

By default, STACK does not allow floating point numbers in algebraic input fields. Go to the properties of the input `ans1` and set _Forbid float_ to _No_. In general, as JSXGraph works using floating point numbers rather than exact arithmetics, you'll generally want to allow floating points numbers for JSXGraph-linked input fields.

## Feedback

In general, when comparing floating point values (such as points or values derived their coordinates), it is advisable to use a numerical answer test, due to rounding errors intrinsic to floating point numbers. For example, you might have noticed that even with the snap to grid option, the values in the input field looked like `[0.8,1.2000000000000002]` rather than `[0.8,1.2]`.

For our question, if there was a unique correct answer, we could simply compare the student answer `ans1` to our teacher answer `ta1`, both being a point (i.e., a list of two coordinates), using an answer test like `NumAbsolute` or `NumRelative`.

However, as there are many possible correct answers, we will compute the slope of the student's line and compare it to the slope of our function.

So in the feedback variables of PRT1, we define

```
student_slope: ans1[2]/ans1[1];
```

and in the PRT1, we compare `student_slope` and `slope` using the answer test `NumRelative` with _Test Options_ 0.02 and _Quiet_ set to _Yes_.

### JSXGraph in feedback

JSXGraph plots can not only be used in the question text, but also in the feedback. For example, in our question, if the student gets the answer incorrect, we could display a plot showing the function f(x) as well as the student answer, for comparsion. Add the following to the false feedback for PRT1:

```
<p>The slope of your line is different from the slope of {@expr@}, see below:</p>

[[jsxgraph]]
var board = JXG.JSXGraph.initBoard(divid, {
    axis: true,
    showCopyright: false,
    showNavigation: false,
    boundingbox: [-6, 6, 6, -6],
    keepaspectratio: true,
    grid:true
});

var pO = board.create('point', [0, 0], {name: 'O', fixed: true, color: "blue" });
var pA = board.create('point', {# ans1 #}, { name: 'A',  fixed: true});
var line = board.create('line', [pO, pA], { fixed: true });
var graph = board.create('functiongraph', '{#expr#}' , {name:'{#expr#}', withLabel: true});
[[/jsxgraph]]
```

Note how we are using the student answer `ans1` in our plot, and we're drawing the graph of f(x) using a `functiongraph` object. Because our function is simple, its maxima string representation works as a definition for the `functiongraph`, but in general read [Basic_plots](Basic_plots.md) to see how to plot functions.

### Blank inputs

Note that if the student does not move the point, the input field will remain blank and they will not get any feedback. In our question, we made sure in our randomization that the initial position of A is not a correct answer. In general, you'll want to make sure that the initial positions of your interactive objects don't form a correct answer.

If you have multiple inputs (points, sliders, etc) and there are correct answers where _some_ of them remain in their initial position, you can define input groups so that if any element of that group is moved by the student, all of them are considered moved and their inputs will get values. In order to do that, after binding the objects using `bind_point`/`bind_slider`/etc, add a call to `define_group` where you pass the list of elements in a group as arguments, similar to this:

```
stack_jxg.define_group([pA, pB, ...]); 
```

## Hiding input fields

While it is useful to see the input field for testing, we probably don't want students to see it. So once we are satisfied with our question, we can hide the input field by putting it into appropriate HTML tags, for example:

```
<p style="display:none">[[input:ans1]] [[validation:ans1]] </p>
```

You may also want to hide the teacher answer from the student feedback, as by default it is displayed as part of the feedback like "A correct answer is:". To do this

 3. Turn off the validation ("Show the validation") and verification ("Student must verify") of the input field.
 4. Use the extra option `hideanswer` to make sure the teacher's answer is not shown to students.

# Tips and tricks
When creating JSXGraph questions in STACK, it is often slow and difficult to find errors in the JSXGraph javascript code. It can be useful to develop the JSXGraph component of the question locally first, and then "STACKify" it. For this purpose, create a file with a `.html` extension on your computer, with the following content:
```
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>JSXGraph template</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <!-- The jsxgraph version is defined in the next 2 lines (eg @1.4.5 defines version 1.4.5) -->
    <link href="https://cdn.jsdelivr.net/npm/jsxgraph@1.4.5/distrib/jsxgraph.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/jsxgraph@1.4.5/distrib/jsxgraphcore.js" type="text/javascript"
        charset="UTF-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js" id="MathJax-script" async></script>
</head>

<body>
    <!-- You can define the width and height of your applet here -->
    <!-- If it's not the standard 600 by 600 then the syntax for STACK is 
        [[jsxgraph height='Apx' width='Bpx']] where A is the height and B
        is the width -->
    <div id="jxgbox" class="jxgbox" style="width:600px; height:600px;"></div>


    <!-- Start your jsxgraph here -->
    <script>
        var board = JXG.JSXGraph.initBoard('jxgbox', { // STACK: when moving to STACK change 'jxgboard' for divid
            boundingbox: [-1, 7, 9, -1],
            keepaspectratio: true,
            showCopyright: false,
            axis: true,
            showNavigation: true
        });


    </script>
</body>
</html>
```
Now edit your JSXGraph code in between the `<script>` tags. To preview the question, open in a browser. After each edit, you can simply refresh the page to see the changes. If nothing displays, there is likely an error in your code. To get the relevant error messages, in your browser, go into developer mode (usually Ctrl+Shift+C). Usually, there is a tab for errors, you may be able to find it looking for a red symbol with a cross. Once you have found the list of errors, they usually indicate where in the code the problem is.
Once you are happy with your JSXGraph component, copy the content within the `<script>` tags into a STACK question and change `'jxgboard'` to `divid` in the `initBoard` function call. You can now work on the randomization and interactive parts.

When previewing a broken JSXGraph question in STACK, it is still possible to use the browser's developer (Ctrl+Shift+C) mode to find errors, as before. However, you won't get useful on where exactly the error is.

In general, it is helpful to keep the inputs visible for debugging, and only hide them once the question is ready.