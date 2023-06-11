# Authoring quick start 6: multipart questions

[1 - First question](Authoring_quick_start_1.md) | [2 - Question variables](Authoring_quick_start_2.md) | [3 - Feedback](Authoring_quick_start_3.md) | [4 - Randomisation](Authoring_quick_start_4.md) | [5 - Question tests](Authoring_quick_start_5.md) | 6 - Multipart questions | [7 - Simplification](Authoring_quick_start_7.md) | [8 - Quizzes](Authoring_quick_start_8.md)



This part of the authoring quick start guide deals with authoring multipart questions. The following video explains the process:

<iframe width="560" height="315" src="https://www.youtube.com/embed/lQhDEnEYZQM" frameborder="0" allowfullscreen></iframe>
Consider the following examples:

### Example 1

Find the equation of the line tangent to \(x^3-2x^2+x\) at the point \(x=2\).

1. Differentiate \(x^3-2x^2+x\) with respect to \(x\).
2. Evaluate your derivative at \(x=2\).
3. Hence, find the equation of the tangent line. \(y=...\)

Since all three parts refer to one polynomial, if randomly generated questions are being used, then each
of these parts needs to reference a single randomly generated equation. Hence parts 1.-3. really form
one item.  Notice here that part 1. is independent of the others. Part 2. requires both the first and second inputs. Part 3. could easily be marked independently, or take into account parts 1 & 2. Notice also that the teacher may choose to award "follow on" marking.

### Example 2

Consider the following question, asked to relatively young school students.

Expand \((x+1)(x+2)\).

In the context it is to be used, it is appropriate to provide students with the
opportunity to "fill in the blanks" in the following equation:

```
(x+1)(x+2) = [?] x2 + [?] x + [?].
```

We argue this is really "one question" with "three inputs". Furthermore, it is likely that the teacher will want the student to complete all boxes before any feedback is assigned, even if separate feedback is generated for each input (i.e. coefficient). This feedback should all be grouped in one place on the screen. Furthermore, in order to identify the possible causes of algebraic mistakes, an automatic marking procedure will require all coefficients simultaneously. It is not satisfactory to have three totally
independent marking procedures.

These two examples illustrate two extreme positions.

1. All inputs within a single multipart item can be assessed independently.
2. All inputs within a single multipart item must be completed before the item can be scored.

Devising multipart questions which satisfy these two extreme positions would be relatively straightforward. However, it is more common to have multipart questions which are between these extremes, as in the case of our first example.

## Authoring a multipart question

Start a new STACK question, and give the question a name, e.g. "Tangent lines".  This question will have three parts.  Start by copying the question variables and question text as follows.  Notice that we have not included any randomisation, but we have used variable names at the outset to facilitate this at a later stage.

__Question variables:__

```
 exp:x^3-2*x^2+x;
 pt:2;
 ta1:diff(exp,x);
 ta2:subst(x=pt,ta1);
 ta3:remainder(exp,(x-pt)^2);
```

__Question text__

Copy the following text into the editor.

	Find the equation of the line tangent to {@exp@} at the point \(x={@pt@}\).
	1. Differentiate {@exp@} with respect to \(x\). [[input:ans1]] [[validation:ans1]] [[feedback:prt1]]
	2. Evaluate your derivative at \(x={@pt@}\). [[input:ans2]] [[validation:ans2]] [[feedback:prt2]]
	3. Hence, find the equation of the tangent line. \(y=\)[[input:ans3]] [[validation:ans3]] [[feedback:prt3]]

Fill in the answer for `ans1` (which exists by default) and remove the `feedback` tag from the "specific feedback" section.  We choose to embed feedback within parts of this question, so that relevant feedback is shown directly underneath the relevant part. Notice there is one potential response tree for each "part".



Update the form by saving your changes, and then ensure the Model Answers are filled in as `ta1`, `ta2` and `ta3`.

STACK creates three potential response trees by detecting the feedback tags automatically.  Next we need to edit potential response trees.  These will establish the properties of the student's answers.

### Stage 1: a working potential response tree

The first stage is to include the simplest potential response trees.  These will simply ensure that answers are "correct".  In each potential response tree, make sure to test that \(\mbox{ans}_i\) is algebraically equivalent to \(\mbox{ta}_i\), for \(i=1,2,3\).  At this stage we have a working question.  Save it and preview the question.  For reference, the correct answers are

```
 ta1 = 3*x^2-4*x+1
 ta2 = 5
 ta3 = 5*x-8
```

### Stage 2: follow-through marking

Next we will implement simple follow-through marking.

Look carefully at part 2.  This does not ask for the "correct answer", only that the student has evaluated the expression in part 1 correctly at the right point.  So the first task is to establish this property by evaluating the answer given in the first part, and comparing it with the second part.  Update node 1 of `prt2` to the following:

```
Answer test: AlgEquiv
SAns: ans2
TAns: subst(x=pt,ans1)
```

Next, add a single node (to `prt2`) with the following:

```
Answer test: AlgEquiv
SAns: ans1
TAns: ta1
```

We now link the true branch of node 1 to node 2 (of `prt2`).  This gives us three outcomes.

Node 1: did they evaluate their expression in part 1 correctly? If "yes", then go to node 2, else if "no", then exit with no marks.

Node 2: did they get part 1 correct?  if "yes" then this is the ideal situation, full marks.  If "no" then choose marks as suit your taste in this situation, and add some feedback, such as the following:

	You have correctly evaluated your answer to part 1 at the given point, but your answer to part 1 is wrong. Please try both parts again.

# Next step #

You should now be able to make a multipart question in STACK. If you have been following this quick-start guide, you should already know some steps you can take to improve this question. For example, you could add [more specific feedback](Authoring_quick_start_3.md), [randomise your question](Authoring_quick_start_4.md) and add [question tests](Authoring_quick_start_5.md).

##### **The next part of the authoring quick start guide looks at [turning simplification off](Authoring_quick_start_7.md).**