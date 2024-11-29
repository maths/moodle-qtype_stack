# Authoring your first GeoGebra question

Information from GeoGebra applets can be linked to STACK inputs and then assessed by potential response trees in the normal way.  The purpose of this document is to help you author your first question using GeoGebra as an input.

Reference documentation for using [GeoGebra](../../Specialist_tools/GeoGebra/index.md) applets is elsewhere.

## Before we start

This document assumes you have worked through the following sections of the Author quick-start guide.

* [Authoring quick start 1](../../AbInitio/Authoring_quick_start_1.md): A basic question.
* [Authoring quick start 2](../../AbInitio/Authoring_quick_start_2.md): Question variables.
* [Authoring quick start 3](../../AbInitio/Authoring_quick_start_3.md): Improving feedback.

__We strongly recommend you do not use an HTML-aware editor when editing STACK questions containing GeoGebra.__  Instead turn off the editor within Moodle and edit the raw HTML.  Individual users can set their editor preferences by following:

    User Dashboard > Preferences > Editor preferences > Manage editors

__A note on licenses:__ Please note that the [GeoGebra's license](https://www.geogebra.org/license) does not match the [STACK licence](https://github.com/maths/moodle-qtype_stack/blob/master/COPYING.txt).  Users of STACK remain entirely responsible for complying with licenes for materials, and media embedded inside STACK questions.

## Question design

The goal is to create the following question.

Move the points \(A\) and \(B\) (on a GeoGebra applet) so that the line \(AB\) is perpendicular to a given line \(y=mx+c\) shown in the applet.

Notes.

* \(m\) and \(c\) are defined by STACK in the question variables.  Ultimately these could be randomly generated.  They illustrate how to _set_ values in an applet.
* We should listen to the _gradient_ of the line \(AB\) as an input.  This illustrates how to _watch_ a value in GeoGebra and link it to a STACK input.
* \(A\) and \(B\) are points in the GeoGebra applet.  We need to _remember_ the position a student leaves the points in.
* In this question, the potential response tree should multiply the variable \(m\) by the gradient of \(AB\) to check this is \(-1\).  We don't worry about the position of the student's line otherwise.

## 1. Create the GeoGebra applet

The first step is to create a GeoGebra applet and publish it online.   You will need the material id from the URL in GeoGebra to link the worksheet to a STACK question.

1. Login to [https://www.geogebra.org/](https://www.geogebra.org/)
2. Create a blank applet directly on the GeoGebra website.
 * Create numbers `m=2` and `c=3`.  These will be given a value by STACK when the question is started by the student, but we must set sensible initial values.
 * Create the line `l=m*x+c`.
 * Create points A and B and a line through them.  Call this line `f`.
 * Create the new variable `ans1=Slope(f)`.   We will setup the question to a STACK input will _watch_ the value of this variable `ans1`.
 * Hide the slope, and other object names as needed in the GGB applet.
 * Hide the algebra window on GeoGebra, leaving only the geometry window.
3. Save and publish the GeoGebra file.  You will need the id from the URL.  For example, if your GeoGebra file has URL `https://www.geogebra.org/calculator/anr6ujyf` then the id is the last part `anr6ujyf`.  This code is needed to link STACK to GeoGebra, for the value of `material_id`.  E.g. `params["material_id"]="anr6ujyf";` within the GeoGebra block.

## 2. Create a minimal STACK question containing the materials

Create a new question.  Set the question variables.  Initially the question variables are fixed values. Later these values will be randomly generated.

    m:2;
    c:-1;
    ta:-1/m;

Set the question text - before entering make sure that the editor is switched to html-mode.:

    [[geogebra set="c,m" watch="ans1"]]
    params["material_id"]="anr6ujyf";
    [[/geogebra]]
    <p>Move the points \(A\) and \(B\) so that the line \(AB\) is perpendicular the line shown in the applet.</p>
    [[input:ans1]][[validation:ans1]]

Notice this uses the `[[geogebra]]` question block. Then complete the question as follows.

1. Make sure the question text is "HTML" format (not Moodle auto format, or something else).
2. Input `ans1` should have Model answer equal to `-1/m`.
3. Input `ans1` should "Forbid float" set to no/false (GeoGebra will return floating point numbers)
4. Set up the PRT with node 1 testing `ATNumAbsolute(ans1*m, -1, 0.1)`.  This checks the product of the gradient of the lines is within \(0.1\) of \(-1\) - i.e. are they close to perpendicular.  (You could opt for a strict algebraic equivalence `ATAlgEquiv(ans1*m, -1)` if you prefer.)

Notes

* The tag `[[geogebra set="c,m" watch="ans1"]]` contains information about which variables within GeoGebra to _set_ and which to _watch_.
* There are strict naming conventions which must be followed, e.g. names _must_ match in GeoGebra and in STACK.  This is explained in more detail in the [GeoGebra](../../Specialist_tools/GeoGebra/index.md) reference documentation.

## 3. Preliminary test of the STACK question

At this point you should have a working, minimal STACK question. So save and preview the question.

1. Confirm the GeoGebra worksheet shows in the question and points \(A\) and \(B\) are visible to move.
2. Confirm as you move the points that the gradient is placed into input `ans1`, which at this point you can see.

## 4. Remember the positions of points \(A\) and \(B\)

While the question sets and watches values inside the applet, the Moodle quiz also needs to _remember_ the positions the student left \(A\) and \(B\) in so that these are retained when the page reloads (after check, or navigation).

For this, add a tag remember with both points \(A\) and \(B\) to the block heading:

    [[geogebra set="c,m" watch="ans1" remember="A,B"]]
    params["material_id"]="anr6ujyf"; 
    [[/geogebra]] 
    <p>Move the points \(A\) and \(B\) so that the line \(AB\) is perpendicular the line shown in the applet.</p>
    [[input:ans1]][[validation:ans1]]
    [[input:remember]][[validation:remember]]
    

Notice two changes.  (1) there is a `remember` argument in the geogebra block tag and (2) there is a new input in the question.  To actually store the coordinates of \(A\) and \(B\), we need to add in a new input `[[input:remember]][[validation:remember]]` at the end of the question text.  Verify the question text and update the form, to set up this new input as follows.

1. The `remember` input _must_ be of type string.
2. For the "model answer" use the empty string `""`.
3. We don't want to show the model answer of "remember" as part of the teacher's final answer (if available during the quiz) so [hide the input](../../Authoring/Inputs/Input_options.md#extra_option_hideanswer) from students with the STACK "extra option" `hideanswer` in the "remember" input.
4. Values in _remember_ are not available to the PRT and can not be used to calculate values in STACK feedback.

## 5. Polish and tidy the question.

Once you have the question working, you can add better feedback, add a worked solution, create random versions, and so on.  For example you could choose

```
    m:rand_with_step(2,3,1);
    c:rand_with_step(2,3,1);
```

Then add in an answer note such as `\[ y={@m*x+c@} \]`.  [Authoring quick start 4](../../AbInitio/Authoring_quick_start_4.md) provides advice on randomisation.

You could also have an additional algebraic input asking the student to find the equation of their line.  At this point there are lots of options for combining a geometric diagram within a larger question.

You should hide the inputs from students with CSS after testing, e.g. `<span hidden="">...</span>`.

For reference the full question text should now be

    [[geogebra set="c,m" watch="ans1" remember="A,B"]]
    params["material_id"]="anr6ujyf"; 
    [[/geogebra]] 
    <p>Move the points \(A\) and \(B\) so that the line \(AB\) is perpendicular the line shown in the applet.</p>
    <span hidden="">
    [[input:ans1]][[validation:ans1]]
    [[input:remember]][[validation:remember]]
    </span>


