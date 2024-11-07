# Authoring quick start 9: setting up a quiz

1 - [First question](Authoring_quick_start_1.md) | [2 - Question variables](Authoring_quick_start_2.md) | [3 - Feedback](Authoring_quick_start_3.md) | [4 - Randomisation](Authoring_quick_start_4.md) | [5 - Question tests](Authoring_quick_start_5.md) | [6 - question library](Authoring_quick_start_6.md) | [7 - Multipart questions](Authoring_quick_start_7.md) | [8 - Simplification](Authoring_quick_start_8.md) | 9 - Quizzes

This part of the authoring quick start guide deals with setting up a Moodle quiz. The following video explains the process:

<iframe width="560" height="315" src="https://www.youtube.com/embed/P3bDdNVC6g0" frameborder="0" allowfullscreen></iframe>
### Introduction

The purpose of this document is to provide a basic guide to some of the steps that need to be taken when setting up mathematics questions in a Moodle quiz.  We assume you are using STACK question which you have written or [imported](Authoring_quick_start_6.md). Note, this guide risks duplicating the Moodle quiz documentation, which should also be consulted.

### Constructing a Moodle quiz

Once you have constructed a question bank (either by [importing](Authoring_quick_start_6.md) them or writing them yourselves) you can put them into a Moodle quiz. 

Included within the STACK sample materials is a "Syntax quiz", and it is recommended that you put a copy of this on your own page. This lets students can practice the syntax of how to enter answers into a STACK quiz before they try a specific quiz for your module, and also checks that they can read the mathematics on their machine.

### Question behaviours

Question behaviours dictate how many attempts students are given and how penalties are distributed. There are a number of question behaviours available for a Moodle quiz. The most important are:

**Immediate feedback**, which only lets students have one try at each question, but gives feedback either immediately after answering a question, or only when all questions are answered and submitted. This is useful for standard formative/summative quizzes.

**Deferred feedback**, which only lets students have one try at each question, and does not give feedback until after some given date. This is useful for examinations and coursework quizzes where you don't want students to share worked solutions. 

**Interactive with multiple tries**, which lets students have a finite amount of tries to solve the question, with a hint being displayed after each. It deducts a penalty mark for each incorrect attempt. This is useful for formative quizzes where you want to give hints. Note that the amount of attempts is set as one more than the amount of  `Hints` given in each question. `Hints` are found under the  `Options` section when editing a question.  

**Adaptive**, which lets the student have as many tries as they want, but deducts a penalty from the total score of the question for each time the student got the answer wrong. This is useful for testing questions and for informal practice quizzes.

### Setting up the quiz

1. Go to the Moodle page and click `Turn editing on`.  
2. Go to the block where you want to put the quiz or add an additional block and click `Add an activity or resource`, click `Quiz` and then `Add`.  
3. Give the quiz a name and put any description you want in the Description box.  LaTeX can be used here if you want.  
4. Click on `Timing` and fix the opening and closing times.  
5. Click on `Grade` and fix the `Attempts allowed`.  E.g. you could use `Unlimited` for a practice quiz and `1` for a coursework quiz.  
6. Click on  `Question behaviour` and choose your desired question behaviour, as discussed above.
7. Under `Review options`, you can choose what students are allowed to see during or after the quiz. This includes options such as whether their answer is correct, their mark and feedback. We recommend turning off `Right answer` for both practice and coursework quizzes, and allowing `General feedback` (worked solution) to be on for a practice quiz, but off for coursework quizzes.
8. Finish by clicking `Save and return to course`.  

You can toggle whether students can see the quiz/topic by clicking `Edit` and `Show`.

Note, the Moodle question bank will automatically create a category for the quiz.  It is sometimes sensible to put all the questions used in the quiz into this category, but note that you will only see the category if you have previously navigated to the quiz.

### Adding questions

Click on the quiz, and then `Edit quiz`.  

2. Click `Add`  then click `from question bank`, select a category then one or more of the STACK questions you have created.
3. Click `Add selected questions to the quiz` then click `Save` and return to the main module page.  

To preview the quiz, click on it, then click `Preview quiz now`.

### Extra time

If you have students who need extra time you need to set up `Groups` with these students in. Here is an example for a group of students who need 25% extra time.  

1. Under `Course Adminstration`, click on `Users`, then `Groups`, then `Create group`.  
2. Give the group a name, e.g. "25% extra time".  You can put more details of who the group is for in the `Group description` box.  Click `Save changes`. 
3. `Add/remove users`, then click on the ID for a particular student for this group and click `Add` to put them in the group.  Repeat for each student who needs to be in this group.  
4. Go back and click on the Moodle quiz. In the `Quiz Adminstration`, click on `Group overrides`, then click `Add group override`, choose the relevant group, set the appropriate `Time limit` for the quiz for that group and click `Save`. 

### Viewing results

To see the students' results in for a particular quiz, go the the quiz, then under `Quiz adminstration` click on `Results`,  then `Grades`. This will let you see all attempts, with the overall grade and the grade for each question. You can choose to download the results in Excel here as well.

# Next steps

You should now be able to work with quizzes in Moodle.

This concludes the authoring quick start guide. The STACK documentation is comprehensive, and there are many things you might want to look at next. For example, you can

- learn about more [input types](../Authoring/Inputs/index.md),
- learn about more [answer tests](../Authoring/Answer_Tests/index.md),
- add [plots](../CAS/Maxima_plot.md) to your [CASText](../Authoring/CASText.md) fields,
- add support for [multiple languages](../Authoring/Languages.md),
- learn about using [equivalence reasoning](../Specialist_tools/Equivalence_reasoning/index.md),
- read about [Curve sketching](../Topics/Curve_sketching.md).
- look at more information on [Maxima](../CAS/index.md), particularly the Maxima documentation if you are not very familiar with Maxima's syntax and function names. A graphical Maxima interface like [wxMaxima](http://andrejv.github.com/wxmaxima/) can also be very helpful for finding the appropriate Maxima commands easily.
