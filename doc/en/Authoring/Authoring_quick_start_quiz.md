# Authoring quick start: setting up a quiz

Once you have authored questions you are likely to want to include these in a quiz.  Alternatively you might like to set up a quiz using the sample questions.  

The purpose of this document is to provide a guide, from a beginner's point of view, of some of the steps that need to be taken when setting up mathematics questions in a Moodle quiz, using the computer aided assessment package STACK.  Note, this document risks duplicating the Moodle quiz documentation which should also be consulted.

*These have been edited from noted created by Dr Maureen McIver, Department of Mathematical Sciences, Loughborough University, UK, July 2016.*

## Find or author some questions

You need to start by identifying questions for the quiz and the easiest way to do this is to start with a question that is already written and modify it to meet your needs.  

Once you get used to the idea of exporting, importing, copying and modifying questions then you may find it more helpful to start from other questions, or to author your own. See the [Author quick-start guide](Authoring_quick_start.md).  

## Import questions from an existing server

To export existing questions:

1. log into the module on the Moodle server from which you wish to export questions and click on `Question bank` in the Administration block then click on `Export`.  
2. Click on `Moodle XML format` then choose the category you want to export.  Moodle only lets you export individual categories.
3. This will download a file with the all the questions that category.

To import these questions into your course

1. Log into your module on the Moodle server and click on `Question bank` in the Administration block.
2. Click on `Import`.  
3. Click on `Moodle XML format` then drag and drop the question `?.xml`  from your Downloads folder on your desktop then click `Import` then click `Continue`.  
4. A copy of the questions should then appear in the question bank for your module and you can modify them as you want.

## Viewing an existing STACK question

* Choose the Moodle course.
* In the `Question bank` click on the `Preview` icon (spy glass) next to an existing question.  
* Check carefully which behaviour you are using.  If you are using the `Adaptive` you will get a `Check` button, but if you are using `deferred feedback` you will not.
* To change the behaviour, click `Start again`. 
* Enter answers that you think are incorrect or partially correct to see what feedback you get.  Do this as many times as you want to familiarize yourself with entering responses in STACK.  When you have finished click `Close preview`.

## Question tests and Deployed variants

`Question Tests` serve two purposes: (1) to ensure it works and (2) to communicate to others what it does.  These mirror "unit tests" in standard software engineering and will check that STACK's processing of a specific response to a particular version of a question that arises, works as you expect it should.   At the very least you want to ensure that if the student enters what you think is the correct answer to a particular version of the question then they will get full marks.   The tests are set up for a general set of random parameters and then when you `Deploy variants` (see later) each test is checked for each particular version of the question generated.  For full documentation see [question tests](Testing.md).

Click on the `Preview` icon for the question then click on `Question tests & deployed versions`.  This takes you to a page which is unique to the STACK question type, (i.e. no other Moodle question type has these facilities).

The primary purpose of this page is to add "[question tests](Testing.md)".    This page also allows you to do the following.

1. Send the question variables to an interactive CAS session.  Very useful for writing the worked solution in an interactive environment, but you will have to cut and paste the worked solution back into the "general feedback" section when editing the question.
2. You can export a single question (note that Moodle normally expects you to export a whole category)
3. Deploy questions.

You can add more test cases by clicking on `Add another test case`.  All you then need to do is  enter a response that you want to check in the input box then click on `Fill in the rest of the form` to make a passing test-case (risky!) then click `Create test case`.  You can also edit or delete the existing test cases by clicking on the relevant button.  

Note, the testing never "simplifies" the input, so you may need to `ev(...,simp)` if you want to simplify the input, or part of an input, before the system assesses it.

Once you have devised the question tests you need to go to the `Deployed variants` section at the top of the screen and put a number (e.g. 10) in the box `Attempt to deploy the following number of variants` and click `Go`.  This enables STACK to produce a set of versions of the question from which an individual question for a student will be chosen at random (questions are not generated on the fly).  STACK will check that each version that it generates behaves as it should by running each version through the question tests that you have set up.  Success will be indicated by a box saying that `All tests passed!` and failure of a particular version of a question to behave as it should, will be highlighted.  STACK doesn't usually manage to be able to produce as many variants as you request before it starts duplicating versions.  If STACK produces more than three existing duplicates it gives up.

## Constructing a Moodle quiz

Once you have constructed a bank of questions you can put them into a Moodle quiz.   I will only give brief details about this and you may want to consult with other local CAA people for help with this.  

Included within the sample materials is a "Syntax quiz" and it is recommended that you put a copy of this on your own page so that students can practice the syntax of how to enter answers into a STACK quiz before they try a specific quiz for your module, and also check that they can read the mathematics on their machine.

### Setting up the quiz

1. Go to the Moodle page and click `Turn editing on`.  
2. Go to the block where you want to put the quiz or add an additional block and click `Add an activity or resource' then click `Quiz' then click `Add'.  
3. Give the quiz a name and put any description you want in the Description box.  LaTeX can be used here if you want.  
4. Click on `Timing` and fix the opening and closing times.  
5. Click on `Grade` and fix the `Attempts allowed`.  E.g. you could use `Unlimited` for a practice quiz and `1` for a coursework quiz.  
6. Click on `Review options` and I turn off `Right answer` for both practice and coursework quizzes.  I allowed `General feedback` (worked solution) to be on for a practice quiz but turned it off the coursework quiz. (I didn't want worked solutions to be available when the students were doing the coursework.)  
7. Finish by clicking `Save and return to module`.  

Don't forget to ensure that the `Show` button next to the quiz is on as well as the `Show` button next to the topic when you want the students to see the quiz.

Note, the Moodle question bank will create a category for the quiz.  It is sometimes sensible to put all the questions used in the quiz into this category, but not that you will only see the category if you have previously navigated to the quiz.

### Adding questions to the quiz

1. Turn editing on and click on the quiz then click `Edit quiz`.  
2. Click `Add`  then click `from a question bank`, select a category then one or more of the STACK questions you have created.
3. Click `Add selected questions to the quiz` then click `Save` and return to the main module page.  
4. Click on `Edit settings` and check that all the settings are as you wish (see previous section) and if not, change these and save.  

To preview the quiz click on it, then click `Preview quiz now`, answer questions and click on `Submit all and finish`.

### Extra time students

If you have students who need extra time you need to set up `Groups` with these students in.  E.g. who needs 25% extra time.  

1. In the Administration block, click on `Users` then `Groups` then `Create group`.  
2. Give the group a name, e.g. "25% extra time".  You can put more details of who the group is for in the `Group description` box.  Click `Save changes`. 
3. `Add/remove users` then click on the ID for a particular student for this group and click `Add` to put them in the group.  Repeat for each student who needs to be in this group.  
4. Set up other groups for students who need different amounts of extra time, if necessary.

Once you have set up the groups, go back and click on the Moodle quiz on the Learn server page.  In the Administration block click on `Group overrides` then click `Add group override`, choose the relevant group and set the appropriate `Time limit` for the quiz for that group and click `Save`.  Repeat for each group requiring a different amount of extra time.

## Viewing students' results

To see the students' results in Excel for a particular quiz go to the Moodle server page and in the `Administration` box click on `Activity results` then click `Export` then click `Excel spreadsheet` then click on the test name then click `Download'.  


