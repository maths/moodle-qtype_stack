# Authoring quick start

Computer aided assessment of mathematics works in the following phases.

1. [Authoring](../Authoring/)
2. [Testing](Testing)
3. [Deploying](Deploying)
4. [Including questions in a Moodle Quiz](../Components/Moodle#Including_questions)
5. [Reviewing](Reviewing)

Each of these links contains detailed instructions.  However, the purpose of this page is to work through a simple example.

## Introduction ##

STACK is designed as a vehicle to manage mathematical questions. Implicit in this is a data structure which represents them.
This page explains the process of authoring a question, by working through an example.

Questions are edited through the Moodle quiz.  Do not be put off by the fact the editing form looks complicated.

There are lots of fields, but the only compulsory field is the [question stem](CASText#Question_stem).
This is the string actually displayed to the student, i.e. this is "the question".

## An example question ##

We are now ready to edit an example question. **Note**: this question will only remain as long as you are in a session.
You will need to SAVE the question in STACK's database before you can test it.

Enter the following information into the question stem. It should be possible to cut and paste.

	Differentiate $(x-1)^3$ with respect to $x$.
	[[input:ans1]]

Then press the update button. 

Notice this text contains LaTeX.  Full details are given under [CASText](CASText).
When the form is refreshed scroll down:  there will be an [inputs](Inputs) section of the editing form.
Into the Teacher's Answer type in the answer as a syntactically valid CAS expression, e.g.

	3*(x-1)^2

Then press the update button.
Now we have a question, and the teacher's answer.  We next have to decide if the student's answer is correct.

## Establishing properties of the student's answer via the potential response tree			{#Answer_props_via_prt}

To establish properties of student's answers we need an algorithm known as a [potential response trees](Potential_response_trees).  

This tree will allow us to establish the mathematical properties of the student's answer and on the basis of these properties provide outcomes, such as feedback and a mark.

We shall provide [feedback](Feedback) which checks

1. For the correct answer.
2. To see if the student integrated by mistake.
3. To see if it is likely that the student expanded out and differentiated.

Locate the section of the form [potential response trees](Potential_response_trees) and where is says ``Add a potential response tree named:'' type in
	
	1

This is the _name_ of the potential response, and it can be anything sensible. 
Then press the update button. 
The question edit form will become quite significantly more complex, but actually each potential response is quite simple.

1. `SAns` is compared to `TAns` with the Answer test.
2. If `true` then we execute the `true` branch.
3. If `false` then we execute the `false` branch.

Each branch can 

* Assign/update the mark.
* Assign formative [feedback](Feedback) to the student.
* Leave an [answer note](Potential_response_trees#Answer_note) for [reviewing](Reviewing) purposes.
* Nominate the next potential response node, or end the process (-1).
We refer to the student's answer in computer algebra calculations by using the name `ans1` since we gave this name to the input in the question stem.  The Teacher's answer was `3*(x-1)^2`.  Update the form fields so that

	SAns = ans1
	TAns = 3*(x-1)^2
	Answer test = AlgEquiv

Then press the SAVE button. 
This has created and saved a minimal question.  To recap we have

1. Typed in the question
2. Typed in the teacher's answer
3. Indicated we wish to establish the student's answer is algebraically equivalent to the teacher's answer `3*(x-1)^2`.

Next we should try out our question.

## Testing the question ##

Assuming there are no errors, you may now choose the link "Try question".
This takes us to a new form where the teacher can experiment with the question.
Try typing in

	3*(x-1)^2

into the answer box.

Press the SUBMIT button.

The system first establishes the syntactical validity of this answer.

Press the SUBMIT button again.

The system executes the potential response tree and establishes whether your answer is algebraically equivalent
to the teacher's answer `3*(x-1)^2`.  Next, try getting the question wrong.  You will need to submit each answer twice.
Notice all your responses are stored in an attempts table.  

We would really like to add better feedback, so it is time to edit the question again.  Choose EDIT from the link at the top of the page.

## Better feedback ##

What if the outcome of applying the first answer test was false?
We would like to check that the student has not integrated by mistake, and we achieve this by adding another potential response node.

Scroll down to the Potential Response Tree and ADD 1 potential response. Click the ADD button.

From the false branch of Node No 0., change the "Next PR" field so it is set to  1.
If the first test is false, we will then perform the test in node No.1.

If the student has integrated, they may or may not have added a constant of integration.
If they have added such a constant we don't know what letter they have used! So, the best way to solve
this problem is to differentiate their answer and compare it to the question. 

Update the form so that node No.1 has

	SAns = diff(ans1,x)
	TAns = (x-1)^3
	Answer test = AlgEquiv

This gives us the test, but what about the outcomes?

1. On the true branch set the `mark=0`
2. On the true branch set the feedback to `You appear to have integrated by mistake!`

Notice here that STACK also adds an "intelligent note to self" in the [answer note](Potential_response_trees#Answer_note) field.
This is useful for statistical grouping of similar outcomes when the feedback depends on randomly generated questions,
and different responses. You have something definite to group over.  This is discussed in [reviewing](Reviewing).

Press the SAVE button and try the question.

## Better feedback still: the form of the answer ##

It is common for students to give the correct answer but use a quite inappropriate method.
For example, they may have expanded out the polynomial and hence give the answer in unfactored form.
In this situation, we might like to provide some encouraging feedback to explain to the student what they have done wrong.

Go back and Add 1 potential response in a similar way as before.  After all, we need to apply another answer test to spot this.

To use this potential response, edit Node 0, and now change the true branch to make the Next node point to the new Node 2.
If we enter Node 2, we know the student has the correct answer. We only need to establish if it is factored or not.
To establish this we need to use a different [answer tests](Answer_tests). 

Update the form so that node No.2 has

	SAns = ans1
	TAns = 3*(x-1)^2
	Answer test = FacForm
	Test opts = x
	Quiet = checked.

The FacForm answer test provides feedback automatically which would be inappropriate here.
We just need to look at whether the answer is factored.  Hence we choose the quiet option.
We needed to add $x$ to the "Test opts" to indicate which variable we are using.

We need to assign outcomes.

1. On the true branch set the `mark=1`
2. On the false branch set the `mark=1`
3. On the false branch set the feedback to something like

~~~~~~~~~~
		Your answer is unfactored.  
		There is no need to expand out the expression in this question.  
		You can differentiate using the chain rule directly and keep 
		the answer in factored form.
~~~~~~~~~~
	
You can continue to add more potential responses as the need arises. These can test for more subtle errors
based upon the common mistakes student's make. In each case an [answer tests](Answer_tests) can be used to
make a different kind of distinction between answers.


## Random questions ##

It is common to want to use random numbers in questions. This is straightforward to do, and we
make use of the optional [question variables](KeyVals#Question_variables) field

Modify the [question variables](KeyVals#Question_variables) from the previous example so that
	
	p = (x-1)^3

Then change the [question stem](CASText#Question_stem) to
	
	Differentiate @p@ with respect to $x$.
	[[input:ans1]]

and in the inputs change the Teacher's answer to

	diff(p,x)

Notice that now we have defined a local variable $p$, and used the value of this in the Question Stem.
Hence, the user will not see a "p" on the screen when the question is instantiated, but the _value_ of `p`.

Notice also that in the Teacher's answer there is a CAS command to differentiate the value of _p_ with respect to _x_.
It is necessary for the CAS to work out the answer in a random question.
You now need to go through the potential response tree to use the variable p whenever you need to refer to the teacher's answer.

We are now in a position to generate a random question. To do this modify the [question variables](KeyVals#Question_variables) to be

	n = 2+rand(3)
	p = (x-1)^n

In this new example, we have an extra variable n which is defined to be a random number.

This is then used to define the variable \(p\) which is in turn used in the question itself.

Edit your trial question, and in the Teacher's trial window use the "new version" button to get new random versions of the question.

When generating random questions in CAA we talk about _random numbers_ when we really mean _pseudo-random numbers_.
To keep track of which random numbers are generated for each user, there is a special command in STACK,
which you should use instead of [Maxima](../CAS/Maxima)'s random command.

This is the `rand` command which is a general "random thing" generator, see the page on [random generation](../CAS/Random) for full details.
It can be used to generate random numbers and also to make selections from a list.

### The question note ###

The question note enables the teacher to track which version of the question is given to each student.
Two versions are the same if and only if the [question note](Question_note) is the same.
Hence a random question may not have an empty question note.

Fill this in as 

	\[ \frac{d}{d@x@}@p@ = @diff(p,x)@ \]

It is crucial to do this now so you can [deploying](deploying) the question for use with students and for subsequent [reviewing](reviewing).

### Further randomisation ###

As a specific example of some of these features, try the question illustrated below.
This contains random numbers, and also examples of variables and expressions selected from a list.

	n = rand(5)+3
	v = rand([x,s,t])
	p = rand([sin(n*v),cos(n*v)])

Then change the Question stem to

Again, we need to use expressions such as `diff(p,v)` throughout the potential response tree.

It is often a good idea to use variables in the question at the outset,
even if there is no need to randomly generate a question initially.

You will also need to update the question note to be

	\[ \frac{d}{d@v@}@p@ = @diff(p,v)@ \]

# Next steps #

STACK's question type is very flexible.

* You can add a [worked solution](CASText#Worked_solution).
* You can change the behaviour of the question with the [options](options)
* You can add plots to all the [CASText](CASText) fields with the [`plot`](../CAS/Maxima#plot) command.

You might like to look at the entry for [feedback](Feedback).
Quality control and  testing your question can be made easier by looking at [testing](Testing).

There are also [sample questions](Sample_questions) for you to import are distributed in the directory
	
	sample_questions

More complex questions are possible including [multi-part mathematical questions](Multi-part_mathematical_questions).
