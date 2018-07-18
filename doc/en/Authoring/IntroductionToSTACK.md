




#

## Answers to multi-part questions

In developing the integration question we assumed that the student was going to give us their final answer. However, if students have only just started to practice integrating functions of the form (\(p*x+q)^{n}\) then guidance might be appropriate:

![Example multi-part question](%CONTENT/multipart_example.png)

In a multi-part question one answer leads on to the next and each part must be marked individually. At each stage we might want to award marks for method in spite of an earlier mistake (an error carried forward). Or if this were a mechanics question aimed at engineering students or an I.V. drug calculation test for medical practicioners then we might not (errors carried forward are unlikely to earn you marks after a bridge collapse or a patient death).

Let's look at two more examples of multi-part questions before configuring our own.

### A multi-part question: Example 1 ###

Find the equation of the line tangent to \(x^3-2x^2+x\) at the point \(x=2\).

1. Differentiate \(x^3-2x^2+x\) with respect to \(x\).
2. Evaluate your derivative at \(x=2\).
3. Hence, find the equation of the tangent line. \(y=...\)

Since all three parts refer to one polynomial, if randomly generated questions are being used then each
of these parts needs to reference a single randomly generated equation. Hence parts 1.-3. really form
one item.  Notice here that part 1. is independent of the others. Part 2. requires both the first and second inputs.
Part 3. could easily be marked independently, or take into account parts 1 & 2. Notice also that the teacher may
choose to award "follow on" marking.

### A multi-part question: Example 2 ###

Consider the following question, asked to relatively young school students.

Expand \((x+1)(x+2)\).

In the context it is to be used it is appropriate to provide students with the
opportunity to "fill in the blanks", in the following equation.

    (x+1)(x+2) = [?] x2 + [?] x + [?].

We argue this is really "one question" with "three inputs".
Furthermore, it is likely that the teacher will want the student to complete all boxes
before any feedback is assigned, even if separate feedback is generated for each input
(i.e. coefficient). This feedback should all be grouped in one place on the screen. Furthermore,
in order to identify the possible causes of algebraic mistakes, an automatic marking procedure
will require all coefficient simultaneously. It is not satisfactory to have three totally
independent marking procedures.

### Building a multi-part question
The two previous examples illustrate two extreme positions.

1. All inputs within a single multi-part item can be assessed independently.
2. All inputs within a single multi-part item must be completed before the item can be scored.

Devising multi-part questions which satisfy these two extreme positions would be relatively straightforward.
However, it is more common to have multi-part questions which are between these extremes, as in the case the example we will create together next.

#### Developing the question text
For students who are struggling to find the anti-derivates of functions-of-functions we will create a multi-part STACK question which guides the student through an effective process using substitution. Within the question itself, we will provide step-by-step instructions showing how to perform the necessary operations, whilst at the same time explaining, as much as possible, how the underlying mathematical properties affect the process.

Multi-part questions, almost by necessity, have a more complicated question text. In this example we provide the question text for you to copy into your own new STACK question.

1. Create a new STACK question
2. Scroll down to the Question text box and press the _Show/hide advanced buttons_ button
3. Press the _HTML_button
4. Copy then paste the following HTML into the Question text edit box:

```<p>In order to find \(\int{@f@}\mathrm{d}x\) we can use substitution.</p>
<p>1) Firstly, let's let \(u={@inner@}\) and find \(\frac{\mathrm{du}}{\mathrm{d}x}\). Use the box below to type in your answer:</p><p>\(\frac{ \mathrm{du} }{ \mathrm{d}x } =\)&nbsp;[[input:ans1]] [[validation:ans1]]</p><p>2) Next, we rearrange to make \(\mathrm{d}x\) the subject. Complete the rearranged equation below:</p>
<table>
<tbody>
<tr>
<td rowspan="2">\(\mathrm{d}x=\)</td>
<td style="border-bottom: 1px solid #000; text-align: center">\(\mathrm{du}\)</td>
</tr>
<tr>
<td>[[input:ans2]]</td>
</tr>
</tbody>
</table>&nbsp;[[validation:ans2]]
Now we can rewrite the integral in terms of the variable \(u\):<br><p>\( \int\frac{{@a@}(u)^{{@n@}}}{{@b@}}\mathrm{du} \)<br></p><p>\(=\int({@sb@})\mathrm{du}\)</p><p>Next we need to find the anti-derivative of this function. Attempt this now and type this in the box below:</p><p>[[input:ans3]] [[validation:ans3]]</p><p>5) Finally, substitute your value for \(u\) back into your expression. Type your final answer in the box below:</p><p>[[input:ans4]] [[validation:ans4]]<br></p><p>Check over each stage again and, when you are happy with each stage and your final answer, press the Submit button.<br></p>
```

5. Press the _HTML_ button again to toggle the Question text editor back to the WYSIWYG view.

Before moving on we briefly note that the question text contains multiple STACK answer and validation tags ([[input:ans1]], [[validation:ans1]], [[input:ans2]], [[validation:ans2]], etc.). Scroll down the page until you reach the `[[Verify the question text and update the form]]` button and press it. The page will be updated to include three new headings: rather than just `input:ans1` we now have, in addition, the new headings `input:ans2`, `input:ans3` and `input:ans4`.

You might also see that we are guiding the student through the process of rearranging a derivative. Students often fail to realise that, for example, the \(\mathrm{dy}\) in \(\frac{\mathrm{dy}}{\mathrm{d}x}\) does not mean (\d\) multipled by \(x\), so part 2) of this question is a 'fill the gap' exercise. To achieve this part 2) uses an HTML table to render a fraction.

