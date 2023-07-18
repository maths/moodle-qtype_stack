# GeoGebra in STACK

Author Tim Lutz - University of Edinburgh and University of Education Heidelberg, 2022-23.

STACK supports inclusion of dynamic graphics using GeoGebra: [https://geogebra.org](https://geogebra.org).  This page documents how to use GeoGebra applets both to display GeoGebra visuals, and as a STACK input.

Information from GeoGebra applets can be linked to STACK inputs and assessed by potential response trees in the normal way.  To help with assessment, STACK provides a number of [geometry related maxima functions](../CAS/Geometry.md).

Please note that significant computation and calculation can be done within GeoGebra itself.  In many cases it might be much better to establish mathematical properties within the GeoGebra applet itself, and link the _results_ to STACK inputs.  These results could be the distance between relevant objects, or boolean results.

## Before we start: Check Editor preferences

Note, we strongly recommend you do not use an HTML-aware editor when using GeoGebra questions.  Instead turn off the editor within Moodle and edit the raw HTML.

    Site administration > Plugins > Text editors > Manage editors

Individual users can also set their editor preferences:

    User Dashboard > Preferences > Editor preferences > Manage editors

## Adding a geogebra question block using the material_id

The easiest way to use GeoGebra in STACK is to use an applet hosted at geogebra.org. The applet must be publicly avaiable through a url. 

To include a GeoGebra applet into a STACK castext field (e.g. the question text), first create or search for an existing GeoGebra applet at geogebra.org. To set things up we need the material_id of an applet, we want to show.

For the material [https://www.geogebra.org/m/seehz3km](https://www.geogebra.org/m/seehz3km) the material_id is: `seehz3km`

An example `[[geogebra]]` [question block](Question_blocks/index.md) is shown below.

    <p>You can show an applet:</p>
    [[geogebra]]
    params["material_id"]="seehz3km";
    [[/geogebra]]

This illustrates how the material_id is used.

## Using the sub-tags "set", "watch" and "remember"

The "set", "watch" and "remember" tags to the `[[geogebra]]` question block link Maxima values to GeoGebra objects in various ways.

* "set" will set a GeoGebra object, point or value to a STACK-calculated value.
* "watch" enables a STACK input to listen to values and points in GeoGebra.
* "remember" is needed when you do not want to calculate feedback with some of the GeoGebra objects in an applet, but you do want to be able to save and restore the state of an applet when the student returns to the question later.

### Naming conventions

The sub-tag `set` parameter value is a string of (unique), comma-separated, GeoGebra-objects with latin names.
For example, the sub-tag value could look like:  `set = "A,B,C,D,a2,E__fixed"` and would be placed in the block as

    [[geogebra set = "A,B,C,D,a2,E__fixed"]]
    [[/geogebra]]

To be able to make things easy for question authors, the following name conventions _must_ be followed:

1. Names of variables must be equal in both STACK and GeoGebra.
2. Value-names must start with lower case letters.
3. Values must be `int` or `float` STACK variables.
4. Angles are used like values, and so must be named lowercase letters in Latin-Alphabet, (not Greek unicode letters!) and values must be in radians.
5. Point-names must start with upper case letters.
6. Points are represented as a list in STACK.  For example `D:[2,3]`, means a point \(D\) with \(x=2, y=3\).  

## Using the "set" sub-tag

With the "set" sub-tag you can set a GeoGebra object, point or value to a STACK-calculated value.

By default points are free to manipulate in the applet, unless you add `__fixed` or other double-underscore-tags to the Point-name. A full list of available options see "set: double-underscore-tags in the "advanced use-cases"-section.

Angles cannot be set directly, set points instead!

### A minimal example

Set the question variables:

    A:[2,3];
    B:[1,2];

Set the question text:

    [[geogebra set="A,B"]]
    params["material_id"]="seehz3km";
    [[/geogebra]]
    Write the coordinates of \(A\): [[input:ans1]][[validation:ans1]]

Then you will need to fill in the teacher's answer and a PRT to make a working question.  (Also, be sure to set HTML format for the question text if useing the plain text edit.)

## Using the "watch" sub-tag 

With the "watch" sub-tag someone can listen to values and points in GeoGebra. These values can then be used to calculate feedback in STACK.  The values will be assigned to a STACK input.  Note, inputs can be "hidden" from the student.

### Minimal example watching point A and value or angle b

Set the question variables to be empty in this example.

Set the question text:

    [[geogebra watch="A,b"]]
    params["material_id"]="seehz3km";
    [[/geogebra]]
    [[input:A]][[validation:A]]
    [[input:b]][[validation:b]]

Recall that since `A` is upper case it must be a point, and since `b` is lower case it will be a value/angle.  Note:

1. `A` _must_ be an algebraic-input and you _must_ allow floats!
2. You can access `A` in STACK for feedback as a list of values for points `A[0]->x-value`, `A[1]->y-value`
3. You can access `b` in STACK as value. If `b` represents an angle then `b` is in radians.
4. Later we will [hide the inputs](Inputs.md#extra_option_hideanswer) from students, but for testing it is helpful to see the input boxes.  This is done with the STACK "extra option" `hideanswer` in the input.

## Using the "remember" sub-tag

If you do not want to calculate feedback with some of the GeoGebra objects in an applet, but you do want to be able to save and restore the state of an applet, you can use the "remember" tag.

You still need an input in the question to store these values. The only way STACK can store "state" is through inputs.
This input _must_ be of type "string" (because we store these values as a JSON-string internally).

### Minimal example remember A,B,C

Set the question variables to be empty in this example.

Set the question text:

    [[geogebra remember="A,B,C"]]
    params["material_id"]="seehz3km";
    [[/geogebra]]
    [[input:remember]][[validation:remember]]

1. The input _must_ be of type string, and can not be used to calculate values in STACK feedback for now. 
2. The name "remember" is for easy restoring purposes.  Of course, any name could be used.
3. Remember to [hide the input](Inputs.md#extra_option_hideanswer) from students with the STACK "extra option" `hideanswer` in the input.

## Advanced use-cases

### set: double-underscore-tags

The set sub-tag allows more control over setting objects:

#### "preserve" keyword

If you want to preserve GeoGebra definitions of points or values when setting them add the __preserve keyword to that object.

##### Common example: "Points on objects"

For example, assume "A" is a GeoGebra-Point on an object, like A is a point on the circle B.
When you set A like:
`set ="A"`, the definition of A will be overwritten by default.
When you set A like:
`set="A__preserve"`
then `"A__preserve"` will preserve that A is a Point on B and tries to set A near to your STACK variable A but on the circle.

##### Common example: "Sliders"

"a" should be a GeoGebra-value controlled by a slider ranging from -5 to 5.
When you set a like:
`set = "a"`, the definition of a will be overwritten, e.g. if the STACK variable a is 10, after initialization a is 10 in GeoGebra.
When you set a like:
`set = "a__preserve"`
then `"a__preserve"` will preserve that a is in range -5 to 5. If you set a in STACK to 10, a in GeoGebra will be set to the nearest value, in this example a will be 5 in GeoGebra.

#### hide and show keyword

If you want to set and hide a value in GeoGebra add the __hide or __show keyword.

1. `set="a__hide"` -> set and hide
2. `set="a__show"` -> set and show

#### Multiple keywords

A__hide__fixed->set A as a fixed point and hide it.
A__fixed__hide-> keyword order is not relevant, set A as a fixed point and hide it.

-special keyword `novalue`
The GeoGebraobject value should not be set, this keyword is helpful, if you just want to hide or show something, see "multiple keywords"

set="A__hide__novalue"->hide A, but do not set A

set="A_hide_novalue" watch="A" -> watch A, but hide it and do not set it.

### Using commands inside `[[geogebra]][[/geogebra]]`

You can use the following commands inside the geogebra tag if the sub tags cant fit your task idea:

* `stack_geogebra_bind_point(args)`
* `stack_geogebra_bind_value(args)`
* `stack_geogebra_bind_value_to_remember_JSON(args)`
* `stack_geogebra_bind_point_to_remember_JSON(args)`

#### minimal example

    [[geogebra input-ref-stateStore="stateRef" set="b" watch="B"]]
    params["material_id"]="AEAVEqPy";
    params["appletOnLoad"]=function(){stack_geogebra.bind_point_to_remember_JSON(stateRef,appletObject, 'A');
    stack_geogebra.bind_value_to_remember_JSON(stateRef,appletObject, 'c')};
    [[/geogebra]]
    [[input:stateStore]]
    [[validation:stateStore]]
    [[input:B]][[validation:B]]


TODO: documentation for common app settings which can be addressed through params["nameOfSetting"] array, as shown in [https://wiki.geogebra.org/en/Reference:GeoGebra_App_Parameters](https://wiki.geogebra.org/en/Reference:GeoGebra_App_Parameters)

TODO: further documentation of the API for more complex tasks and custom named inputs

TODO: document the use of `<div style="display:none"> ... </div>` as an alternative to the hide inputs.  

TODO: does GeoGebra have a boolean type? If so, can we have an example linking a GeoGebra boolean to STACK?  If people start writing assessment code within a GeoGebra applet itself then it would be helpful to just return true/false to STACK inputs!
