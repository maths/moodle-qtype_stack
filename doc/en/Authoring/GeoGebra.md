# GeoGebra in STACK
Author Tim Lutz - University of Edinburgh and University of Education Heidelberg

This page documents use of GeoGebra to display visuals.

STACK supports inclusion of dynamic graphs using GeoGebra: [https://geogebra.org](https://geogebra.org).

## Before we start: Check Editor preferences

Note, we strongly recommend you do not use an HTML-aware editor when using GeoGebra questions.  Instead turn off the editor within Moodle and edit the raw HTML.

    Site administration > Plugins > Text editors > Manage editors

Individual users can also set their editor preferences:

    User Dashboard > Preferences > Editor preferences > Manage editors

## Find the material_id

To include a GeoGebra applet into a STACK question, first create or search for an existing GeoGebra applet at geogebra.org. The easiest way to use GeoGebra in STACK is to use an applet at geogebra.org. The applet must be publicly avaiable through a url. To set things up we need the material-id of an applet, we want to show.
    for the material [https://www.geogebra.org/m/seehz3km](https://www.geogebra.org/m/seehz3km) the material_id is: seehz3km
    
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
assumes: Points will be set free to manipulate, unless you add '__fixed' to the Point-name
angles can not be set directly, set points instead

### minimal example

question variables:

A:[2,3]
B:[1,2]
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

## Using commands inside [[geogebra]][[/geogebra]]
You can use the following commands inside the geogebra tag if the sub tags cant fit your task idea:
stack_geogebra_bind_point(args)
stack_geogebra_bind_value(args)
stack geogebra_bind_value_to_remember_JSON(args)
stack geogebra_bind_point_to_remember_JSON(args)
###minimal example
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
