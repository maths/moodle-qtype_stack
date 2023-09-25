# Authoring your first GeoGebra question

Information from GeoGebra applets can be linked to STACK inputs and then assessed by potential response trees in the normal way.  The purpose of this document is to help you author your first question using GeoGebra as an input.

Reference documentation for [GeoGebra](../Authoring/GeoGebra.md) is elsewhere.

## Before we start

This document assumes you have worked through the following sections of the Author quick-start guide.

* [Authoring quick start 1](../AbInitio/Authoring_quick_start_1.md): A basic question.
* [Authoring quick start 2](../AbInitio/Authoring_quick_start_2.md): Question variables.
* [Authoring quick start 3](../AbInitio/Authoring_quick_start_3.md): Improving feedback.
* [Authoring quick start 4](../AbInitio/Authoring_quick_start_4.md): Randomisation.

__We strongly recommend you do not use an HTML-aware editor when using GeoGebra questions.__  Instead turn off the editor within Moodle and edit the raw HTML.  Individual users can set their editor preferences by following:

    User Dashboard > Preferences > Editor preferences > Manage editors


## Question design

The goal is to create the following question.

Move the points \(A\) and \(B\) (on a GeoGebra applet) so that the line \(AB\) is perpendicular to a given line \(y=mx+c\) shown in the applet.

Notes.

* \(m\) and \(c\) are randomly generated integers by STACK.
* We should listen to the _gradient_ of the line \(AB\) as an input.
* \(A\) and \(B\) are points in the GeoGebra applet.  We need to _remember_ the position a student leaves the points in.
* The potential response tree should multiply \(m\) by the gradient of \(AB\) to check this is \(-1\).

## 1. Create the GeoGebra applet

The first step is to create a GeoGebra applet and publish it online.   You will need the material id from the URL in GeoGebra to link the worksheet to a STACK question.

1. Login to [https://www.geogebra.org/](https://www.geogebra.org/) 
2. Create a blank applet directly on the GeoGebra website.
 * Create numbers `m=2` and `c=3`.  These will be given a value by STACK when the question is started by the student.
 * Create the line `l=m*x+c`.
 * Create points A and B and a line through them.  Call this line `f`.
 * Create the new variable `ans1=Slope(f)`.   STACK will listen to the value of this variable `ans1`.
 * Hide the slope, and other object names as needed.
 * Hide the algebra window on GeoGebra, leaving only the geometry window.
3. Publish the GeoGebra file.  You will need the id from the URL.  For example, if your GeoGebra file has URL `https://www.geogebra.org/calculator/anr6ujyf` then the id is the last part `anr6ujyf`.  This code is needed for `material_id`.  E.g. `material_id:"anr6ujyf"`.


## 2. Create a minimal STACK question containing the materials

Add the following text to the STACK question where you would like the GeoGebra applet to appear.  This uses the `[[geogebra]]` block.

Set the question variables.  Initially the question variables are fixed values. Later these values will be randomly generated.

    m:2;
    c:-1;
    ta:-1/m;

Set the question text:

    [[geogebra set="c,m" watch="ans1"]]
    params["material_id"]="anr6ujyf";
    [[/geogebra]]
    Move the points \(A\) and \(B\) so that the line \(AB\) is perpendicular the line shown in the applet.
    [[input:ans1]][[validation:ans1]]

Then complete the question as follows.

1. Make sure the question text is "HTML" format (not moodle auto format)
2. Input `ans1` should have Model answer equal to `-1/m`.
3. Input `ans1` should "Forbid float" set to no/false (GeoGebra will return floating point numbers)
4. Set up the PRT with node 1 testing `ATNumAbsolute(ans1*m, -1, 0.1)`.  This checks the product of the gradient of the lines is within \(0.1\) of \(-1\) - i.e. are they close to perpendicular.  (You could opt for a strict algebraic equivalence if you prefer.)

Notes

* The tag `[[geogebra set="c,m" watch="ans1"]]` contains information about which variables within GeoGebra to set and which to watch.
* There are strict naming conventions which must be followed, e.g. names _must_ match in GeoGebra and in STACK.  This is explained in more detail in the [GeoGebra](../Authoring/GeoGebra.md) reference documentation.

## 3. Preliminary test of the STACK question

At this point you should have a working, minimal STACK question. So save and preview the question.

1. Confirm the GeoGebra worksheet shows in the question and points \(A\) and \(B\) are visible to move.
2. Confirm as you move the points that the gradient is placed into input `ans1`

## 4. Remember the positions of points \(A\) and \(B\)

While the question sets and watches values inside the applet, we also need to _remember_ the positions of \(A\) and \(B\) so these are retained when the page reloads (after check, or navigation).

You still need an input in the question to store these values. The only way STACK can store "state" is through inputs.
This input _must_ be of type "string" (because we store these values as a JSON-string internally).

We need to add in a new input `[[input:remember]][[validation:remember]]` at the end of the question text.  Verify the question text and update the form, to set up this new input as follows.

1. The `remember` input _must_ be of type string, and can not be used to calculate values in STACK feedback.
2. For the "model answer" use the empty string `""`.
3. We don't want to show the model answer of "remember" as part of the teacher's final answer (if available during the quiz) so [hide the input](../Authoring/Inputs.md#extra_option_hideanswer) from students with the STACK "extra option" `hideanswer` in the "remember" input.

## 5. Polish and tidy the question.

Once you have the question working, you can create random versions, add a worked solution and so on.  For example you could choose

```
    m:rand_with_step(2,3,1);
    c:rand_with_step(2,3,1);
```

Then add in an answer note such as `\[ y={@m*x+c@} \]`.

You should hide the inputs with CSS, e.g. `<p style="display:none">...</p>`.

For reference the full question text should now be

    [[geogebra set="c,m" watch="ans1" remember="A,B"]]
    params["material_id"]="anr6ujyf";
    [[/geogebra]]
    Move the points \(A\) and \(B\) so that the line \(AB\) is perpendicular the line shown in the applet.
    <p style="display:none">
    [[input:ans1]][[validation:ans1]]
    [[input:remember]][[validation:remember]]
    </p>


