# Author quick start 4: equivalence reasoning


This part of the author quick start guide shows you how to write STACK questions using the line by line equivalence reasoning.
Using these tools we develop a question to assess a student's ability with, and understanding of, algebraic equivalence and equivalence transformations.

As an example, we want the students to expand the cubic \((x+2)^3\) showing their working in a stepwise fashion, line by line.
The student's response to this question will allow us to test their knowledge and competency in the following:

1. Expanding brackets
2. Simplifiying by collecting like terms

Therefore we need them to show their working. We also need to build a potential response tree, with each node using a suitable answer test, in order to assess these aspects.

### Authoring the question

Create a new STACK question, give it a suitable name and then copy the following into the Question variables box:


	p:(x+2)^3;
	taf:ev(expand(p),simp);
	ta:[(x+2)^3,stackeq((x+2)*(x+2)^2),stackeq((x+2)*(x^2+4*x+4)),stackeq(x^3+4*x^2+4*x+2*x^2+8*x+8),stackeq(taf)];

The first variable, `p`, is the question. The variable `taf` is the final model answer.
The variable `ta` is an array containing each step we are expecting our students to express as they work towards the final answer:

\((x+2)^{3}\)

\(=(x+2)(x+2)^{2}\)

\(=(x+2)(x^{2}+4x+4)\)

\(=x^{3}+4x^{2}+4x+2x^{2}+8x+8\)

\(=x^{3}+6x^{2}+8x+8\)


Notice again that we are using the CAS, and specifically the CAS functions `expand()` to expand `p` and `ev()` to simply the output of `expand()`, to determine the model answer. 

Note also the use of the `stackeq()` function.
This allows us to start a line with the \(=\) sign and have nothing on one side of the \(=\) symbol (not having anything written on one side of an equals symbol would suggest we are comparing something to nothing - which wouldn't make any sense).

Copy the following text into the Question text box:

<textarea readonly="readonly" rows="2" cols="50">
Expand {@p@}, remembering to show your working.
[[input:ans1]] [[validation:ans1]]
</textarea>

### Setting the input options ###

Under the `Input:ans1` header specify _Equivalence reasoning_ from the Input type drop-down and `ta` as the model answer.

We want students to work through the expansion one line at a time, so let's include a hint. Copy the following into the Syntax hint box:

	[(x+2)^3,stackeq(?)]

The special function `stackeq` is replaced by a unary equals symbols.  Maxima expects equality to be an infix \(a=b\) not unary prefix \(=b\), so STACK needs this special operator.  Students can just start a line with \(=\), but teachers cannot!

For students in this context, it is probably next to "insert stars" and provide the most forgiving input syntax avaiable, but that is optional.

We need to tell STACK to compare each line of the student's working to the first (i.e. if each line is equivalent to the first line then each line will be equivalent to the one before). Type `firstline` into the Extra options box.  This ensures a student's response will be invalid if they don't have the correct first line.

### Setting the potential response tree ###

As a minimal potential response tree have one node, with 

    SAns = ans1
    TAns = ta
    answer test = EquivFirst
    Auto-simplify = no

Note, the `Auto-simplify` field is not in the node of the PRT, but a PRT option.

### Setting the question options ###

Set question level options

    Auto-simplify = no

Save the question.  This should be a minimal working question, so preview it and add in at least one question test.

