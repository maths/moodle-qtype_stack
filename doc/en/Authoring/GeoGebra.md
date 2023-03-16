# GeoGebra in STACK
Author Tim Lutz - University of Edinburgh and University of Education Heidelberg, 2022-23.

STACK supports inclusion of dynamic graphs using GeoGebra: [https://geogebra.org](https://geogebra.org).  This page documents how to use GeoGebra applets to display visuals, and as a STACK input.


## Before we start: Check Editor preferences

Note, we strongly recommend you do not use an HTML-aware editor when using GeoGebra questions.  Instead turn off the editor within Moodle and edit the raw HTML.

    Site administration > Plugins > Text editors > Manage editors

Individual users can also set their editor preferences:

    User Dashboard > Preferences > Editor preferences > Manage editors

## Find the material_id

To include a GeoGebra applet into a STACK question, first create or search for an existing GeoGebra applet at geogebra.org. The easiest way to use GeoGebra in STACK is to use an applet at geogebra.org. The applet must be publicly avaiable through a url. To set things up we need the material-id of an applet, we want to show.

For the material [https://www.geogebra.org/m/seehz3km](https://www.geogebra.org/m/seehz3km) the material_id is: `seehz3km`

## Add the material_id to a new STACK task

Then include the following question text, which includes a simple `[[geogebra]]` [block](Question_blocks.md).

    <p>You can show an applet:</p>
    [[geogebra]]
    params["material_id"]="seehz3km";
    [[/geogebra]]
## Using the sub-tags "set", "watch" and "remember"

### naming conventions

To be able to make things easy for task authors, name conventions must be holded.

sub tag value is a string of (unique) comma-separated GeoGebra-objects with latin names.
sub tag value could look like: 
set = "A,B,C,D,a2,E__fixed"
and would be placed like

    [[geogebra set = "A,B,C,D,a2,E__fixed"]]
    [[/geogebra]]

assumes: names of variables are equal in STACK and GeoGebra
assumes: values are int or float STACK variables
assumes: points are represented as array in STACK like: 
[2,3] means point with x=2, y=3) maxima object would be for point D e.g. D:[2,3]

assumes: value-names are starting with lower case letters (angles are used like values, must be named lowercase Latin-Alphabet, values are radian values)
assumes: Point-names are starting with upper case letters

## Using the "set" sub-tag
With the "set" sub-tag you can set a GeoGebra object point or value to a STACK-calculated value
assumes: Points will be set free to manipulate, unless you add '__fixed' or other double-underscore-tags to the Point-name. A full list of available options see "set: double-underscore-tags in the "advanced use-cases"-section.
angles can not be set directly, set points instead

### minimal example

question variables:

    A:[2,3];
    B:[1,2];

question text:

    [[geogebra set="A,B"]]
    params["material_id"]="seehz3km";
    [[/geogebra]]

Write the coordinates of A:

    [[input:ans1]][[validation:ans1]]

## Using the "watch" sub-tag 
With the "watch" sub-tag someone can listen to values and points in GeoGebra. These values can then be used to calculate feedback in STACK

### minimal example watching point A and value or angle b

question variables:

question text:

    [[geogebra watch="A,b"]]
    params["material_id"]="seehz3km";
    [[/geogebra]]
    [[input:A]][[validation:A]]
    [[input:b]][[validation:b]]

A must be an algebraic-input to allow floats.
You can access A in feedback as a list of values for points A[0]->x-value, A[1]->y-value
You can access b as value. if b represents an angle, b is in radians.

## Using the "remember" sub-tag
if you do not want to calculate feedback with some of the GeoGebra objects in an applet, but you do want to be able to save and restore the state of an applet, you can use the "remember" tag

### minimal example remember A,B,C

question variables:

question text:

    [[geogebra remember="A,B,C"]]
    params["material_id"]="seehz3km";
    [[/geogebra]]
    [[input:remember]][[validation:remember]]

remember input must be of type string, and can not be used to calculate values in STACK feedback for now. remember is for easy restoring purposes.

## Advanced use-cases

### set: double-underscore-tags

The set sub-tag allows more control over setting objects:

#### "preserve" keyword

If you want to preserve GeoGebra definitions of points or values when setting them add the __preserve keyword to that object.

##### Common example: "Points on objects"

"A" should be a GeoGebra-Point on an object, like A is a point on the circle B
When you set A like:
set ="A", the definition of A will be overwritten by default.
When you set A like:
set="A__preserve"
"A__preserve" will preserve that A is a Point on B and tries to set A near to your STACK variable A but on the circle.

##### Common example: "Sliders"

"a" should be a GeoGebra-value controlled by a slider ranging from -5 to 5
When you set a like:
set = "a", the definition of a will be overwritten, e.g. if the STACK variable a is 10, after initialization a is 10 in GeoGebra.
When you set a like:
set = "a__preserve"
"a__preserve" will preserve that a is in range -5 to 5. If you set a in STACK to 10, a in GeoGebra will be set to the nearest value, in this example a will be 5 in GeoGebra.

#### hide and show keyword

If you want to set and hide a value add the __hide or __show keyword
set="a__hide" -> set and hide
set="a__show" -> set and show


#### Multiple keywords

A__hide__fixed->set A as a fixed point and hide it.
A__fixed__hide-> keyword order is not relevant, set A as a fixed point and hide it.

-special keyword novalue
the GeoGebraobject value should not be set, this keyword is helpful, if you just want to hide or show something, see "multiple keywords"

set="A__hide__novalue"->hide A, but do not set A

set="A_hide_novalue" watch="A" -> watch A, but hide it and do not set it.

### Using commands inside `[[geogebra]][[/geogebra]]`

You can use the following commands inside the geogebra tag if the sub tags cant fit your task idea:

* `stack_geogebra_bind_point(args)`
* `stack_geogebra_bind_value(args)`
* `stack geogebra_bind_value_to_remember_JSON(args)`
* `stack geogebra_bind_point_to_remember_JSON(args)`

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