#### Question variables

Copy the following into the Question variables box:

<textarea readonly="readonly" rows="11" cols="50">
a:1+rand(9);
b:1+rand(9);
c:1+rand(9);
n:(-1)-rand(9);
inner:b&#42;x-c;
f:a&#42;(inner)^n;
sa:diff(inner, x);
sb:ev((a&#42;(u)^n/b, simp));
sc:diff(sb, u);
model:integrate(f, x)+C;
</textarea>

Again, you will see that we are using the STACK `rand()` function to generate random coefficients, constants and powers. The function of a function is assigned to the variable `f`, the inner function of which has been assigned to the variable `inner`. The variables `sa`, `sb` and `sc` are the answers to intermediate parts of this question (we will be employing these variables shortly). See also that we are using CAS functions to find derivatives and anti-derivatives where necessary.

Now we specify the correct responses.

#### Specifying model responses

Click on the `Input:ans1` heading, select `Algebraic input` from the Input type drop-down and `sa` as the model answer.

Click on the `Input:ans2` heading, select `Numerical` from the Input type drop-down and `b` as the model answer. This number will be rendered as the numerator of a fraction so set the Input box size to 2.

Click on the `Input:ans3` heading, select `Algebraic input` as the Input type and `sc` as the model answer.

Finally, click on `Input:ans4`, select `Algebraic input` as the Input type. Specify the variable `model` as the model answer.

As before, because this question uses the `rand()` function we need to include a Question note. In the Question note box enter the following:

<textarea readonly="readonly" rows="1" cols="50">
Find \(\int{@f@}\mathrm{d}x\) using substitution
</textarea>

Let us add one node to the Potential Response Tree that verifies that the answer to the last part - the student's final answer - is correct. Under the heading `Potential response tree:prt1` specify `Int` from the Answer text drop-down, `ans4` as SAns, `model` as TAns and, finally, `x` for Test options as, ultimately, we are tasking the student with finding the anti-derivate with respect to (\x\).

Now we are in a position to preview the question. Press the `[[Save changes and continue editing]]` button. Once the page has reloaded, scroll down the page and click the `Preview` link.

Here is a preview of the page:

![Multi-part preview](%CONTENT/multipart_preview.png)

#### Follow through marking

** NOT FINISHED ** 


## Assessing algebraic transformations - Reasoning by Equivalence

In this next question we will be exploring STACK's built-in equivalence reasoning tools. Using these tools we develop a question to assess a student's ability with, and understanding of, algebraic equivalence and equivalence transformations. This takes us on a departure from the integration question we have based most of our work on so far.

We wish our students to expand the cubic \((x+2)^3\) showing their working in a stepwise fashion, line by line. The student's response to this question will allow us to test their knowledge and competency in the following:

1. Expanding brackets
2. Simplifiying by collecting like terms

Therefore we need them to show their working. We also need to build a potential response tree, with each node using a suitable answer test, in order to assess these aspects.

### Authoring the question

Create a new STACK question, give it a suitable name and then copy the following into the Question variables box:


	p:(x+2)^3;
	taf:ev(expand(p),simp);
	ta:[(x+2)^3,stackeq((x+2)*(x+2)^2),stackeq((x+2)*(x^2+4*x+4)),stackeq(x^3+4*x^2+4*x+2*x^2+8*x+8),stackeq(taf)];

The first variable, `p`, is the question. The variable `taf` is the final model answer. The variable `ta` is an array containing each step we are expecting our students to express as they work towards the final answer:

\((x+2)^{3}\)

\(=(x+2)(x+2)^{2}\)

\(=(x+2)(x^{2}+4x+4)\)

\(=x^{3}+4x^{2}+4x+2x^{2}+8x+8\)

\(=x^{3}+6x^{2}+8x+8\)


Notice again that we are using the CAS, and specifically the CAS functions `expand()` to expand `p` and `ev()` to simply the output of `expand()`, to determine the model answer. See also the use of the `stackeq()` function. This allows us to start a line with the \(=\) sign and have nothing on one side of the \(=\) symbol (not having anything written on one side of an equals symbol would suggest we are comparing something to nothing - which wouldn't make any sense).

Copy the following text into the Question text box:

<textarea readonly="readonly" rows="2" cols="50">
Expand {@p@}, remembering to show your working.

[[input:ans1]] [[validation:ans1]]
</textarea>

### Defining the answer ###

Under the `Input:ans1` header specify _Equivalence reasoning_ from the Input type drop-down and `ta` as the model answer.

Because we are wanting students to work through the expansion one line at a time let's include a hint. Copy the following into the Syntax hint box:

	[(x+2)^3,stackeq(?)]
	
Finally, we need to tell STACK to compare each line of the student's working to the first (i.e. if each line is equivalent to the first line then each line will be equivalent to the one before). Type `firstline` into the Extra options box.

### Building the Potential Response Tree ###

Now we need to assess the studen't response. Recall that there are a [large number of answer tests](Answer_tests.md) included in STACK we can use to assess a student's answer. For the first node, change the answer test to 


There is a more detailed expanation of how STACK manages equivalence reasoning questions in the [CAS documentation](../CAS/Equivalence_reasoning.md).

## Answer Tests

Potential response trees can, even with relatively straightforward questions, become quite complicated and it can often be difficult to appreciate whether or not the tree properly handles correct responses, incorrect responses and any edge conditions. 

