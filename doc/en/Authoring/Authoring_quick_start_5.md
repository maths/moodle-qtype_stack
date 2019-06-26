# Authoring Quick Start 5: Question Tests

Authoring Quick Start: [1 - First Question](Authoring_quick_start.md) | [2 - Question Variables](Authoring_quick_start_2.md) | [3 - Feedback](Authoring_quick_start_3.md) |[4 - Randomisation](Authoring_quick_start_4.md) | <u>5 - Question Tests</u> | [6 - Multiple-part Questions](Authoring_quick_start_6.md) | [7 - Simplification](Authoring_quick_start_7.md) | [8 - Quizzes](Authoring_quick_start_8.md)



This part of the Authoring Quick Start Guide deals with using question tests. The following video explains the process:

EMBED VIDEO HERE

## Introduction

In the last couple of parts, we have been working with a simple integration question. Before you continue, confirm that your question variables are set up as follows:

```
a1 : 1+rand(6);
a2 : 1+rand(6);
nn : 2+rand(4);
exp : a1*(x-a2)^(-nn);
ta: int(p, x)+c;
```

Testing questions is time consuming and tedious, but important to ensure questions work.  To help with this process, STACK enables teachers to define "question tests".  The principle is the same as "unit testing" in software engineering.

## Question testing

Scroll to the top of your question and click on `Question tests & deployed variants`. In the last part we used this window to deploy random variants.

Click `Add a test case` to add a test to your question. A test case takes a student input. You then specify what the expected outcome is for that input, namely the score, penalty and answer note you expect to land on. Recall from the last part that the `Answer note` is the name for a specific outcome on a potential response tree.

The penalty is a number deducted from the total mark for each incorrect attempt the student has. By default, it is set to 0.1. You can change the penalty in the `General section` under `Penalty`. Note that this feature is only used in the question behaviours `Interactive with multiple tries` and `Adaptive mode`, as they are the only ones that allow multiple attempts. We will discuss question behaviours in a later part. 

Fill in the following information for your first test case:

```
ans1 = ta
score = 1
penalty = 0
answernote = prt1-2-T
```

I.e., if the student puts in the model answer they should pass the first node (checks if they have integrated correctly) and pass the second node (tests that their answer is factored) and end up with a score of 1 and no penalty. 

Note that the input is evaluated before the test is conducted. Students are not allowed to enter the variable  `ta` because it is a teacher-defined variable, however the evaluated form, fx.  `(x-1)^(-3)`, is an allowed input. For each test case, you can see the un-evaluated input under `Test input`, and the actual input tested under `Value entered`. 

The test will automatically run on all deployed variants. You can also do this manually by clicking on  `Run all tests on all deployed variants` .

You can add as many tests as you think is needed, and it is usually a sensible idea to add one for each case you anticipate.  Add in another test case for

```
ans1 = int(p,x)
score = 0
penalty = 0.1
answernote = prt1-1-F
```

Here we create a test case without a constant of integration. In this case STACK should fail to give students any marks, indicating the test passes!

Finally, let us test that our branch giving feedback on expanded answers works. Add a final test case for 

```
ans1 = expand(ta)
score = 1
penalty = 0
answernote = prt1-2-F
```

You should also use question tests to check that solving every variant requires the competences that you desire. For example, in this question we want students to know (1) increase the power by 1 and (2) divide by the new power. They should not be able to get away with, for example, increasing the power and *multiplying* by the new power. Let's add a test case to check this.

```
ans1 = (a1*(-nn+1))*(x-a2)^(-nn+1)
score = 0
penalty = 0.1
answernote = prt1-F
```

If students are required this knowledge for *all* variants, then all variants should pass this test. However, some of them do not! Specifically when \(nn=2\), \(-nn+1=-1\) so multiplying and dividing are equivalent. Essentially, these random variants are "easier" than the others. This illustrates another key use of question tests - ensuring that all variants are the same difficulty. In light of this, you may want to change `nn` again to ` 3+rand(4)` . Now all variants should pass all question tests.

Quality control is essential, and more information is given in the page on [testing](Testing.md).

# Aside: forbidden words

STACK allows students to use standard mathematical functions, such as `sin`, `cos`, etc. Perhaps surprisingly, it also allows students to use `int`. So in theory, students could input `int(...)+c`, and the system would mark it correct!

To stop this, go to `input:ans1` and under forbidden words, enter `int`. Forbidden words will render words that are normally allowed invalid.

This example nicely illustrates the way validity can be used to help students.  An answer `int(p,x)+c` is a correct response to the question, but it is invalid.  In this example we want them to perform integration, not have the CAS do it!

# Next step #

You should now be able to use question tests in STACK.

##### The next part of the authoring quick start guide looks at [multi-part questions](Authoring_quick_start_6.md).