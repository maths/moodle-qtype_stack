# Sample Questions

NOT UPDATED FOR VERSION 3.

STACK is distributed with sample questions.  These are kept 
under the language files.   There are currently sample questions in English and Dutch. 

We would encourage colleagues to consider releasing their 
materials under a suitable creative commons licence. 

# Materials released with STACK #

## Test questions ##

The following questions are distributed with the STACK source 
code in 

    lang/en/sample_questions/

### test_0.xml ###

A single, trivial "What is \(1+1\)?" question, mostly used for testing of the simplest item.

### test_1.xml ###

A randomly generated integration question.  Note the automatically generated feedback from the CAS, i.e. get it wrong. A worked solution is provided, which also reflects the random numbers in the question.

Also note that the question comes with [Testing](testing) for quality control,
so when you "try" the question as a teacher you do not need to try "obvious" things the student might do,
for example differentiation rather than integration.  These are included at the bottom of the teacher's page.

### test_2.xml ###

This is the simplest multi-part question.
There are multiple [inputs](Inputs.md) in an equation with one potential response tree.

### test_3.xml ###

This is another simple multi-part question.  Here there are four [inputs](Inputs.md) each with its own potential response tree.
Note also in this item that 'properties' of the student's answer are being established, not just "a correct answer".

### test_4.xml ###

This item demonstrates dynamically generated [Maxima](../CAS/Maxima.md) in the question, feedback and worked solution.
Note that if a wrong answer is provided the feedback plot is generated to include a plot of the student's answer.

### test_5.xml ###

This is a complex multi-part item, which is randomly generated with a full worked solution.  The question has follow through marking.  Note carefully the phrasing of part 2.  i.e. "solve your equation". 

The mark scheme actually takes the equation used in the first part (whether correct or not) and solves it, so the student can get marks for this part regardless of whether they get the first part right.

Is this sensible?  Maybe not, but this question demonstrates this feature.  

### test_6.xml ###

Demonstrates a simple matrix question, with the grid [inputs](Inputs.md) within a displayed equation.
Notes on how Maxima deals with matrices are given in [Maxima](../CAS/Maxima.md).

### test_7.xml ###

This question asks for the general solution to a second order linear differential equation with constant coefficients.
This question has a more complicated way of establishing if the answer is "correct", i.e. the answer must

* satisfy the equation, establish by substituting the answer into the equation
* be a linear combination of all the solutions
* be sufficiently general.

This is not so easy to establish and is certainly not a matter of just looking for the answer \(Ae^t+Be^{5t}\).

### test_8.xml ###

A simple question asking for complex numbers, testing roots of unity.

### test_9.xml] ###

This question is from slightly more advanced topics, involving match up part to create a smooth function.
Feedback is given as a dynamically generated plot.

### test_10.xml ###

(version 2.2 onwards) This is a trivial question, but it includes a slider bar to illustrate the implementation of [confidence testing](../Diagnostics/Confidence_testing).

### test-11.xml ###

This question demonstrates how to include external images into a STACK question,
in particular using the dynamically generated [Google charts](http://code.google.com/apis/chart/).
This is a powerful feature which out-sources the production of graphics to another web service.

### input_test.xml ###

This file contains questions which demonstrate the range of [inputs](Inputs.md) available to question authors.   These are not serious questions, but just examples.

# Other sources of questions #

If you have materials you would like to release, please add the details here.

### Open Educational Resources ###

The FETLAR project released a large collection of STACK questions in English covering calculus and algebra
in April 2010.  These can be found in

    lang/en/sample_questions/oer/

* Questions to cover all parts of the [The Map of Algebraic Manipulation](http://www.mth.kcl.ac.uk/staff/ad_barnard/Pocket.pdf).  You can try these questions via the [worksheet interface](http://stack.bham.ac.uk/worksheets/index.php). 
* Questions to cover all of the [Calculus Refresher](http://www.mathcentre.ac.uk/resources/exercisebooks/mathcentre/final0502-calc-ref-ukmlsc.pdf) by Dr Tony Croft and Dr Anthony Kay.


