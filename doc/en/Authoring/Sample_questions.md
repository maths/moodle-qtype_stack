# Sample Questions

STACK is distributed with sample questions in

     /qtype_stack/samplequestions/

The sample questions are in English.

We would encourage colleagues to release their materials under a creative commons licence.  Please contact the developers.

# Moodle courses released with STACK #

STACK is released with a demonstration course.

     /qtype_stack/samplequestions/STACK-demo.mbz

This contains many of the materials listed below.  You can "restore" this into your version of Moodle.  It has a number of quizzes, pre-created with questions and deployed versions.

# Materials released with STACK #

### `test_1_basic_integral.xml` ###

This is a simple, single part integral question with randomization.

### `test_2_rectangle.xml` ###

A rectangle has length 6cm greater than its width. If it has an area of 27cm\(^2\), find the dimensions of the rectangle.

This is a multi-part question which illustrates _follow-through marking_.  Get the first part wrong, but the second part correct based on the first part to see the feedback.

### `test_3_matrix.xml` ###

This question creates two random matrices and asks students to multiply them together.  See the notes on [matrices](../CAS/Matrix.md).

### `test_4_complex.xml` ###

Given a complex number \(z=ae^{ib}\) determine \(|z^{n}|\) and \(\arg(z^{n})\).  Where \(a\), \(b\) and \(n\) are randomly generated numbers.

See the [authoring quick start 3](Authoring_quick_start_3.md).

### `railways.xml` ###

In a railway journey of 90km an increase of 5 kilometers per hour in the speed decreases the time taken by 15 minutes.

This question asks students to write a system of equations (one equation per line) to represent this situation using \(v\) as the speed of the train and \(t\) as the time.  This multi-part question uses the text area input method, and systems of equations answer test.

### `tangent-line.xml` ###

This question asks students to find the line tangent to a randomly generated cubic polynomial at a point.  It is broken down into three parts to provide structure.  This randomly generated three part question has follow through marking.  Details of how to write this question are given in the the section on [multi-part mathematical questions](Authoring_quick_start_3.md).

### `cubic-spline.xml` ###

In this question a student is asked to find a cubic spline.  This question illustrates the following.

* Separate properties are established, i.e. the answer is a cubic, has the right end point values and correct derivatives at the end points.
* Plots are incorporated, including a plot of a student's incorrect answer.

### `odd-even.xml` ###

In this question a student is asked to give examples of odd and even functions.

* There are four separate parts.  Separate properties are established by each.
* For two of the parts there are non-unique correct answers.
* Feedback based on calculations of students' answers is included.

### `continuous-non-differentiable.xml` ###

In this question a student is asked to give examples of a functionwith a stationary point at x=n and which is continuous but not differentiable at x=0.

* There are three separate properties to be established.
* There is a randomly generated point.

It is interesting to discuss the extent to which the properties required by this question can be assessed automatically....

### `ODE_2nd_order_linear.xml` ###

This file contains three example questions on 2nd order linear ODEs with constant coefficients.  They illustrate the need to establish multiple independent properties, even in cases where the teacher might be tempted to _look_ at the specific answer.

# Other sources of questions #

If you have materials you would like to release, please add the details here.

### Open Educational Resources ###

The FETLAR project released a large collection of STACK 2 questions in English covering calculus and algebra
in April 2010.

* Questions to cover all parts of the [The Map of Algebraic Manipulation](http://www.mth.kcl.ac.uk/staff/ad_barnard/Pocket.pdf).
* Questions to cover all of the [Calculus Refresher](http://www.mathcentre.ac.uk/resources/exercisebooks/mathcentre/final0502-calc-ref-ukmlsc.pdf) by Dr Tony Croft and Dr Anthony Kay.

We plan to convert these into STACK 3 format and add them to the distribution in due course.

