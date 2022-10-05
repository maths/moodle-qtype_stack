# Authoring a question using GeoGebra.

__Note, this document describes prototype features developed as part of the AuthOMath project.  The associated code is not yet public.__

The goal is to create the following question.

1. Move the points \(A\) and \(B\) (on the GeoGebra applet) so that the line \(AB\) is perpendicular to the line \(y=mx+c\).
2. If the given line has equation \(y=mx+c\) then what is the value of the gradient?

Notes.

* This has two related inputs. The first is a GeoGebra input, the second a normal algebraic input.
* \(m\) and \(c\) are randomly generated integers by STACK.
* \(A\) and \(B\) are in the GeoGebra applet.
* We should listen to the _gradient_ of the line \(AB\).
* The potential response tree should multiply \(m\) by the gradient of \(AB\) to check this is \(-1\).

## Create a minimum working STACK question.

1. Start to write a STACK question, with a sensible name.  For the question variables choose the following.

```
    m:rand_with_step(2,3,1);
    c:rand_with_step(2,3,1);
```

2. For the question text start with.

```
   (a) Move the two points \(A\) and \(B\) so that the line \(AB\) is perpendicular to the given line.

   (b) If the given line has equation {@y=m*x+c@} then what is the value of the gradient?
   [[input:ans1]] [[validation:ans1]]
```

3.  The question has random variants, so we need a non-trivial question note, e.g. `{@y=m*x+c@}`.

4. For input `ans1` (the default) just use `-1/m` as the correct answer.

5. In the potential response tree set up a single node with `sans:ans1*m` and `tans:-1`. Algebraic equvalence is fine.

Now save the question - we have a minimum working STACK question.  The next task is to create, add the GeoGebra applet to the question, and get the two connected up.

## Create the GeoGebra applet

1. Login to [https://www.geogebra.org/](https://www.geogebra.org/) 

1. Create the applet in GeoGebra.  This needs to have the following.
 * Create numbers `m=2` and `c=3`.  These will be given a value by STACK when the question is started by the student.
 * Create the line `l=m*x+c`.
 * Create points A and B and a line through them.  Call this line `f`.
 * Create the new variable `m2=Slope(f)`.  STACK will listen to the value of this variable `m2`.
 * Create the new _point_ `M2=(Slope(f),0)`.  For now STACK can only listen to the value of points.  (TODO: listen to numbers as well)
 * Hide the slope, and other object names as needed.
2. Publish the GeoGebra file.  You will need the id from the URL.  For example, if your GeoGebra file has URL `https://www.geogebra.org/calculator/qwnduskv` then the id is the last part `qwnduskv`.  This code is needed for `material_id`.  E.g. `material_id:"qwnduskv"`.

## Link the GeoGebra applet to the STACK question

Add the following text to the STACK question where you would like the GeoGebra applet to appear.  This uses the `[[geogebra]]` block.


```
[[geogebra input-ref-M2 = "M2"]]
var params = {
material_id: "qwnduskv",
"id": "applet",
"width": 800,
"height": 600,
"appName": "classic",
"showToolBar": true,
"showAlgebraInput": true,
"showMenuBar": true,
"appletOnLoad": function() {
  var appletObject = applet.getAppletObject();
  appletObject.evalCommand('m= {#m#}');
  appletObject.evalCommand('c= {#c#}');
  stack_geogebra.bind_point(M2, appletObject, "M2"); 
  }
};
[[/geogebra]]
[[input:M2]] [[validation:M2]]
```

Save and then set the following options on the (new) input `M2`.

* Input type: algebraic
* Model answer: `null`
* Check the type of the response: No
* Student must verify: No
* Show the validation: Yes (for debugging, then No).

At this point it is sensible to save and preview the question.

1. The applet should pick up the random values of \(m\) and \(c\).
2. As \(A\) and \(B\) are moved the STACK input M2 should update to hold the gradient of the line joining \(AB\).

## Use the GeoGebra applet values in the STACK PRT

1. Add in a new PRT `[[feedback:prt0]]` just after the validation feedback for input `M2`.  We could use the existing PRT, of course, but adding a new PRT makes more sense in this question and ensures they are independent.
2. Save and continue editing, then fill in the PRT values as follows.  `sans:M2[1]*m`, `tans:-1`.   Start with Algebraic equvalence.  Since we have integer values for \(m\) and \(c\) , we can expect the student to lock \(A\) and \(B\) to a grid and get a precise perpendicular.  If approximations are acceptible, then a [numerical test](Answer_tests_numerical.md), e.g. `ATNumRel` with an option of `0.05` to give a \(5\%\) tollerance.

## Tidy away debugging/development settings

In its current form the question reveals a lot of internal information.  Once the question is working, make the following changes.

1. In the input `M2` set show the validation: No.
2. Hide the input with CSS, e.g. `<p style="display:none">[[input:M2]] [[validation:M2]]</p>` 
3. In the input `M2` set extra options to contain `hideanswer` to suppress the input in the list of teacher's answers.
4. In the applet settings modify as follows.

```
"showToolBar": false,
"showAlgebraInput": false,
"showMenuBar": false,
```


## Follow up (developer notes)

TODO.

1. Make sure we can listen to a variable, not just the coordinates of a point.

2. We need to bind the values of A & B to the state as well.  This is needed when the page reloads.  Do we need a separate `stateRef` input here?

3. What happens if the student creates a perpendicular line, not a line perpendicular to `l`.  What does GeoGebra send back and how can we deal with this?!

4. In the above example `M2` needs to be written four times to bind the object `M2` in GeoGebra to an input.  Can we simplify this?
