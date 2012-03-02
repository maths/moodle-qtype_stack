# Multipart mathematical questions

## Introduction ##

To illustrate multi-part mathematical questions we introduce three examples.

### Example 1 ###

Consider the following sequence of traditional practice problems.

Draw the graphs of

1. \(y=x^2\)
2. \(y=-x^2\)
3. \(y=2x^2\)
4. \(y=x^2+2.5\)
5. \(y=(x-1)^2\)
6. \(y=(x+2)^2+1\)
7. \(y=x^2+4x+6\)
8. \(y=x^2-3x+1\)
9. Write out a general statement of the difference between the graphs of \(y=x^2\) and of \(y=\pm a((x-b)^2+c)\).

Certainly we could treat each of questions 1. to 9. as individual items. But

* What is the value of question 4. individually?
* How would we describe it?
* How do we ensure a random version "still fits"?

It only makes sense in the context of the sequence which is highly structured.
To create very large numbers of separate items is characteristic of mathematics CAA,
but this inevitably leads to very large question banks, with the associated problems
of describing questions, cataloguing, and meta-data. These simply become unmanageable
to all except, at best, the original author. If we want to create random versions, we
need to preserve this structure. This can only be done if random numbers affect more
than one of the individual items shown. Hence, it seems to make sense to treat Figure 1
as one item with separately marked sub-questions.

(Note we are not claiming that this question could be assessed by STACK at present.)

### Example 2 ###

Consider the following question, asked to relatively young school students.

Expand \((x+1)(x+2)\).

In the context it is to be used it is appropriate to provide students with the
opportunity to "fill in the blanks", in the following equation.

	(x+1)(x+2) = [¯] x2 + [¯] x + [¯].
	
We argue this is really "one question" with "three inputs".
Furthermore, it is likely that the teacher will want the student to complete all boxes
before any feedback is assigned, even if separate feedback is generated for each input
(i.e. coefficient). This feedback should all be grouped in one place on the screen. Furthermore,
in order to identify the possible causes of algebraic mistakes, an automatic marking procedure
will require all coefficient simultaneously. It is not satisfactory to have three totally
independent marking procedures.

These two examples illustrate two extreme positions.

1. All inputs within a single multi-part item can be assessed independently.
2. All inputs within a single multi-part item must be completed before the item can be scored.

Devising multi-part questions which satisfy these two extreme positions would be relatively straightforward.
However, it is more common to have multi-part questions which are between these extremes.
The STACK datastructure implements various options.

### Example 3 ###

The position \(t_0\) of a particle moving along a coordinate line is \(s=10\cos(t+90^o)\).

1. What is the particle's starting position \((t=0)\)?
2. What are the points farthest to the left and right of the origin reached by this particle?
3. Find the particle's velocity and acceleration at the points in (2.)
4. When does the particle first reach the origin? What are its velocity, speed and acceleration then?

Since all four parts refer to one equation, if randomly generated questions are being used then each
of these parts needs to reference a single randomly generated equation. Hence parts 1.-4. really form
one item. Notice here that part 1. is independent of the others. Part 2. requires two inputs which
are independent of each other. Part 3. depends on a response to part 2. Notice also that the teacher may
choose to award "follow on" marking if an incorrect response to 2. is subsequently used in 3. The last
part, 4., is independent of the others.

### Response processing ###

Response processing is the means by which a student's answer is evaluated and feedback, of various forms,
assigned. The crucial observation in STACK is a complete separation between two important components.

1. a list of [inputs](Inputs);
2. a list of [potential response trees](potential_response_trees).

## [inputs](Inputs) ##

The [question stem](CASText#Question_stem), i.e. the text actually displayed to the student,
may have an arbitrary number of [inputs](Inputs). An element may be positioned
anywhere within the question stem, including within mathematical expressions, e.g. equations. Each
input will be associated with a number of fields. For example

1. The name of a CAS variable to which the student's answer (if any) is assigned during response processing.
   This could be automatically assigned, e.g. in order `ans1`, `ans2`, etc. Each variable is known as an answer variable.
2. The type of the input. Examples include
  1. direct linear algebraic input, e.g. `2*e^x`.
  2. graphical input tool, e.g. a slider.
  3. True/False selection.
  4. string data type.
3. The teacher's correct answer.

## [Potential response trees](Potential_response_trees) ##

A potential response tree (technically an acyclic directed graph) consists of an arbitrary number of linked nodes
we call potential responses. In each node two expressions are compared using a specified Answer Test,
and the result is either TRUE or FALSE. A corresponding branch of the tree has the opportunity to

1. Adjust the score, (e.g. assign a value, add or subtract a value);
2. Add written feedback specifically for the student;
3. Generate an "Answer Note", used by the teacher for evaluative assessment;
4. Nominate the next node, or end the process.

Each question will have zero or more potential response trees. For each potential response tree there will be the following.

1. A maximum number of marks available, i.e. score.
2. A list of which answer variables are required for this response tree. Only when a student has
   provided a **valid** response to all the elements in this list will the tree be traversed and outcomes assigned.
3. A collection of potential response variables, which may depend on the relevant answer variables, question
   variables and so on. These are evaluated before the potential response tree is traversed.
4. A nominated point in the question itself into which feedback is inserted.
   This feedback will be the mark, and textual feedback generated by this tree.

Permitting zero potential response trees is necessary to include a survey question which is not
automatically scored. An input which is not used in any potential response tree is
therefore treated as a survey and is simply recorded.
