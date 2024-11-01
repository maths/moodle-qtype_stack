# GeoGebra question block

GeoGebra blocks are included with the `[[GeoGebra ...]]` block.  This page provides reference documentation of all features of that block.

## Adding a GeoGebra question block using the `material_id`

To use GeoGebra in STACK an applet must be hosted at [geogebra.org](https://geogebra.org) and the applet must be publicly available through a url. 

To include a GeoGebra applet into a STACK castext field (e.g. the question text), first create or search for an existing GeoGebra applet at geogebra.org. To set things up we need the `material_id` of an applet, we want to show.

For the material [https://www.geogebra.org/m/seehz3km](https://www.geogebra.org/m/seehz3km) the `material_id` is: `seehz3km`

An example `[[geogebra]]` [question block](../../Authoring/Question_blocks/index.md) is shown below.

    <p>You can show an applet:</p>
    [[geogebra]]
    params["material_id"]="seehz3km";
    [[/geogebra]]

This illustrates how the material_id is used.

## Control the size of the applet

There are two places where the size of the applet can be defined:

Within the block, adding values to the GeoGebra parameters width and height will define the section of the applet that is to be shown.

Within the block's header, adding values to the iframe parameters width and height will enlarge or reduce the size of the applet, or even distort it.

```
[[geogebra height="100px" width="175px"]]
params["material_id"]="seehz3km";
params["height"]=200;
params["width"]=350;
[[/geogebra]]
```
In the block's head, `width="80%" aspect-ratio="2/3"` could be used instead to define relative sizes and possible distortions if needed.

If no size is defined the default is to have `width="500px" height="400px"` and these are also the dimensions used if values are missing and no aspect-ratio has been defined.

## Using the sub-tags "set", "watch" and "remember"

The "set", "watch" and "remember" tags to the `[[geogebra]]` question block link Maxima values to GeoGebra objects in various ways.

* "set" will set a GeoGebra object, point or value to a STACK-calculated value.
* "watch" enables a STACK input to listen to values and points in GeoGebra.
* "remember" is needed when you do not want to calculate feedback with some of the GeoGebra objects in an applet, but you do want to be able to save and restore the state of an applet when the student returns to the question later.

### Naming conventions

To be able to make things consistent and easy for question authors, the following name conventions _must_ be followed:

1. Names of variables must be equal in both STACK and GeoGebra.  However, no explicit checking is done.
2. Value-names must start with lower case letters.
3. Values must be `int` or `float` STACK variables.
4. Angles are used like values, and so must be named lowercase letters in Latin-Alphabet, (not Greek unicode letters!) and values must be in radians.  (If you want to show a Greek letter to the student, then have a parallel internal variable which is used by STACK.  E.g. call the angle \(\alpha\) visibly in GGB, but have a hidden GGB variable \(a\) which you can watch.)
5. Point-names must start with upper case letters.
6. Points are represented as a list in STACK.  For example `D:[2,3]`, means a point \(D\) with \(x=2, y=3\).  (While STACK has an inert `ntuple` command which can be used for representing and displaying coordinates, we have chosen to use lists in this design.)

The value of the `set` parameter must be a string of (unique), comma-separated, GeoGebra-objects with latin names.
For example, the sub-tag value could look like:  `set = "A,B,C,D,a2,E__fixed"` and would be placed in the block as

    [[geogebra set = "A,B,C,D,a2,E__fixed"]]
    [[/geogebra]]

## Using the "set" sub-tag

With the "set" sub-tag you can set a GeoGebra object (currently a point or a value) to a STACK-calculated value when the applet is first loaded.

By default points are free to manipulate in the applet, unless you add `__fixed` or other double-underscore-tags to the Point-name. A full list of available options see "set: double-underscore-tags in the "advanced use-cases"-section.

Notes

1. No checking is done that the object in STACK matches one in GeoGebra.  If it does not exist it will be created by GeoGebra.
2. Currently setting points and values are the only supported objects.  Users can set objects, e.g. you could define `g:x^3` and set this in an applet. 


3. Angles cannot be set directly, set points instead!

### A minimal example question with "set": can a student read (randomly) generated coordinates?

Set the question variables:

    A:[2,3];
    B:[1,2];

Set the question text:

    [[geogebra set="A,B"]]
    params["material_id"]="seehz3km";
    [[/geogebra]]
    Write the coordinates of \(A\): [[input:ans1]][[validation:ans1]]

Then complete the question as follows.

1. In the input, make the model answer `A`.  This is a list.
2. Make sure you set "forbid floats" option in the input to be false, if you want to!
3. Complete the default potential response tree `prt1` as `ATAlgEquiv(ntupleify(ans1),ntupleify(A))`

This should give a minimal working GGB question with "set".

The use of the STACK function `ntupleify` ensures both the student's answer and teacher's answer is converted from a list to an `ntuple`.  STACK defines `ntuple` as data type allowing an "n-tuple" such as \( (1,2) \) to be a different data type from a "list" \( [1,2] \).  Internally in STACK/GGB lists are given preference in the design, but completing the PRT as above will allow student input of coordinates using traditional round brackets, which is interpreted by STACK as a data type `ntuple`.  See the docs on [sets, lists, sequences and n-typles](../../CAS/Maxima_background.md#sets-lists-sequences-n-tuples).

The question can readily be adapted by making `A` a randomly generated object, if required.

## Using the "watch" sub-tag 

With the "watch" sub-tag someone can listen to values and points in GeoGebra. These values can then be used to calculate feedback in STACK.  The values will be assigned to a STACK input.  Note, inputs can be "hidden" from the student.

General notes for watched objects

1. Points, e.g. `A`, _must_ be an algebraic-input and you _must_ allow floats!
2. You can access `A` in STACK for feedback as a list of values for points `A[1]->x-value`, `A[2]->y-value`
3. Numbers/angles e.g. `b` can be an algebraic or numerical input, and you _must_ allow floats!
4. You can access `b` in STACK as value. If `b` represents an angle then `b` is in radians.

### Minimal example watching point A.

Set the question variables to be

    ta1:[2,3];

Set the question text:

    [[geogebra watch="A"]]
    params["material_id"]="seehz3km";
    [[/geogebra]]
    Move \(A\) to be the point \({@ntupleify(ta1)@}\)
    [[input:A]][[validation:A]]

Recall that since the object in `watch="A"` is written in upper case it must be a point.

Then complete the question as follows.

1. The question expects an input `A`.  In this input, make the model answer `ta1`.  This is a list, and has a different name from the watched point..
2. Make sure you set "forbid floats" option in the input to be false, if you want to!
3. Complete the default potential response tree `prt1` as `ATAlgEquiv(ntupleify(A), ntupleify(ta1))`

Once the question is working you can hide the inputs from students, but for testing it is helpful to see the input boxes.

1. Hide an input with CSS, e.g. `<p style="display:none">[[input:A]] [[validation:A]]</p>` (but probably not while you develop the question!)
2. Turn off the validation ("Show the validation") and verification ("Student must verify") of the input field. 

Extensions to this basic question:

1. The question can readily be adapted by making `ta1` a randomly generated object, if required.
2. The answer test requires _exact_ positioning of point `A` on the required coordinates.  In this GGB sheet we have "snap to grid" so it is reasonable to ask for exact positioning of the point `A` in this case.  An alternative approximate positioning \( ||A-ta1||<0.1 \) can be established using the Num-GT answer test: `ATGT(0.1, Distance(A,ta1))`.  STACK provides a number of [geometry related maxima functions](../../CAS/Geometry.md), including `Distance` which is used here.


## Using the "remember" sub-tag

If you do not want to calculate feedback with some of the GeoGebra objects in an applet, but you do want to be able to save and restore the state of an applet, you can use the "remember" tag.

You still need an input in the question to store these values. The only way STACK can store "state" is through inputs.
This input _must_ be of type "string" (because we store these values as a JSON-string internally).
The name "remember" is hard-wired (for now).

### Minimal example remember B,C

In the above example (watch), we want to remember the positions of \(B\) and \(C\)

Set the question text:

    [[geogebra watch="A" remember="B,C"]]
    params["material_id"]="seehz3km";
    [[/geogebra]]
    Move \(A\) to be the point \({@ntupleify(ta1)@}\)
    [[input:A]][[validation:A]]
    [[input:remember]][[validation:remember]]

1. The `remember` input _must_ be of type string, and can not be used to calculate values in STACK feedback.
2. For the "model answer" use the empty string `""`.
3. The name "remember" is hard-wired (in this version).
4. We don't want to show the model answer of "remember" as part of the teacher's final answer (if available during the quiz) so [hide the input](../../Authoring/Inputs/Input_options.md#extra_option_hideanswer) from students with the STACK "extra option" `hideanswer` in the "remember" input.
5. Once working, hide the "remember" input with CSS, e.g. `<p style="display:none">[[input:remember]][[validation:remember]]</p>` (but probably not while you develop the question!)

### Minimal example watching an indirect GGB object, e.g. angle k.

In the above example we have angle \(k\).  To watch this value we can add `k` to the list of watched variables.  E.g.

    [[geogebra watch="A,k" remember="B,C"]]
    params["material_id"]="seehz3km";
    [[/geogebra]]
    Move \(A\) to be the point \({@ntupleify(ta1)@}\), and points \(B,C\) so that there is a right angle at \(B\).
    [[input:A]][[validation:A]]
    [[input:k]][[validation:k]]
    [[input:remember]][[validation:remember]]

1. Numbers/angles e.g. input `k` can be an algebraic or numerical input, and you _must_ allow floats!
2. The value of \(k\) will come through as a float.  Hence, you need to check if this is sufficiently close to \(\pi/2\) with a numerical test.  You could add the test `ATNumAbsolute(k,%pi/2,0.01)` to check \(|k-\pi/2|<0.01\) as a check the angle is right.

An alternative would be to check this in GeoGebra and create a variable with a value of \(0\) or \(1\), and watch this proxy variable.  The advantage of a numerical test is that you could give feedback which includes the angle.

   Your angle is {@round(k*180/%pi)@} degrees, which is not a right angle!

### Example: using some advanced features.

This example illustrates some of the advanced features 

Set the question variables to be

    A:[-2,0];
    B:[1,0];

Set the question text to be

    [[geogebra set="A,B" watch="a,b" remember="P"]]
    params["material_id"]="rukrpcs5";
    [[/geogebra]]
    Move \(P\) so that the angle \(\alpha\) is a right angle.
    [[input:a]][[validation:a]]
    [[input:b]][[validation:b]]
    [[input:remember]][[validation:remember]]

Notes

1. This GGB sheet has a variable `a` (hidden) which stores the angle \(\alpha\) in a way STACK can access the Greek letter.
2. This GGB sheet has a boolean variable `b`.  This will comes through to STACK as a number, \(0\) or \(1\).
3. The use of `"remember"` means we need an string input to store the state of `P` between attempts.
   

# Advanced use-cases

## set: double-underscore-tags

The set sub-tag allows more control over setting objects using double-underscore-tags.

### "fixed" keyword

Using this keyword stops users from moving the point in GeoGebra.  E.g. `set = "A__fixed"`.

### "preserve" keyword

If you want to preserve GeoGebra definitions of points or values when setting them add the `__preserve` keyword to that object.  For example, if a point \(P\) lies on a circle then setting `P__preserve` keeps the point on the circle.

##### Common example: "Points on objects"

For example, assume "A" is a GeoGebra-Point on an object, like \(A\) is a point on the circle \(B\).
When you set A like: `set ="A"`, the definition of \(A\) will be overwritten by default.
When you set A like: `set="A__preserve"` then `"A__preserve"` will preserve that A is a Point on B and tries to set A near to your STACK variable A but on the circle.

##### Common example: "Sliders"

"a" should be a GeoGebra-value controlled by a slider ranging from -5 to 5.
When you set a like: `set = "a"`, the definition of a will be overwritten, e.g. if the STACK variable a is 10, after initialization a is 10 in GeoGebra.
When you set a like: `set = "a__preserve"` then `"a__preserve"` will preserve that a is in range -5 to 5. If you set a in STACK to 10, a in GeoGebra will be set to the nearest value, in this example a will be 5 in GeoGebra.

### Hide and show keyword

If you want to set and hide a value in GeoGebra add the __hide or __show keyword.

1. `set="a__hide"` -> set and hide
2. `set="a__show"` -> set and show

### Multiple keywords

It is possible to use multiple keywords.  E.g. both

* `A__hide__fixed`
* `A__fixed__hide`

will set \(A\) as a fixed point and hide it. Keyword order is not relevant.

### Special keyword `novalue`

The GeoGebra object value should not be set, this keyword is helpful, if you just want to hide or show something, see "multiple keywords".

* `set="A__hide__novalue"` Hide A but do not set the value for \(A\)
* `set="A__hide__novalue"` watch="A"` Watch \(A\), but hide it and do not set it.

## Using commands inside `[[geogebra]][[/geogebra]]` blocks

You can use the following commands inside the geogebra tag if the sub tags do not fit your task idea:

* `stack_geogebra_bind_point(args)`
* `stack_geogebra_bind_value(args)`
* `stack_geogebra_bind_value_to_remember_JSON(args)`
* `stack_geogebra_bind_point_to_remember_JSON(args)`

#### Example of using commands inside `[[geogebra]][[/geogebra]]` blocks

    [[geogebra input-ref-stateStore="stateRef" set="b" watch="B"]]
    params["material_id"]="AEAVEqPy";
    params["appletOnLoad"]=function(){stack_geogebra.bind_point_to_remember_JSON(stateRef,appletObject, 'A');
    stack_geogebra.bind_value_to_remember_JSON(stateRef,appletObject, 'c')};
    [[/geogebra]]
    [[input:stateStore]]
    [[validation:stateStore]]
    [[input:B]][[validation:B]]

Advanced users might want to look at documentation for common app settings which can be addressed through params["nameOfSetting"] array, as shown in [https://wiki.geogebra.org/en/Reference:GeoGebra_App_Parameters](https://wiki.geogebra.org/en/Reference:GeoGebra_App_Parameters).

#### Example of using commands inside `[[geogebra]][[/geogebra]]` blocks to define geogbra objects

It is possible to update existing objects, or indeed to create new objects inside GeoGebra.  An example is below.

     I want to display the function \(x^3\) in GeoGebra
     [[geogebra]]
     params["appletOnLoad"]=function(){appletObject.evalCommand("f(x):=x^3")}; 
     [[/geogebra]]
     [[input:ans1]] [[validation:ans1]]

Please note that the STACK and GeoGebra syntax do not match perfectly.  For example, `g:%pi*x^3` will throw an error in GeoGebra because `%pi` in Maxima does not match `pi` in GeoGebra.  (Functionality to match syntax may be supported in the future but matching maxima syntax to GeoGebra syntax is a lot of work.)
### Future plans

1. GeoGebra boolean types should come through to STACK as just return true/false (not 0,1).
2. Suppport set/watch of more complex objects.

### Disclaimer

The creation of these resources has been (partially) funded by the ERASMUS+ grant program of the European Union under grant No. 2021-1-DE01-KA220-HED-000032031. Neither the European Commission nor the project's national funding agency DAAD are responsible for the content or liable for any losses or damage resulting of the use of these resources.

