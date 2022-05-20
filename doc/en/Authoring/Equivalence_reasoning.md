# Getting started with equivalence reasoning


This guide shows you how to write STACK questions using the line by line [equivalence reasoning](../CAS/Equivalence_reasoning.md) input type.

As an example, we want the students to expand the cubic \((x+2)^3\) showing their working in a stepwise fashion, line by line.
The student's response to this question will allow us to test their knowledge and competency in the following:

1. Expanding brackets
2. Simplifying by collecting like terms

Therefore we need them to show their working. 

## Minimal working question ##

Create a new STACK question, give it a suitable name and then copy the following into the Question variables box:


	p:(x+2)^3;
	taf:ev(expand(p),simp);
	ta:[(x+2)^3,stackeq((x+2)*(x+2)^2),stackeq((x+2)*(x^2+4*x+4)),stackeq(x^3+4*x^2+4*x+2*x^2+8*x+8),stackeq(taf)];

The first variable, `p`, is the expression in the question. The variable `taf` is the final model answer.
The variable `ta` is a list containing each step we are expecting our students to express as they work towards the final answer:

\((x+2)^{3}\)

\(=(x+2)(x+2)^{2}\)

\(=(x+2)(x^{2}+4x+4)\)

\(=x^{3}+4x^{2}+4x+2x^{2}+8x+8\)

\(=x^{3}+6x^{2}+12x+8\)


Notes:

* We use the CAS functions `expand()` and `ev(...,simp)` to simply the output of `expand()`, to determine the model answer. 
* The special function `stackeq` is replaced by unary equals.  Maxima expects equality to be an infix \(a=b\) not unary prefix \(=b\), so STACK needs this special operator.  Students using the input area can just start a line with \(=\), but teachers cannot!

In this context the teacher's answer and the student's answer is a list.  The whole answer is a single object, which we assess.

Copy the following text into the Question text box:

	Expand {@p@}, remembering to show your working.
	[[input:ans1]] [[validation:ans1]]

### Setting the input options ###

Under the `Input:ans1` header specify _Equivalence reasoning_ from the Input type drop-down and `ta` as the model answer.

We want students to work through the expansion one line at a time, so let's include a hint. Copy the following into the Syntax hint box, within the `Input:ans1` header::

    [(x+2)^3,stackeq(?)]

This is a list, and uses `stackeq`.

For students in this context, it is probably sensible to "insert stars" and provide the most forgiving input syntax available, but that is optional.

We need to tell STACK to compare the first line of the student's working to the first line of the question. This makes sure the student "answers the right question".
Type `firstline` into the Extra options box within the `Input:ans1` header.
This ensures a student's response will be invalid if they don't have the correct first line.

`firstline` can also be used in the Syntax hint box. The first line is then already written in the answer-field when the student opens the question.

### Setting the potential response tree ###

As a minimal potential response tree have one node, with 

    Answer test = EquivFirst
    SAns = ans1
    TAns = ta
    Auto-simplify = no

Note, the `Auto-simplify` field is not in the node of the PRT, but a PRT option.

### Setting the question options ###

Under the options section, turn off simplification by setting

    Auto-simplify = no

Save the question.  This should be a minimal working question, so preview it and add in at least one question test.

## More specific feedback

At this point the question only checks

1. Has the student started from the right expression, specifically is the first line of their argument equivalent to the first line of `ta` using `EqualComAss` test (commutativity and associativity)?
2. Are all the lines in the student's answer algebraically equivalent?

Clearly, more is needed for a complete sensible question.

At this point please read the [equivalence reasoning](../CAS/Equivalence_reasoning.md) input type documentation.

## Getting to the right place ##

We probably want the student to end up at the expression \(x^{3}+6x^{2}+12x+8\).

To check is the student has reached this point, add another node to the PRT.  If node 1 is true (i.e. the student started in the correct place and didn't make a mistake) then connect to node 2.
Node 2 should be set up as

    SAns = last(ans1)
    TAns = last(ta)
    answer test = EqualComAss
    Auto-simplify = no

This node adds in feedback to check the student has reached the right place.

Note, by using `EqualComAss` both \(x^{3}+6x^{2}+12x+8\) and \(x^{3}+x^{2}6+8+12x\) will be accepted.
If you really want the term order as well, as in, \(x^{3}+6x^{2}+12x+8\) then you need to use `CasEqual` as the answer test instead.

## What is a legitimate step?

At this point, any expressions which are equivalent are considered to be a legitimate step.

Clearly this is not entirely satisfactory.
At this point in the development there is no concept of "a step" and indeed this appears to be very hard to define.
In the future we will develop better tools for checking "step size", and any contributions in this direction are welcome.

Teachers can check the students answer is long enough or not too long by looking at `length(ta)`.

Teachers can check if specific expressions appear somewhere inside the student's answer.  To facilitate this search we provide the function `stack_equiv_find_step(ex, exl)`.  This looks for expression `ex` in the list `exl` using `ATEqualComAss`.  It returns the list of indices of the position.  If you just want to know if the expression is missing use the predicate `emptyp`.

As an alternative you can check that the factored form exists somewhere in the student's answers using the following code in the [feedback variables](Variables.md).

    foundfac:sublist(ans1,lambda([ex], equationp(ex) and is(rhs(ex)=0)));
    foundfac:ev(any_listp(lambda([ex], second(ATFacForm(lhs(ex),lhs(ex),x))), foundfac), simp);

At this stage there are few in-built features within STACK.  A lot is possible, but as the above example illustrates, this requires question authors to write more Maxima code than with other question types.

This feature will be developed by use over the next few years.
If you have experience, and views, on how this should work please contact the developers.
