# Sample Questions

STACK is distributed with sample questions in

     /qtype_stack/samplequestions/

The sample questions are in English.

We would encourage colleagues to release their materials under a creative commons licence.  Please contact the developers.

# Moodle courses released with STACK #

STACK is released with a demonstration course which contains hundreds of tested STACK questions.  Many have a full worked solution and random variants, and this represents a substantial resource.

     /qtype_stack/samplequestions/STACK-demo.mbz

You can "restore" this into your version of Moodle.  It has a number of quizzes, pre-created with questions and deployed variants.  It also has a large question bank, with questions not arranged into quizzes.

# Materials released with STACK #

The file `sample_questions.xml` contains the following questions.

### `test_1_basic_integral` ###

This is a simple, single part integral question with randomization.

### `test_2_rectangle` ###

A rectangle has length 6cm greater than its width. If it has an area of 27cm\(^2\), find the dimensions of the rectangle.

This is a multi-part question which illustrates _follow-through marking_.  Get the first part wrong, but the second part correct based on the first part to see the feedback.

### `test_3_matrix` ###

This question creates two random matrices and asks students to multiply them together.  See the notes on [matrices](../CAS/Matrix.md).

### `test_4_complex` ###

Given a complex number \(z=ae^{ib}\) determine \(|z^{n}|\) and \(\arg(z^{n})\).  Where \(a\), \(b\) and \(n\) are randomly generated numbers.

See the [authoring quick start 7](/AbInitio/Authoring_quick_start_7.md).

### `test_5_cubic-spline` ###

In this question a student is asked to find a cubic spline.  This question illustrates the following.

* Separate properties are established, i.e. the answer is a cubic, has the right end point values and correct derivatives at the end points.
* Plots are incorporated, including a plot of a student's incorrect answer.

### `text_6_odd_even` ###

In this question a student is asked to give examples of odd and even functions.

* There are four separate parts.  Separate properties are established by each.
* For two of the parts there are non-unique correct answers.
* Feedback based on calculations of students' answers is included.

### `text_7_solve_quadratic` ###

In this question a student is asked to solve a quadratic by working line by line.

## `ODE_2nd_order_linear.xml` ##

This file contains three example questions on 2nd order linear ODEs with constant coefficients.  They illustrate the need to establish multiple independent properties, even in cases where the teacher might be tempted to _look_ at the specific answer.

## `MCQ-sample-questions.xml` ##

This file contains two example multiple choice questions. (Not the place to start when authoring in STACK: see the authoring guide for more details.)

## `v4-syntax-samples.xml` ##

With v4.0 of STACK the CASText parts of the question gain blocks which allow the text to contain segments that are evaluated further and which may have parameters that define their behaviour.  See the specific documentation about [blocks](Question_blocks/index.md) for more details.

## Open Educational Resources ##

* The FETLAR project released a large collection of STACK questions in English covering calculus and algebra in April 2010.  These are now part of the [demonstration course](https://stack2.maths.ed.ac.uk/demo2018/).
* Questions to cover all parts of the [The Map of Algebraic Manipulation](http://www.mth.kcl.ac.uk/staff/ad_barnard/Pocket.pdf).
* Questions to cover all of the [Calculus Refresher](https://docs.stack-assessment.org/content/final0502-calc-ref-ukmlsc.pdf) by Dr Tony Croft and Dr Anthony Kay.

Abacus is a material bank for STEM education which seeks to produce, share and host high-quality educational material between collaborators.  For more information see [https://abacus.aalto.fi/](https://abacus.aalto.fi/)

## Other sources of questions ##

If you have materials you would like to release, please add the details here.

