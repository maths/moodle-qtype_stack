# Introduction to STACK

Computer aided assessment of mathematics works in the following phases.

1. [Authoring](../Authoring/index.md)
2. [Testing](Testing.md)
3. [Deploying](Deploying.md)
4. [Reporting](Reporting.md)

In this guide we introduce you to STACK question authoring.  We show you how easy it is to convert a senior school-level integration question to Moodle for automatic marking and feedback.

## Before you begin

We assume you are familiar with the following:

1. Adding questions to a Moodle quiz.
2. \(\LaTeX\) formatting.  Some basic examples are provided at the end of the [CASText](CASText.md) documentation.
3. Maxima CAS strings.

## Background

STACK is built on top of the Maxima computer algebra system - referred to from this point on simply as the CAS.

To introduce you to question authoring we use the real world example of senior school mathematics students who are learning to integrate, with respect to \(x\), functions of the form \( r(px+q)^n \). Students are being asked to complete a set of questions from _Advanced Mathematics for AS and A level 2_:  

![Integration exercises](%CONTENT/IntegrationExercises.jpg)

(Reproduced with permission from _Advanced Mathematics for AS and A level 2_ (A-level mathematics), Haese Mathematics (2018) [978-1-925489-32-3](http://www.haesemathematics.com/books/advanced-mathematics-2-for-a-level))

We start this guide by showing you how easy it is to move questions like these online to Moodle and have Moodle automatically grade the student response. Not only that, but we will also demonstrate how easy it is to catch common slips such as, for example in the case of integration, forgetting to include the constant of integration or accidentally differentiating instead of integrating. 

At the end of this guide you will be able to:

- Create a new STACK question, ensuring mathematical notation is displayed correctly using \(\LaTeX\) notation.
- Catch, and provide feedback on, common errors by building a *Potential Response Tree*. 
- Preview and test STACK questions.

## Preparing to write a STACK question

Let us begin by considering question (i) from the text book, shown in the image above:
\(
\int \frac{5}{(3x - 2)^3} dx
\)

Below is a student's written response, which demonstrates two common slips:

![Student written response](%CONTENT/student_written_response.png)

Notice that the student has:

- Forgotten to include the constant of integration.
- Differentiated the outer function, instead of integrating.

Of course, these are things which students are likley to do with any integration question.  Indeed, through force of habit students have been known to differentiate by mistake and still add a constant of integration!  Also, there are mistakes students have made which are much more specific to this particular question:

- Forgetting to use substitution and hence not dividing by \(p\), and effectively integrating \( \int r(px+q)^n dx \rightarrow \frac{r}{n+1}(px+q)^{n+1}+c \).
- having difficulties in increasing a negative number (in this case \(-3\) by one).  In our example \( \int \frac{5}{(3x - 2)^3} dx \rightarrow \frac{5}{3}\frac{1}{(3x - 2)^4}+c\).

One of the benefits of using online assessment, such as STACK, is that it's easy to check for these errors.

When checking a student's answer with STACK a teacher needs to ask themselves _"What are the mathematical properties which makes a student's answer correct/incorrect?"_  In our case these questions include:

- Is the student's answer a symbolic anti-derivative of the integrand?
- Does the student have a constant of integration in an appropriate form?

Next, a teacher needs to ask _"What might a student do incorrectly, and what will this give them as an answer?"_  This second question is more difficult. The answer might come through experience or from asking upfront diagnostic questions (again using STACK). It is often sensible to review questions after a year and build in better feedback in the light of experience with students. 

## Creating a new STACK question

To begin, go to Moodle and navigate to your course's *Course administration* page and, from the *Question bank* section, click on *Questions*:

![Accessing the Question Bank](%CONTENT/access_the_question_bank.png)

On the Question Bank page, press the 'Create new question' button:

![Press the Create new question button](%CONTENT/question_bank_page.png)

From the 'Choose a question type to add' dialog select 'STACK' and press 'Add':

![Select STACK question](%CONTENT/select_STACK_question.png)

The 'Editing a STACK question' page is displayed. Now we can begin to create the actual question. Don't be put off by the amount of configuration options as, in order to get started, there are only a few we need to worry about. 

### Question name ###

Firstly, give your question a name. This needs to be something meaningful so that you can easily identify it. For example, the name could simply be the question as it's identified in the text book:

![Question name](%CONTENT/question_name.png)

### Question variables ###

Next we can assign some variables. Firstly the question itself, which we store in a variable called `definition`. Then the model answer stored in a variable called `model`. Type the following into the Question variables box:

	definition: 5*(3*x-2)^-3;
	model: integrate(definition, x)+C;

![Question variables](%CONTENT/question_variables.png)

### Question text ###

Next we need to input the question text itself. Copy the following into the Question text box:

	Find \(\int{@definition@}\mathrm{dx}\)

	[[input:ans1]] [[validation:ans1]]

![Question text](%CONTENT/question_text.png)

Note that two tags are already added to the question text: the [[input:ans1]] tag is replaced by a text box where the student types in their response. The student response should be in CAS text format, which is automatically validated as they type. This is displayed where the [[validation:ans1]] tag is positioned. So, for example, we could remodel the question as:

![Remodelled question text](%CONTENT/remodelled_question_text.png) 

## Input: ans1

Next we need to begin to configure how the student's response is processed. The response is stored in the answer variable `ans1`. Note that a question can have more than one answer variable (for example in multi-part questions, which are discussed later in this guide) but for now we'll assume the student is going to provide their final answer for marking. Click on the heading `Input: ans1` to reveal the relevant settings. 

From the _Input type_ drop-down menu select _Algebraic input_, as this is the form of student response we are expecting.

Next we need to specify the model answer. As STACK analyses a student's response, the model answer will be referred to more than once so it is usually easiest to assign the model answer to a variable as we have done. You probably noticed that we are using the CAS to determine the model answer by calling the `integrate()` function to find the anti-derivative. When the CAS determines an anti-derivative it does not include a constant of integration so we have to add it ourselves (by adding `+C` onto the end of the `integrate()` function's return value). Because we have the model answer assigned to the variable `model` we can simply type the variable name in the `model answer` box:

![Model answer](%CONTENT/model_answer.png)

We have now configured the question. In the next section we learn how a Potential Response Tree (PRT) can managing both the grading of - and providing suitable feedback for - a student's answer.

## Grading a response - the Potential Response Tree (PRT)

To grade the student's response we need to determine its mathematical properties. Potentially, there are multiple properties we need to check in order to award an appropriate score, or provide appropriate feedback. To establish properties of student's answer we use an algorithm known as a [potential response tree](Potential_response_trees.md).

By default, a new question contains one [potential response tree](Potential_response_trees.md) called `prt1`.
This is the _name_ of the potential response, and it can be anything sensible (letters, optionally followed by numbers, no more than 18 characters).

There can be any number of [potential response trees](Potential_response_trees.md).

Feedback generated by these trees replaces the tag `[[feedback:prt1]]`.
By default this tag is placed in the Specific feedback field, but it could also be placed in the question text.

In due course, we shall provide [feedback](Feedback.md) which checks

1. For the correct answer.
2. To see if the student differentiated by mistake.
3. To see if the student forgot to use substitution.

We start with the first check. In the next section we will build a simple PRT that checks if the student has integrated correctly.

### Configuring a potential response node

By default each question has one potential response node. At each node we can compare the student's response `SAns` with a teacher answer `TAns`. The comparison is carried out using an [answer test](Answer_tests.md), and STACK contains a variety of build-in tests - including one for general indefinite integral questions such as ours. 

Let us configure the first node to determine if the student has integrated correctly.

1. Click on the _Answer test_ drop-down menu and select _Int_

2. The teacher answer we should compare to is stored in the _model_ variable we declared ealier (an answer determined by STACK itself plus the constant of integration we added on the end). Specify this in the `TAns` setting:

The node should now be configured as follows:

![Configured PRT node](%CONTENT/configured_node.png)

### Feedback

The answer test itself sometimes produces automatic [feedback](Feedback.md) for the student (which the teacher might choose to suppress with the _Quiet_ option). The answer test also produces an internal [answer note](Potential_response_trees.md#Answer_note) for the teacher which is essential for Reporting students' attempts later.

## Saving the question

Now scroll to the bottom of the page and press the `[Save changes and continue editing]` button.  If the question fails to save check carefully for any errors, correct them and save again.

This has created and saved a minimal question.  To recap we have

1. Specified the variables used in the question, using Maxima formatted text. These variables included:
	1. the expression for which students should find attempt to find the antiderivative
	2. the model answer, which is determined by Maxima.
2. Indicated we wish to establish the student's answer is mathematically equivalent to the model answer using STACK's built-in _Int_ test.

Next we should try out our question, by pressing the preview button at the bottom of the page:

![Preview button](%CONTENT/preview_button.png)

## Previewing the question

To speed up the testing process, scroll down the preview window and under Attempt options, make sure you have "How questions behave" set to "Adaptive Mode". If necessary "Start again with these options". This will allow you to check your answers without having to _Submit_ and _Start again_ repeatedly.

With the preview open, try typing in

    -5/6(3*x-2)^-2 + C

into the answer box.

The default is for STACK to use "instant validation".  That is, when the student finishes typing the system automatically validates their answer and provides feedback.

The system first establishes the syntactical validity of this answer.

Press the `[Check]` button.

The system executes the potential response tree and establishes whether your answer is equivalent
to the model answer `-5/6(3*x-2)^-2`. Next, try getting the question wrong.  If your server does not have "instant validation" switched on (an administrator/installation option) you will need to submit each answer twice.
Notice all your responses are stored in an attempts table.

To demonstrate that it's the mathematical properties of the student's response that is being compared type

    -5/6(3*x-2)^-2 + K

into the answer box.

I am including a constant of integration (as I specified) so this is, again, correct.

Built into the _Int_ answer test is a check to ensure the response includes a constant of integration. Now type

    -5/6(3*x-2)^-2

into the answer box.

We also wanted to check that the student hadn't differentiated by mistake. Fortunately this is also handled by the _Int_ answer test. Finally, type

	-45*(3*x-2)^-4

See that built-in feedback is provided to the student - a warning that they have forgotten the constant of integration. Again, this can be disabled with the _Quiet_ option. 

## Enhancing student feedback

There are two further common mistakes for students to make when finding the anti-derivative of simple functions-of-functions:

1. Accidentally finding the derivative of the outer function (multiplying by the power and taking one off the power - i.e. following the wrong process).
2. Expanding brackets when they didn't need to - not remembering to leave their final answer in factored form. 

### Catching a common slip: following the wrong process ###

Let us continue to enhance feedback by checking that the student has not differentiated the outer function by mistake. We do this by adding another potential response node.

Close the preview, scroll down to the Potential Response Tree and click `[Add another node]` button at the bottom of the list of nodes:

![Adding a new node](%CONTENT/add_new_node.png)

From the false branch of Node 1, change the "Next" field so it is set to `[Node 2]`.
If the first test is false, we will then perform the test in Node 2.

Update the form so that Node 2 has

    SAns = diff(ans1, x)
    TAns = 60*(3*x-2)^-5
    Answer test = AlgEquiv

See that we are using Maxima to differentiate the student's answer. We then compare that result, algebraically, to what the question would have been for the student to respond in the way they have.

This gives us the test, but what about the outcomes?

1. On the true branch set the `score=0`
2. On the true branch set the feedback to `It looks like you have subtracted one off the power of the outer function instead of adding one to the power!`

Notice here that STACK also adds an "intelligent note to self" in the [answer note](Potential_response_trees.md#Answer_note) field:

![Answer note](%CONTENT/answer_node.png)

This is useful for statistical grouping of similar outcomes when the feedback depends on randomly generated questions,
and different responses. You have something definite to group over.  This is discussed in [reporting](Reporting.md).

Press the `[Save changes and continue editing]` button and preview the question.

Type the following response into the answer box:

	-5/12*(3*x-2)^-4+C

Because we are using Maxima to differentiate the student's response, whether or not the student includes a constant of integration in their answer. You can verify this by typing the above response but missing off the constant.

### The Form of a Response: not leaving an answer in factored form

Because we are using the mathematical properties of a student's response to judge its accuracy, we can even check to ensure that the student has responded in the correct form. For example, to answer our question a student answering this correctly is likely to respond with \(-\frac{5}{6}(3x-2)^{-2} + C \) but they might equally well respond with \( -\frac{5}{54x^{2}-72x+24} + C\). This is, of course, mathematically correct but not in the factored form convention demands. The student is correct but we still should guide them towards not expanding brackets when they don't need to. 

We need to go back and `[Add another node]` to the Potential Response Tree. A third node is added.

To use this potential response, edit Node 1, and now change the true branch to make the Next node point to the new Node 3.
If we enter Node 3, we know the student has the correct answer and just need to establish if it is factored or not and provide the appropriate feedback. To establish this we need to use the [FacForm answer test](Answer_tests.md).

Update the form so that Node 3 has

    SAns = diif(ans1)
    TAns = -5/6(3*x-2)^-2 + C
    Answer test = FacForm
    Test options = x
    Quiet = Yes.

The FacForm answer test provides automatic feedback which would be inappropriate here, hence we choose the quiet option.

We also need to assign outcomes.

1. On the true branch set the `score=1`
2. On the false branch set the `score=1`
3. On the false branch set the feedback to `Your answer is not factored. Well done for getting the correct answer but remember that there is no need to expand out the brackets.`

Having developed our integration question to the point where we can provide some quite detailed guidance to students (based on the mathematical properties of their answer) we can now consider using this particular question as the basis for a whole set of random questions.

Before moving on you might consider saving the current question as a new question so you don't lose your work.

## Random questions ##

To generate random questions we again make use of the [question variables](KeyVals.md#Question_variables) field.

Right at the very start of this guide we introduced the idea of question variables. Let's take a look again the the question variables we declared:

<textarea readonly="readonly" rows="2" cols="50">
definition:5*(3*x-2)^-3
model: integrate(definition, x)+C
</textarea>

We defined two local variables `definition` and `model`, and used these values in both the Question text and in the potential response tree. 

In the question text we entered the following:

<textarea readonly="readonly" rows="3" cols="50">
Find \(\int{@definition@}\mathrm{dx}\)
[[input:ans1]] [[validation:ans1]]
</textarea>

The difference is between mathematics enclosed between `\(..\)` symbols and `{@..@}` symbols. All the text-based fields in the question, including feedback, are [CAS text](CASText.md).  This is HTML into which mathematics can be inserted.  \(\LaTeX\) is placed between `\(..\)`s, and CAS expressions (including your variables) between matching `{@..@}` symbols.  There is more information in the specific documentation.   

Notice also that in the model answer there is a CAS command to integrate the value of `definition` with respect to `x`. It wasn't necessary for the CAS to work out the answer to our original question (we could have specified it ourselves) but it is certainly necessary in a random question.

We are now in a position to generate a random question. To do this modify the [question variables](KeyVals.md#Question_variables) to be

	a : 1+rand(6)
	b : 1+rand(6)
    c : 1+rand(6)
    n : -1-rand(6)
    definition : a(b*x-c)^n;
    model: integrate(definition, x)+C

In this new question we are asking the student to find the anti-derivative of a question with a definite form \(\frac{a}{(b*x-c)^n}\). `a`, `b`, `c` and `n` are all variables, which are assigned random numbers. These are then used to define the variable `definition`, used in the question itself. We also have the CAS integrate the definition and store the result in the variable `model`.

Remember that when generating random questions in STACK then we talk about _random numbers_ when we really mean _pseudo-random numbers_. To keep track of which random numbers are generated for each user, there is a special `rand` command in STACK,
which you should use instead of [Maxima](../CAS/Maxima.md)'s random command. The `rand` command is a general "random thing" generator, see the page on [random generation](../CAS/Random.md) for full details. Not only can it can be used to generate random numbers and also to make selections from a list (discussed later in this guide).

### Question note ###

Now that our question contains random numbers we need to record the actual question (i.e. so that we know what was actually asked). As soon as we use the `rand` function STACK forces us to add a _Question note_. For our new, randomised integration question fill this in as

	Find \[\int{@definition@}\mathrm{dx}\]


### Handling random variables in the Potential Response Tree ###

We also need to ensure the test answers, `TAns`, in each node of the potential response tree are updated accordingly. If the student has differentiated the outer function by mistake then the derivative of their response will be of the form:

	\[n*(n-1)*a*(b*x-c)^{n-2}\]
	
We are already using the `definition` and `model` variables in both the integration and factor form tests we configured earlier so these can stay unchanged.

Edit your trial question, save and preview it to get new random versions of the question.

### Futher randomisation ###

The `rand` function can also be used to select items from a list. For example, we can use random selection from a list to vary our integration question even further:

	a : 1+rand(6);
	b : 1+rand(6);
	c : 1+rand(6);
	sign: rand[-1,1];
	n : rand(6)*sign;
	definition : a(b*x-c)^n;
	model: integrate(definition, x)+C;

As questions become increasingly complex, it is a good habit to comment complicated lines in the Maxima code in the Question variables and Feedback variables, in order to make the code easier to read for anyone wishing to edit the question. Comments are entered as follows: `sign: rand[-1,1]; /* The power is randomly set to either positive or negative */`.

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

```<p>In order to find \(\int{@f@}\mathrm{dx}\) we can use substitution.</p>
<p>1) Firstly, let's let \(u={@inner@}\) and find \(\frac{\mathrm{du}}{\mathrm{dx}}\). Use the box below to type in your answer:</p><p>\(\frac{ \mathrm{du} }{ \mathrm{dx} } =\)&nbsp;[[input:ans1]] [[validation:ans1]]</p><p>2) Next, we rearrange to make \(\mathrm{dx}\) the subject. Complete the rearranged equation below:</p>
<table>
<tbody>
<tr>
<td rowspan="2">\(\mathrm{dx}=\)</td>
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

You might also see that we are guiding the student through the process of rearranging a derivative. Students often fail to realise that, for example, the \(\mathrm{dy}\) in \(\frac{\mathrm{dy}}{\mathrm{dx}}\) does not mean (\d\) multipled by \(x\), so part 2) of this question is a 'fill the gap' exercise. To achieve this part 2) uses an HTML table to render a fraction.

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
Find \(\int{@f@}\mathrm{dx}\) using substitution
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

