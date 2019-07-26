# Authoring quick start 4: randomisation

[1 - First question](Authoring_quick_start.md) | [2 - Question variables](Authoring_quick_start_2.md) | [3 - Feedback](Authoring_quick_start_3.md) | 4 - Randomisation | [5 - Question tests](Authoring_quick_start_5.md) | [6 - Multipart questions](Authoring_quick_start_6.md) | [7 - Simplification](Authoring_quick_start_7.md) | [8 - Quizzes](Authoring_quick_start_8.md)



This part of the authoring quick start guide deals with randomisation. The following video explains the process:

<iframe width="560" height="315" src="https://www.youtube.com/embed/8FTqZ1fTmgs" frameborder="0" allowfullscreen></iframe>
## Introduction

In the last part, we worked with a problem about integrating \(3(x-1)^{-4}\) with respect to x. However, we do not want every student to get the exact same question, as that would allow them to share answers! To solve this problem, we need to randomise the question.

## Random questions

Let's take a look again at the question variables we declared:

```
exp: 3*(x-1)^(-4);
ta: int(exp,x)+c;
```

We defined two local variables `exp` and `ta`, and used these values in other places such as the question text, input and potential response tree. 

We are now in a position to generate a random question. To do this, modify the [question variables](Variables.md#Question_variables) to be

```
a1 : 1+rand(6);
a2 : 1+rand(6);
nn : 1+rand(4);
exp : a1*(x-a2)^(-nn);
ta: int(exp, x)+c;
```

In this new question we are asking the student to find the anti-derivative of a question with a definite form \(a_1(x-a_2)^-nn\). `a1`, `a2` and `nn` are all variables which are assigned random positive integers.  These are then used to define the variable `exp`, used in the question itself. We also have the CAS integrate the expression `exp` and store the result in the variable `ta`. It is good practice to use variables names with more than one character as single-character variables, like `x`, are meant for student input.

Remember that when generating random questions in STACK we talk about _random numbers_ when we really mean _pseudo-random numbers_. To keep track of which random numbers are generated for each user, there is a special `rand` command in STACK, which you should use instead of [Maxima](../CAS/Maxima.md)'s random command. The `rand` command is a general "random thing" generator, see the page on [random generation](../CAS/Random.md) for full details. `rand` can be used to generate random numbers and also to make selections from a list. `rand(n)` will select a random integer from 0 up to, **and not including**, `n`. So  `rand(3)` will select a random number from the list  `[0,1,2]` .

## Question note

Now that as our question contains random numbers, we need to record the actual question variant seen by a particular student. As soon as we use the `rand` function, STACK forces us to add a _Question note_. 
Fill the question note in as

```
\[ \int {@exp@} \mathrm{d}x = {@ta@}.\]
```

Two question variants are considered to be the same if and only if the question note is the same. It is the teacher's responsibility to create sensible notes.

## Deploying random variants

Before a student sees the questions, it is sensible to deploy random variants.  This controls exactly which variants are shown to a student and lets you check that the randomisation is sensible. Scroll to the top of your question and click on `Question tests & deployed variants`. 

To ask STACK to generate a number of question variants, you need `Attempt to automatically deploy the following number of variants:`. Select, for example, `10` and press `Go`.  You should then be able to see 10 random variants of the question. Now students will only be shown one of these.

You also have the option to remove any variants that you don't like. For example, you might not like the variants where nn=1, as these have answers involving logarithms. Hence, you could cross out all these variants. Perhaps a better solution is to return to your `Question variables` and change `nn` to `2+rand(4)`. When you save and go back to `Question tests & deployed variants`, you will see your variants changed. This illustrates a key use of deployed variants: checking for unintentional consequences of the randomisation.

## Preview options

Try previewing your question. As previously mentioned, under `Attempt options`, you have the option to change the question behaviour. `Adaptive mode` is the most useful one for question testing, as it allows you to `check` questions repeatedly. We will discuss question behaviours in more detail later. 

However, notice also that you can choose which deployed `Question variant`  you are answering. This is useful if you want to test a specific variant.

# Next step #

You should now be able to make and deploy random questions in STACK.

##### The next part of the authoring quick start guide looks at [question tests](Authoring_quick_start_5.md).